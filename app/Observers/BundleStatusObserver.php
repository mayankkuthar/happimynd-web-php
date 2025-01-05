<?php

namespace App\Observers;

use App\Models\BundleStatus;
use App\Services\BitrixService;

class BundleStatusObserver
{
    /**
     * Handle the BundleStatus "created" event.
     *
     * @param  \App\Models\BundleStatus  $bundleStatus
     * @return void
     */
    public function created(BundleStatus $bundleStatus)
    {
        // dd($bundleStatus->receipt_id);
        \Log::debug('bundle observer' . json_encode($bundleStatus));
        /**
         * if user has given assessmnet without buying package
         * and then buys package make assessment plan percentage
         * as completed and generate report
         */
        if (
            $bundleStatus->percentage_covered != 100.00 &&
            $bundleStatus->plans->package->name == 'HappiLIFE Screening' &&
            $bundleStatus->user->assessment &&
            $bundleStatus->user->assessment->first() &&
            $bundleStatus->user->assessment->first()->ended_at != null
        ) {
            $bundleStatus->percentage_covered = 100.00;
            $bundleStatus->save();
            $bundleStatus->user->generateReportAndSendMail();
        }

        //if bundle is created while signup(organization user token plans) or manually same update in its bitrix deal
        // if (config('constants.bitrix')) {
        //     $user = $bundleStatus->user->toArray();
        //     $deal_id = $user['deal_id'];
        //     /** Update the deal after payment of the B2C deal */
        //     if ($deal_id && ($bundleStatus->receipt_id == null || $bundleStatus->receipt_id == '')) {
        //         $bitrixResponse = (new BitrixService)->addProductDeal($deal_id, [[
        //             "package_id" => $bundleStatus->plans->package_id,
        //             "price" => 0,
        //             "quantity" => 1,
        //         ]]);
        //     }
        // }

        \Log::debug(' exit bundle observer');
    }

    /**
     * Handle the BundleStatus "updated" event.
     *
     * @param  \App\Models\BundleStatus  $bundleStatus
     * @return void
     */
    public function updated(BundleStatus $bundleStatus)
    {
        //
    }

    /**
     * Handle the BundleStatus "deleted" event.
     *
     * @param  \App\Models\BundleStatus  $bundleStatus
     * @return void
     */
    public function deleted(BundleStatus $bundleStatus)
    {
        //
    }

    /**
     * Handle the BundleStatus "restored" event.
     *
     * @param  \App\Models\BundleStatus  $bundleStatus
     * @return void
     */
    public function restored(BundleStatus $bundleStatus)
    {
        //
    }

    /**
     * Handle the BundleStatus "force deleted" event.
     *
     * @param  \App\Models\BundleStatus  $bundleStatus
     * @return void
     */
    public function forceDeleted(BundleStatus $bundleStatus)
    {
        //
    }
}
