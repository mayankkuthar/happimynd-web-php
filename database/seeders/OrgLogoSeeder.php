<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrgLogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(
            "INSERT INTO `organization_logos` (`id`, `image_link`, `created_at`, `updated_at`) VALUES
            (1, '1634145751-dhl.webp', '2021-10-13 17:22:32', '2021-10-13 17:22:32'),
            (2, '1634145809-next.webp', '2021-10-13 17:23:30', '2021-10-13 17:23:30'),
            (3, '1634147257-aviva.webp', '2021-10-13 17:23:53', '2021-10-13 17:47:38'),
            (4, '1634145847-santander.webp', '2021-10-13 17:24:08', '2021-10-13 17:24:08'),
            (5, '1634145859-serco.webp', '2021-10-13 17:24:20', '2021-10-13 17:24:20'),
            (6, '1634145877-healthshield.webp', '2021-10-13 17:24:38', '2021-10-13 17:24:38'),
            (7, '1634145909-thrive_logo_happyapp.svg', '2021-10-13 17:25:09', '2021-10-13 17:25:09');"
        );
    }
}
