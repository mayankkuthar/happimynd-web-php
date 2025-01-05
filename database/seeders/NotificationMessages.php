<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificationMessage;

class NotificationMessages extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = [
            'When someone downloads the app',
            'Immediately after App download',
            'When a service is purchased',
            'After password is changed',
            'As soon as a user books the appointment',
            '24 hours before a scheduled appointment',
            '30 minutes prior to scheduled appointment',
            'When the psychologist joins the session',
            'When there is new message from HappiBuddy Session and the user is offline',
            'As soon as a user completes a module',
            'When a user achieves certain Reward points ( 1000 , 2000 , 5000 etc.)',
            "Sign in from another device",
            "Any update in the profile",
            "Prompt to use Moodometer",
            "If a user does not respond to moodometer for 7 consecutive prompts, then add a blog explaining importance of expressing your emotions",
            "If a user reports bad mood for 7 consecutive entries",
            "Once a week on Sunday afternoon",
            "If someone has not shared the mood in the last week",
            "Sunday Post 2:00 PM",
            "If a user has not engaged and logged in the last module/ completely dormant (in 7 days)",
            "Within 24 hours a user downloaded the app",
            "If a user has taken the screening but not downloaded the report (Within 30 minutes)",
            "Upsell for HappiGUIDE",
        ];


        $english_message = [
            "Hi!
            Now that you have completed the most difficult task of taking the first step towards your wellbeing, let’s get the easier ones completed too. Explore our services, create your account and start your journey to become a HAPPIER YOU!",

            "Self awareness is now trending! Want to boast your strengths and become a stronger you? Take the HappiLIFE screening now!",

            "Congratulations on Prioritising Your Emotional Health😃🤗",

            "YO! No One Can Lock You Out NOW! ",

            "You are On The Right Path!🛤️😀
            Your HappiTALK session has been proposed with your expert psychologist (name) for (date and time)",

            "Your HappiTALK session starts in the next 24 hours! Are you ready to learn, unlearn and relearn?",

            "ATTENTION! In 30 minutes, your Happi Expert will see you in the session! 📲",

            "I'm here waiting for you! Please Join in Quickly 🤓",

            "Your Buddy has left a message for you! Check your LOG ROOM now!",

            "That's the Winning SPIRIT 💪 Keep the VIBE ON!",

            "Doesn’t it feel great to be rewarded for self work?
            Congratulations 🎉 👏 You Earned 1000/2000/3000/..5000 Reward Points 🙆",

            "Is that you?
            Help us keep you safe. Tell us if you signed in from another device😵🤯😨 Check Here NOW!",

            "Thank you for sharing more  about you! We are always glad to keep it updated.",

            "We are sure that you are going through some moods and emotions currently? Share your mood today through the Moodometer! ",

            "Have you ever wondered why a certain thing feels great on one day and unpleasant on the other? It is because of the emotions we are dealing with and our mood is greatly affected by our emotions. 
            Click on the moodometer and tell us how you are feeling today!",

            "Hey! It's seems you're feeling dull 😟 these days. Don't forget you have a BUDDY waiting for you to share and care. 🤗 Login to HappiBUDDY now!",

            "Alt1- Was the week tough for you? If you can identify your problematic emotions, you can take action before it becomes a part of your personality.",

            "A gift of self awareness for yourself, and for others. Share with us how you feel and we can help you in building emotional intelligence that lasts a lifetime.",

            "Option- Wanna PERFECT the Art of Prevention? HappiLEARN is here for you to simplify understanding and ease things out.😀
            Check out these interesting wellbeing articles for self improvement.
            Option- Learning something new never goes out of style. Click here for an exciting blog.📲
            Option- HappiLEARN skills you with PREVENTION strategies. Aren't you curious to try it?🤔",

            "Where have you been? A better and stronger you is just a step away. Start your module from where you left off.",

            "Option- We see you're enjoying the platform. Why not share with your friends and earn rewards? Sounds exciting, isn't it?🤓",

            "Opton- It's time to leverage your strengths and know more about your personal style. Download the HappiLIFE screening summary report now and understand yourself better.
            Option- A glimpse at HappiMynd wheel can answer all your questions! Download the HappiLIFE screening summary report now and understand yourself better.",

            "Option- Understand the intricacies of your HappiLIFE screening summary and implement the right steps for improvement with a summary reading session by our emotional wellbeing expert.
            Option- HappiGUIDE resolves all your queries under the canopy of trained professionals. What's stopping you? Click here to CONNECT! ",
        ];



        for ($i=0; $i <=22 ; $i++) { 
            
            $message_type = $type[$i];
            $english_message_text = $english_message[$i];

            $data = [
                'type' => $message_type,
                'english' => $english_message_text,
            ];


            NotificationMessage::create($data);
        }

    }
}









