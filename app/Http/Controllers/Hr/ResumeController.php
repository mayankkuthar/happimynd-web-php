<?php

namespace App\Http\Controllers\Hr;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * HR Auto resume endpoints (API_SPEC §4–§5) over the `records` table.
 *
 * `text_content` is never returned in lists. Manual edits and reprocess touch
 * only the JSON columns so the STORED generated candidate columns follow.
 */
class ResumeController extends HrController
{
    private const LIST_COLUMNS = [
        'id', 'file_name', 'size_bytes', 'content_type', 'source', 'department',
        'status', 'viewed', 'storage_key', 'fields', 'confidence', 'error',
        'used_ocr', 'page_count', 'ai_status', 'duplicate_of', 'log',
        'created_at', 'finished_at',
    ];

    /** GET /resumes?source= (alias /records?source=). */
    public function index(Request $request): JsonResponse
    {
        $source = $request->query('source');

        if ($err = $this->checkSource($source, 'query')) {
            return $err;
        }

        $rows = $this->db()->table('records')
            ->where('source', $source)
            ->orderBy('created_at')
            ->get(self::LIST_COLUMNS);

        $records = [];
        foreach ($rows as $row) {
            $records[] = $this->presentSummary($row);
        }

        return $this->ok([
            'records' => $records,
            'stats' => $this->stats($source),
        ]);
    }

    /** GET /resumes/{id} (alias /records/{id}). */
    public function show(string $id): JsonResponse
    {
        $row = $this->fetchRow($id);

        if ($row === null) {
            return $this->detail('Record not found', 404);
        }

        return $this->ok(array_merge(
            $this->presentSummary($row),
            [
                'text' => (string) ($row->text_content ?? ''),
                'candidate' => $this->presentCandidate($row),
            ]
        ));
    }

    /** PATCH /resumes/{id} — manual edit of extracted fields (API_SPEC §4). */
    public function update(Request $request, string $id): JsonResponse
    {
        $body = $this->jsonBody($request);
        $fields = $body['fields'] ?? null;

        if (!is_array($fields) || $fields === []) {
            return $this->missing('fields', 'body');
        }

        $listFields = (array) config('hr.list_fields', []);
        $knownFields = (array) config('hr.fields', []);

        $fieldParts = [];
        $bindings = [];
        $confidenceParts = [];

        foreach ($fields as $key => $value) {
            if (!is_string($key) || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) {
                return $this->invalid('fields', "Unknown field '" . (is_string($key) ? $key : '?') . "'");
            }

            if ($knownFields !== [] && !in_array($key, $knownFields, true)) {
                return $this->invalid('fields', "Unknown field '" . $key . "'");
            }

            $path = "'$." . $key . "'";

            if (in_array($key, $listFields, true)) {
                $normalised = $this->normaliseList($value);
                if ($normalised === null) {
                    return $this->invalid('fields', "Field '" . $key . "' must be a list");
                }
                $fieldParts[] = $path . ', CAST(? AS JSON)';
                $bindings[] = json_encode($normalised);
            } else {
                if (is_array($value)) {
                    return $this->invalid('fields', "Field '" . $key . "' is not a list");
                }
                $fieldParts[] = $path . ', ?';
                $bindings[] = $this->normaliseScalar($value);
            }

            $confidenceParts[] = $path . ", 'high'";
        }

        $fieldsSql = 'UPDATE `records` SET `fields` = JSON_SET(`fields`, ' . implode(', ', $fieldParts)
            . ') WHERE `id` = ?';

        $confidenceSql = 'UPDATE `records` SET `confidence` = JSON_SET(`confidence`, '
            . implode(', ', $confidenceParts) . ') WHERE `id` = ?';

        if (!$this->db()->table('records')->where('id', $id)->exists()) {
            return $this->detail('Record not found', 404);
        }

        $this->db()->transaction(function () use ($fieldsSql, $bindings, $confidenceSql, $id) {
            $this->db()->update($fieldsSql, array_merge($bindings, [$id]));
            $this->db()->update($confidenceSql, [$id]);
        });

        $row = $this->fetchRow($id);

        return $row === null ? $this->detail('Record not found', 404) : $this->ok($this->presentSummary($row));
    }

    /** PATCH /resumes/{id}/viewed. */
    public function viewed(Request $request, string $id): JsonResponse
    {
        $body = $this->jsonBody($request);

        if (!array_key_exists('viewed', $body) || !is_bool($body['viewed'])) {
            return $this->invalid('viewed', 'Field required', 'body');
        }

        $row = $this->fetchRow($id);
        if ($row === null) {
            return $this->detail('Record not found', 404);
        }

        $this->db()->table('records')->where('id', $id)->update(['viewed' => $body['viewed'] ? 1 : 0]);

        $row = $this->fetchRow($id);

        return $this->ok($this->presentSummary($row));
    }

    /** POST /resumes/{id}/reprocess. */
    public function reprocess(string $id): JsonResponse
    {
        $row = $this->fetchRow($id);
        if ($row === null) {
            return $this->detail('Record not found', 404);
        }

        $this->db()->table('records')->where('id', $id)->update([
            'status' => 'queued',
            'log' => $this->db()->raw('JSON_ARRAY()'),
            'error' => null,
            'created_at' => $this->db()->raw('NOW(3)'),
            'finished_at' => null,
            'claimed_at' => null,
            'attempts' => 0,
        ]);

        $row = $this->fetchRow($id);

        return $this->ok($this->presentSummary($row));
    }

    /** DELETE /resumes/{id} — stats count every source (API_SPEC §4). */
    public function destroy(string $id): JsonResponse
    {
        $storageKey = null;
        $found = false;

        $this->db()->transaction(function () use ($id, &$storageKey, &$found) {
            $row = $this->db()->table('records')->where('id', $id)->lockForUpdate()->first(['id', 'storage_key']);
            if ($row === null) {
                return;
            }
            $found = true;
            $storageKey = $row->storage_key;
            $this->db()->table('records')->where('id', $id)->delete();
        });

        if (!$found) {
            return $this->detail('Record not found', 404);
        }

        return $this->ok([
            'deleted' => $id,
            'storage_key' => $storageKey,
            'stats' => $this->stats(null),
        ]);
    }

    /** DELETE /resumes?source= (alias /records?source=). */
    public function clear(Request $request): JsonResponse
    {
        $source = $request->query('source');

        if ($err = $this->checkSource($source, 'query')) {
            return $err;
        }

        $keys = [];

        $deleted = $this->db()->transaction(function () use ($source, &$keys) {
            $rows = $this->db()->table('records')
                ->where('source', $source)
                ->lockForUpdate()
                ->get(['id', 'storage_key']);

            foreach ($rows as $row) {
                if ($row->storage_key !== null && $row->storage_key !== '') {
                    $keys[] = $row->storage_key;
                }
            }

            return $this->db()->table('records')->where('source', $source)->delete();
        });

        return $this->ok([
            'deleted' => (int) $deleted,
            'storage_keys' => $keys,
            'stats' => $this->stats($source),
        ]);
    }

    /** GET /logs?source= — plain-text extraction log (API_SPEC §5). */
    public function logs(Request $request)
    {
        $source = $request->query('source');

        if ($err = $this->checkSource($source, 'query')) {
            return $err;
        }

        $rows = $this->db()->table('records')
            ->where('source', $source)
            ->orderBy('created_at')
            ->get(['id', 'file_name', 'status', 'page_count', 'used_ocr', 'error', 'log', 'confidence']);

        if (count($rows) === 0) {
            $body = "No records processed yet.\n";
        } else {
            $blocks = [];
            foreach ($rows as $row) {
                $ocr = ((int) $row->used_ocr) === 1 ? 'True' : 'False';
                $line = '=== ' . $row->file_name . ' [' . $row->status . '] ===' . "\n";
                $line .= '  id: ' . $row->id . '  pages: ' . (int) $row->page_count . '  ocr: ' . $ocr . "\n";

                if ($row->error !== null && $row->error !== '') {
                    $line .= '  ERROR: ' . $row->error . "\n";
                }

                foreach ($this->jsonArray($row->log) as $entry) {
                    $line .= '  - ' . $entry . "\n";
                }

                $low = [];
                $confidence = json_decode((string) $row->confidence, true);
                if (is_array($confidence)) {
                    foreach ($confidence as $field => $level) {
                        if ($level === 'low') {
                            $low[] = $field;
                        }
                    }
                }
                if ($low !== []) {
                    sort($low);
                    $line .= '  low confidence: ' . implode(', ', $low) . "\n";
                }

                $blocks[] = $line;
            }

            $body = implode("\n", $blocks) . "\n";
        }

        return response($body, 200)
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="extraction_log.txt"');
    }

    /**
     * POST /records — the database half of upload, for the intern app.
     *
     * The intern app stores the file in GCS, then calls this with the metadata.
     * We do the source-scoped duplicate check and insert (API_SPEC §4).
     */
    public function store(Request $request): JsonResponse
    {
        $body = $this->jsonBody($request);

        $source = $body['source'] ?? null;
        if ($err = $this->checkSource($source, 'body')) {
            return $err;
        }

        $department = $body['department'] ?? null;
        if ($err = $this->checkDepartment($department, 'body')) {
            return $err;
        }

        $fileName = isset($body['file_name']) ? trim((string) $body['file_name']) : '';
        if ($fileName === '') {
            return $this->missing('file_name', 'body');
        }

        $checksum = isset($body['checksum']) ? strtolower(trim((string) $body['checksum'])) : '';
        if (!preg_match('/^[a-f0-9]{64}$/', $checksum)) {
            return $this->invalid('checksum', 'checksum must be a 64-character SHA-256 hex string');
        }

        $sizeBytes = isset($body['size_bytes']) ? (int) $body['size_bytes'] : 0;
        if ($sizeBytes < 0) {
            return $this->invalid('size_bytes', 'size_bytes must be >= 0');
        }

        $contentType = isset($body['content_type']) && $body['content_type'] !== ''
            ? (string) $body['content_type']
            : 'application/pdf';

        $storageKey = isset($body['storage_key']) && $body['storage_key'] !== ''
            ? (string) $body['storage_key']
            : null;

        $id = bin2hex(random_bytes(6)); // 12 lowercase hex characters

        $duplicateOf = null;
        $log = [];

        try {
            $this->db()->transaction(function () use (
                $source, $checksum, $id, $fileName, $sizeBytes, $contentType,
                $department, $storageKey, &$duplicateOf, &$log
            ) {
                $existing = $this->db()->table('records')
                    ->where('source', $source)
                    ->where('checksum', $checksum)
                    ->whereNull('duplicate_of')
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->first(['id', 'file_name']);

                if ($existing !== null) {
                    $duplicateOf = $existing->id;
                    $log = ['Duplicate of ' . $existing->file_name . ' (identical file contents).'];
                }

                $this->db()->insert(
                    'INSERT INTO `records` '
                    . '(`id`,`file_name`,`size_bytes`,`checksum`,`source`,`department`,`status`,`viewed`,'
                    . '`storage_key`,`content_type`,`fields`,`confidence`,`text_content`,`log`,`error`,'
                    . '`used_ocr`,`page_count`,`ai_status`,`duplicate_of`,`attempts`,`created_at`) '
                    . 'VALUES (?,?,?,?,?,?,?,?,?,?,JSON_OBJECT(),JSON_OBJECT(),?,CAST(? AS JSON),NULL,0,0,?,?,0,NOW(3))',
                    [
                        $id, $fileName, $sizeBytes, $checksum, $source, $department, 'queued', 0,
                        $storageKey, $contentType, '', json_encode($log), 'disabled', $duplicateOf,
                    ]
                );
            });
        } catch (Throwable $e) {
            return $this->detail('Could not be saved — please retry', 500);
        }

        $row = $this->fetchRow($id);

        return $this->ok([
            'record' => $row ? $this->presentSummary($row) : null,
            'stats' => $this->stats($source),
        ], 200);
    }

    private function fetchRow(string $id)
    {
        return $this->db()->table('records')->where('id', $id)->first();
    }

    private function normaliseList($value): ?array
    {
        if ($value === null) {
            return [];
        }

        if (is_array($value)) {
            $items = $value;
        } elseif (is_string($value)) {
            $items = explode(';', $value);
        } else {
            return null;
        }

        $out = [];
        foreach ($items as $item) {
            if (!is_scalar($item) && $item !== null) {
                return null;
            }
            $trimmed = trim((string) $item);
            if ($trimmed !== '') {
                $out[] = $trimmed;
            }
        }

        return $out;
    }

    private function normaliseScalar($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }

    /** Best-effort candidate profile derived from `fields` (API_SPEC §4). */
    private function presentCandidate($row): array
    {
        $fields = json_decode((string) $row->fields, true);
        if (!is_array($fields)) {
            $fields = [];
        }

        $get = function (string $key) use ($fields) {
            return array_key_exists($key, $fields) && $fields[$key] !== '' ? $fields[$key] : null;
        };

        $asStringArray = function ($value) {
            if (is_array($value)) {
                return array_values(array_filter(array_map('strval', $value), fn ($v) => trim($v) !== ''));
            }
            if (is_string($value) && $value !== '') {
                return [$value];
            }

            return [];
        };

        $years = $get('total_experience_years');
        $years = is_numeric($years) ? (float) $years : null;

        return [
            'id' => (string) $row->id,
            'name' => $get('candidate_name'),
            'email' => $get('email'),
            'phone' => $get('phone'),
            'location' => $get('location'),
            'education' => $asStringArray($get('education') ?? $get('highest_qualification')),
            'experience' => $asStringArray($get('employment_history')),
            'skills' => $asStringArray($get('technical_skills')),
            'projects' => $asStringArray($get('project_names')),
            'certifications' => $asStringArray($get('certification_names')),
            'linkedin' => $get('linkedin'),
            'github' => $get('github'),
            'portfolio' => $get('portfolio'),
            'yearsOfExperience' => $years,
        ];
    }
}
