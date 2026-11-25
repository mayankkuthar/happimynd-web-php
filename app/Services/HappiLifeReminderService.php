<?php

namespace App\Services;

use App\Jobs\NotificationForSingleUser;
use App\Mail\HappiLifeReminderMail;
use App\Models\Assessment;
use App\Models\HappilifeReminder;
use App\Models\NotificationList;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Finds unfinished HappiLIFE attempts and nudges their owners to finish them,
 * either on a schedule (happilife:send-reminders) or when an admin asks.
 *
 * Progress lives on the server, so the reminder can say exactly how far the
 * user got ("42 of 115 answered, 36%") and they resume from the same point on
 * the website or in the app.
 */
class HappiLifeReminderService
{
    /**
     * Latest unfinished attempt per user (the one start-assessment resumes).
     */
    public function latestIncompleteAssessmentIds()
    {
        return Assessment::whereNull('ended_at')
            ->whereHas('user')
            ->selectRaw('MAX(id) as id')
            ->groupBy('user_id')
            ->pluck('id');
    }

    /**
     * Progress for one attempt, or null when it can't be worked out (e.g. the
     * user's profile/batch was deleted).
     */
    public function progress(Assessment $assessment): ?array
    {
        try {
            return (new AssessmentService)->progressSnapshot($assessment->id);
        } catch (\Throwable $e) {
            Log::warning('HappiLIFE progress unavailable for assessment '.$assessment->id.': '.$e->getMessage());
            return null;
        }
    }

    /**
     * The automatic reminder stage due now for this attempt, or null if none.
     * The sequence restarts whenever the user answers again.
     */
    public function dueAutoStage(Assessment $assessment, array $progress, ?Carbon $now = null): ?int
    {
        $now = $now ?: Carbon::now();
        $lastActivity = Carbon::parse($progress['last_activity_at']);
        $inactiveDays = $lastActivity->diffInDays($now);

        if ($inactiveDays > config('happilife.max_inactive_days', 30)) return null;

        $sinceActivity = HappilifeReminder::where('assessment_id', $assessment->id)
            ->where('created_at', '>', $lastActivity)
            ->orderBy('created_at', 'desc');

        $autoSent = (clone $sinceActivity)->where('source', HappilifeReminder::SOURCE_AUTO)->count();
        $thresholds = array_values(array_filter(config('happilife.reminder_days', [1, 3, 7, 14])));
        if ($autoSent >= count($thresholds)) return null;
        if ($inactiveDays < $thresholds[$autoSent]) return null;

        // Any reminder (auto or manual) counts towards the minimum gap
        $lastReminder = (clone $sinceActivity)->first();
        if ($lastReminder && $lastReminder->created_at->diffInDays($now) < config('happilife.min_gap_days', 2)) {
            return null;
        }

        return $autoSent + 1;
    }

    /**
     * Default copy, varied by stage so repeat reminders don't read the same.
     */
    public function defaultMessage(array $progress, ?int $stage = null): string
    {
        $answered = $progress['answered'];
        $total = $progress['total'];
        $percent = $progress['percent'];
        $left = $progress['remaining'];
        $minutes = max(1, (int) ceil($left * config('happilife.seconds_per_question', 10) / 60));

        if ($answered === 0) {
            return "You've started your HappiLIFE Self Check In. It takes about {$minutes} minutes — begin whenever you're ready.";
        }

        switch ($stage) {
            case 2:
                return "Only {$left} questions left in your HappiLIFE Self Check In (about {$minutes} min). You're {$percent}% done — your answers are saved.";
            case 3:
                return "Your HappiLIFE insights are waiting. Answer the last {$left} questions to unlock your personalised report.";
            case 4:
                return "A gentle reminder: your HappiLIFE Self Check In is {$percent}% complete. Pick up from question ".($answered + 1)." whenever you're ready.";
            default:
                return "You're {$percent}% through your HappiLIFE Self Check In ({$answered} of {$total} answered). Continue right where you left off.";
        }
    }

    /**
     * Send one reminder through every available channel and log it.
     */
    public function send(Assessment $assessment, array $progress, string $source, ?int $stage = null, ?string $message = null, ?int $adminId = null): HappilifeReminder
    {
        $user = $assessment->user;
        $message = trim((string) $message) !== '' ? trim($message) : $this->defaultMessage($progress, $stage);
        $channels = ['inapp'];

        $deviceToken = $user->device_token;
        if (config('happilife.channels.push', true) && $deviceToken && strlen($deviceToken) > 20) {
            // Sends the push and records the in-app notification
            dispatch(new NotificationForSingleUser($deviceToken, $message, $user->id));
            $channels[] = 'push';
        } else {
            NotificationList::create(['user_id' => $user->id, 'message' => $message]);
        }

        if (config('happilife.channels.email', true) && filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($user->email)->send(new HappiLifeReminderMail($user, $progress, $message));
                $channels[] = 'email';
            } catch (\Throwable $e) {
                Log::warning('HappiLIFE reminder email failed for user '.$user->id.': '.$e->getMessage());
            }
        }

        return HappilifeReminder::create([
            'user_id' => $user->id,
            'assessment_id' => $assessment->id,
            'source' => $source,
            'stage' => $stage,
            'channels' => implode(',', $channels),
            'answered' => $progress['answered'],
            'total' => $progress['total'],
            'message' => $message,
            'sent_by_admin_id' => $adminId,
        ]);
    }

    /**
     * Run the automatic reminders. Returns [checked, sent, skipped].
     */
    public function runAutomatic(bool $dryRun = false, ?callable $report = null): array
    {
        $checked = $sent = $skipped = 0;

        Assessment::whereIn('id', $this->latestIncompleteAssessmentIds())
            ->with('user')
            ->chunkById(100, function ($assessments) use ($dryRun, $report, &$checked, &$sent, &$skipped) {
                foreach ($assessments as $assessment) {
                    $checked++;
                    $progress = $this->progress($assessment);
                    if (!$progress || $progress['total'] === 0 || $progress['completed']) { $skipped++; continue; }

                    $stage = $this->dueAutoStage($assessment, $progress);
                    if ($stage === null) { $skipped++; continue; }

                    if ($report) {
                        $report("user {$assessment->user_id} · attempt {$assessment->id} · {$progress['answered']}/{$progress['total']} ({$progress['percent']}%) · reminder #{$stage}");
                    }
                    if (!$dryRun) {
                        try {
                            $this->send($assessment, $progress, HappilifeReminder::SOURCE_AUTO, $stage);
                        } catch (\Throwable $e) {
                            Log::error('HappiLIFE reminder failed for assessment '.$assessment->id.': '.$e->getMessage());
                            $skipped++;
                            continue;
                        }
                    }
                    $sent++;
                }
            });

        return [$checked, $sent, $skipped];
    }
}
