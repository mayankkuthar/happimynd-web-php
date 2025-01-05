<?php

namespace App\Observers;

use App\Models\RaiseQuery;
use App\Services\BitrixService;
use Exception;

class RaiseQueryObserver
{
    /**
     * Handle the RaiseQuery "created" event.
     *
     * @param  \App\Models\RaiseQuery  $raiseQuery
     * @return void
     */
    public function created(RaiseQuery $raiseQuery)
    {
        try {
            if (config('constants.bitrix')) {
                $bitrix = new BitrixService();
                $user = $raiseQuery->user()->first();
                $data = [
                    "dealCategory" => "RaisedQuery",
                    "queryType" => $raiseQuery->category,
                    "queryDescription" => $raiseQuery->query,
                ];
                $bitrixResponse = $bitrix->addDeal($data);
                $newDealId  = $bitrixResponse->result;
                $updateContactResponse = $bitrix->addOrUpdateContactForDeal($newDealId, $user->toArray());
                if ((isset($updateContactResponse->result)) && $updateContactResponse->result) {
                    $bitrix->updateDeal(
                        $newDealId,
                        "",
                        array('contactId' => $updateContactResponse->result)
                    );
                }
            }
        } catch (Exception $e) {
            \Log::error($e);
        }
    }

    /**
     * Handle the RaiseQuery "updated" event.
     *
     * @param  \App\Models\RaiseQuery  $raiseQuery
     * @return void
     */
    public function updated(RaiseQuery $raiseQuery)
    {
        //
    }

    /**
     * Handle the RaiseQuery "deleted" event.
     *
     * @param  \App\Models\RaiseQuery  $raiseQuery
     * @return void
     */
    public function deleted(RaiseQuery $raiseQuery)
    {
        //
    }

    /**
     * Handle the RaiseQuery "restored" event.
     *
     * @param  \App\Models\RaiseQuery  $raiseQuery
     * @return void
     */
    public function restored(RaiseQuery $raiseQuery)
    {
        //
    }

    /**
     * Handle the RaiseQuery "force deleted" event.
     *
     * @param  \App\Models\RaiseQuery  $raiseQuery
     * @return void
     */
    public function forceDeleted(RaiseQuery $raiseQuery)
    {
        //
    }
}
