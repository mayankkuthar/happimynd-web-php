<?php

namespace App\Http\Controllers\Hub;

use App\Services\Hub\HubAdminAuth;
use App\Services\Hub\HubAdminStats;
use App\Services\Hub\HubContentService;
use App\Services\Hub\HubContentValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin dashboard (API_SPEC §5).
 */
class AdminController extends HubController
{
    /** @var HubAdminAuth */
    private $auth;

    /** @var HubAdminStats */
    private $stats;

    /** @var HubContentService */
    private $content;

    /** @var HubContentValidator */
    private $validator;

    public function __construct(
        HubAdminAuth $auth,
        HubAdminStats $stats,
        HubContentService $content,
        HubContentValidator $validator
    ) {
        $this->auth = $auth;
        $this->stats = $stats;
        $this->content = $content;
        $this->validator = $validator;
    }

    public function login(Request $request)
    {
        if (!$this->auth->configured()) {
            return $this->fail("Admin login isn't set up yet.", 503);
        }

        $ip = $this->auth->ip($request);
        $lockedFor = $this->auth->lockMinutesRemaining($ip);
        if ($lockedFor > 0) {
            return $this->fail('Too many attempts. Try again in ' . $lockedFor . ' min.', 429);
        }

        $body = $this->jsonBody($request);
        $email = trim((string) ($body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        // Derive the hash and check the email even on a wrong email so the
        // response timing does not reveal which part was wrong.
        $emailOk = mb_strtolower($email) === mb_strtolower(trim((string) config('hub.admin.email')));
        $passwordOk = $this->auth->verifyPassword($password);

        if (!($emailOk && $passwordOk)) {
            $this->auth->registerFailure($ip);

            return $this->fail('Incorrect email or password.', 401);
        }

        $this->auth->clearFailures($ip);

        return $this->ok()->withCookie($this->auth->makeCookie($request));
    }

    public function logout(Request $request)
    {
        return $this->ok()->withCookie($this->auth->expiredCookie($request));
    }

    public function session(Request $request)
    {
        $authenticated = $this->auth->isValid($request);

        $data = [
            'configured' => $this->auth->configured(),
            'authenticated' => $authenticated,
        ];

        if ($authenticated) {
            $data['email'] = (string) config('hub.admin.email');
        }

        return $this->ok($data);
    }

    public function overview(Request $request)
    {
        $days = (int) $request->query('days', 30);
        $tz = (int) $request->query('tz', 0);

        return $this->ok(['overview' => $this->stats->overview($days, $tz)]);
    }

    public function catalog(Request $request)
    {
        return $this->ok(['overrides' => $this->content->overrides()]);
    }

    public function content(Request $request)
    {
        return $this->ok(['content' => $this->content->allContent()]);
    }

    public function publish(Request $request)
    {
        $body = $this->jsonBody($request);
        $limits = config('hub.limits');

        $catalog = $body['catalog'] ?? null;
        $content = $body['content'] ?? null;

        // ── Validate catalog ─────────────────────────────────────────────
        $catalogRows = [];
        if ($catalog !== null) {
            if (!is_array($catalog)) {
                return $this->fail('Catalog must be a list.');
            }
            if (count($catalog) > $limits['catalog_entries']) {
                return $this->fail('Too many catalog entries (max ' . $limits['catalog_entries'] . ').');
            }

            foreach ($catalog as $index => $entry) {
                if (!is_array($entry)) {
                    return $this->fail('Catalog entry ' . ($index + 1) . ' is invalid.');
                }

                $slug = (string) ($entry['slug'] ?? '');
                if (!preg_match(config('hub.slug_pattern'), $slug)) {
                    return $this->fail('Invalid game slug "' . $slug . '".');
                }

                $normalized = [];
                foreach ([
                    'name' => $limits['name'],
                    'tagline' => $limits['tagline'],
                    'description' => $limits['description'],
                ] as $field => $max) {
                    $provided = array_key_exists($field, $entry) ? $entry[$field] : null;
                    $value = $this->normalizeText($provided);

                    if ($provided !== null && $value === '') {
                        return $this->fail($slug . ' · ' . $field . ': cannot be empty.');
                    }
                    if ($value !== null && mb_strlen($value) > $max) {
                        return $this->fail($slug . ' · ' . $field . ' is too long (max ' . $max . ').');
                    }

                    $normalized[$field] = $value;
                }

                $catalogRows[$slug] = [
                    'name' => $normalized['name'],
                    'tagline' => $normalized['tagline'],
                    'description' => $normalized['description'],
                    'hidden' => (bool) ($entry['hidden'] ?? false),
                ];
            }
        }

        // ── Validate content ─────────────────────────────────────────────
        $contentRows = [];
        if ($content !== null) {
            if (!is_array($content)) {
                return $this->fail('Content must be a list.');
            }
            if (count($content) > $limits['content_entries']) {
                return $this->fail('Too many content entries (max ' . $limits['content_entries'] . ').');
            }

            foreach ($content as $index => $entry) {
                if (!is_array($entry)) {
                    return $this->fail('Content entry ' . ($index + 1) . ' is invalid.');
                }

                $game = (string) ($entry['game'] ?? '');
                $collection = (string) ($entry['collection'] ?? '');

                if (!preg_match(config('hub.slug_pattern'), $game)) {
                    return $this->fail('Invalid game slug in content entry ' . ($index + 1) . '.');
                }
                if ($collection === '') {
                    return $this->fail('Content entry ' . ($index + 1) . ' is missing a collection.');
                }
                if (!$this->validator->collectionExists($game, $collection)) {
                    return $this->fail($game . ' · ' . $collection . ': unknown list.');
                }

                $items = array_key_exists('items', $entry) ? $entry['items'] : null;
                if ($items !== null) {
                    if (!is_array($items)) {
                        return $this->fail($game . ' · ' . $collection . ': items must be a list or null.');
                    }
                    $error = $this->validator->validateCollection($game, $collection, $items);
                    if ($error !== null) {
                        return $this->fail($error);
                    }
                }

                $contentRows[] = ['game' => $game, 'collection' => $collection, 'items' => $items];
            }
        }

        // ── Write in one transaction ─────────────────────────────────────
        $now = $this->utcStr();

        DB::connection('hub')->transaction(function () use ($catalogRows, $contentRows, $now) {
            foreach ($catalogRows as $slug => $row) {
                DB::connection('hub')->table('game_overrides')->where('slug', $slug)->delete();

                $allNull = $row['name'] === null && $row['tagline'] === null && $row['description'] === null;
                if ($allNull && $row['hidden'] === false) {
                    continue; // back to default: no row
                }

                DB::connection('hub')->table('game_overrides')->insert([
                    'slug' => $slug,
                    'name' => $row['name'],
                    'tagline' => $row['tagline'],
                    'description' => $row['description'],
                    'hidden' => $row['hidden'] ? 1 : 0,
                    'updated_at' => $now,
                ]);
            }

            foreach ($contentRows as $row) {
                DB::connection('hub')->table('game_content')
                    ->where('game_slug', $row['game'])
                    ->where('collection', $row['collection'])
                    ->delete();

                if ($row['items'] === null) {
                    continue; // back to the built-in list
                }

                DB::connection('hub')->table('game_content')->insert([
                    'game_slug' => $row['game'],
                    'collection' => $row['collection'],
                    'items' => json_encode($row['items'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => $now,
                ]);
            }
        });

        $this->content->clearCache();

        return $this->ok([
            'overrides' => $this->content->overrides(),
            'content' => $this->content->allContent(),
        ]);
    }

    /**
     * Trim and collapse runs of whitespace to one space. Null stays null.
     */
    private function normalizeText($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return preg_replace('/\s+/u', ' ', trim((string) $value));
    }
}
