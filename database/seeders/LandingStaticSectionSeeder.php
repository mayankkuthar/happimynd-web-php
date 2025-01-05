<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataContent;
use App\Models\StaticSection;
use App\Models\CarouselSection;
class LandingStaticSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $d = DataContent::create([
            'title' => 'Want to live a Healthy & Happy Life?',
            'content' =>'<p>
            <b>Physical Well-being and Mental Well-being work in synergy.</b> <br><br>At HappiMynd, we provide globally proven unique, digitally empowered tools that are accessible, affordable &amp; reliable with utmost confidentiality. We invite you to take the first step of your journey towards holistic wellness with HappiMynd.</p>',
            'data_group_id' => 2
        ]);
        StaticSection::create([
            'data_group_id' => 2,
            'data_content_id' => $d->id,
            'section' => 'section1'
        ]);
        $d = DataContent::create([
            'title' => 'Healthy You = Healthy Body + Happy Mind',
            'content' => '<p>We are conditioned to give priority to only our Physical Fitness while our Emotional Health is often neglected.</p>',
            'data_group_id' => 2
        ]);
        StaticSection::create([
            'data_group_id' => 2,
            'data_content_id' => $d->id,
            'section' => 'section3'
        ]);
        $d = DataContent::create([
            'title' => '<h1>Why Emotional  Wellbeing is important?</h1>',
            'content' => '<p>Your Physical Health is directly related to your Emotional  Health.
            One of the strongest links between emotional and physical health is longevity.
            Emotional  Wellness is way more important than you think. Emotional wellbeing help us to achieve much more than physical health.</p>',
            'data_group_id' => 2
        ]);
        StaticSection::create([
            'data_group_id' => 2,
            'data_content_id' => $d->id,
            'section' => 'section4'
        ]);
        $d = DataContent::create([
            'title' => 'How to take care of Emotional wellbeing?',
            'content' => '<p>The first step of your journey towards holistic wellness is to be aware of your emotional status.
            <br><br>HappiMynd’s method of evaluation is
            <br>
            Customized,
            <br>
            Confidential,
            <br>
            Deeply researched &amp;
            <br>
            Backed by global best practices
            <br><br>You can thrive only when you know where you stand.
            </p>',
            'data_group_id' => 2
        ]);
        
        StaticSection::create([
            'data_group_id' => 2,
            'data_content_id' => $d->id,
            'section' => 'section5'
        ]);
        
        $d = DataContent::create([
            'title' => 'Give yourself the gift of Emotional Well-being',
            'content' => '',
            'data_group_id' => 2
        ]);

        StaticSection::create([
            'data_group_id' => 2,
            'data_content_id' => $d->id,
            'section' => 'section6'
        ]);
        
        $d = DataContent::create([
            'title' => 'The world is talking about it',
            'content' => '“A healthy mind, is the greatest treasure to find.’’',
            'data_group_id' => 2
        ]);
        
        StaticSection::create([
            'data_group_id' => 2,
            'data_content_id' => $d->id,
            'section' => 'section7'
        ]);
        

        $d = DataContent::create([
            'title' => 'Have you ever explored your behavioral & emotional Health?',
            'content' => '',
            'data_group_id' => 2
        ]);
        
        StaticSection::create([
            'data_group_id' => 2,
            'data_content_id' => $d->id,
            'section' => 'section8'
        ]);
       
        $d = CarouselSection::create([
            'data_group_id' => 2,
            'name' => 'feelings_carousel',

        ]);
        DataContent::create([
            'title' => 'Share your feelings',
            'content' => '',
            'data_group_id' => 2,
            'preference' => 1,
            'carousel_section_id' => $d->id
        ]);
        
        DataContent::create([
            'title' => 'Be aware & take self care',
            'content' => '',
            'data_group_id' => 2,
            'preference' => 2,
            'carousel_section_id' => $d->id
        ]);
    
        DataContent::create([
            'title' => 'Know early signs',
            'content' => '',
            'data_group_id' => 2,
            'preference' => 3,
            'carousel_section_id' => $d->id
        ]);

        $d = CarouselSection::create([
            'data_group_id' => 2,
            'name' => 'people_carousel',

        ]);
        
        DataContent::create([
            'title' => 'coursel_content',
            'content' => '<h1>People talk about physical fitness, but mental health is equally important.</h1><p>Deepika Padukone</p>',
            'data_group_id' => 2,
            'preference' => 1,
            'carousel_section_id' => $d->id,
        ]);
        
        DataContent::create([
            'title' => 'coursel_content',
            'content' => '<h1>It’s Okay not to be Okay,but It’s not “Okay” to not seek help.</h1><p>Amitabh Bachchan</p>',
            'data_group_id' => 2,
            'preference' => 2,
            'carousel_section_id' => $d->id
        ]);

        DataContent::create([
            'title' => 'coursel_content',
            'content' => "<h1>If ... something inside you feels like it's wounded just like a physial injury. You've got to get help. There's nothing weak about that. It's strong.</h1><p>Barack Obama</p>",
            'data_group_id' => 2,
            'preference' => 3,
            'carousel_section_id' => $d->id
        ]);
        
        DataContent::create([
            'title' => 'coursel_content',
            'content' => '<h1>As far as the game goes, I have always been very keen on improving my mental state and not really focus on practising long hours in the nets</h1><p>Virat Kohli</p>',
            'data_group_id' => 2,
            'preference' => 4,
            'carousel_section_id' => $d->id
        ]);

        $d = CarouselSection::create([
            'data_group_id' => 2,
            'name' => 'achievement_carousel',

        ]);

        DataContent::create([
            'title' => 'Clients Worldwide',
            'content' => '2800',
            'data_group_id' => 2,
            'carousel_section_id' => $d->id
        ]);
        

        DataContent::create([
            'title' => 'App Users',
            'content' => '38',
            'data_group_id' => 2,
            'carousel_section_id' => $d->id
        ]);
        
        DataContent::create([
            'title' => 'App Usage Rate',
            'content' => '40',
            'data_group_id' => 2,
            'carousel_section_id' => $d->id
        ]);

        DataContent::create([
            'title' => 'Assessment Users',
            'content' => '15',
            'data_group_id' => 2,
            'carousel_section_id' => $d->id
        ]);
    }
}
