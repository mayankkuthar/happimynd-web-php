<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PsychologistController;
use App\Http\Controllers\Admin\AuthController;

use App\Http\Controllers\api\v1\UserAuthenticationController;
use App\Http\Controllers\api\v1\PsychologistAuthenticationController;
use App\Http\Controllers\api\v1\ChatVideoController;
use App\Http\Controllers\api\v1\PaymentController;
use App\Http\Controllers\api\v1\AssignPsychologistController;
use App\Http\Controllers\api\v1\HappiLearnController;
use App\Http\Controllers\api\v1\UserControllerApi;
use App\Http\Controllers\api\v1\PsychologistControllerApi;
use App\Http\Controllers\api\v1\VideoChatController;
use App\Http\Controllers\api\v1\HappiTalkController;
use App\Http\Controllers\api\v1\RatingController;
use App\Http\Controllers\api\v1\NotificationApiController;
use App\Http\Controllers\api\v1\FaqTermController;
use App\Http\Controllers\api\v1\WhiteLabelingController;
use App\Http\Controllers\api\v1\HappiselfController;
use App\Http\Controllers\api\v1\HappiguideController;
use App\Http\Controllers\api\v1\PromptController;
use App\Http\Controllers\api\v1\ScoreController;
use App\Http\Controllers\api\v1\ChatBot\ChatBotController;
use App\Http\Controllers\api\v1\ChatBot\ChatBotAssessmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::post('register', [AuthController::class, 'register'])->name('admin.register');
Route::post('login', [AuthController::class, 'login'])->name('admin.login');





Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('user', [AuthController::class, 'getAuthenticatedUser']);
    Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');
});
Route::get('get-psychologists/', [PsychologistController::class, 'getPsychologists'])->name('getPsychologists');
Route::post('username-exists/', [UserController::class, 'usernameExistOrNot'])->name('check-username-exists');
Route::post('forget-password-reset/', [UserController::class, 'forgetPasswordReset'])->name('forgetPasswordReset');




Route::group(['prefix' => 'v1',  'namespace' => 'api\v1'], function () {

    Route::get('on-off-status', [UserAuthenticationController::class, 'onOffStatus']);

    Route::match(['GET' , 'POST'],'onboarding', [UserAuthenticationController::class,'onBoarding']);
    Route::match(['GET' , 'POST'],'organizer-list', [UserAuthenticationController::class,'organizerList']);
    Route::match(['GET' , 'POST'],'user-profile', [UserAuthenticationController::class,'userProfile']);
    Route::match(['GET' , 'POST'],'language-list', [UserAuthenticationController::class,'languageList']);


    //USER
    Route::match(['GET' , 'POST'],'signup', [UserAuthenticationController::class,'signup']);
    Route::match(['GET' , 'POST'],'entry-via-org', [UserAuthenticationController::class,'entryViaOrg']);
    Route::match(['GET' , 'POST'],'login', [UserAuthenticationController::class,'login']);
    Route::match(['GET' , 'POST'],'login-with-code', [UserAuthenticationController::class,'loginWithCode']);
    Route::match(['GET' , 'POST'],'forgot-password', [UserAuthenticationController::class,'forgotPassword']);
    Route::match(['GET' , 'POST'],'verify-otp', [UserAuthenticationController::class,'verifyOtp']);
    Route::match(['GET' , 'POST'],'reset-password', [UserAuthenticationController::class,'resetPassword']);
    Route::match(['GET' , 'POST'],'guardian-verification', [UserAuthenticationController::class,'gurdianVerification']);
    Route::match(['GET' , 'POST'],'verify-guardian-otp', [UserAuthenticationController::class,'verifyGuardianOtp']);




    //FAQS & TERMS
    Route::match(['GET' , 'POST'],'general-faqs', [FaqTermController::class,'generalFaqs']);
    Route::match(['GET' , 'POST'],'org-faqs', [FaqTermController::class,'orgFaqs']);
    Route::match(['GET' , 'POST'],'privacy-policy', [FaqTermController::class,'privacyPolicy']);
    Route::match(['GET' , 'POST'],'term-conditions', [FaqTermController::class,'termConditions']);


    Route::match(['GET' , 'POST'],'offer-screen-content', [FaqTermController::class,'offerScreenContent']);


    Route::match(['GET' , 'POST'],'reward-instances-list', [UserAuthenticationController::class,'rewardInstancesList']);




    //ASSIGN EMAIL PASS TO PSY
    Route::match(['GET' , 'POST'],'assign-email-pass-to-existing-psy', [PsychologistAuthenticationController::class,'assignEmailPassToExistingPsy']);


    Route::match(['GET' , 'POST'],'handle-webhook' , [PaymentController::class , 'handleWebhook']);


    Route::group(['middleware' => 'auth:api'], function () {
        Route::match(['GET' , 'POST'],'check', [UserAuthenticationController::class,'check']);

        //Authentication
        Route::match(['GET' , 'POST'],'change-password', [UserAuthenticationController::class,'changePassword']);
        Route::match(['GET' , 'POST'],'get-profile', [UserAuthenticationController::class,'getProfile']);
        Route::match(['GET' , 'POST'],'edit-profile', [UserAuthenticationController::class,'editProfile']);

        Route::match(['GET' , 'POST'],'save-email', [UserAuthenticationController::class,'saveEmail']);


        Route::match(['GET' , 'POST'],'logout', [UserAuthenticationController::class,'logout']);
        Route::match(['GET' , 'POST'],'send-verification-otp', [UserControllerApi::class,'sendVerificationOtp']);

        Route::match(['GET' , 'POST'],'delete-account', [UserAuthenticationController::class,'deleteAccount']);

        Route::match(['GET' , 'POST'],'my-referral-code', [UserAuthenticationController::class,'myReferralCode']);



        //Notification List
        Route::match(['GET' , 'POST'],'notification-list', [NotificationApiController::class,'notificationList']);
        Route::match(['GET' , 'POST'],'read-single-notification', [NotificationApiController::class,'readSingleNotification']);
        Route::match(['GET' , 'POST'],'read-all-notification', [NotificationApiController::class,'readAllNotification']);




        //Assessment
        Route::match(['GET' , 'POST'],'start-assessment', [UserAuthenticationController::class,'startAssessment']);
        Route::match(['GET' , 'POST'],'checkifany', [UserAuthenticationController::class,'checkIfAnyAssessmentCompleted']);
        Route::match(['GET' , 'POST'],'save-option', [UserAuthenticationController::class,'saveOption']);
        Route::match(['GET' , 'POST'],'complete-assessment', [UserAuthenticationController::class,'completeAssessment']);
        Route::match(['GET' , 'POST'],'view-report', [UserAuthenticationController::class,'viewReport']);
        Route::match(['GET' , 'POST'],'get-report', [UserAuthenticationController::class,'getReport']);
        Route::match(['GET' , 'POST'],'get-all-report', [UserAuthenticationController::class,'getAllReports']);
        Route::match(['GET' , 'POST'],'assessment-status', [UserControllerApi::class,'assessmentStatus']);
        Route::match(['GET' , 'POST'],'update-last-answer', [UserAuthenticationController::class,'updateLastAnswer']);
        Route::match(['GET' , 'POST'],'raise-query-app', [UserAuthenticationController::class,'raiseQueryApp']);
        Route::match(['GET' , 'POST'],'feedback', [UserAuthenticationController::class,'feedback']);
        Route::match(['GET' , 'POST'],'mood-emoji-list', [UserAuthenticationController::class,'moddEmojiList']);
        Route::match(['GET' , 'POST'],'user-mood', [UserAuthenticationController::class,'userMood']);

        Route::match(['GET' , 'POST'],'total-reward-points-user', [UserAuthenticationController::class,'totalRewardPointsUser']);


        //Payment
        Route::get('buy-plan', [PaymentController::class, 'buyPlan']);
        Route::match(['GET','POST'], 'payment' , [PaymentController::class , 'payment']);
        Route::match(['GET','POST'], 'payment-for-happitalk' , [PaymentController::class , 'paymentForHappitalk']);
        Route::match(['GET','POST'], 'payment-for-happiguide' , [PaymentController::class , 'paymentForHappiguide']);
        Route::match(['GET','POST'], 'my-subscribed-services' , [PaymentController::class , 'mySubscribedServices']);
        Route::match(['GET','POST'], 'apply-coupon' , [PaymentController::class , 'applyCoupon']);
        Route::match(['GET','POST'], 'avail-free-services' , [PaymentController::class , 'availFreeService']);
        Route::match(['GET','POST'], 'payment-for-ios' , [PaymentController::class , 'PaymentForIos']);





        //Assign psychologist
        Route::match(['GET','POST'], 'assign-psychologist' , [AssignPsychologistController::class , 'assignpsychologist']);
        Route::match(['GET','POST'], 'switch-language-while-chat' , [AssignPsychologistController::class , 'switchLanguage']);
        Route::match(['GET','POST'], 'psy-whom-user-currently-chatting' , [AssignPsychologistController::class , 'psyWhomUserCurrentlyChatting']);
        Route::match(['GET','POST'], 'all-psy-to-whom-user-chat' , [AssignPsychologistController::class , 'allPsyToWhomUserChat']);
        Route::match(['GET' , 'POST'],'send-message-by-user-to-psy', [AssignPsychologistController::class,'sendMessageByUserToPsy']);
        Route::match(['GET' , 'POST'],'clear-message-batch-of-user', [AssignPsychologistController::class,'clearMessageBatchOfUser']);



        //Happilearn
        Route::match(['GET','POST'], 'happi-learn-content' , [HappiLearnController::class , 'HappiLearnContent']);
        Route::match(['GET','POST'], 'happi-learn-content-by-id' , [HappiLearnController::class , 'HappiLearnContentById']);
        Route::match(['GET','POST'], 'like-happi-learn-post' , [HappiLearnController::class , 'likeHappiLearnPost']);
        Route::match(['GET','POST'], 'unlike-happi-learn-post' , [HappiLearnController::class , 'unLikeHappiLearnPost']);
        Route::match(['GET' , 'POST'],'search-parameters', [HappilearnController::class,'searchParameters']);



        //HappiSelf
        Route::match(['GET' , 'POST'],'course-list', [HappiselfController::class,'courseList']);
        Route::match(['GET' , 'POST'],'sub-course-list', [HappiselfController::class,'subCourseList']);
        Route::match(['GET' , 'POST'],'get-sub-course-content', [HappiselfController::class,'getSubCourseContent']);
        Route::match(['GET' , 'POST'],'start-sub-course', [HappiselfController::class,'startSubCourse']);
        Route::match(['GET' , 'POST'],'end-sub-course', [HappiselfController::class,'endSubCourse']);

        Route::match(['GET','POST'], 'like-happiself-course' , [HappiselfController::class , 'likeHappiselfCourse']);
        Route::match(['GET','POST'], 'unlike-happiself-course' , [HappiselfController::class , 'unLikeHappiselfCourse']);
        Route::match(['GET' , 'POST'],'happiself-library-list', [HappiselfController::class,'happiselfLibraryList']);
        Route::match(['GET' , 'POST'],'happiself-library-content', [HappiselfController::class,'happiselfLibraryContent']);

        Route::match(['GET' , 'POST'],'happiself-add-notes', [HappiselfController::class,'addNotes']);
        Route::match(['GET' , 'POST'],'happiself-update-notes', [HappiselfController::class,'happiselfUpdateNotes']);
        Route::match(['GET' , 'POST'] , 'happiself-get-notes-list' , [HappiselfController::class, 'happiselfGetNotesList']);
        Route::match(['GET' , 'POST'] , 'happiself-get-notes-by-id' , [HappiselfController::class, 'happiselfGetNotesByID']);
        Route::match(['GET' , 'POST'] , 'happiself-delete-notes-by-id' , [HappiselfController::class, 'happiselfDeleteNotesByID']);


        Route::match(['GET' , 'POST'] , 'save-happiself-content-answer' , [HappiselfController::class, 'saveHappiselfContentAnswer']);







        //HappiTalk User
        Route::match(['GET' , 'POST'] , 'psychologist-listing' , [HappiTalkController::class,'psychologistListing']);
        Route::match(['GET' , 'POST'] , 'get-slots-of-psy' , [HappiTalkController::class,'getSlotsOfPsy']);
        Route::match(['GET' , 'POST'] , 'psychologist-city' , [HappiTalkController::class,'psychologistCity']);
        Route::match(['GET' , 'POST'] , 'psychologist-specialization' , [HappiTalkController::class,'psychologistSpecialization']);
        Route::match(['GET' , 'POST'] , 'psychologist-expert-category' , [HappiTalkController::class,'psychologistExpertCategory']);
        Route::match(['GET' , 'POST'] , 'psychologist-language' , [HappiTalkController::class,'psychologistLanguage']);
        Route::match(['GET' , 'POST'] , 'my-booking-user' , [HappiTalkController::class,'myBookingUser']);
        Route::match(['GET' , 'POST'] , 'reschedule-booking-user' , [HappiTalkController::class,'rescheduleBookingUser']);
        Route::match(['GET' , 'POST'] , 'cancel-booking-user' , [HappiTalkController::class,'cancelBookingUser']);
        Route::match(['GET' , 'POST'] , 'list-to-book-another-session-user' , [HappiTalkController::class,'listToBookAnotherSessionUser']);
        Route::match(['GET' , 'POST'] , 'book-another-session-user' , [HappiTalkController::class,'bookAnotherSessionUser']);
        Route::match(['GET' , 'POST'] , 'emoji-and-reason-list' , [HappiTalkController::class,'emojiAndReasonList']);
        Route::match(['GET' , 'POST'] , 'submit-opinion-after-session-user' , [HappiTalkController::class,'submitOpinionAfterSessionUser']);
        Route::match(['GET' , 'POST'] , 'join-talk-room-user' , [HappiTalkController::class,'joinTalkRoomUser']);
        Route::match(['GET' , 'POST'] , 'avail-haapitalk-user' , [HappiTalkController::class,'availHappiTalkUser']);

        Route::match(['GET' , 'POST'] , 'get-penalty-clause-user' , [HappiTalkController::class,'getPenaltyClauseUser']);




        //HappiGuide
        Route::match(['GET' , 'POST'] , 'happiguide-session-user' , [HappiguideController::class,'happiguideSessionUser']);

        Route::match(['GET' , 'POST'] , 'avail-happiguide-user' , [HappiguideController::class,'availHappiguideUser']);

        Route::match(['GET' , 'POST'] , 'happiguide-reschedule-session-user' , [HappiguideController::class,'happiguideRescheduleSessionUser']);
        Route::match(['GET' , 'POST'] , 'join-guide-room-user' , [HappiguideController::class,'joinGuideRoomUser']);
        Route::match(['GET' , 'POST'] , 'submit-opinion-after-guide-session-user' , [HappiguideController::class,'submitOpinionAfterGuideSessionUser']);


        //Videochat
        Route::match(['GET' , 'POST'],'create-video-room', [VideoChatController::class,'createVideoRoom']);
        Route::match(['GET' , 'POST'],'grant-room-access', [VideoChatController::class,'grantRoomAccess']);

        Route::match(['GET' , 'POST'],'check-participant-in-room', [VideoChatController::class,'checkparticipantInRoom']);

        Route::match(['GET' , 'POST'],'disconnect-all-user-from-room', [VideoChatController::class,'disconnectAllFromRoom']);


        Route::match(['GET' , 'POST'],'make-composition-of-room', [VideoChatController::class,'makeCompositionOfRoom']);
        Route::match(['GET' , 'POST'],'download-composition', [VideoChatController::class,'downloadComposition']);


        //


        //Rating
        Route::match(['GET' , 'POST'],'emoji-list', [RatingController::class,'EmojiList']);
        Route::match(['GET' , 'POST'],'submit-rating', [RatingController::class,'submitRating']);


        //Whitelabelling
        Route::match(['GET' , 'POST'],'white-labelling-status', [WhiteLabelingController::class,'whiteLabellingStatus']);


    });


    //Psychologist
    Route::match(['GET' , 'POST'],'forgot-pw-p', [PsychologistAuthenticationController::class,'forgotPassword']);
    Route::match(['GET' , 'POST'],'psy-verify-otp', [PsychologistAuthenticationController::class,'psychologistVerifyOtp']);
    Route::match(['GET' , 'POST'],'psy-set-password', [PsychologistAuthenticationController::class,'psySetPassword']);


    Route::match(['GET' , 'POST'],'psychologist-login', [PsychologistAuthenticationController::class,'psychologistLogin']);

    Route::group(['middleware' => 'psychologist'], function () {
        Route::match(['GET' , 'POST'],'psychologist-check', [PsychologistAuthenticationController::class,'psychologistCheck']);

        //Authentication
        Route::match(['GET' , 'POST'],'psychologist-logout', [PsychologistAuthenticationController::class,'psychologistLogout']);
        Route::match(['GET' , 'POST'],'change-pw-p', [PsychologistAuthenticationController::class,'changePassword']);
        Route::match(['GET' , 'POST'],'get-psychologist-profile', [PsychologistAuthenticationController::class,'getProfile']);
        Route::match(['GET' , 'POST'],'edit-psychologist-profile', [PsychologistAuthenticationController::class,'editProfile']);


        //Chat
        Route::match(['GET' , 'POST'],'psy-chat-listing', [AssignPsychologistController::class,'psyChatListing']);
        Route::match(['GET' , 'POST'],'get-group-id-by-psychologist', [AssignPsychologistController::class,'getGroupIdByPsychologist']);
        Route::match(['GET' , 'POST'],'send-message-by-psy-to-user', [AssignPsychologistController::class,'sendMessageByPsyToUser']);
        Route::match(['GET' , 'POST'],'clear-message-batch-of-psy', [AssignPsychologistController::class,'clearMessageBatchOfPsy']);

        Route::match(['GET' , 'POST'],'submit-users-buddy-report-psy', [AssignPsychologistController::class,'usersBuddyReportPsy']);

        Route::match(['GET' , 'POST'],'get-users-buddy-report-psy', [AssignPsychologistController::class,'getUsersBuddyReportPsy']);






        //UserReport
        Route::match(['GET' , 'POST'],'get-user-report-by-psy', [PsychologistControllerApi::class,'getUserReportByPsy']);


        //Happitalk Psychologist
        Route::match(['GET' , 'POST'] , 'my-booking-psychologist' , [HappiTalkController::class,'myBookingPsychologist']);
        Route::match(['GET' , 'POST'] , 'my-pending-request-psychologist' , [HappiTalkController::class,'myPendingRequestPsychologist']);
        Route::match(['GET' , 'POST'] , 'my-all-slots-psychologist' , [HappiTalkController::class,'myAllSlotsPsychologist']);
        Route::match(['GET' , 'POST'] , 'get-slots-of-perticular-date-psy' , [HappiTalkController::class,'getSlotsOfPerticularDatePsy']);
        Route::match(['GET' , 'POST'] , 'session-mark-as-complete-psy' , [HappiTalkController::class,'sessionMarkAsCompletePsy']);
        Route::match(['GET' , 'POST'] , 'check-room-participant-psy' , [HappiTalkController::class,'checkRoomParticipantPsy']);
        Route::match(['GET' , 'POST'] , 'get-session-of-perticular-date-psy' , [HappiTalkController::class,'getSessionOfPerticularDatePsy']);
        Route::match(['GET' , 'POST'] , 'accept-session-request' , [HappiTalkController::class,'acceptSessionRequest']);
        Route::match(['GET' , 'POST'] , 'reject-session-request' , [HappiTalkController::class,'rejectSessionRequest']);
        Route::match(['GET' , 'POST'] , 'get-session-between-two-dates-psy' , [HappiTalkController::class,'getSessionBetweenTwoDatesPsy']);
        Route::match(['GET' , 'POST'] , 'reschedule-booking-psy' , [HappiTalkController::class,'rescheduleBookingPsy']);
        Route::match(['GET' , 'POST'] , 'delivered-sessions-psy' , [HappiTalkController::class,'deliveredSessionsPsy']);
        Route::match(['GET' , 'POST'] , 'delete-single-slot-psy' , [HappiTalkController::class,'deleteSingleSlotPsy']);
        Route::match(['GET' , 'POST'] , 'delete-slot-between-two-dates-psy' , [HappiTalkController::class,'deleteSlotBetweenTwoDatesPsy']);
        Route::match(['GET' , 'POST'] , 'add-slots-psy' , [HappiTalkController::class,'addSlotsPsy']);
        Route::match(['GET' , 'POST'] , 'submit-opinion-after-session-psy' , [HappiTalkController::class,'submitOpinionAfterSessionPsy']);
        Route::match(['GET' , 'POST'] , 'submit-session-note-psy' , [HappiTalkController::class,'submitSessionNotePsy']);
        Route::match(['GET' , 'POST'] , 'users-previous-sessions-psy' , [HappiTalkController::class,'userPreviousSessionsPsy']);
        Route::match(['GET' , 'POST'] , 'join-talk-room-psy' , [HappiTalkController::class,'joinTalkRoomPsy']);
        Route::match(['GET' , 'POST'] , 'get-session-note-psy' , [HappiTalkController::class,'getSessionNotePsy']);
        Route::match(['GET' , 'POST'] , 'dashboard-psy' , [HappiTalkController::class,'dashboardPsy']);

        Route::match(['GET' , 'POST'] , 'get-session-recording-psy' , [HappiTalkController::class,'getSessionRecordingPsy']);


        Route::match(['GET' , 'POST'] , 'users-all-talk_notes-by-psy' , [HappiTalkController::class,'usersAllTalkSessionNotesByPsy']);


        //HappiGuide Psychologist
        Route::match(['GET' , 'POST'] , 'happiguide-session-psy' , [HappiguideController::class,'happiguideSessionPsy']);
        Route::match(['GET' , 'POST'] , 'join-guide-room-psy' , [HappiguideController::class,'joinGuideRoomPsy']);
        Route::match(['GET' , 'POST'] , 'happiguide-session-mark-as-completed-psy' , [HappiguideController::class,'happiguideSessionMarkAsCompletedPsy']);
        Route::match(['GET' , 'POST'] , 'submit-guide-session-note-psy' , [HappiguideController::class,'submitGuideSessionNotePsy']);
            Route::match(['GET' , 'POST'] , 'check-guide-room-participant-psy' , [HappiguideController::class,'checkGuideRoomParticipantPsy']);
        Route::match(['GET' , 'POST'] , 'get-happiguide-session-recording-psy' , [HappiguideController::class,'getHappiguideSessionRecordingPsy']);
        Route::match(['GET' , 'POST'] , 'submit-opinion-after-guide-session-psy' , [HappiguideController::class,'submitOpinionAfterGuideSessionPsy']);
    });


    //PAYMENT
    Route::match(['GET','POST'], 'payment-link/{order_id}/{user_id}/{plan_id}/{coupen_id}' , [PaymentController::class , 'paymentLink']);
    Route::match(['GET','POST'], 'success-payment-page/{order_id}/{user_id}/{plan_id}/{coupen_id}' , [PaymentController::class , 'successPaymentPage']);


    //PAYMENT FOR HAPPITALK
    Route::match(['GET','POST'], 'payment-link-for-happitalk/{order_id}/{user_id}/{plan_id}/{psychologist_id}/{date}/{time}/{sessions}/{user_recording_permission}/{coupen_id}' , [PaymentController::class , 'paymentLinkForHappitalk']);
    Route::match(['GET','POST'], 'success-payment-page-for-happitalk/{order_id}/{user_id}/{plan_id}/{psychologist_id}/{date}/{time}/{sessions}/{user_recording_permission}/{coupen_id}' , [PaymentController::class , 'successPaymentPageForHappitalk']);


    //PAYMENT FOR HAPPIGUIDE
    Route::match(['GET','POST'], 'payment-link-for-happiguide/{order_id}/{user_id}/{plan_id}/{date}/{time}/{coupen_id}' , [PaymentController::class , 'paymentLinkForHappiguide']);
    Route::match(['GET','POST'], 'success-payment-page-for-happiguide/{order_id}/{user_id}/{plan_id}/{date}/{time}/{coupen_id}' , [PaymentController::class , 'successPaymentPageForHappiguide']);

    // PROMPTS
    Route::get('prompt-list', [PromptController::class, 'promptList']);

    // SCORES
    Route::post('score', [ScoreController::class, 'saveScore']);

    // Chat Bot
    Route::get('chat-bot/discussion-topics', [ChatBotController::class, 'discussionTopics']);
    Route::get('chat-bot/suicidal-thoughts', [ChatBotController::class, 'suicidalThoughts']);
    Route::get('chat-bot/assessment-concerns', [ChatBotController::class, 'assessmentConcerns']);
    Route::get('chat-bot/assessment-questions', [ChatBotController::class, 'assessmentQuestions']);
    Route::get('chat-bot/recommendation-categories', [ChatBotController::class, 'recommendationCategories']);
    Route::get('chat-bot/recommendations', [ChatBotController::class, 'recommendations']);
    Route::get('chat-bot/square-breathing', [ChatBotController::class, 'squareBreathing']);

    // //Route::group(['middleware' => 'auth:api'], function () {

    // // Assessment
    Route::get('chat-bot/categories', [ChatBotAssessmentController::class, 'categories']);
    Route::get('chat-bot/questions', [ChatBotAssessmentController::class, 'questions']);
    Route::get('chat-bot/options', [ChatBotAssessmentController::class, 'questions']);
    Route::get('chat-bot/report-characteristics', [ChatBotAssessmentController::class, 'reportCharacteristics']);
    Route::get('chat-bot/assessments', [ChatBotAssessmentController::class, 'assessments']);
    Route::post('chat-bot/add-assessment', [ChatBotAssessmentController::class, 'addAssessment']);

    //});
});









