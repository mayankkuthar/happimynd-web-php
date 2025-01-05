<?php

namespace App\Observers;

use App\Models\BatchCategory;
use App\Models\Question;

class BatchCategoryObserver
{
    /**
     * Handle the BatchCategory "created" event.
     *
     * @param  \App\Models\BatchCategory  $batchCategory
     * @return void
     */
    public function created(BatchCategory $batchCategory)
    {
        //
    }

    /**
     * Handle the BatchCategory "updated" event.
     *
     * @param  \App\Models\BatchCategory  $batchCategory
     * @return void
     */
    public function updated(BatchCategory $batchCategory)
    {
        //
    }

    /**
     * Handle the BatchCategory "deleted" event.
     *
     * @param  \App\Models\BatchCategory  $batchCategory
     * @return void
     */
    public function deleted(BatchCategory $batchCategory)
    {
        $batchCategory->category->delete();
        foreach ($batchCategory->reportCharacteristic as $reportCharacteristic) {
            $reportCharacteristic->delete();
        }
        Question::where('batch_category_id', $batchCategory->id)->delete();
    }

    /**
     * Handle the BatchCategory "restored" event.
     *
     * @param  \App\Models\BatchCategory  $batchCategory
     * @return void
     */
    public function restored(BatchCategory $batchCategory)
    {
        $reportCharacteristic = $batchCategory->reportCharacteristic()->withTrashed()->restore();
        $batchCategory->category()->withTrashed()->restore();
        Question::where('batch_category_id', $batchCategory->id)->withTrashed()->restore();
    }

    /**
     * Handle the BatchCategory "force deleted" event.
     *
     * @param  \App\Models\BatchCategory  $batchCategory
     * @return void
     */
    public function forceDeleted(BatchCategory $batchCategory)
    {
        $batchCategory->category()->withTrashed()->forceDelete();
        Question::where('batch_category_id', $batchCategory->id)->withTrashed()->forceDelete();
    }
}
