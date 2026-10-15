<?php

namespace App\Services\Hub;

/**
 * Validates admin-published in-game content lists (API_SPEC §5).
 *
 * Port of `validateCollection()` from the reference `content-schemas.ts`.
 * Returns the first error as a friendly message, or null when the list is
 * valid. Warnings never block publishing (only errors do), so warnings are
 * discarded here.
 *
 * Message format matches the documented example:
 *   "guess-my-answer · Questions (item 1): Needs exactly 4 options."
 */
class HubContentValidator
{
    /** Unicode whitespace trimmed by JS String.prototype.trim(). */
    private const JS_WS = '\x{0009}-\x{000D}\x{0020}\x{00A0}\x{1680}\x{2000}-\x{200A}\x{2028}\x{2029}\x{202F}\x{205F}\x{3000}\x{FEFF}';

    /**
     * Does this collection exist for this game?
     */
    public function collectionExists(string $game, string $collection): bool
    {
        return HubContentSchema::find($game, $collection) !== null;
    }

    /**
     * @return string|null error message, or null if valid
     */
    public function validateCollection(string $game, string $collection, array $items): ?string
    {
        $def = HubContentSchema::find($game, $collection);
        if ($def === null) {
            return null;
        }

        foreach ($this->validate($def, array_values($items)) as $issue) {
            if ($issue['severity'] === 'error') {
                return $this->format($game, $def, $issue);
            }
        }

        return null;
    }

    /**
     * Every problem with a list, in the same order as the reference. Errors
     * would break the game; warnings are cosmetic.
     *
     * @return array<int, array>
     */
    private function validate(array $def, array $items): array
    {
        $issues = [];
        $count = count($items);

        if ($count < $def['minItems']) {
            $noun = $def['minItems'] === 1 ? 'item' : 'items';
            $issues[] = HubContentSchema::issue(
                'error',
                "This list needs at least {$def['minItems']} {$noun} (it has {$count})."
            );
        }
        if ($count > $def['maxItems']) {
            $issues[] = HubContentSchema::issue('error', "This list can have at most {$def['maxItems']} items.");
        }

        $records = $this->toRecords($def, $items);
        $seen = [];
        $firstTextField = null;
        foreach ($def['fields'] as $f) {
            if ($f['type'] === 'text') {
                $firstTextField = $f;
                break;
            }
        }

        foreach ($records as $index => $item) {
            foreach ($def['fields'] as $f) {
                $key = $f['key'];
                $value = $item[$key] ?? null;
                $fieldIssues = $this->checkField($index, $key, $value, $f);
                foreach ($fieldIssues as $issue) {
                    $issues[] = $issue;
                }
            }

            // Duplicates (by the first text field) are confusing but harmless.
            if ($firstTextField !== null) {
                $dupKey = mb_strtolower($this->trimJs($this->jsString($item[$firstTextField['key']] ?? null)));
            } else {
                $dupKey = '';
            }

            if ($dupKey !== '') {
                if (array_key_exists($dupKey, $seen)) {
                    $issues[] = HubContentSchema::issue(
                        'warning',
                        'Same as item ' . ($seen[$dupKey] + 1) . '.',
                        $index,
                        $firstTextField['key']
                    );
                } else {
                    $seen[$dupKey] = $index;
                }
            }
        }

        if (isset($def['rules']) && is_callable($def['rules'])) {
            foreach (($def['rules'])($records) as $issue) {
                $issues[] = $issue;
            }
        }

        return $issues;
    }

    /**
     * @param array $schema one field definition
     * @return array<int, array> issues (may be empty)
     */
    private function checkField(int $index, string $key, $value, array $schema): array
    {
        $type = $schema['type'] ?? 'text';
        $label = $schema['label'];
        $optional = (bool) ($schema['optional'] ?? false);

        if ($type === 'text') {
            $s = is_string($value) ? $this->trimJs($value) : '';
            if ($s === '') {
                return $optional ? [] : [HubContentSchema::issue('error', "{$label} can't be empty.", $index, $key)];
            }

            $out = [];
            $len = $this->lenOf($s);
            if ($len > $schema['max']) {
                $out[] = HubContentSchema::issue('error', "{$label} is over {$schema['max']} characters.", $index, $key);
            } elseif ($len > $schema['soft']) {
                $out[] = HubContentSchema::issue('warning', "{$label} is long ({$len} characters) and may wrap or be cut off in the game.", $index, $key);
            }

            foreach ($schema['mustInclude'] ?? [] as $placeholder) {
                if (mb_strpos($s, $placeholder) === false) {
                    $out[] = HubContentSchema::issue('error', "{$label} must include {$placeholder} (it's replaced with a name in the game).", $index, $key);
                }
            }

            return $out;
        }

        if ($type === 'emoji') {
            $s = is_string($value) ? $this->trimJs($value) : '';
            if ($s === '') {
                return [HubContentSchema::issue('error', "{$label} can't be empty.", $index, $key)];
            }
            if ($this->lenOf($s) > 16 || !$this->isEmoji($s)) {
                return [HubContentSchema::issue('error', "{$label} should be a single emoji.", $index, $key)];
            }

            return [];
        }

        if ($type === 'select') {
            $options = $schema['options'] ?? [];

            if (empty($options)) {
                // Defensive: a select with no known options accepts any
                // non-empty value rather than rejecting everything.
                if (!is_string($value) || $this->trimJs($value) === '') {
                    return [HubContentSchema::issue('error', 'Pick a ' . mb_strtolower($label) . '.', $index, $key)];
                }

                return [];
            }

            foreach ($options as $o) {
                if ($o['value'] === $value) {
                    return [];
                }
            }

            return [HubContentSchema::issue('error', 'Pick a ' . mb_strtolower($label) . '.', $index, $key)];
        }

        if ($type === 'textList') {
            $list = is_array($value) ? array_values($value) : [];
            if (count($list) !== $schema['count']) {
                return [HubContentSchema::issue('error', 'Needs exactly ' . $schema['count'] . ' ' . mb_strtolower($label) . '.', $index, $key)];
            }

            $out = [];
            foreach ($list as $i => $o) {
                $s = is_string($o) ? $this->trimJs($o) : '';
                if ($s === '') {
                    $out[] = HubContentSchema::issue('error', "{$label} " . ($i + 1) . " can't be empty.", $index, $key);
                    continue;
                }
                $len = $this->lenOf($s);
                if ($len > $schema['max']) {
                    $out[] = HubContentSchema::issue('error', "{$label} " . ($i + 1) . " is over {$schema['max']} characters.", $index, $key);
                } elseif ($len > $schema['soft']) {
                    $out[] = HubContentSchema::issue('warning', "{$label} " . ($i + 1) . ' is long and may wrap.', $index, $key);
                }
            }

            $distinct = [];
            foreach ($list as $o) {
                $distinct[mb_strtolower($this->trimJs($this->jsString($o)))] = true;
            }
            if (count($distinct) < count($list)) {
                $out[] = HubContentSchema::issue('error', 'Two ' . mb_strtolower($label) . ' are the same, so a match would be ambiguous.', $index, $key);
            }

            return $out;
        }

        return [];
    }

    /**
     * Scalar lists store plain strings; treat them as { value } while checking.
     *
     * @return array<int, array<string, mixed>>
     */
    private function toRecords(array $def, array $items): array
    {
        $scalar = (bool) ($def['scalar'] ?? false);

        return array_map(function ($item) use ($scalar) {
            if ($scalar) {
                return ['value' => $item];
            }

            return is_array($item) ? $item : [];
        }, $items);
    }

    private function format(string $game, array $def, array $issue): string
    {
        $out = $game . ' · ' . $def['label'];

        if (array_key_exists('index', $issue) && $issue['index'] !== null) {
            $out .= ' (item ' . ($issue['index'] + 1) . ')';
        }

        return $out . ': ' . $issue['message'];
    }

    /** JS String(value): '' for null/undefined/array, else the scalar. */
    private function jsString($value): string
    {
        if ($value === null || is_array($value)) {
            return '';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }

    /** Mirrors JS String.prototype.trim(). */
    private function trimJs(string $s): string
    {
        $pattern = '/^[' . self::JS_WS . ']+|[' . self::JS_WS . ']+$/u';

        return (string) preg_replace($pattern, '', $s);
    }

    /** String length in UTF-16 code units, matching JS `.length`. */
    private function lenOf(string $s): int
    {
        return (int) (strlen(mb_convert_encoding($s, 'UTF-16LE', 'UTF-8')) / 2);
    }

    /** Mirrors the reference isEmoji(): Extended_Pictographic or Regional_Indicator. */
    private function isEmoji(string $s): bool
    {
        $pictographic = preg_match('/^(?:\p{Extended_Pictographic}|\p{Regional_Indicator})/u', $s);

        if ($pictographic !== false) {
            return $pictographic === 1;
        }

        // Fallback for PCRE builds without those Unicode properties.
        return (bool) preg_match(
            '/^(?:\x{1F1E6}-\x{1F1FF}|[\x{1F300}-\x{1FAFF}]|[\x{2600}-\x{27BF}]|[\x{2B00}-\x{2BFF}])/u',
            $s
        );
    }
}
