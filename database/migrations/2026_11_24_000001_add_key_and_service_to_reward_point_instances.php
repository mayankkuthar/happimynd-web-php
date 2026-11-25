<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reward points used to be looked up in code by their display text
 * (action_performed), so the text could never be changed. This gives every
 * reward a fixed `key` that the code uses instead, plus the service it
 * belongs to, how often it can be earned and whether it is switched on.
 * The display text is then rewritten in plain language.
 */
return new class extends Migration
{
    /** old action_performed text => [key, service, new text] */
    const EXISTING = [
        'When HappiLEARN Subscribed'                => ['happilearn_subscribed', 'HappiLEARN', 'Subscribe to HappiLEARN'],
        'When HappiBUDDY Subscribed'                => ['happibuddy_subscribed', 'HappiBUDDY', 'Subscribe to HappiBUDDY'],
        'When message is shared in HappiBUDDY'      => ['happibuddy_message', 'HappiBUDDY', 'Send a message in HappiBUDDY'],
        'When HappiLIFE Assessment is taken up'     => ['happilife_completed', 'HappiLIFE', 'Complete the HappiLIFE screening'],
        'When HappiTALK is booked'                  => ['happitalk_booked', 'HappiTALK', 'Book a HappiTALK session'],
        'When feedback is shared'                   => ['feedback_shared', 'General', 'Share your feedback'],
        'When punch in Mood in Mood O meter'        => ['mood_meter', 'Mood-O-Meter', 'Log your mood in the Mood-O-Meter'],
        'When HappiSELF subscribed'                 => ['happiself_subscribed', 'HappiSELF', 'Subscribe to HappiSELF'],
        'When sub module is completed in HappiSELF' => ['happiself_submodule_completed', 'HappiSELF', 'Complete a HappiSELF sub-module'],
        'When module is completed in HappiSELF'     => ['happiself_module_completed', 'HappiSELF', 'Complete a HappiSELF module'],
        'When gives email ID'                       => ['email_added', 'Profile', 'Add your email ID'],
        'When gives phone number'                   => ['phone_added', 'Profile', 'Add your phone number'],
        'When share app'                            => ['app_shared', 'Referral', 'Invite a friend who signs up with your referral code'],
        'When HappiGUIDE Subscribed'                => ['happiguide_booked', 'HappiGUIDE', 'Book a HappiGUIDE session'],
    ];

    /** Rewards for newer services, added switched on with 0 points until an admin sets them */
    const NEW = [
        ['happivoice_check', 'HappiVOICE', 'Complete a HappiVOICE check', 'daily'],
    ];

    public function up()
    {
        Schema::table('reward_point_instances', function (Blueprint $table) {
            $table->string('key', 64)->nullable()->unique()->after('id');
            $table->string('service', 64)->nullable()->after('key');
            // system = awarded by the backend; app = reported by an app/site via POST earn-reward-points
            $table->string('awarded_by', 16)->default('system')->after('points_to_be_given');
            // every_time | daily | once
            $table->string('frequency', 16)->default('every_time')->after('awarded_by');
            $table->boolean('is_active')->default(true)->after('frequency');
        });

        Schema::table('user_reward_point_records', function (Blueprint $table) {
            $table->string('reward_key', 64)->nullable()->after('task_performed');
            $table->index(['user_id', 'reward_key']);
        });

        $now = now();
        foreach (self::EXISTING as $oldText => [$key, $service, $text]) {
            DB::table('reward_point_instances')
                ->where('action_performed', $oldText)
                ->whereNull('key')
                ->orderBy('id')
                ->limit(1)
                ->update(['key' => $key, 'service' => $service, 'action_performed' => $text, 'updated_at' => $now]);

            // a missing row would make that reward silently stop, so create it with 0 points
            if (!DB::table('reward_point_instances')->where('key', $key)->exists()) {
                DB::table('reward_point_instances')->insert([
                    'key' => $key, 'service' => $service, 'action_performed' => $text,
                    'points_to_be_given' => '0', 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        foreach (self::NEW as [$key, $service, $text, $frequency]) {
            if (!DB::table('reward_point_instances')->where('key', $key)->exists()) {
                DB::table('reward_point_instances')->insert([
                    'key' => $key, 'service' => $service, 'action_performed' => $text,
                    'points_to_be_given' => '0', 'frequency' => $frequency,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    public function down()
    {
        foreach (self::EXISTING as $oldText => [$key]) {
            DB::table('reward_point_instances')->where('key', $key)->update(['action_performed' => $oldText]);
        }
        DB::table('reward_point_instances')->whereIn('key', array_column(self::NEW, 0))->delete();

        Schema::table('user_reward_point_records', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'reward_key']);
            $table->dropColumn('reward_key');
        });
        Schema::table('reward_point_instances', function (Blueprint $table) {
            $table->dropUnique(['key']);
            $table->dropColumn(['key', 'service', 'awarded_by', 'frequency', 'is_active']);
        });
    }
};
