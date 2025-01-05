<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RewardPointInstance;

class RewardPointInstanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RewardPointInstance::create(['action_performed' => 'When HappiLEARN Subscribed']);
        RewardPointInstance::create(['action_performed' => 'When HappiBUDDY Subscribed']);
        RewardPointInstance::create(['action_performed' => 'When message is shared in HappiBUDDY']);
        RewardPointInstance::create(['action_performed' => 'When HappiLIFE Assessment is taken up']);
        RewardPointInstance::create(['action_performed' => 'When HappiTALK is booked']);
        RewardPointInstance::create(['action_performed' => 'When feedback is shared']);
        RewardPointInstance::create(['action_performed' => 'When punch in Mood in Mood O meter']);
        RewardPointInstance::create(['action_performed' => 'When HappiSELF subscribed']);
        RewardPointInstance::create(['action_performed' => 'When sub module is completed in HappiSELF']);
        RewardPointInstance::create(['action_performed' => 'When module is completed in HappiSELF']);
        RewardPointInstance::create(['action_performed' => 'When gives email ID']);
        RewardPointInstance::create(['action_performed' => 'When gives phone number']);
        RewardPointInstance::create(['action_performed' => 'When share app']);

        RewardPointInstance::create(['action_performed' => 'When HappiGUIDE Subscribed']);
        

    }
}
