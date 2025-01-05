<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(
            "INSERT INTO `service_images` (`overview`,`title`,`image_link`) VALUES
            ('App based Self-Help','HappiAPP','1635415823-sec9_happyapp.svg'),
            ('Counselling','HappiTALK','1635412663-sec9_happytalk.svg'),
            ('For Organisations','HappiSPACE','1635412695-sec9_happyspace.svg'),
            ('Chat + Library','HappiCHAT','1635415883-sec9_happychat.svg'),
            ('Screening','HappiLIFE','1635412813-sec9_happylife.svg');"
        );
    }
}
