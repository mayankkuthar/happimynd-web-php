<?php

namespace App\Services;

use App\Jobs\NotificationForSinglePsy;
use App\Jobs\NotificationForSingleUser;
use App\Mail\HappitalkSessionMail;
use App\Models\HappitalkSession;
use App\Models\HappitalkSessionNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * HappiTALK booking intimation and session reminders for the user and the psychologist.
 *
 * Automatic (notification:cron, every minute):
 *   booking  - once, for sessions booked in the last BOOKING_LOOKBACK_MINUTES
 *   24h, 1h, 5m - when the session is that far away
 *   start    - when the session starts
 * Manual: the admin "Notify" button on HappiTALK > Upcoming Sessions.
 *
 * Each automatic message is sent once per session start time (see the
 * happitalk_session_notifications log), so a rescheduled session is reminded again.
 */
class HappitalkSessionNotifier
{
    const BOOKING = 'booking';
    const REMINDER = 'reminder'; // manual "remind now"

    /**
     * Automatic reminders: kind => minutes before the start. Each is sent while
     * the session is between this and the next (smaller) offset away, so a
     * late booking gets only the reminders still ahead of it.
     */
    const SCHEDULE = [
        '24h'   => 1440,
        '1h'    => 60,
        '5m'    => 5,
        'start' => 0,
    ];

    /** The "start" message is still sent up to this many minutes after the start */
    const START_GRACE_MINUTES = 10;

    /** Bookings made within this many minutes get the automatic booking intimation */
    const BOOKING_LOOKBACK_MINUTES = 60;

    /** Kinds also sent by email (push goes for every kind) */
    const EMAIL_KINDS = [self::BOOKING, '24h', '1h', self::REMINDER];

    const LABELS = [
        self::BOOKING  => 'Booking intimation',
        '24h'          => '24 hours before',
        '1h'           => '1 hour before',
        '5m'           => '5 minutes before',
        'start'        => 'At start',
        self::REMINDER => 'Manual reminder',
    ];

    const RECIPIENTS = ['user', 'psychologist'];

    /** What the last send() could not deliver, e.g. "Psychologist push (no app device token)" */
    public $lastSkipped = [];

    /**
     * Sessions that can still be notified: not cancelled / rejected / ended.
     */
    public function activeSessions()
    {
        return HappitalkSession::query()
            ->where('is_cancel', '!=', '1')
            ->where('is_req_accepted', '!=', '2')
            ->where('is_end', '!=', '1');
    }

    /**
     * Upcoming (or just started) sessions, soonest first, with their start time
     * as ->starts_at_carbon.
     */
    public function upcomingSessions()
    {
        $now = Carbon::now();
        return $this->activeSessions()
            ->where('date', '>=', $now->copy()->subDay()->toDateString())
            ->with(['userDetail', 'psychologistDetail', 'sentNotifications'])
            ->get()
            ->map(function ($session) {
                $session->starts_at_carbon = $session->startsAt();
                return $session;
            })
            ->filter(function ($session) use ($now) {
                // keep sessions that started up to an hour ago (likely still running)
                return $session->starts_at_carbon && $session->starts_at_carbon->gte($now->copy()->subHour());
            })
            ->sortBy(function ($session) {
                return $session->starts_at_carbon->timestamp;
            })
            ->values();
    }

    /**
     * Run by the every-minute cron. Returns how many messages were queued.
     */
    public function sendDueReminders(): int
    {
        $now = Carbon::now();
        $sent = 0;

        $sessions = $this->activeSessions()
            ->whereBetween('date', [$now->copy()->subDay()->toDateString(), $now->copy()->addDays(2)->toDateString()])
            ->with(['userDetail', 'psychologistDetail', 'sentNotifications'])
            ->get();

        foreach ($sessions as $session) {
            $startsAt = $session->startsAt();
            if (!$startsAt) {
                continue;
            }

            try {
                $kinds = $this->dueKinds($session, $startsAt, $now);
                foreach ($kinds as $kind) {
                    $sent += $this->send($session, $kind, self::RECIPIENTS);
                }
            } catch (\Throwable $e) {
                Log::error('HappiTALK reminder failed for session ' . $session->id . ': ' . $e->getMessage());
            }
        }

        return $sent;
    }

    /**
     * Automatic kinds due for $session right now and not sent yet for this start time.
     */
    public function dueKinds(HappitalkSession $session, Carbon $startsAt, Carbon $now): array
    {
        $alreadySent = $session->sentNotifications
            ->filter(function ($n) use ($startsAt) {
                return $n->session_starts_at && $n->session_starts_at->equalTo($startsAt);
            })
            ->pluck('kind')
            ->all();

        $due = [];

        // booking intimation: newly booked sessions that haven't started yet
        if (!in_array(self::BOOKING, $alreadySent, true)
            && $session->created_at
            && $session->created_at->gte($now->copy()->subMinutes(self::BOOKING_LOOKBACK_MINUTES))
            && $startsAt->gt($now)) {
            $due[] = self::BOOKING;
        }

        $minutesToStart = ($startsAt->timestamp - $now->timestamp) / 60;
        $offsets = array_values(self::SCHEDULE);
        $kinds = array_keys(self::SCHEDULE);
        foreach ($kinds as $i => $kind) {
            $from = $offsets[$i];
            $until = $offsets[$i + 1] ?? -self::START_GRACE_MINUTES;
            if ($minutesToStart <= $from && $minutesToStart > $until && !in_array($kind, $alreadySent, true)) {
                // the booking intimation already says when it is; skip the far-off reminder sent with it
                if (in_array(self::BOOKING, $due, true) && in_array($kind, ['24h', '1h'], true)) {
                    continue;
                }
                $due[] = $kind;
            }
        }

        return $due;
    }

    /**
     * Send one kind of message for a session to the given recipients (push and,
     * for EMAIL_KINDS, email). Every attempt is logged. Returns messages queued.
     */
    public function send(HappitalkSession $session, string $kind, array $recipients, $adminId = null): int
    {
        $startsAt = $session->startsAt();
        $queued = 0;
        $this->lastSkipped = [];

        foreach (array_intersect(self::RECIPIENTS, $recipients) as $recipient) {
            $message = $this->message($session, $kind, $recipient, $startsAt);
            $person = $recipient === 'user' ? $session->userDetail : $session->psychologistDetail;

            // push
            $token = $person->device_token ?? null;
            if ($token) {
                if ($recipient === 'user') {
                    dispatch(new NotificationForSingleUser($token, $message, $session->user_id));
                } else {
                    dispatch(new NotificationForSinglePsy($token, $message));
                }
                $this->log($session, $kind, $recipient, 'push', 'queued', null, $startsAt, $adminId);
                $queued++;
            } else {
                $this->log($session, $kind, $recipient, 'push', 'skipped', 'No app device token', $startsAt, $adminId);
            }

            // email
            if (in_array($kind, self::EMAIL_KINDS, true)) {
                $email = $person->email ?? null;
                if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($email)->queue(new HappitalkSessionMail(
                        $this->subject($kind, $startsAt),
                        $this->personName($session, $recipient),
                        $message,
                        $this->details($session, $recipient, $startsAt)
                    ));
                    $this->log($session, $kind, $recipient, 'email', 'queued', $email, $startsAt, $adminId);
                    $queued++;
                } else {
                    $this->log($session, $kind, $recipient, 'email', 'skipped', 'No valid email', $startsAt, $adminId);
                }
            }
        }

        return $queued;
    }

    public function message(HappitalkSession $session, string $kind, string $recipient, $startsAt): string
    {
        $with = $recipient === 'user'
            ? $this->personName($session, 'psychologist')
            : $this->personName($session, 'user');
        $when = $startsAt ? $startsAt->format('D, d M') . ' at ' . $startsAt->format('g:i A') : trim($session->date . ' ' . $session->time);
        $time = $startsAt ? $startsAt->format('g:i A') : $session->start_time;

        switch ($kind) {
            case self::BOOKING:
                return $recipient === 'user'
                    ? "Your HappiTALK session with {$with} is booked for {$when}."
                    : "New HappiTALK session booked with {$with} for {$when}.";
            case '24h':
                return "Reminder: your HappiTALK session with {$with} is in 24 hours ({$when}).";
            case '1h':
                return "Reminder: your HappiTALK session with {$with} starts in 1 hour, at {$time}.";
            case '5m':
                return "Your HappiTALK session with {$with} starts in 5 minutes. Get ready to join!";
            case 'start':
                return "Your HappiTALK session with {$with} is starting now. Please join!";
            default:
                $in = $startsAt && $startsAt->isFuture() ? ' (' . $startsAt->diffForHumans(null, false, false, 2) . ')' : '';
                return "Reminder: your HappiTALK session with {$with} is on {$when}{$in}.";
        }
    }

    private function subject(string $kind, $startsAt): string
    {
        $when = $startsAt ? ' - ' . $startsAt->format('d M, g:i A') : '';
        return ($kind === self::BOOKING ? 'HappiTALK session booked' : 'HappiTALK session reminder') . $when;
    }

    public function personName(HappitalkSession $session, string $recipient): string
    {
        if ($recipient === 'user') {
            $u = $session->userDetail;
            return trim((string) ($u->nickname ?? '')) ?: ((string) ($u->username ?? '') ?: 'your HappiUser');
        }
        $p = $session->psychologistDetail;
        $name = trim(($p->first_name ?? '') . ' ' . ($p->last_name ?? ''));
        return $name !== '' ? $name : 'your psychologist';
    }

    private function details(HappitalkSession $session, string $recipient, $startsAt): array
    {
        return [
            'Session' => 'HappiTALK',
            'Date' => $startsAt ? $startsAt->format('l, d M Y') : $session->date,
            'Time' => $session->time,
            ($recipient === 'user' ? 'Psychologist' : 'User') => $this->personName($session, $recipient === 'user' ? 'psychologist' : 'user'),
        ];
    }

    private function log(HappitalkSession $session, $kind, $recipient, $channel, $status, $detail, $startsAt, $adminId)
    {
        if ($status === 'skipped') {
            $this->lastSkipped[] = ucfirst($recipient) . ' ' . $channel . ' (' . strtolower((string) $detail) . ')';
        }
        HappitalkSessionNotification::create([
            'happitalk_session_id' => $session->id,
            'kind' => $kind,
            'session_starts_at' => $startsAt,
            'recipient' => $recipient,
            'channel' => $channel,
            'status' => $status,
            'detail' => $detail ? mb_substr($detail, 0, 255) : null,
            'admin_id' => $adminId,
        ]);
    }
}
