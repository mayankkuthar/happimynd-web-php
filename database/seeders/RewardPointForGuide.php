<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RewardPointInstance;



class RewardPointForGuide extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        RewardPointInstance::create(['action_performed' => 'When HappiGUIDE Subscribed']);

    }
}
