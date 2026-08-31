<?php

use App\Models\DurationType;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        foreach ([3, 8] as $frequency) {
            DurationType::firstOrCreate(
                ['type' => DurationType::TYPES['session'], 'frequency' => $frequency],
                ['value' => 45]
            );
        }
    }

    public function down()
    {
        DurationType::where('type', DurationType::TYPES['session'])->whereIn('frequency', [3, 8])->delete();
    }
};
