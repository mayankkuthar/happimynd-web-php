<?php

namespace App\Observers;

use App\Models\AssessmentApprove;
use App\Models\User;
use App\Services\BitrixService;

class AssessmentApproveObserver
{
    /**
     * Handle the AssessmentApprove "created" event.
     *
     * @param  \App\Models\AssessmentApprove  $assessmentApprove
     * @return void
     */
    public function created(AssessmentApprove $assessmentApprove)
    {
        //
    }

    /**
     * Handle the AssessmentApprove "updated" event.
     *
     * @param  \App\Models\AssessmentApprove  $assessmentApprove
     * @return void
     */
    public function updated(AssessmentApprove $assessmentApprove)
    {
        $assessment = $assessmentApprove->assessment()->first();
        $user = User::find($assessment->user_id);
        if($user && $user->deal_id){
            if(config('constants.bitrix')){
                $bitrix = new BitrixService();
                $data = array(
                    "reportLink"=>($assessment)?$assessment->report:"",
                    "calltime"=>$assessmentApprove->available_date,
                    "slot"=>$assessmentApprove->slot,
                    "detailLink"=>route('downloadAssessmentDetail', [base64_encode('assessment_id')=>base64_encode($assessment->id)]),
                );
            }
        }
    }

    /**
     * Handle the AssessmentApprove "deleted" event.
     *
     * @param  \App\Models\AssessmentApprove  $assessmentApprove
     * @return void
     */
    public function deleted(AssessmentApprove $assessmentApprove)
    {
        //
    }

    /**
     * Handle the AssessmentApprove "restored" event.
     *
     * @param  \App\Models\AssessmentApprove  $assessmentApprove
     * @return void
     */
    public function restored(AssessmentApprove $assessmentApprove)
    {
        //
    }

    /**
     * Handle the AssessmentApprove "force deleted" event.
     *
     * @param  \App\Models\AssessmentApprove  $assessmentApprove
     * @return void
     */
    public function forceDeleted(AssessmentApprove $assessmentApprove)
    {
        //
    }
}
