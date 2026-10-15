<?php

namespace App\Services\Hub;

/**
 * Admin-editable in-game text: which lists exist, the fields each item has,
 * and the rules that keep the games working.
 *
 * This is a faithful PHP port of the reference `src/config/content-schemas.ts`
 * (shipped with the migration package as `content-schemas.ts`). The dashboard
 * and the server must agree on what is valid, so message text and rules are
 * kept identical to that file.
 *
 * `content-schemas.ts` derives two option lists from game content files:
 *   - `AREA_OPTIONS` from  games/control-cant-control/content.ts  (AREAS)
 *   - `GMA_DECKS`    from  games/guess-my-answer/content.ts        (DECKS)
 * Both are filled in below (values supplied by the front-end author), so the
 * select fields and their list-level count rules validate exactly like the
 * dashboard.
 */
class HubContentSchema
{
    // ── Game-dependent option lists ───────────────────────────────────────
    /** @var array<int, array{value: string, label: string}> */
    public static $areaOptions = [ // AREAS.filter(a => a.id !== "all")
        ['value' => 'work', 'label' => '💼 Work & study'],
        ['value' => 'relationships', 'label' => '💞 Relationships & family'],
        ['value' => 'health', 'label' => '🩺 Health & body'],
        ['value' => 'money', 'label' => '🏠 Money & home'],
        ['value' => 'online', 'label' => '📱 Online life'],
        ['value' => 'inner', 'label' => '🧠 Inner world'],
        ['value' => 'everyday', 'label' => '🌍 Everyday & world'],
    ];

    /** @var array<int, array{value: string, label: string}> */
    public static $deckOptions = [ // DECKS.filter(d => d.id !== "mix")
        ['value' => 'fun', 'label' => '🍿 Light & fun'],
        ['value' => 'favourites', 'label' => '⭐ Favourites'],
        ['value' => 'deep', 'label' => '💭 Deeper'],
    ];

    /** @var array<int, array{value: string, label: string}> */
    private static $thoughtKinds = [
        ['value' => 'pass', 'label' => 'Let it pass'],
        ['value' => 'park', 'label' => 'Park it'],
    ];

    /** @var array<int, array{value: string, label: string}> */
    private static $situationKinds = [
        ['value' => 'cant', 'label' => "Can't control"],
        ['value' => 'can', 'label' => 'Can control'],
        ['value' => 'both', 'label' => 'A bit of both'],
    ];

    /** @var array<int, array{value: string, label: string}> */
    private static $complimentMoods = [
        ['value' => 'Sweet', 'label' => 'Sweet'],
        ['value' => 'Grateful', 'label' => 'Grateful'],
        ['value' => 'Deep', 'label' => 'Deep'],
        ['value' => 'Playful', 'label' => 'Playful'],
    ];

    /**
     * Every collection definition, in the same order as content-schemas.ts.
     *
     * @return array<int, array>
     */
    public static function collections(): array
    {
        return [
            // ── Overthinking & Worry ─────────────────────────────────────
            [
                'game' => 'thought-traffic',
                'key' => 'thoughts',
                'label' => 'Thoughts on the cars',
                'fields' => [
                    self::text('text', 'Thought', 48, 70),
                    self::select('kind', 'Best choice', self::$thoughtKinds),
                    self::text('tag', 'Thinking pattern', 18, 24, ['hint' => 'e.g. "What-if", "Mind reading"']),
                    self::text('note', 'Insight shown after choosing', 100, 160, ['multiline' => true]),
                ],
                'minItems' => 6,
                'maxItems' => 400,
                'rules' => function (array $items) {
                    $issues = self::countBy($items, 'kind', self::$thoughtKinds, 1, 0, function ($label, $n) {
                        return [
                            'error' => "Add at least one \"{$label}\" thought. The game needs both kinds.",
                            'warning' => "Only {$n} \"{$label}\" thoughts.",
                        ];
                    });

                    $pass = count(array_filter($items, function ($i) {
                        return ($i['kind'] ?? null) === 'pass';
                    }));
                    $park = count(array_filter($items, function ($i) {
                        return ($i['kind'] ?? null) === 'park';
                    }));

                    if ($pass < 8 || $park < 4) {
                        $issues[] = self::issue('warning', 'A round uses 8 "Let it pass" and 4 "Park it" thoughts. With fewer, rounds get shorter.');
                    }

                    return $issues;
                },
            ],
            [
                'game' => 'control-cant-control',
                'key' => 'situations',
                'label' => 'Situations to sort',
                'fields' => [
                    self::text('text', 'Situation', 44, 70),
                    self::emoji('emoji', 'Emoji'),
                    self::select('kind', 'Answer', self::$situationKinds),
                    self::select('area', 'Area', self::$areaOptions),
                    self::text('note', 'Explanation shown after sorting', 100, 160, ['multiline' => true]),
                ],
                'minItems' => 12,
                'maxItems' => 600,
                'rules' => function (array $items) {
                    return array_merge(
                        self::countBy($items, 'area', self::$areaOptions, 3, 6, function ($label, $n) {
                            return [
                                'error' => "Area \"{$label}\" has only {$n} situations. Players who pick it would get an almost empty round. Add at least 3.",
                                'warning' => "Area \"{$label}\" has only {$n} situations, so its rounds will be short.",
                            ];
                        }),
                        self::countBy($items, 'kind', self::$situationKinds, 1, 5, function ($label, $n) {
                            return [
                                'error' => "Add at least one \"{$label}\" situation.",
                                'warning' => "Only {$n} \"{$label}\" situations. Rounds aim for about 5 of each.",
                            ];
                        })
                    );
                },
            ],
            [
                'game' => 'worry-clouds',
                'key' => 'suggestions',
                'label' => 'Suggested worries',
                'scalar' => true,
                'fields' => self::one('Suggestion', 26, 40),
                'minItems' => 0,
                'maxItems' => 12,
            ],

            // ── Low Mood ─────────────────────────────────────────────────
            [
                'game' => 'star-collector',
                'key' => 'affirmations',
                'label' => 'Affirmations',
                'scalar' => true,
                'fields' => self::one('Affirmation', 44, 70),
                'minItems' => 1,
                'maxItems' => 200,
            ],
            [
                'game' => 'tiny-wins',
                'key' => 'quick',
                'label' => 'Quick wins',
                'fields' => [
                    self::text('text', 'Win', 22, 32),
                    self::emoji('emoji', 'Emoji'),
                ],
                'minItems' => 0,
                'maxItems' => 30,
            ],
            [
                'game' => 'tiny-wins',
                'key' => 'cheers',
                'label' => 'Cheers',
                'scalar' => true,
                'fields' => self::one('Cheer', 44, 70),
                'minItems' => 1,
                'maxItems' => 100,
            ],

            // ── Stress & Anger ───────────────────────────────────────────
            [
                'game' => 'stress-smash',
                'key' => 'stressors',
                'label' => 'Words on the objects',
                'scalar' => true,
                'fields' => self::one('Word', 14, 18),
                'minItems' => 3,
                'maxItems' => 100,
            ],

            // ── Connection ───────────────────────────────────────────────
            [
                'game' => 'secret-compliment',
                'key' => 'prompts',
                'label' => 'Compliment prompts',
                'fields' => [
                    self::select('mood', 'Mood', self::$complimentMoods),
                    self::emoji('emoji', 'Emoji'),
                    self::text('text', 'Prompt', 70, 100),
                ],
                'minItems' => 3,
                'maxItems' => 200,
            ],
            [
                'game' => 'secret-compliment',
                'key' => 'starters',
                'label' => 'Sentence starters',
                'scalar' => true,
                'fields' => self::one('Starter', 24, 40),
                'minItems' => 0,
                'maxItems' => 12,
            ],
            [
                'game' => 'guess-my-answer',
                'key' => 'questions',
                'label' => 'Questions',
                'fields' => [
                    self::select('deck', 'Deck', self::$deckOptions),
                    self::emoji('emoji', 'Emoji'),
                    self::text('you', 'Question for the answerer', 40, 70),
                    self::text('them', 'Question for the guesser', 50, 80, ['mustInclude' => ['{name}']]),
                    self::textList('options', 'Options', 4, 34, 44, ['hint' => 'Start each with an emoji, e.g. "🍕 Pizza"']),
                ],
                'minItems' => 6,
                'maxItems' => 500,
                'rules' => function (array $items) {
                    return self::countBy($items, 'deck', self::$deckOptions, 6, 10, function ($label, $n) {
                        return [
                            'error' => "Deck \"{$label}\" has only {$n} questions. It needs at least 6 (the shortest game).",
                            'warning' => "Deck \"{$label}\" has {$n} questions, so a 10-question game will be shorter.",
                        ];
                    });
                },
            ],
            [
                'game' => 'guess-my-answer',
                'key' => 'talk-prompts',
                'label' => 'Nudges after a miss',
                'scalar' => true,
                'fields' => self::one('Nudge', 44, 70),
                'minItems' => 1,
                'maxItems' => 50,
            ],
            [
                'game' => 'guess-my-answer',
                'key' => 'match-lines',
                'label' => 'Lines for a match',
                'scalar' => true,
                'fields' => self::one('Line', 28, 40),
                'minItems' => 1,
                'maxItems' => 50,
            ],
            [
                'game' => 'send-some-good',
                'key' => 'nudges',
                'label' => 'Who-to-think-of nudges',
                'scalar' => true,
                'fields' => self::one('Nudge', 56, 80, ['hint' => 'Completes the sentence: Think of someone who…']),
                'minItems' => 1,
                'maxItems' => 50,
            ],
            [
                'game' => 'send-some-good',
                'key' => 'prompts',
                'label' => 'Message prompts',
                'autoId' => 'id',
                'fields' => [
                    self::emoji('emoji', 'Emoji'),
                    self::text('label', 'Button label', 24, 32),
                    self::text('starter', 'Opening line', 72, 120, ['mustInclude' => ['{name}']]),
                ],
                'minItems' => 1,
                'maxItems' => 20,
            ],
            [
                'game' => 'send-some-good',
                'key' => 'endings',
                'label' => 'Closing lines',
                'scalar' => true,
                'fields' => self::one('Closing line', 56, 80),
                'minItems' => 0,
                'maxItems' => 12,
            ],
            [
                'game' => 'send-some-good',
                'key' => 'sent-lines',
                'label' => 'After-sending headlines',
                'scalar' => true,
                'fields' => self::one('Headline', 40, 60),
                'minItems' => 1,
                'maxItems' => 30,
            ],

            // ── Every game ───────────────────────────────────────────────
            [
                'game' => '*',
                'key' => 'end-titles',
                'label' => 'End screen titles',
                'scalar' => true,
                'fields' => self::one('Title', 28, 40),
                'minItems' => 1,
                'maxItems' => 20,
            ],
            [
                'game' => '*',
                'key' => 'end-messages',
                'label' => 'End screen messages',
                'scalar' => true,
                'fields' => self::one('Message', 90, 140),
                'minItems' => 1,
                'maxItems' => 20,
            ],
        ];
    }

    /**
     * Collections that apply to a game: its own plus the "*" (every game) ones.
     *
     * @return array<int, array>
     */
    public static function forGame(string $game): array
    {
        return array_values(array_filter(self::collections(), function ($c) use ($game) {
            return $c['game'] === $game || $c['game'] === '*';
        }));
    }

    /**
     * @return array|null the collection definition, or null when unknown
     */
    public static function find(string $game, string $key): ?array
    {
        foreach (self::collections() as $c) {
            if ($c['key'] === $key && ($c['game'] === $game || $c['game'] === '*')) {
                return $c;
            }
        }

        return null;
    }

    // ── Field builders ───────────────────────────────────────────────────

    public static function text(string $key, string $label, int $soft, int $max, array $extra = []): array
    {
        return array_merge(['key' => $key, 'label' => $label, 'type' => 'text', 'soft' => $soft, 'max' => $max], $extra);
    }

    public static function emoji(string $key, string $label): array
    {
        return ['key' => $key, 'label' => $label, 'type' => 'emoji'];
    }

    public static function select(string $key, string $label, array $options): array
    {
        return ['key' => $key, 'label' => $label, 'type' => 'select', 'options' => $options];
    }

    public static function textList(string $key, string $label, int $count, int $soft, int $max, array $extra = []): array
    {
        return array_merge(['key' => $key, 'label' => $label, 'type' => 'textList', 'count' => $count, 'soft' => $soft, 'max' => $max], $extra);
    }

    /** A single text field keyed "value" (for scalar lists). */
    public static function one(string $label, int $soft, int $max, array $extra = []): array
    {
        return [self::text('value', $label, $soft, $max, $extra)];
    }

    // ── Rule helper (mirrors countBy in content-schemas.ts) ──────────────

    /**
     * Errors when a select value has fewer than `min` items, warnings below `want`.
     *
     * @param array<int, array> $items
     * @param array<int, array{value:string,label:string}> $options
     * @return array<int, array>
     */
    public static function countBy(array $items, string $key, array $options, int $min, int $want, callable $what): array
    {
        $out = [];

        foreach ($options as $o) {
            $n = count(array_filter($items, function ($i) use ($key, $o) {
                return ($i[$key] ?? null) === $o['value'];
            }));

            $text = $what($o['label'], $n);

            if ($n < $min) {
                $out[] = self::issue('error', $text['error']);
            } elseif ($n < $want) {
                $out[] = self::issue('warning', $text['warning']);
            }
        }

        return $out;
    }

    /**
     * @return array{severity:string,message:string,index?:int,field?:string}
     */
    public static function issue(string $severity, string $message, ?int $index = null, ?string $field = null): array
    {
        $issue = ['severity' => $severity, 'message' => $message];

        if ($index !== null) {
            $issue['index'] = $index;
        }
        if ($field !== null) {
            $issue['field'] = $field;
        }

        return $issue;
    }
}
