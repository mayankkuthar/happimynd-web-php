<?php

namespace App\Http\Controllers\Hub;

use App\Services\Hub\HubContentService;
use Illuminate\Http\Request;

/**
 * Public content endpoints (API_SPEC §4).
 */
class ContentController extends HubController
{
    /** @var HubContentService */
    private $content;

    public function __construct(HubContentService $content)
    {
        $this->content = $content;
    }

    public function catalog()
    {
        return $this->ok(['overrides' => $this->content->cachedOverrides()])
            ->header('Cache-Control', 'public, max-age=30');
    }

    public function content(Request $request, $slug)
    {
        if (!preg_match(config('hub.slug_pattern'), (string) $slug)) {
            return $this->fail('Not found.', 404);
        }

        $map = $this->content->cachedContentMap();
        $collections = $map[$slug] ?? [];

        return $this->ok(['collections' => (object) $collections])
            ->header('Cache-Control', 'public, max-age=30');
    }
}
