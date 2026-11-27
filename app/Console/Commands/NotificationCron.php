<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\ScheduleNotification;
use App\Jobs\SendNotificationJob;

use App\Models\HappitalkSession;
use App\Models\Psychologist;
use App\Models\User;

use App\Models\HappiguideSession;

use App\Jobs\NotificationForSingleUser;
use App\Jobs\NotificationForSinglePsy;
use DB;

class NotificationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification command according to date time';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Cron is working fine!");

        
        $today_date = Date('Y-m-d');
        $current_time = date('h:i A');


        // HappiTALK: booking intimation + reminders 24h / 1h / 5 min before and at the
        // start, to the user and the psychologist (see HappitalkSessionNotifier).
        // Replaces the old blocks here, which only matched is_req_accepted = 1
        // (never set on bookings) and compared times as "h:i A" strings.
        try {
            $talkSent = app(\App\Services\HappitalkSessionNotifier::class)->sendDueReminders();
            if ($talkSent > 0) {
                \Log::info("HappiTALK session notifications queued: " . $talkSent);
            }
        } catch (\Throwable $e) {
            \Log::error('HappiTALK session notifications failed: ' . $e->getMessage());
        }

        $current_time_plus_5_min = date('h:i A', strtotime($current_time) + (5*60));


        //Booked Guide Sessions
        $bookingList = HappiguideSession::where('date' , $today_date)
                                                ->where('is_notification_emit' , '0')
                                                ->whereBetween(DB::raw('TIME(start_time)') , [$current_time, $current_time_plus_5_min])
                                                ->get();
        foreach($bookingList as $row){
            $userDetail = User::where('id' , $row->user_id)->first();
            $user_device_token = $userDetail->device_token;
            if($user_device_token != null){
                $user_message = 'Your HappiGUIDE session will start at '.$row->start_time.'. Don'."'".'t forgot to join!';
                $user_id = $row->user_id;
                dispatch(new NotificationForSingleUser($user_device_token, $user_message, $user_id));
            }
            $psyDetail = Psychologist::where('id' , $row->psychologist_id)->first();
            $psy_device_token = $psyDetail->device_token;
            if($psy_device_token != null){
                $psy_message = 'Your HappiGUIDE session will start at '.$row->start_time.'. Don'."'".'t forgot to join!';
                dispatch(new NotificationForSinglePsy($psy_device_token, $psy_message));
            }
            $row->is_notification_emit = '1';
            $row->save();
            \Log::info("Guide notification successfully emit");
        }




        //Scheduled notification
        $all_notifications = ScheduleNotification::where('is_notification_delivered' , '0')->get();
        $current_date_time =  date('Y-m-d H:i');

        foreach($all_notifications as $row){
            if(strtotime($row->scheduled_date_time) <= strtotime($current_date_time) || strtotime($row->scheduled_date_time) == strtotime($current_date_time)){

                \Log::info("Send notification");

                $user_language = $row->user_language;
                $message = $row->message;
                $type = $row->user_type;
                dispatch(new SendNotificationJob($user_language, $message , $type));

                ScheduleNotification::where('id' , $row->id)->update(['is_notification_delivered' => '1']);
            }
        }

    }
}
