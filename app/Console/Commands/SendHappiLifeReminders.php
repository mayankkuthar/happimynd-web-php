<?php

namespace App\Console\Commands;

use App\Services\HappiLifeReminderService;
use Illuminate\Console\Command;

class SendHappiLifeReminders extends Command
{
    protected $signature = 'happilife:send-reminders
                            {--dry-run : List who would be reminded without sending anything}';

    protected $description = 'Remind users with an unfinished HappiLIFE Self Check In to complete it';

    public function handle(HappiLifeReminderService $reminders)
    {
        $dryRun = (bool) $this->option('dry-run');

        if (!$dryRun && !config('happilife.reminders_enabled', true)) {
            $this->info('HappiLIFE reminders are disabled (HAPPILIFE_REMINDERS_ENABLED=false).');
            return 0;
        }

        [$checked, $sent, $skipped] = $reminders->runAutomatic($dryRun, function ($line) {
            $this->line($line);
        });

        $summary = sprintf('HappiLIFE reminders%s: %d unfinished attempts checked, %d %s, %d not due.',
            $dryRun ? ' (dry run)' : '', $checked, $sent, $dryRun ? 'would be sent' : 'sent', $skipped);
        $this->info($summary);
        \Log::info($summary);

        return 0;
    }
}
