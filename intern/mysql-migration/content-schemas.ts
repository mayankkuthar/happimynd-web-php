/**
 * Admin-editable in-game text: which lists exist, the fields each item has,
 * and the rules that keep the games working. Shared by the dashboard (to show
 * issues while editing) and the server (to refuse anything that would break
 * a game). The coded defaults live with each game; see
 * src/games/content-defaults.ts.
 *
 * Items are stored in exactly the shape the game already uses, so a game just
 * swaps its coded list for the published one.
 */

import { AREAS } from "../games/control-cant-control/content";
import { DECKS } from "../games/guess-my-answer/content";

export type Severity = "error" | "warning";

export interface ContentIssue {
  severity: Severity;
  message: string;
  /** Item position in the list, when the issue is about one item. */
  index?: number;
  field?: string;
}

interface BaseField {
  key: string;
  label: string;
  hint?: string;
}

export type FieldDef =
  | (BaseField & {
      type: "text";
      max: number;
      /** Past this, text may wrap or get cut off in the game: warn only. */
      soft: number;
      optional?: boolean;
      multiline?: boolean;
      /** Placeholders that must stay in the text, e.g. "{name}". */
      mustInclude?: string[];
    })
  | (BaseField & { type: "emoji" })
  | (BaseField & { type: "select"; options: { value: string; label: string }[] })
  | (BaseField & { type: "textList"; count: number; max: number; soft: number });

export interface CollectionDef {
  /** Game slug, or "*" for collections every game has (end screens). */
  game: string;
  key: string;
  label: string;
  description: string;
  /** Items are plain strings (the single field's value) rather than objects. */
  scalar?: boolean;
  fields: FieldDef[];
  minItems: number;
  maxItems: number;
  /** Generate this field for new items (an id the game uses internally). */
  autoId?: string;
  /** Extra list-level checks. */
  rules?: (items: Record<string, unknown>[]) => ContentIssue[];
}

const text = (
  key: string,
  label: string,
  soft: number,
  max: number,
  extra: Partial<Extract<FieldDef, { type: "text" }>> = {},
): FieldDef => ({ key, label, type: "text", soft, max, ...extra });

const one = (label: string, soft: number, max: number, hint?: string): FieldDef[] => [
  text("value", label, soft, max, hint ? { hint } : {}),
];

/** Errors when a select value has fewer than `min` items, warnings below `want`. */
function countBy(
  items: Record<string, unknown>[],
  key: string,
  options: { value: string; label: string }[],
  min: number,
  want: number,
  what: (label: string, n: number) => { error: string; warning: string },
): ContentIssue[] {
  const out: ContentIssue[] = [];
  for (const o of options) {
    const n = items.filter((i) => i[key] === o.value).length;
    if (n < min) out.push({ severity: "error", message: what(o.label, n).error });
    else if (n < want) out.push({ severity: "warning", message: what(o.label, n).warning });
  }
  return out;
}

const THOUGHT_KINDS = [
  { value: "pass", label: "Let it pass" },
  { value: "park", label: "Park it" },
];
const SITUATION_KINDS = [
  { value: "cant", label: "Can't control" },
  { value: "can", label: "Can control" },
  { value: "both", label: "A bit of both" },
];
const AREA_OPTIONS = AREAS.filter((a) => a.id !== "all").map((a) => ({
  value: a.id,
  label: `${a.emoji} ${a.label}`,
}));
const GMA_DECKS = DECKS.filter((d) => d.id !== "mix").map((d) => ({
  value: d.id,
  label: `${d.emoji} ${d.label}`,
}));
const COMPLIMENT_MOODS = ["Sweet", "Grateful", "Deep", "Playful"].map((m) => ({
  value: m,
  label: m,
}));

export const COLLECTIONS: CollectionDef[] = [
  // ── Overthinking & Worry ──────────────────────────────────────────
  {
    game: "thought-traffic",
    key: "thoughts",
    label: "Thoughts on the cars",
    description:
      "Each round picks 8 thoughts to let pass and 4 to park. The note appears after the player chooses.",
    fields: [
      text("text", "Thought", 48, 70),
      { key: "kind", label: "Best choice", type: "select", options: THOUGHT_KINDS },
      text("tag", "Thinking pattern", 18, 24, { hint: 'e.g. "What-if", "Mind reading"' }),
      text("note", "Insight shown after choosing", 100, 160, { multiline: true }),
    ],
    minItems: 6,
    maxItems: 400,
    rules: (items) =>
      countBy(items, "kind", THOUGHT_KINDS, 1, 0, (label, n) => ({
        error: `Add at least one "${label}" thought. The game needs both kinds.`,
        warning: `Only ${n} "${label}" thoughts.`,
      })).concat(
        items.filter((i) => i["kind"] === "pass").length < 8 ||
          items.filter((i) => i["kind"] === "park").length < 4
          ? [
              {
                severity: "warning",
                message:
                  'A round uses 8 "Let it pass" and 4 "Park it" thoughts. With fewer, rounds get shorter.',
              },
            ]
          : [],
      ),
  },
  {
    game: "control-cant-control",
    key: "situations",
    label: "Situations to sort",
    description:
      "Each round deals 12 cards: about 5 can't control, 5 can control and 2 a bit of both. Players can also pick one area.",
    fields: [
      text("text", "Situation", 44, 70),
      { key: "emoji", label: "Emoji", type: "emoji" },
      { key: "kind", label: "Answer", type: "select", options: SITUATION_KINDS },
      { key: "area", label: "Area", type: "select", options: AREA_OPTIONS },
      text("note", "Explanation shown after sorting", 100, 160, { multiline: true }),
    ],
    minItems: 12,
    maxItems: 600,
    rules: (items) => [
      ...countBy(items, "area", AREA_OPTIONS, 3, 6, (label, n) => ({
        error: `Area "${label}" has only ${n} situations. Players who pick it would get an almost empty round. Add at least 3.`,
        warning: `Area "${label}" has only ${n} situations, so its rounds will be short.`,
      })),
      ...countBy(items, "kind", SITUATION_KINDS, 1, 5, (label, n) => ({
        error: `Add at least one "${label}" situation.`,
        warning: `Only ${n} "${label}" situations. Rounds aim for about 5 of each.`,
      })),
    ],
  },
  {
    game: "worry-clouds",
    key: "suggestions",
    label: "Suggested worries",
    description: "One-tap chips under the text box, for players who don't know what to write.",
    scalar: true,
    fields: one("Suggestion", 26, 40),
    minItems: 0,
    maxItems: 12,
  },

  // ── Low Mood ──────────────────────────────────────────────────────
  {
    game: "star-collector",
    key: "affirmations",
    label: "Affirmations",
    description: "A gentle line appears each time a star is collected.",
    scalar: true,
    fields: one("Affirmation", 44, 70),
    minItems: 1,
    maxItems: 200,
  },
  {
    game: "tiny-wins",
    key: "quick",
    label: "Quick wins",
    description: "One-tap buttons for common small wins.",
    fields: [text("text", "Win", 22, 32), { key: "emoji", label: "Emoji", type: "emoji" }],
    minItems: 0,
    maxItems: 30,
  },
  {
    game: "tiny-wins",
    key: "cheers",
    label: "Cheers",
    description: "A random cheer pops up after each win is added.",
    scalar: true,
    fields: one("Cheer", 44, 70),
    minItems: 1,
    maxItems: 100,
  },

  // ── Stress & Anger ────────────────────────────────────────────────
  {
    game: "stress-smash",
    key: "stressors",
    label: "Words on the objects",
    description: "Each floating object shows one of these. Keep them to a word or two.",
    scalar: true,
    fields: one("Word", 14, 18),
    minItems: 3,
    maxItems: 100,
  },

  // ── Connection ────────────────────────────────────────────────────
  {
    game: "secret-compliment",
    key: "prompts",
    label: "Compliment prompts",
    description: "Both players answer the same prompt about each other.",
    fields: [
      { key: "mood", label: "Mood", type: "select", options: COMPLIMENT_MOODS },
      { key: "emoji", label: "Emoji", type: "emoji" },
      text("text", "Prompt", 70, 100),
    ],
    minItems: 3,
    maxItems: 200,
  },
  {
    game: "secret-compliment",
    key: "starters",
    label: "Sentence starters",
    description: "Tap-to-insert openings under the writing box.",
    scalar: true,
    fields: one("Starter", 24, 40),
    minItems: 0,
    maxItems: 12,
  },
  {
    game: "guess-my-answer",
    key: "questions",
    label: "Questions",
    description:
      "Each question is written twice: for the person answering, and for the one guessing ({name} becomes the answerer's name). Each needs exactly 4 options.",
    fields: [
      { key: "deck", label: "Deck", type: "select", options: GMA_DECKS },
      { key: "emoji", label: "Emoji", type: "emoji" },
      text("you", "Question for the answerer", 40, 70),
      text("them", "Question for the guesser", 50, 80, { mustInclude: ["{name}"] }),
      {
        key: "options",
        label: "Options",
        type: "textList",
        count: 4,
        soft: 34,
        max: 44,
        hint: 'Start each with an emoji, e.g. "🍕 Pizza"',
      },
    ],
    minItems: 6,
    maxItems: 500,
    rules: (items) =>
      countBy(items, "deck", GMA_DECKS, 6, 10, (label, n) => ({
        error: `Deck "${label}" has only ${n} questions. It needs at least 6 (the shortest game).`,
        warning: `Deck "${label}" has ${n} questions, so a 10-question game will be shorter.`,
      })),
  },
  {
    game: "guess-my-answer",
    key: "talk-prompts",
    label: "Nudges after a miss",
    description: "Shown when the guess was wrong, to spark a conversation.",
    scalar: true,
    fields: one("Nudge", 44, 70),
    minItems: 1,
    maxItems: 50,
  },
  {
    game: "guess-my-answer",
    key: "match-lines",
    label: "Lines for a match",
    description: "Shown when the guess was right.",
    scalar: true,
    fields: one("Line", 28, 40),
    minItems: 1,
    maxItems: 50,
  },
  {
    game: "send-some-good",
    key: "nudges",
    label: "Who-to-think-of nudges",
    description: 'Shown after "Think of someone who…", so start in lowercase.',
    scalar: true,
    fields: one("Nudge", 56, 80, "Completes the sentence: Think of someone who…"),
    minItems: 1,
    maxItems: 50,
  },
  {
    game: "send-some-good",
    key: "prompts",
    label: "Message prompts",
    description: "Picking one fills in its opening line. {name} becomes the person's name.",
    autoId: "id",
    fields: [
      { key: "emoji", label: "Emoji", type: "emoji" },
      text("label", "Button label", 24, 32),
      text("starter", "Opening line", 72, 120, { mustInclude: ["{name}"] }),
    ],
    minItems: 1,
    maxItems: 20,
  },
  {
    game: "send-some-good",
    key: "endings",
    label: "Closing lines",
    description: "One-tap closing lines added to the end of the message.",
    scalar: true,
    fields: one("Closing line", 56, 80),
    minItems: 0,
    maxItems: 12,
  },
  {
    game: "send-some-good",
    key: "sent-lines",
    label: "After-sending headlines",
    description: "The big headline after a message is sent.",
    scalar: true,
    fields: one("Headline", 40, 60),
    minItems: 1,
    maxItems: 30,
  },

  // ── Every game ────────────────────────────────────────────────────
  {
    game: "*",
    key: "end-titles",
    label: "End screen titles",
    description: "One is picked at random each time the player finishes.",
    scalar: true,
    fields: one("Title", 28, 40),
    minItems: 1,
    maxItems: 20,
  },
  {
    game: "*",
    key: "end-messages",
    label: "End screen messages",
    description: "One is picked at random and shown under the title.",
    scalar: true,
    fields: one("Message", 90, 140),
    minItems: 1,
    maxItems: 20,
  },
];

export function collectionsFor(game: string): CollectionDef[] {
  return COLLECTIONS.filter((c) => c.game === game || c.game === "*");
}

export function findCollection(game: string, key: string): CollectionDef | undefined {
  return COLLECTIONS.find((c) => c.key === key && (c.game === game || c.game === "*"));
}

const asRecord = (item: unknown): Record<string, unknown> =>
  item && typeof item === "object" ? (item as Record<string, unknown>) : {};

/** Scalar lists store plain strings; treat them as { value } while checking. */
export function toRecords(def: CollectionDef, items: unknown[]): Record<string, unknown>[] {
  return def.scalar ? items.map((v) => ({ value: v })) : items.map(asRecord);
}

const isEmoji = (s: string) =>
  /^\p{Extended_Pictographic}/u.test(s) || /^\p{Regional_Indicator}/u.test(s);

/** Every problem with a list. Errors would break the game; warnings are cosmetic. */
export function validateCollection(def: CollectionDef, items: unknown[]): ContentIssue[] {
  const issues: ContentIssue[] = [];
  if (items.length < def.minItems) {
    issues.push({
      severity: "error",
      message: `This list needs at least ${def.minItems} ${def.minItems === 1 ? "item" : "items"} (it has ${items.length}).`,
    });
  }
  if (items.length > def.maxItems) {
    issues.push({
      severity: "error",
      message: `This list can have at most ${def.maxItems} items.`,
    });
  }

  const records = toRecords(def, items);
  const seen = new Map<string, number>();
  records.forEach((item, index) => {
    for (const f of def.fields) {
      const v = item[f.key];
      const at = { index, field: f.key };
      if (f.type === "text") {
        const s = typeof v === "string" ? v.trim() : "";
        if (!s) {
          if (!f.optional)
            issues.push({ ...at, severity: "error", message: `${f.label} can't be empty.` });
          continue;
        }
        if (s.length > f.max)
          issues.push({
            ...at,
            severity: "error",
            message: `${f.label} is over ${f.max} characters.`,
          });
        else if (s.length > f.soft)
          issues.push({
            ...at,
            severity: "warning",
            message: `${f.label} is long (${s.length} characters) and may wrap or be cut off in the game.`,
          });
        for (const p of f.mustInclude ?? []) {
          if (!s.includes(p))
            issues.push({
              ...at,
              severity: "error",
              message: `${f.label} must include ${p} (it's replaced with a name in the game).`,
            });
        }
      } else if (f.type === "emoji") {
        const s = typeof v === "string" ? v.trim() : "";
        if (!s) issues.push({ ...at, severity: "error", message: `${f.label} can't be empty.` });
        else if (s.length > 16 || !isEmoji(s))
          issues.push({
            ...at,
            severity: "error",
            message: `${f.label} should be a single emoji.`,
          });
      } else if (f.type === "select") {
        if (!f.options.some((o) => o.value === v))
          issues.push({ ...at, severity: "error", message: `Pick a ${f.label.toLowerCase()}.` });
      } else if (f.type === "textList") {
        const list = Array.isArray(v) ? v : [];
        if (list.length !== f.count) {
          issues.push({
            ...at,
            severity: "error",
            message: `Needs exactly ${f.count} ${f.label.toLowerCase()}.`,
          });
          continue;
        }
        list.forEach((o, i) => {
          const s = typeof o === "string" ? o.trim() : "";
          if (!s)
            issues.push({
              ...at,
              severity: "error",
              message: `${f.label} ${i + 1} can't be empty.`,
            });
          else if (s.length > f.max)
            issues.push({
              ...at,
              severity: "error",
              message: `${f.label} ${i + 1} is over ${f.max} characters.`,
            });
          else if (s.length > f.soft)
            issues.push({
              ...at,
              severity: "warning",
              message: `${f.label} ${i + 1} is long and may wrap.`,
            });
        });
        const distinct = new Set(list.map((o) => String(o).trim().toLowerCase()));
        if (distinct.size < list.length)
          issues.push({
            ...at,
            severity: "error",
            message: `Two ${f.label.toLowerCase()} are the same, so a match would be ambiguous.`,
          });
      }
    }
    // Duplicates (by the first text field) are confusing but harmless.
    const first = def.fields.find((f) => f.type === "text");
    const key = first
      ? String(item[first.key] ?? "")
          .trim()
          .toLowerCase()
      : "";
    if (key) {
      const prev = seen.get(key);
      if (prev !== undefined)
        issues.push({
          index,
          field: first!.key,
          severity: "warning",
          message: `Same as item ${prev + 1}.`,
        });
      else seen.set(key, index);
    }
  });

  if (def.rules) issues.push(...def.rules(records));
  return issues;
}
