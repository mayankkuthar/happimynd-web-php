<?php

namespace App\Observers;

use App\Models\VerifyUser;
use App\Services\BitrixService;

class VerifyUserObserver
{
    /**
     * Handle the VerifyUser "created" event.
     *
     * @param  \App\Models\VerifyUser  $verifyUser
     * @return void
     */
    public function created(VerifyUser $verifyUser)
    {
        //
    }

    public function updating(VerifyUser $verifyUser)
    {
        \Log::debug('VerifyUser observer');
        if ($verifyUser->isDirty('email_verify') && $verifyUser->email_verify == 1) {
            \Log::debug('invoking report job from verifyuser observer');
            $verifyUser->user->generateReportAndSendMail();
        }
        \Log::debug(' exit VerifyUser observer');
    }

    /**
     * Handle the VerifyUser "updated" event.
     *
     * @param  \App\Models\VerifyUser  $verifyUser
     * @return void
     */
    public function updated(VerifyUser $verifyUser)
    {
        if ($verifyUser->email_verify == 1 && $verifyUser->user->bundleStatus->count() > 0) {
            foreach ($verifyUser->user->bundleStatus as $bundleStatus) {
                if ($bundleStatus->plans->package->name == "HappiCHAT") {
                    $verifyUser->user->copyBitrixDealToPipeline("HappiCHAT");
                } else if ($bundleStatus->plans->package->name == "HappiTALK") {
                    $verifyUser->user->copyBitrixDealToPipeline("HappiTALK");
                }
            }
        }
    }

    /**
     * Handle the VerifyUser "deleted" event.
     *
     * @param  \App\Models\VerifyUser  $verifyUser
     * @return void
     */
    public function deleted(VerifyUser $verifyUser)
    {
        //
    }

    /**
     * Handle the VerifyUser "restored" event.
     *
     * @param  \App\Models\VerifyUser  $verifyUser
     * @return void
     */
    public function restored(VerifyUser $verifyUser)
    {
        //
    }

    /**
     * Handle the VerifyUser "force deleted" event.
     *
     * @param  \App\Models\VerifyUser  $verifyUser
     * @return void
     */
    public function forceDeleted(VerifyUser $verifyUser)
    {
        //
    }
}
