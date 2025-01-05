<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EditButtonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(
            "INSERT INTO `edit_buttons` (`button_content`,`page_name`,`button_name`) VALUES
            ('HappiLIFE Screening','services','HappiLIFE'),
            ('Get HappiAPP now','services','HappiAPP'),
            ('Get HappiCHAT now','services','HappiCHAT'),
            ('Talk with Experts','services','HappiTALK'),
            ('Get HappiSPACE now','services','HappiSPACE'),
            ('Explore our services','quotes','Our Services'),
            ('HappiLIFE Screening', 'landing', 'button_section1'),           
            ('HappiLIFE Screening', 'landing', 'button_section8'),
            ('Get HappiSPACE now', 'organisation', 'organisation_contact'),
            ('Get HappiSPACE now', 'organisation', 'organisation_score');"
        );
    }
}
