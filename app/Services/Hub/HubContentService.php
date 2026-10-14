<?php

namespace App\Services\Hub;

use App\Models\Hub\HubGameContent;
use App\Models\Hub\HubGameOverride;
use Illuminate\Support\Facades\Cache;

/**
 * Reads/returns the admin-edited catalog and in-game content, with the short
 * public cache described in API_SPEC §6.
 */
class HubContentService
{
    /**
     * Catalog overrides (API_SPEC §4). Only games with a row are listed; a
     * null field means "use the text coded in the app".
     *
     * @return array<int, array{slug: string, name: ?string, tagline: ?string, description: ?string, hidden: bool}>
     */
    public function overrides(): array
    {
        return HubGameOverride::query()
            ->orderBy('slug')
            ->get()
            ->map(function ($row) {
                return [
                    'slug' => (string) $row->slug,
                    'name' => $row->name,
                    'tagline' => $row->tagline,
                    'description' => $row->description,
                    'hidden' => (bool) $row->hidden,
                ];
            })
            ->all();
    }

    /**
     * Every customised list, parsed. Rows whose items are not a JSON array are
     * skipped so the game falls back to its built-in list.
     *
     * @return array<int, array{game: string, collection: string, items: array}>
     */
    public function allContent(): array
    {
        $out = [];

        foreach (HubGameContent::query()->orderBy('game_slug')->orderBy('collection')->get() as $row) {
            $items = json_decode((string) $row->items, true);
            if (!is_array($items)) {
                continue;
            }

            $out[] = [
                'game' => (string) $row->game_slug,
                'collection' => (string) $row->collection,
                'items' => $items,
            ];
        }

        return $out;
    }

    /**
     * Collection map for one game: collection => parsed items.
     *
     * @return array<string, array>
     */
    public function collectionsForSlug(string $slug): array
    {
        $map = [];

        foreach (HubGameContent::query()->where('game_slug', $slug)->get() as $row) {
            $items = json_decode((string) $row->items, true);
            if (!is_array($items)) {
                continue;
            }
            $map[(string) $row->collection] = $items;
        }

        return $map;
    }

    public function cachedOverrides(): array
    {
        return Cache::remember(
            config('hub.cache.catalog_key'),
            config('hub.cache.ttl'),
            function () {
                return $this->overrides();
            }
        );
    }

    /**
     * Content grouped by game slug, for the cached public content endpoint.
     *
     * @return array<string, array<string, array>>
     */
    public function cachedContentMap(): array
    {
        return Cache::remember(
            config('hub.cache.content_key'),
            config('hub.cache.ttl'),
            function () {
                $map = [];
                foreach (HubGameContent::query()->get() as $row) {
                    $items = json_decode((string) $row->items, true);
                    if (!is_array($items)) {
                        continue;
                    }
                    $map[(string) $row->game_slug][(string) $row->collection] = $items;
                }

                return $map;
            }
        );
    }

    public function clearCache(): void
    {
        Cache::forget(config('hub.cache.catalog_key'));
        Cache::forget(config('hub.cache.content_key'));
    }
}
