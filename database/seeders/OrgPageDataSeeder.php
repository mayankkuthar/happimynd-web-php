<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrgPageDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(
            "INSERT INTO `organization_page_data` (`id`, `name`, `title`, `description`,`image_link`,`created_at`, `updated_at`) VALUES
            (1, 'ORGANISATIONS', 'Workplace stress management and Employee wellbeing programs', '<p>HappiSPACE is an anonymous and highly confidential space that actively works towards redefining mental wellbeing of employees. Our end-to-end tech-enabled platform aims at creating awareness about emotional wellness and offering self-management tools to improve it.</p>','1634123089-orgainsation_img1.svg','2021-10-13 07:22:52', '2021-10-13 09:00:03'),
            (2, 'HOW CAN WE', 'Make Employees Happy?', '<p>*Emotional strength to deal with complexities at work &amp; personal priorities.</p><p>*Awareness about emotional wellbeing issues</p><p>*Knowledge &amp; Skills to prevent emotional issues</p><p>*Tools for Early detection &amp; Self Treatment</p><p>*Complete confidentiality of usage &amp; Security of personal data</p>','1634123130-orgainsation_img2.svg', '2021-10-13 07:24:28', '2021-10-13 08:59:26'),
            (3, 'WHY HAPPIMYND', 'What’s unique about us!', '<p>*Create visibility of overall wellbeing in employees.</p><p>*Generate confidence in users about the need of emotional &amp; mental well being tools.</p><p>*Reliable, Secure &amp; Affordable Tools for safe &amp; secure being.</p><p>*Tangible metrics of usage &amp; improvement of emotional wellbeing</p><p>*Improvement in overall Engagement &amp; enhanced Productivity of Employees.</p>','1634123148-orgainsation_img3.svg' ,'2021-10-13 07:25:26', '2021-10-13 07:25:26');"
        );
    }
}
