<?php

namespace App\Observers;

use App\Models\Assessment;
use App\Models\AssessmentApprove;
use App\Models\User;
use App\Services\BitrixService;

class AssessmentObserver
{
    /**
     * Handle the Assessment "created" event.
     *
     * @param  \App\Models\Assessment  $assessment
     * @return void
     */
    public function created(Assessment $assessment)
    {
        //
    }

    /**
     * Handle the Assessment "updated" event.
     *
     * @param  \App\Models\Assessment  $assessment
     * @return void
     */
    public function updated(Assessment $assessment)
    {

        $user = User::find($assessment->user_id);
        $assessmentApprove = AssessmentApprove::where('assessment_id', $assessment->id)->first();
        if ($user && $user->deal_id) {
            if (config('constants.bitrix')) {
                $bitrix = new BitrixService();
                $data = array(
                    "reportLink" => $assessment->report,
                    "slot" => ($assessmentApprove) ? $assessmentApprove->slot : "",
                    "calltime" => ($assessmentApprove) ? $assessmentApprove->available_date : "",
                    "detailLink" => route('downloadAssessmentDetail', [base64_encode('assessment_id') => base64_encode($assessment->id)]),
                );
                $bitrix->updateDeal($user->deal_id, $user, $data);
            }
        }
    }

    /**
     * Handle the Assessment "deleted" event.
     *
     * @param  \App\Models\Assessment  $assessment
     * @return void
     */
    public function deleted(Assessment $assessment)
    {
        $assessment->answer()->delete();
        if ($assessment->score) {
            $assessment->score->delete();
        }
    }

    /**
     * Handle the Assessment "restored" event.
     *
     * @param  \App\Models\Assessment  $assessment
     * @return void
     */
    public function restored(Assessment $assessment)
    {
        $assessment->answer()->withTrashed()->restore();
        $score = $assessment->score()->withTrashed()->first();
        if ($score) {
            $score->restore();
        }
    }

    /**
     * Handle the Assessment "force deleted" event.
     *
     * @param  \App\Models\Assessment  $assessment
     * @return void
     */
    public function forceDeleted(Assessment $assessment)
    {
        $assessment->answer()->withTrashed()->forceDelete();
        if ($assessment->score()->withTrashed()->first()) {
            $assessment->score()->withTrashed()->first()->forceDelete();
        }
    }
}
