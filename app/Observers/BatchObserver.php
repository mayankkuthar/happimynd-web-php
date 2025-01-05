<?php

namespace App\Observers;

use App\Models\Assessment;
use App\Models\Batch;

class BatchObserver
{
    /**
     * Handle the Batch "created" event.
     *
     * @param  \App\Models\Batch  $batch
     * @return void
     */
    public function created(Batch $batch)
    {
        //
    }

    /**
     * Handle the Batch "updated" event.
     *
     * @param  \App\Models\Batch  $batch
     * @return void
     */
    public function updated(Batch $batch)
    {
        //
    }

    /**
     * Handle the Batch "deleted" event.
     *
     * @param  \App\Models\Batch  $batch
     * @return void
     */
    public function deleted(Batch $batch)
    {
        foreach ($batch->batchCategory as $batchCategory) {
            $batchCategory->delete();
        }
        $batch->assessment()->delete();
    }

    /**
     * Handle the Batch "restored" event.
     *
     * @param  \App\Models\Batch  $batch
     * @return void
     */
    public function restored(Batch $batch)
    {
        foreach ($batch->batchCategory()->withTrashed()->get() as $batchCategory) {
            $batchCategory->restore();
        }
        $batch->assessment()->withTrashed()->restore();
    }

    /**
     * Handle the Batch "force deleted" event.
     *
     * @param  \App\Models\Batch  $batch
     * @return void
     */
    public function forceDeleted(Batch $batch)
    {
        foreach ($batch->batchCategory()->withTrashed()->get() as $batchCategory) {
            $batchCategory->forecDetele();
        }
        $batch->assessment()->withTrashed()->forceDelete();
    }
}
