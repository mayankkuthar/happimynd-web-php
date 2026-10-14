<?php

namespace App\Services\Hub;

/**
 * Validates admin-published in-game content lists (API_SPEC §5).
 *
 * Returns the first error as a friendly message, or null when the list is
 * valid. Warnings never block publishing (only errors do), so this returns
 * errors only.
 */
class HubContentValidator
{
    /**
     * Does this collection exist for this game?
     */
    public function collectionExists(string $game, string $collection): bool
    {
        if ($this->schemaFor($game, $collection) !== null) {
            return true;
        }

        // Permissive by default; see config/hub_content.php.
        return !(bool) config('hub_content.strict', false);
    }

    /**
     * @return string|null error message, or null if valid
     */
    public function validateCollection(string $game, string $collection, array $items): ?string
    {
        $schema = $this->schemaFor($game, $collection);

        if ($schema === null) {
            // Unknown schema: structural check only.
            foreach ($items as $i => $item) {
                if (!is_array($item) && !is_scalar($item)) {
                    return $this->err($game, $collection, $i, 'is not a valid item.');
                }
            }

            return null;
        }

        $type = $schema['type'] ?? 'fields';

        $countError = $this->checkItemCount($game, $collection, $items, $schema);
        if ($countError !== null) {
            return $countError;
        }

        foreach ($items as $index => $item) {
            if ($type === 'textList') {
                $error = $this->checkField($game, $collection, $index, null, $item, $schema['item'] ?? []);
            } else {
                if (!is_array($item)) {
                    return $this->err($game, $collection, $index, 'is not a valid item.');
                }
                $error = null;
                foreach (($schema['fields'] ?? []) as $fieldName => $fieldSchema) {
                    $error = $this->checkField(
                        $game,
                        $collection,
                        $index,
                        $fieldName,
                        $item[$fieldName] ?? null,
                        $fieldSchema
                    );
                    if ($error !== null) {
                        break;
                    }
                }
            }

            if ($error !== null) {
                return $error;
            }
        }

        return null;
    }

    private function checkItemCount(string $game, string $collection, array $items, array $schema): ?string
    {
        $min = $schema['minItems'] ?? null;
        $max = $schema['maxItems'] ?? null;
        $count = count($items);

        if ($min !== null && $count < $min) {
            return "{$game} · {$collection}: needs at least {$min} items.";
        }
        if ($max !== null && $count > $max) {
            return "{$game} · {$collection}: needs at most {$max} items.";
        }

        return null;
    }

    /**
     * @param int|string|null $fieldName null for a plain textList item
     */
    private function checkField(string $game, string $collection, int $index, $fieldName, $value, array $schema): ?string
    {
        $type = $schema['type'] ?? 'text';
        $label = $fieldName !== null ? (string) $fieldName : 'value';
        $optional = (bool) ($schema['optional'] ?? false);

        if ($value === null || (is_string($value) && trim($value) === '')) {
            if ($optional) {
                return null;
            }

            return $this->err($game, $collection, $index, "{$label} cannot be empty.");
        }

        switch ($type) {
            case 'text':
                return $this->checkText($game, $collection, $index, $label, (string) $value, $schema);

            case 'emoji':
                $str = (string) $value;
                if (mb_strlen($str) > 16) {
                    return $this->err($game, $collection, $index, "{$label} must be 16 characters or fewer.");
                }
                if (!$this->startsWithEmoji($str)) {
                    return $this->err($game, $collection, $index, "{$label} must start with an emoji.");
                }

                return null;

            case 'select':
                $options = $schema['options'] ?? [];
                if (!in_array($value, $options, true)) {
                    return $this->err($game, $collection, $index, "{$label} is not one of the allowed values.");
                }

                return null;

            case 'textList':
                if (!is_array($value)) {
                    return $this->err($game, $collection, $index, "{$label} must be a list.");
                }
                $count = $schema['count'] ?? null;
                if ($count !== null && count($value) !== $count) {
                    return $this->err($game, $collection, $index, "needs exactly {$count} options.");
                }
                $min = $schema['min'] ?? null;
                $max = $schema['maxItems'] ?? null;
                if ($min !== null && count($value) < $min) {
                    return $this->err($game, $collection, $index, "{$label} needs at least {$min} options.");
                }
                if ($max !== null && count($value) > $max) {
                    return $this->err($game, $collection, $index, "{$label} needs at most {$max} options.");
                }

                $seen = [];
                foreach ($value as $sub) {
                    if (!is_string($sub) || trim($sub) === '') {
                        return $this->err($game, $collection, $index, "{$label} has an empty option.");
                    }
                    $itemSchema = $schema['item'] ?? ['type' => 'text'];
                    $subError = $this->checkField($game, $collection, $index, $label, $sub, $itemSchema);
                    if ($subError !== null) {
                        return $subError;
                    }
                    $key = mb_strtolower(trim($sub));
                    if (isset($seen[$key])) {
                        return $this->err($game, $collection, $index, "{$label} has a duplicate option.");
                    }
                    $seen[$key] = true;
                }

                return null;
        }

        return null;
    }

    private function checkText(string $game, string $collection, int $index, string $label, string $value, array $schema): ?string
    {
        $max = $schema['max'] ?? null;
        if ($max !== null && mb_strlen($value) > $max) {
            return $this->err($game, $collection, $index, "{$label} is too long (max {$max}).");
        }

        foreach (($schema['mustInclude'] ?? []) as $placeholder) {
            if (strpos($value, $placeholder) === false) {
                return $this->err($game, $collection, $index, "{$label} must include {$placeholder}.");
            }
        }

        return null;
    }

    private function schemaFor(string $game, string $collection): ?array
    {
        $games = config('hub_content.games', []);
        if (isset($games[$game][$collection])) {
            return $games[$game][$collection];
        }

        $common = config('hub_content.common', []);

        return $common[$collection] ?? null;
    }

    private function err(string $game, string $collection, $index, string $problem): string
    {
        return "{$game} · {$collection} (item " . ($index + 1) . "): {$problem}";
    }

    private function startsWithEmoji(string $value): bool
    {
        // Match a leading regional indicator, emoji, or symbol/pictograph.
        return (bool) preg_match(
            '/^(?:\x{1F1E6}-\x{1F1FF}|[\x{1F300}-\x{1FAFF}]|[\x{2600}-\x{27BF}]|[\x{2B00}-\x{2BFF}])/u',
            $value
        );
    }
}
