<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\HappilifeReminder;
use App\Services\HappiLifeReminderService;
use Illuminate\Http\Request;

/**
 * Admin: see who has an unfinished HappiLIFE attempt and remind them manually.
 * Automatic reminders run daily via `php artisan happilife:send-reminders`.
 */
class HappiLifeReminderController extends Controller
{
    protected $reminders;

    public function __construct(HappiLifeReminderService $reminders)
    {
        $this->reminders = $reminders;
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 50);
        $search = trim((string) $request->get('search', ''));

        $assessments = Assessment::whereIn('id', $this->reminders->latestIncompleteAssessmentIds())
            ->with('user')
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('username', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->except('page'));

        $ids = $assessments->getCollection()->pluck('id');
        $reminderStats = HappilifeReminder::whereIn('assessment_id', $ids)
            ->selectRaw('assessment_id, COUNT(*) as sent_count, MAX(created_at) as last_sent_at')
            ->groupBy('assessment_id')
            ->get()
            ->keyBy('assessment_id');

        $rows = $assessments->getCollection()->map(function ($assessment) use ($reminderStats) {
            $progress = $this->reminders->progress($assessment);
            $stats = $reminderStats->get($assessment->id);
            return (object) [
                'assessment' => $assessment,
                'user' => $assessment->user,
                'progress' => $progress,
                'next_auto_stage' => $progress ? $this->reminders->dueAutoStage($assessment, $progress) : null,
                'sent_count' => $stats->sent_count ?? 0,
                'last_sent_at' => $stats->last_sent_at ?? null,
            ];
        });

        $recent = HappilifeReminder::with('user')->latest()->limit(20)->get();

        return view('Backend.happilifeReminders', [
            'assessments' => $assessments,
            'rows' => $rows,
            'recent' => $recent,
            'search' => $search,
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'assessment_ids' => 'required|array|min:1|max:500',
            'assessment_ids.*' => 'integer',
            'message' => 'nullable|string|max:500',
        ]);

        $adminId = optional(auth('admin')->user())->id;
        $sent = 0;
        $skipped = 0;

        $assessments = Assessment::whereIn('id', $request->input('assessment_ids'))
            ->whereNull('ended_at')
            ->with('user')
            ->get();

        foreach ($assessments as $assessment) {
            $progress = $assessment->user ? $this->reminders->progress($assessment) : null;
            if (!$progress) { $skipped++; continue; }
            try {
                $this->reminders->send($assessment, $progress, HappilifeReminder::SOURCE_MANUAL, null, $request->input('message'), $adminId);
                $sent++;
            } catch (\Throwable $e) {
                \Log::error('Manual HappiLIFE reminder failed for assessment '.$assessment->id.': '.$e->getMessage());
                $skipped++;
            }
        }

        $skipped += count($request->input('assessment_ids')) - $assessments->count();

        return redirect()->back()->with('happilife_reminder_status',
            "Reminder sent to {$sent} user(s)".($skipped ? ", {$skipped} skipped (already finished or unavailable)." : '.'));
    }
}
