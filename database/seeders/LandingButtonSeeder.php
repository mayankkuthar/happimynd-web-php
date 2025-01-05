<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class LandingButtonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(
            "INSERT INTO `edit_buttons` (`button_content`,`page_name`, `button_name`) VALUES
            ('HappiLIFE Screening', 'landing', 'button_section1'),           
            ('HappiLIFE Screening', 'landing', 'button_section8');"
        );

        DB::unprepared(
            "INSERT INTO `edit_buttons` (`button_content`,`page_name`, `button_name`) VALUES
            ('Get HappiSPACE now', 'organisation', 'organisation_contact'),
            ('Get HappiSPACE now', 'organisation', 'organisation_score');"
        );

    }
}
