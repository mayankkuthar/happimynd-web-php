<?php

namespace App\Http\Controllers\Hr;

use App\Services\Hr\ExcelExport;
use App\Services\Hr\GcsStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * POST /export (API_SPEC §5): the completed records of one source as .xlsx.
 * Port of the intern app's export.routes.ts + services/excel.ts.
 *
 *  - Body: { source, ids?: string[] | null, highlight_low_confidence?: bool = true }.
 *  - Only status = 'done', ordered by created_at. An empty `ids` selects nothing.
 *  - None found: 400 "No completed records to export".
 *  - Marks them viewed BEFORE building, so the "Reviewed" column agrees with
 *    the table; a failure there only logs a warning.
 *  - A copy goes to storage under exports/<YYYY-MM>/<file name>, after the
 *    response is sent, and never affects the download.
 */
class ExportController extends HrController
{
    private const XLSX_MIME = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    public function export(Request $request, GcsStorage $storage): Response
    {
        $body = $this->jsonBody($request);

        $source = $body['source'] ?? null;
        if ($err = $this->checkSource(is_string($source) ? $source : null, 'body')) {
            return $err;
        }

        $ids = $body['ids'] ?? null;
        if ($ids !== null) {
            if (!is_array($ids) || array_filter($ids, function ($id) {
                return !is_string($id);
            }) !== []) {
                return $this->invalid('ids', 'ids must be a list of strings or null');
            }
        }

        $highlight = $body['highlight_low_confidence'] ?? true;
        if (!is_bool($highlight)) {
            return $this->invalid('highlight_low_confidence', 'highlight_low_confidence must be a boolean');
        }

        $rows = [];
        if ($ids === null || $ids !== []) {
            $query = $this->db()->table('records')
                ->where('source', $source)
                ->where('status', 'done')
                ->orderBy('created_at');
            if ($ids !== null) {
                $query->whereIn('id', array_values(array_unique($ids)));
            }
            $rows = $query->get(['id', 'viewed', 'department', 'used_ocr', 'fields', 'confidence'])->all();
        }

        if ($rows === []) {
            return $this->detail('No completed records to export', 400);
        }

        $viewedNow = false;
        try {
            $this->db()->table('records')->whereIn('id', array_map(function ($r) {
                return $r->id;
            }, $rows))->update(['viewed' => 1]);
            $viewedNow = true;
        } catch (Throwable $e) {
            Log::warning('HR export: could not mark records viewed', ['message' => $e->getMessage()]);
        }

        $records = array_map(function ($r) use ($viewedNow) {
            return [
                'viewed' => $viewedNow || ((int) $r->viewed) === 1,
                'department' => $r->department !== null ? (string) $r->department : null,
                'used_ocr' => ((int) $r->used_ocr) === 1,
                'fields' => $this->decodeJson($r->fields),
                'confidence' => $this->decodeJson($r->confidence),
            ];
        }, $rows);

        $bytes = ExcelExport::fromSchemaFile(resource_path('hr/schema.json'))->build($records, $highlight);
        $fileName = ExcelExport::fileName();

        // Archive after the response has gone out (exports/<YYYY-MM>/<file>).
        $archiveKey = 'exports/' . date('Y-m') . '/' . $fileName;
        app()->terminating(function () use ($storage, $archiveKey, $bytes) {
            try {
                if (!$storage->configured() || !$storage->upload($archiveKey, $bytes, self::XLSX_MIME)) {
                    Log::warning('HR export: could not archive the export', ['key' => $archiveKey]);
                }
            } catch (Throwable $e) {
                Log::warning('HR export: could not archive the export', ['message' => $e->getMessage()]);
            }
        });

        return response($bytes, 200, [
            'Content-Type' => self::XLSX_MIME,
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => (string) strlen($bytes),
        ]);
    }

    private function decodeJson($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : [];
    }
}
