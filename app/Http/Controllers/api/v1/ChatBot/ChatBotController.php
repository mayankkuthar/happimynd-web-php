<?php

namespace App\Http\Controllers\api\v1\ChatBot;

use App\Models\ChatBot\DiscussionTopic;
use App\Http\Controllers\Controller;
use App\Models\ChatBot\Recommendation;
use App\Models\ChatBot\RecommendationCategory;
use App\Models\ChatBot\SuicidalThought;

class ChatBotController extends Controller
{
    /**
     * Serve the video.
     */
    public function squareBreathing()
    {
        return [
            'status' => 'Success',
            'message' => 'Video URL retrieved successfully.',
            'content' => asset('video/square-breathing.mp4'),
        ];
    }

    /**
     * Retrieves all chat bot discussion topics.
     */
    public function discussionTopics()
    {
        $content = DiscussionTopic::select('id', 'description')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Discussion topics retrieved successfully.',
            'content' => $content,
        ]);
    }

    /**
     * Retrieves the suicidal thoughts help text message.
     */
    public function suicidalThoughts()
    {
        $content = SuicidalThought::select('description')->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Suicidal thoughts help message retrieved successfully.',
            'content' => $content,
        ]);
    }

    /**
     * Retrieves all assessment concerns.
     */
    public function assessmentConcerns()
    {
        $content = [
            'Stressful Situations',
            'Constant Worrying ',
            'Feeling Low',
            'Sleep Issues',
            'Relationship Challenges',
            'Low Self Confidence',
            'Anger Management',
            'Getting Bullied ',
            'Body Image Issues',
            'Work Life Balance',
            'Frequent Loneliness',
            'Wavering Motivation ',
            'Gaining Life Satisfaction ',
            'Managing Emotions',
            'Seeking Happiness',
            'Pregnancy related Anxiety',
            'Loss in Life',
            'Exam related Anxiety',
            'Traumatic Past Events',
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Assessment concerns retrieved successfully.',
            'content' => $content,
        ]);
    }

    /**
     * Retrieves all assessment questions.
     */
    public function assessmentQuestions()
    {
        $content = [
            'Stress' => [
                'In the last month, how often have you been upset because of something that happened unexpectedly?',
                'In the last month, how often have you felt that you were unable to control the important things in your life?',
                'In the last month, how often have you felt nervous and stressed?',
                'In the last month, how often have you felt confident about your ability to handle your personal problems?',
                'In the last month, how often have you felt that things were going your way?',
                'In the last month, how often have you found that you could not cope with all the things that you had to do?',
                'In the last month, how often have you been able to control irritations in your life?',
                'In the last month, how often have you felt that you were on top of things?',
                'In the last month, how often have you been angered because of things that happened that were outside of your control?',
                'In the last month, how often have you felt difficulties were piling up so high that you could not overcome them?',
            ],
            'Worry' => [
                'Over the last 2 weeks, how often have you felt nervous, anxious or on edge?',
                'Over the last 2 weeks, how often have you not been able to stop or control worrying?',
                'Over the last 2 weeks, how often have you worried too much about different things?',
                'Over the last 2 weeks, how often have you had trouble relaxing?',
                'Over the last 2 weeks, how often have you been so restless that it is hard to sit still?',
                'Over the last 2 weeks, how often have you become easily annoyed or irritable?',
                'Over the last 2 weeks, how often have you felt afraid as if something awful might happen?',
            ],
            'Feeling low' => [
                'Over the last 2 weeks, how often have you felt little interest or pleasure in doing things?',
                'Over the last 2 weeks, how often have you felt down, depressed, or hopeless?',
                'Over the last 2 weeks, how often have you had trouble falling or staying asleep, or sleeping too much?',
                'Over the last 2 weeks, how often have you felt tired or had little energy?',
                'Over the last 2 weeks, how often have you had poor appetite or have been overeating?',
                'Over the last 2 weeks, how often have you felt bad about yourself or that you are a failure or have let yourself or your family down?',
                'Over the last 2 weeks, how often have you had trouble concentrating on things, such as reading the newspaper or watching television?',
                'Over the last 2 weeks, how often have you been moving or speaking so slowly that other people could have noticed. Or the opposite- being so figety or restless that you have been moving around a lot more than usual?',
                'Over the last 2 weeks, how often have you had thoughts that you would be better off dead, or of hurting yourself?',
            ],
            'Sleep trouble' => [
                'Do you have trouble falling asleep?',
                'Do you awaken in the middle of the night and have trouble falling asleep again?',
                'Do you get the amount of sleep you needed?',
                'Do you tired when you first wake in the morning?',
                'Do you overthink before going to sleep?',
                'Do you have trouble staying awake during the day?',
                'Do you get nightmares?',
                'Do you find it difficult to relax before going to sleep?',
                'Does poor sleep affect your mood, energy, or relationships?',
                'Does poor sleep affect your concentration and productivity?',
            ],
            'Relationship challenges' => [
                'How well does your partner meet your needs?',
                'In general, how satisfied are you with your relationship?',
                'How good is your relationship compared to most?',
                'How often do you wish you hadn\'t gotten into this relationship?',
                'To what extent has your relationship met your original expectations?',
                'How much do you love your partner?',
                'How many problems are there in your relationship?',
            ],
            'Low self confidence' => [
                'On the whole, I am satisfied with myself.',
                'At times, I think I am no good at all.',
                'I feel that I have a number of good qualities.',
                'I am able to do things as well as most other people.',
                'I feel I do not have much to be proud of.',
                'I certainly feel useless at times.',
                'I feel that I\'m a person of worth, at least on an equal plane with others.',
                'I wish I could have more respect for myself.',
                'All in all, I am inclined to feel that I am a failure.',
                'I take a positive attitude toward myself.',
            ],
            'Anger management' => [
                'Do you get upset when others disagree with you?',
                'When you become angry, do you withdraw from people?',
                'Do you have a tendency to take your anger out on someone other than the person you\'re angry with?',
                'Are you satisfied with the way you settle differences with others?',
                'Do you often act politely even though you\'re fuming?',
                'Do you tend to feel very guilty or bad after getting angry?',
                'Do you keep things in until you finally explode with anger?',
                'Do you have a tendency to criticise others?',
                'Do you hit something or want to hit something when you feel angry?',
                'Do you tend to yell, curse, and say things that you later regret?',
            ],
            'Bullying' => [
                'Do others make hurtful comments about you?',
                'Do others imitate you or make fun of your appearance?',
                'Do you feel isolated at work, school, home, or other places?',
                'Are others spreading rumors or false information about you?',
                'Are you ever afraid to go to work, school, or places where you feel hurt or excluded?',
                'Are you being harassed online or do others post mean things about you?',
                'Do you feel anxious or depressed when you have to interact with a hurtful person?',
                'Do others dismiss what you\'re saying or "put you down"?',
                'Do others frequently act impatient with you, treating you like you\'re incompetent?',
                'Do others routinely blame and criticize you?',
            ],
            'Body image issues' => [
                'I am satisfied with my body shape and weight.',
                'I feel people ignore me because of my looks.',
                'It is difficult for me to accept compliments about my looks.',
                'I respect my body.',
                'My body makes me feel confident.',
                'I wish I could change certain parts of my body.',
                'I feel I am unattractive.',
                'My body makes me feel insecure.',
            ],
            'Work life balance' => [
                'I work after working hours as well.',
                'Outside of work, I pursue and participate in a variety of activities.',
                'I often feel exhausted – even early in the week.',
                'I frequently think about work when I\'m not working.',
                'I usually have enough time to spend with my loved ones.',
                'Usually, I work through my lunch break.',
                'I feel that I have time in the day to take a pause/break.',
                'I can concentrate well on my work without getting distracted by personal matters, social media, or any other external things.',
                'I can handle work pressure and can switch between tasks.',
                'I feel like I have little or no control over my life.',
            ],
            'Loneliness' => [
                'How often do you feel that you lack companionship?',
                'How often do you feel left out?',
                'How often do you feel isolated from others?',
            ],
            'Motivation' => [
                'New ideas and projects sometimes distract me from previous ones',
                'Setbacks don\'t discourage me.',
                'I have been obsessed with a certain idea or project for a short time but later lost interest',
                'I am a hard worker.',
                'I often set a goal but later choose to pursue a different one.',
                'I have difficulty maintaining my focus on projects that take more than a few months to complete.',
                'I finish whatever I begin.',
                'I am diligent.',
            ],
            'Managing emotions' => [],
            'Happiness' => [],
            'Pregnancy related anxiety' => [
                'Do you feel anxious that your relationship with your partner will change after child birth?',
                'Do you feel anxious that you wont be able to take care of your baby\'s needs?',
                'Do you worry about having a complicated pregnancy?',
                'Do you worry that your baby might not be healthy?',
                'Do you feel upset thinking about how your body (or your wife\'s body) will change after pregnancy?',
                'Do you feel anxious that you wont be a good parent?',
                'Do you feel stressed about your financial responsibility?',
                'Do you feel that your partner is not as interested as you in the pregnancy?',
            ],
            'Grief and loss' => [],
            'Exam anxiety' => [
                'The closer I am to a major exam, the harder it is for me to concentrate on the material.',
                'When I study, I worry that I will not remember the material on the exam.',
                'During important exams, I think that I am doing awful or that I may fail.',
                'I lose focus on important exams, and I cannot remember material that I knew before the exam.',
                'I finally remember the answer to exam questions after the exam is already over.',
                'I worry so much before a major exam that I am too worn out to do my best on the exam.',
                'I feel out of sorts or not really myself when I take important exams.',
                'I find that my mind sometimes wanders when I am taking important exams.',
                'After an exam, I worry about whether I did well enough.',
                'I struggle with writing assignments, or avoid them as long as I can. I feel that whatever I do will not be good enough.',
            ],
            'Trauma due to distressing event' => [
                'Have you ever served in a war zone, or have you ever served in a noncombat job that exposed you to war-related casualties (for example, as a medic or on graves registration duty?)',
                'Have you ever been in a serious car accident, or a serious accident at work or somewhere else?',
                'Have you ever been in a major natural or technological disaster, such as a fire, tornado, hurricane, flood, earthquake, orchemical spill?',
                'Have you ever had a life-threatening illness such as cancer, a heart attack, leukemia, AIDS, multiple sclerosis, etc.?',
                'Before age 18, were you ever physically punished or beaten by a parent, caretaker, or teacher so that: you were very frightened; or you thought you would be injured; or you received bruises, cuts, welts, lumps or other injuries?',
                'Not including any punishments or beatings you already reported in Question 5, have you ever been attacked, beaten, or mugged by anyone, including friends, family members or strangers?',
                'Has anyone ever made or pressured you into having some type of unwanted sexual contact? (Note: By sexual contact we mean any contact between someone else and your private parts or between you and some else\'s private parts.)',
                'Have you ever been in any other situation in which you were seriously injured, or have you ever been in any other situation in which you feared you might be seriously injured or killed?',
                'Has a close family member or friend died violently, for example, in a serious car crash, mugging, or attack?',
                'Have you ever witnessed a situation in which someone was seriously injured or killed, or have you ever witnessed a situation in which you feared someone would be seriously injured or killed? (Note: Do not answer "yes" for any event you already reported in Questions 1-9).',
            ],
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Assessment questions retrieved successfully.',
            'content' => $content,
        ]);
    }

    /**
     * Recommendation categories.
     */
    public function recommendationCategories()
    {
        $recommendationCategories = RecommendationCategory::get();

        return [
            'status' => 'Success',
            'message' => 'Recommendations retrieved successfully.',
            'content' => $recommendationCategories,
        ];
    }

    /**
     * Recommendations.
     */
    public function recommendations(int $profile, int $category)
    {
        $recommendations = Recommendation::where('user_profile_id', $profile)->where('recommendation_category_id', $category)->get();

        return [
            'status' => 'Success',
            'message' => 'Recommendations retrieved successfully.',
            'content' => $recommendations,
        ];
    }
}
