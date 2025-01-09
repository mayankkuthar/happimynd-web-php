<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

use App\Exports\AssessmentDataExport;
use Illuminate\Http\Request;

use App\Services\AssessmentService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DataExportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ExpertLevelController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PsychologistController;
use App\Http\Controllers\QuestionController;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\SpecializationController;
use App\Models\User;
use App\Models\UserProfile;
use Twilio\Rest\Messaging\V1\CampaignContext;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportCharacteristicController;
use App\Http\Controllers\EducationServiceAuthorController;
use App\Http\Controllers\HappilearnController;
use App\Http\Controllers\HappitalkController;
use App\Http\Controllers\HappiguideController;
use App\Http\Controllers\Notification;
use App\Http\Controllers\HappiselfController;
use App\Http\Controllers\HappibuddyController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\ChatBot\DiscussionTopicsController;
use App\Http\Controllers\ChatBot\RecommendationCategoriesController;
use App\Http\Controllers\ChatBot\RecommendationsController;
use App\Http\Controllers\ChatBot\SuicidalThoughtsController;
use App\Http\Controllers\ChatBot\ChatBotCategoryController;
use App\Http\Controllers\ChatBot\ChatBotQuestionController;
use App\Http\Controllers\ChatBot\ChatBotReportCharacteristicController;
use App\Http\Controllers\ChatBot\ChatBotAssessmentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// Route::get('/download-composition-web/{room_id}/{composition_id}', [UserController::class, 'downloadCompositionWeb'])->name('downloadCompositionWeb');






Route::get('/', [UserController::class, 'landingPageView'])->name('landingPage');

Route::get('/get-service-button-data', [UserController::class, 'getServiceButtonData'])->name('getServiceButtonData');

Route::get('/login', function (Request $request) {
    return view('Frontend/login/login');
})->middleware('custom.guest:user')->name('user.loginView');

Route::get('/signup', function () {
    return view('Frontend/signup/signup');
})->name('user.signupView')->middleware('custom.guest:user');



//web api payment
// Route::get('payment-link/{order_id}/{user_id}', [UserController::class, 'paymentLink']);
// Route::any('payment-successfull', [UserController::class, 'paymentSuccessfull']);
//end we api payment


// Route::any('calculate-score-app/{assessment_id}', [AssessmentController::class, 'calculateAssessmentScoreApp'])->name('calculateAssessmentScoreApp');



Route::post('signup', [UserController::class, 'signup'])->name('user.signup.post');
Route::post('login', [UserController::class, 'signin'])->name('user.login.post');
Route::any('verifyToken', [UserController::class, 'verifyToken'])->name('verifyCode');

Route::get('psychologist', [PsychologistController::class, 'getPsychologistView'])->name('user.psychologist');

Route::middleware(['jwt.verify:user'])->name('user.')->group(function () {
    Route::get('logout', [AuthController::class, 'userLogout'])->name('signout');
    Route::get('dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('edit-profile', [UserController::class, 'editProfileView'])->name('editProfileView');
    Route::post('edit-profile', [UserController::class, 'editProfile'])->name('editProfile');
    Route::get('change-password', [UserController::class, 'changePasswordView'])->name('changePasswordView');
    Route::get('download-report', [UserController::class, 'downloadReport'])->name('downloadReport');
    Route::get('thrivecode', [UserController::class, 'thriveCode'])->name('thrivecode');
    Route::get('get-thrivecode', [UserController::class, 'getThriveCode'])->name('getThriveCode');
    Route::get('screening', [UserController::class, 'assessment'])->name('assessment');
    Route::get('/explore-services', [UserController::class, 'exploreServices'])->name('exploreServices');
    Route::post('change-password', [UserController::class, 'changePassword'])->name('changePassword');
    Route::post('raise-query', [UserController::class, 'postRaiseQuery'])->name('raiseQuery');
    Route::any('update-email', [UserController::class, 'updateEmail'])->name('updateEmail');
    Route::any('update-mobile', [UserController::class, 'updateMobile'])->name('updateMobile');
    Route::get('check-verify', [UserController::class, 'checkVerify'])->name('checkVerify');
    Route::get('report-preview', [AssessmentController::class, 'reportPreview'])->name('reportPreview');
    Route::any('update-calltime', [AssessmentController::class, 'updateCalltime'])->name('updateCalltime');

    Route::get('mark-as-read', [NotificationController::class, 'markAsRead'])->name('markNotificationsAsRead');
    //TODO: Request methods
    Route::any('get-questions', [AssessmentController::class, 'getQuestions'])->name('getQuestions');
    Route::post('start-assessment', [AssessmentController::class, 'startAssessment'])->name('startAssessment');
    Route::post('save-option', [AssessmentController::class, 'saveAssessmentOption'])->name('saveOption');

    Route::get('buy-bundles/', [PaymentController::class, 'buyBundle'])->name('payment.buyBundle');

    Route::any('check-for-thrive-code', [UserController::class, 'checkForThriveCode'])->name('checkForThriveCode');
    Route::get('/get-available-dates', [UserController::class, 'getAvailableDates'])->name('availableDates.get');
    Route::get('/subscribedservices', [PaymentController::class, 'subscribedServices'])->name('subscribedServices');

    Route::get('/verificationData', [UserController::class, 'verificationData'])->name('verificationData.get');
    Route::get('/booked-dates', [UserController::class, 'getBookedDates'])->name('bookedDates.get');

    // Route::get('psychologist', [PsychologistController::class, 'getPsychologistView'])->name('psychologist');
    Route::get('psychologist-available-dates', [PsychologistController::class, 'getPsychologistAvailableDates'])->name('psychologistAvailableDates.get');
    Route::post('psychologist-save-appointment', [PsychologistController::class, 'updatePsychologistAppointment'])->name('updatePsychologistAppointment.post');

    //coupon
    Route::post('/verify-coupon', [CouponController::class, 'verifyCoupon'])->name('verify-coupon');
    Route::post('/coupon-exists', [AdminController::class, 'couponExists'])->name('coupon-exists');
});

//campaign routes
Route::get('/campaign-payment', [CampaignController::class, 'getPlansPage'])->name('campaign.plansPage.get');
Route::get('campaign/payment/orderBundle', [CampaignController::class, 'orderBundle'])->name('campaign.payment.orderBundle');
Route::any('campaign/payment/responseBundle', [CampaignController::class, 'responseBundle'])->name('campaign.payment.responseBundle');

Route::any('generate-otp-{type}', [UserController::class, 'generateOTP'])->name('generateOTP');

Route::post('generate-otp-email-one-page', [UserController::class, 'generateOTPEmail'])->name('generateOTPEmail');


Route::any('generate-guardian-otp-{type}', [UserController::class, 'generateGuardianOTP'])->name('generateGuardianOTP');
Route::post('verify-{type}-otp', [UserController::class, 'verifyOtpByCode'])->name('verifyOtpByCode');
Route::post('generate-guardian-otp-{type}', [UserController::class, 'generateSendGuardianOTP'])->name('generateSendGuardianOTP');
Route::get('verify-guardian-otp', [UserController::class, 'verifyGuardianOtpByCode'])->name('verifyGuardianOtpByCode');

Route::get('verify-{type}/{user_id}/{otp}', [UserController::class, 'verifyOtpByLink'])->name('verifyOtpByLink');

Route::get('payment/orderBundle', [PaymentController::class, 'orderBundle'])->name('payment.orderBundle');
Route::any('payment/responseBundle', [PaymentController::class, 'responseBundle'])->name('payment.responseBundle');
Route::post('payment/response-other-services', [PaymentController::class, 'responseOtherServices'])->name('payment.responseOtherServices');

Route::get('payment/book-psychologist', [PaymentController::class, 'bookPsychologist'])->name('payment.bookPsychologist');
Route::any('payment/psychologist-payment-response', [PaymentController::class, 'psychologistPaymentResponse'])->name('payment.psychologistPaymentResponse');

Route::get('/sponsersignup', [UserController::class, 'sponserSignupView'])->name('sponserSignupView')->middleware('custom.guest:user');
Route::any('calculate-score', [AssessmentController::class, 'calculateAssessmentScore'])->name('calculateAssessmentScore');

Route::get('/individualsignup', [UserController::class, 'individualSignupView'])->name('user.individualSignupView')->middleware('custom.guest:user');

Route::get('/privacy', [UserController::class, 'getPrivacy'])->name('privacy');
Route::get('/terms', [UserController::class, 'getTerms'])->name('getTerms');

Route::get('/search', [PsychologistController::class, 'filterPsychologist'])->name('filterPsychologist.get');
Route::post('/submit-contact', [ContactController::class, 'store'])->name('submit.contact');

Route::get('/services', [UserController::class, 'Services'])->name('services');
Route::get('/educationalservices', [UserController::class, 'educationServices'])->name('educationalservices');
Route::get('/otherservices', [UserController::class, 'otherServices'])->name('otherservices');
Route::get('/other-services/{id}', [UserController::class, 'showOtherServices'])->name('otherservices.show');
Route::post('/other-services-mail', [UserController::class, 'saveOtherServicesMailList'])->name('OtherServicesMailList.post');
Route::post('/other-services-payment', [PaymentController::class, 'otherServicePayment'])->name('otherServicePayment');

Route::get('/faq', [UserController::class, 'getFaq'])->name('faq');

Route::get('/404', function () {
    return view('Frontend/errorpages/404');
})->name('404');

Route::get('/401', function () {
    return view('Frontend/errorpages/401');
})->name('401');

Route::get('/500', function () {
    return view('Frontend/errorpages/500');
})->name('500');

Route::get('/maintenance', function () {
    return view('Frontend/includes/maintenance');
})->name('maintenance');


Route::get('/report', function () {
    return view('Frontend/report/report');
})->name('report');

Route::get('/aboutus', function () {
    return view('Frontend/aboutus/aboutus');
})->name('aboutus');

Route::get('/happispaceform', [LandingPageController::class, 'happispaceform'])->name('happispaceform');
Route::get('/ourteam', [LandingPageController::class, 'ourTeam'])->name('ourteam');
Route::get('/blog', [LandingPageController::class, 'freeBlog'])->name('blog');
Route::get('/blog/{slug}', [LandingPageController::class, 'readFreeBlog'])->name('readFreeBlog');
Route::get('/organisation', [LandingPageController::class, 'organization'])->name('organisation');
Route::get('/allblog/{slug}', [LandingPageController::class, 'allBlog'])->name('allblogs');



/** Download happimynd data */
Route::get('download-happimynd-token', [DownloadController::class, 'downloadHappimyndToken'])->name('downloadHappimyndToken');
Route::get('download-thrive-code', [DownloadController::class, 'downloadThriveCode'])->name('downloadThriveCode');
Route::get('download-assessment-detail', [DownloadController::class, 'downloadAssessmentDetail'])->name('downloadAssessmentDetail');

/* Admin Routes start  */
Route::get('/admin/login', function () {
    return view('Backend/login');
})->name('admin.getLogin')->middleware('custom.guest:admin');
Route::post('admin/login', [AuthController::class, 'adminLogin'])->name('postLogin');
Route::any('/admin/logout', [AuthController::class, 'adminLogout'])->name('admin.logout')->middleware('jwt.verify:admin');
Route::get('/admin/contacts', [ContactController::class, 'index'])->name('admin.contacts');


Route::prefix('admin')->name('admin.')->middleware(['jwt.verify:admin'])->group(function () {


    //HappiBUDDY
    Route::match(['GET' , 'POST'],'all-psy-list-for-buddy', [HappibuddyController::class, 'allPsychologistListForBuddy'])->name('allPsychologistListForBuddy');
    Route::match(['GET' , 'POST'],'buddy-psy-list', [HappibuddyController::class, 'buddyPsyList'])->name('buddyPsyList');
    Route::match(['GET' , 'POST'],'map-psy-with-buddy/{psy_id}', [HappibuddyController::class, 'mapPsyWithBuddy'])->name('mapPsyWithBuddy');
    Route::match(['GET' , 'POST'],'un-map-psy-with-buddy/{psy_id}', [HappibuddyController::class, 'unMapPsyWithBuddy'])->name('unMapPsyWithBuddy');

    Route::get('/user-list-to-whom-psychologist-assign', [AdminController::class, 'userListToWhomPsyAssigned'])->name('userListToWhomPsyAssigned.get');

    Route::match(['GET' , 'POST'], '/user-list-to-whom-psychologist-assign-by-username', [AdminController::class, 'userListToWhomPsyAssignedByUsername'])->name('userListToWhomPsyAssignedByUsername.get');


    Route::match(['GET' , 'POST'], '/psy-list-based-on-user/{user_id}', [AdminController::class, 'psyListBasedOnUser'])->name('psyListBasedOnUser.get');
    Route::get('/change-buddy-psy/{user_id}', [AdminController::class, 'changeBuddyPsy']);

    Route::get('/change-buddy-psy/{user_id}', [AdminController::class, 'changeBuddyPsy']);

    Route::get('/monthly-report-of-buddy-user/{user_id}', [AdminController::class, 'monthlyReportOfBuddyUser']);


    Route::get('/action-switch-buddy-psy/{user_id}/{psy_id}', [AdminController::class, 'actionSwitchBuddyPsy']);

        Route::any('download-buddy-session-list-xl', [HappibuddyController::class, 'downloadBuddyListxl'])->name('downloadBuddyListxl');



    //End


    //HappiTalk
        Route::match(['GET' , 'POST'],'talk-tds', [HappitalkController::class,'talkTds'])->name('talkTds');
        Route::match(['GET' , 'POST'],'penalty-clause', [HappitalkController::class,'penaltyClause'])->name('penaltyClause');
        Route::match(['GET' , 'POST'],'all-psy-list-for-talk', [HappitalkController::class, 'allPsychologistListForTalk'])->name('allPsychologistListForTalk');
        Route::match(['GET' , 'POST'],'happitalk-psychologist-list', [HappitalkController::class, 'happitalkPsychologistList'])->name('happitalkPsychologistList');
        Route::match(['GET' , 'POST'],'map-psy-with-talk/{psy_id}', [HappitalkController::class, 'mapPsyWithTalk'])->name('mapPsyWithTalk');
        Route::match(['GET' , 'POST'],'un-map-psy-with-talk/{psy_id}', [HappitalkController::class, 'unMapPsyWithTalk'])->name('unMapPsyWithTalk');
        Route::match(['GET' , 'POST'],'all-org-list-for-happitalk', [HappitalkController::class, 'allOrgListForHappitalk'])->name('allOrgListForHappitalk');
        Route::match(['GET' , 'POST'],'assign-psy-to-org/{org_id}', [HappitalkController::class, 'assignPsyToOrg'])->name('assignPsyToOrg');
        Route::match(['GET' , 'POST'],'un-map-psy-to-org/{id}', [HappitalkController::class, 'unMapPsyToOrg'])->name('unMapPsyToOrg');
        Route::match(['GET' , 'POST'],'happitalk-booking-list', [HappitalkController::class, 'happitalkBookingList'])->name('happitalkBookingList');

        Route::match(['GET' , 'POST'],'happitalk-booking-by-username', [HappitalkController::class, 'happitalkBookingListByUsername'])->name('happitalkBookingListByUsername');


        Route::match(['GET' , 'POST'],'session-list-based-on-booking-id/{booking_id}', [HappitalkController::class, 'sessionListBasedOnBookingId'])->name('sessionListBasedOnBookingId');

        Route::match(['GET' , 'POST'],'talk-notes-detail/{id}', [HappitalkController::class, 'talkNotesDetail'])->name('talkNotesDetail');

        Route::any('download-talk-session-list-xl', [HappitalkController::class, 'downloadtalkListxl'])->name('downloadtalkListxl');

        Route::match(['GET' , 'POST'] , 'users-credit', [HappitalkController::class, 'usersCredit'])->name('usersCredit');

        Route::match(['GET' , 'POST'] , 'edit-users-credit/{booking_id}', [HappitalkController::class, 'editUsersCredit'])->name('editUsersCredit');




    //EndHappiTalk


    //HappiGuide
        Route::match(['GET' , 'POST'],'all-psy-list-for-guide', [HappiguideController::class, 'allPsychologistListForGuide'])->name('allPsychologistListForGuide');
        Route::match(['GET' , 'POST'],'happiguide-psychologist-list', [HappiguideController::class, 'happiguidePsychologistList'])->name('happiguidePsychologistList');
        Route::match(['GET' , 'POST'],'happiguide-session-list', [HappiguideController::class, 'happiguideSessionList'])->name('happiguideSessionList');

        Route::match(['GET' , 'POST'],'happiguide-session-by-username', [HappiguideController::class, 'happiguideSessionListByUsername'])->name('happiguideSessionListByUsername');

        Route::match(['GET' , 'POST'],'map-psy-with-guide/{psy_id}', [HappiguideController::class, 'mapPsyWithGuide'])->name('mapPsyWithGuide');
        Route::match(['GET' , 'POST'],'un-map-psy-with-guide/{psy_id}', [HappiguideController::class, 'unMapPsyWithGuide'])->name('unMapPsyWithGuide');
        Route::match(['GET' , 'POST'],'change-guide-session-psy-list/{guide_session_id}', [HappiguideController::class, 'changeGuideSessionPsyList'])->name('changeGuideSessionPsyList');
        Route::match(['GET' , 'POST'],'action-switch-psy/{guide_session_id}/{psy_id}', [HappiguideController::class, 'actionSwitchPsy'])->name('actionSwitchPsy');

        Route::match(['GET' , 'POST'],'happiguide-get-notes-date', [HappiguideController::class, 'happiguideGetNotesDate'])->name('happiguideGetNotesDate');
        Route::match(['GET' , 'POST'],'happiguide-notes-based-on-dates', [HappiguideController::class, 'happiguideNotesBasedOnDates'])->name('happiguideNotesBasedOnDates');

        Route::match(['GET' , 'POST'],'guide-opinion/{id}', [HappiguideController::class, 'guideOpinionDetail'])->name('guideOpinionDetail');



        Route::any('download-guide-session-list-xl', [HappiguideController::class, 'downloadGuideListxl'])->name('downloadGuideListxl');


    //EndHappiGuide


    //HappiLearn
    Route::match(['GET' , 'POST'],'import-happilearn-content', [HappilearnController::class, 'importHappiLearnContent'])->name('import-happilearn-content');

    Route::match(['GET' , 'POST'],'delete-happilearn-content/{id}', [HappilearnController::class, 'deleteHappiLearnContent'])->name('delete-happilearn-content');


    Route::match(['GET' , 'POST'],'happilearn-content-list', [HappilearnController::class, 'happiLearnContentList'])->name('happilearn-content-list');
    Route::match(['GET' , 'POST'],'upload-learn-media', [HappilearnController::class, 'uploadLearnMedia'])->name('uploadLearnMedia');



    //EndhappiLearn


    //Start HappiSelf
        Route::match(['GET' , 'POST'],'happiself-courses-list', [HappiselfController::class, 'happiselfCoursesList'])->name('happiselfCoursesList');
        Route::match(['GET' , 'POST'],'add-happiself-course', [HappiselfController::class, 'addHappiselfCourses'])->name('addHappiselfCourses');
        Route::match(['GET' , 'POST'],'add-sub-course/{id}', [HappiselfController::class, 'addSubCourse'])->name('addSubCourse');
        Route::match(['GET' , 'POST'],'edit-happiself-course/{id}', [HappiselfController::class, 'editHappiselfCourse'])->name('editHappiselfCourse');

        Route::match(['GET' , 'POST'],'edit-sub-course/{id}', [HappiselfController::class, 'editSubCourse'])->name('editSubCourse');
        Route::match(['GET' , 'POST'],'view-sub-course/{id}', [HappiselfController::class, 'viewSubCourse'])->name('viewSubCourse');
        Route::match(['GET' , 'POST'],'delete-sub-course/{library_content_id}', [HappiselfController::class, 'deleteSubCourse'])->name('deleteSubCourse');

        Route::match(['GET' , 'POST'],'import-happiself', [HappiselfController::class, 'importHappiself'])->name('importHappiself');
        Route::match(['GET' , 'POST'],'add-happiself-library', [HappiselfController::class, 'addHappiselflibrary'])->name('addHappiselflibrary');
        Route::match(['GET' , 'POST'],'happiself-library-list', [HappiselfController::class, 'happiselfLibraryList'])->name('happiselfLibraryList');
        Route::match(['GET' , 'POST'],'import-happiself-library-content', [HappiselfController::class, 'importHappiselfLibraryContent'])->name('importHappiselfLibraryContent');
        Route::match(['GET' , 'POST'],'content-list/{sub_course_id}', [HappiselfController::class, 'contentList'])->name('contentList');
        Route::match(['GET' , 'POST'],'delete-happiself-content/{content_id}', [HappiselfController::class, 'deleteSelfContent'])->name('deleteSelfContent');
        Route::match(['GET' , 'POST'],'delete-course/{course_id}', [HappiselfController::class, 'deleteCourse'])->name('deleteCourse');

        Route::match(['GET' , 'POST'],'edit-library/{library_id}', [HappiselfController::class, 'editLibrary'])->name('editLibrary');
        Route::match(['GET' , 'POST'],'delete-library/{library_id}', [HappiselfController::class, 'deleteLibrary'])->name('deleteLibrary');
        Route::match(['GET' , 'POST'],'view-library-content/{library_id}', [HappiselfController::class, 'viewLibraryContent'])->name('viewLibraryContent');
        Route::match(['GET' , 'POST'],'delete-library-content/{library_content_id}', [HappiselfController::class, 'deleteLibraryContent'])->name('deleteLibraryContent');


        Route::match(['GET' , 'POST'] , 'date-for-export-self-data', [HappiselfController::class, 'dateForExportSelfdata'])->name('dateForExportSelfdata');
        Route::any('get-self-data', [HappiselfController::class, 'getSelfdata'])->name('getSelfdata');



        Route::match(['GET' , 'POST'],'upload-self-media', [HappiselfController::class, 'uploadSelfMedia'])->name('uploadSelfMedia');



    //End HAppiSelf


    //Import Questions
    Route::match(['GET' , 'POST'],'import-questions', [QuestionController::class, 'importQuestions'])->name('importQuestions');
    Route::match(['GET' , 'POST'],'batch-category-ids', [QuestionController::class, 'batchCategoryIds'])->name('batchCategoryIds');
    Route::match(['GET' , 'POST'],'view-category-ids/{id}', [QuestionController::class, 'viewCategoryIds'])->name('viewCategoryIds');
    //End import question





    //Push Notification
    Route::match(['GET' , 'POST'],'push-notification', [Notification::class, 'pushNotification'])->name('pushNotification');
    Route::match(['GET' , 'POST'],'delete-scheduled-notification/{id}', [Notification::class, 'deleteScheduledNotification'])->name('deleteScheduledNotification');

    Route::match(['GET' , 'POST'],'notification-messages', [Notification::class, 'notificationMessages'])->name('notificationMessages');
    Route::match(['GET' , 'POST'],'update-notification-message', [Notification::class, 'updateNotificationMessage'])->name('updateNotificationMessage');
    //End Push Notification


    //Organization Logo
    Route::match(['GET' , 'POST'],'organization-details-with-logo', [AdminController::class, 'organizationDetailsWithLogo'])->name('organizationDetailsWithLogo');
    Route::match(['GET' , 'POST'],'edit-org-logo/{id}', [AdminController::class, 'editOrgLogo'])->name('editOrgLogo');



    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::any('logout', [AuthController::class, 'adminLogout'])->name('logout');
    Route::post('addAdminUser', [AdminController::class, 'addAdminUser'])->name('addAdminUser');
    Route::any('deleteUser/{id}', [AdminController::class, 'deleteUser'])->name('deleteUser');
    Route::get('addUser', [AdminController::class, 'getAddAdminView'])->name('addAdminView');
    Route::get('/list/customers', [AdminController::class, 'getCustomerList'])->name('customerListView');
    Route::any('download-user-list-xl', [AdminController::class, 'downloadUserListXL'])->name('downloadUserListXL');

    Route::get('/view-user-mood/{user_id}', [AdminController::class, 'viewUserMood'])->name('viewUserMood');
    Route::get('/view-user-rewards/{user_id}', [AdminController::class, 'viewUserRewards'])->name('viewUserRewards');


    Route::get('/list/feedback', [AdminController::class, 'getFeedbackList'])->name('getFeedbackList');
    Route::any('download-feedback-list-xl', [AdminController::class, 'downloadFeedbackListxl'])->name('downloadFeedbackListxl');




    Route::get('/list/reward-points-instance', [AdminController::class, 'getRewardPointsInstanceList'])->name('getRewardPointsInstanceList');
    Route::match(['GET' , 'POST'] , '/list/edit-reward-points/{id}', [AdminController::class, 'getEditRewardPointsList'])->name('getEditRewardPointsList');


    Route::get('/list/admins', [AdminController::class, 'getAdminList'])->name('adminListView');
    Route::get('/edit/{id}', [AdminController::class, 'editAdminView'])->name('editAdminView');
    Route::post('/update', [AdminController::class, 'updateAdmin'])->name('editAdmin');
    Route::get('/delete//{id}', [AdminController::class, 'deleteAdmin'])->name('deleteAdmin');
    Route::get('/generateToken', [AdminController::class, 'generateTokenView'])->name('generateTokenView');
    Route::post('/generateToken', [AdminController::class, 'generateTokens'])->name('generateToken.post');
    Route::get('/tokenList', [AdminController::class, 'tokenList'])->name('tokenListView');
    Route::post('/tokenList', [AdminController::class, 'tokenList'])->name('tokenList.post');
    Route::get('/thriveCodeList', [AdminController::class, 'thriveCodeList'])->name('thriveCodeListView');
    Route::post('/thriveCodeList', [AdminController::class, 'thriveCodeList'])->name('thriveCodeList.post');
    Route::get('Organizations', [AdminController::class, 'OrganizationView'])->name('OrganizationView');
    Route::get('organization-detail', [AdminController::class, 'OrganizationDetail'])->name('OrganizationDetail');
    Route::post('organization-detail', [AdminController::class, 'OrganizationDetail'])->name('OrganizationDetail.post');
    Route::post('addOrganization', [AdminController::class, 'addOrganization'])->name('addOrganization.post');
    Route::get('deleteOrganization/{organization_id}', [AdminController::class, 'deleteOrganization'])->name('deleteOrganization.post');
    Route::get('invalidatetokens/{organization_id}/{type}', [AdminController::class, 'expireTokens'])->name('expireTokens');
    Route::get('reactivatetokens/{organization_id}/{type}', [AdminController::class, 'reactivateTokens'])->name('activateTokens');
    Route::get('expireToken/{id}/{type}', [AdminController::class, 'expireToken'])->name('expireToken');
    Route::get('reactivatetoken/{id}/{type}', [AdminController::class, 'reactivateToken'])->name('reactivateToken');
    Route::get('/list/raisedQuery', [AdminController::class, 'raisedQuery'])->name('raisedQueryView');
    Route::post('/raisedQuery-change-status', [AdminController::class, 'RaiseQueryStatusChange'])->name('changeRaisedQueryStatus.post');
    Route::get('notifyUser/', [AdminController::class, 'notifyUserView'])->name('notifyUserView');
    Route::get('send-notification', [AdminController::class, 'sendNotificationToUserView'])->name('getSendNotificationView');
    Route::post('send-notification', [NotificationController::class, 'sendNotification'])->name('sendNotificationToUser');

    Route::match(['GET' , 'POST'], 'create-bundle', [AdminController::class, 'createBundle'])->name('createBundle');

    Route::get('bundle-detail/', [AdminController::class, 'bundleDetail'])->name('bundleDetail');
    Route::post('bundle-update/', [AdminController::class, 'updateBundlePrice']);
    Route::post('priority-update/', [AdminController::class, 'updateOurTeamPriority']);
    Route::post('priority-update-our-clients/',  [AdminController::class, 'updateOurClientPriority'])->name('priorityClientUpdate');
    Route::get('manage-bundle/', [AdminController::class, 'manageBundle'])->name('manageBundle');
    Route::get('payment-detail/', [PaymentController::class, 'paymentDetail'])->name('paymentDetail');
    Route::get('payment-detail-ios/', [PaymentController::class, 'paymentDetailIos'])->name('paymentDetailIos');

    Route::get('assesmentList/', [AdminController::class, 'assesmentList'])->name('assesmentList');
    Route::match(['GET' , 'POST'],'assesmentListByUsername/', [AdminController::class, 'assesmentListByUsername'])->name('assesmentListByUsername');


    Route::get('deleteAssessment/{assessment_id}', [AdminController::class, 'deleteAssessment'])->name('deleteAssessment');
    Route::get('assesment-approve/{assessment_id}/{status}', [AdminController::class, 'assesmentApprove'])->name('assesmentApprove');

    Route::get('dashboard-picture', [AdminController::class, 'dashboardPictureUploadView'])->name('staticData.uploadDashboardCoverPic.get');
    Route::post('dashboard-picture', [AdminController::class, 'uploadDashboardCoverPic'])->name('staticData.uploadDashboardCoverPic.post');
    Route::prefix('staticdata')->name('staticData.')->group(function () {
        Route::get('termsandservices', [AdminController::class, 'termsServices'])->name('termServices');
        Route::post('termsandservices', [AdminController::class, 'saveStaticContent'])->name('post');
        Route::post('deleteTerms', [AdminController::class, 'deleteStaticContent'])->name('deleteContent');
        Route::get('landing-page-video', [AdminController::class, 'landingPageVideoUploadView'])->name('landingPageVideoUploadView');
        Route::get('landing-page-bitrix', [AdminController::class, 'landingPageBitrixFormView'])->name('landingPageBitrixFormView');
        Route::post('landing-page-bitrix', [AdminController::class, 'saveLandingPageBitrixFormView'])->name('landingPageBitrixForm');
        Route::post('happy-space-bitrix', [AdminController::class, 'saveHappySpaceBitrixForm'])->name('saveHappySpaceBitrixForm');
        Route::get('dashboard-app-download-view', [AdminController::class, 'dashboardAppDownloadView'])->name('dashboardAppDownloadView');
        Route::post('dashboard-app-download', [AdminController::class, 'dashboardAppDownload'])->name('dashboardAppDownload');
        Route::post('dashboard-ios-app-download', [AdminController::class, 'saveDashboardIosLink'])->name('saveDashboardIosLink');
        Route::get('ourteam', [AdminController::class, 'ourteam'])->name('ourteam');
        Route::post('ourteam', [AdminController::class, 'saveOurTeam'])->name('OurTeamFormSave');
        Route::get('ourteam/{id}', [AdminController::class, 'editOurteam'])->name('ourteamFormEdit');
        Route::get('ourteam/delete/{id}', [AdminController::class, 'deleteOurteam'])->name('ourteamFormDelete');
        Route::get('organization', [AdminController::class, 'organization'])->name('organization');
        Route::post('organization', [AdminController::class, 'saveOrganization'])->name('organizationFormSave');
        Route::post('organizationLogo', [AdminController::class, 'saveOrganizationLogo'])->name('organizationLogoSave');
        Route::get('quotes', [AdminController::class, 'quotes'])->name('quotes');
        Route::post('quotes', [AdminController::class, 'saveQuotes'])->name('quotesFormSave');
        Route::post('quotes-button', [AdminController::class, 'saveEditableQuoteButton'])->name('saveEditableQuoteButton');
        Route::get('blog', [AdminController::class, 'blog'])->name('blogFormView');
        Route::get('blog/{slug}', [AdminController::class, 'editBlog'])->name('blogFormEdit');
        Route::get('blog/delete/{id}', [AdminController::class, 'deleteBlog'])->name('blogFormDelete');
        Route::post('blog', [AdminController::class, 'saveBlog'])->name('blogFormSave');

        Route::put('blog', [AdminController::class, 'saveBlog'])->name('blogFormUpdate');
        Route::get('landing-page-faq', [AdminController::class, 'landingFaqView'])->name('landingFaqView');
        Route::post('landing-page-faq', [AdminController::class, 'landingFaqPost'])->name('landingFaqPost');
        Route::get('organization-faq', [AdminController::class, 'faqOrganizationView'])->name('faqOrganizationView');
        Route::post('organization-fag', [AdminController::class, 'faqOrganizationPost'])->name('faqOrganizationPost');
        Route::get('terms', [AdminController::class, 'terms'])->name('terms');
        Route::post('terms', [AdminController::class, 'saveTerms'])->name('saveTerms');
        Route::get('explore-services', [AdminController::class, 'exploreServices'])->name('exploreServices');
        Route::post('explore-services', [AdminController::class, 'saveExploreServices'])->name('saveExploreServices');
        Route::get('edit-landing-buttons', [AdminController::class, 'editLandingButtons'])->name('editLandingButtons');
        Route::post('explore-services-buttons', [AdminController::class, 'saveEditableServicesButton'])->name('saveEditableServicesButton');
        Route::post('package-name', [AdminController::class, 'savePackageName'])->name('savePackageName');
        Route::post('landing-page-buttons', [AdminController::class, 'saveEditableLandingButton'])->name('saveEditableLandingButton');
        Route::post('organistation-page-buttons', [AdminController::class, 'saveEditableOrganisationButton'])->name('saveEditableOrganisationButton');
        Route::get('orientation-mail', [AdminController::class, 'editOrientationMail'])->name('editOrientationEmail.get');
        Route::post('orientation-mail', [AdminController::class, 'saveOrientationMail'])->name('saveOrientationEmail.post');
        Route::get('orientation-mail-preview', [AdminController::class, 'previewOrientationMail'])->name('orientationEmailPreview.get');
        Route::get('our-clients', [AdminController::class, 'ourClientsGet'])->name('ourClientsGet');
        Route::post('our-clients', [AdminController::class, 'saveOurClients'])->name('ourClientFormSave');
        Route::get('our-clients/edit/{id}', [AdminController::class, 'ourClientsEdit'])->name('ourClientEdit');
        Route::post('our-clients/update/{id}', [AdminController::class, 'saveOurClients'])->name('ourClientUpdate');
        Route::get('our-clients/delete/{id}', [AdminController::class, 'ourClientsDelete'])->name('ourClientDelete');
        Route::get('carousel-show/{carousel}', [AdminController::class, 'showCarouselContent'])->name('showCarouselContent');
        Route::post('save-carousel-content/{carousel}', [AdminController::class, 'saveCarousel'])->name('saveCarousel');
        Route::get('edit-carousel-content/{carousel}/{id}', [AdminController::class, 'editCarouselContent'])->name('editCarouselContent');
        Route::get('landing-static-section/{section}', [AdminController::class, 'landingPageSection'])->name('landingPageSection');
        Route::post('save-content', [AdminController::class, 'saveContent'])->name('saveContent');
        Route::post('update-content-priority', [AdminController::class, 'priorityCarouselUpdate'])->name('priorityCarouselUpdate');
        Route::match(['GET' , 'POST'] , 'offer-screen', [AdminController::class, 'offerScreen'])->name('offerScreen');
        // Route::match(['GET' , 'POST'] , 'add-ponts-to-offer-screen', [AdminController::class, 'addPointsToOfferScreen'])->name('addPointsToOfferScreen');
        // Route::match(['GET' , 'POST'] , 'delete-ponts-to-offer-screen/{id}', [AdminController::class, 'deletePointsToOfferScreen'])->name('deletePointsToOfferScreen');



    });
    Route::get('/add-all-available-dates', [AdminController::class, 'addAllAvailableDates'])->name('addAllAvailableDates.get');
    Route::post('landing-page-video', [AdminController::class, 'uploadLandingPageVideo'])->name('staticData.uploadLandingPageVideo.post');
    Route::get('/add-unavailable-dates', [AdminController::class, 'addUnavailableDates'])->name('addUnavailableDates.get');
    Route::post('/add-unavailable-dates', [AdminController::class, 'postAddUnavailableDates'])->name('addUnavailableDates.post');
    Route::get('buy-bundles/', [PaymentController::class, 'buyBundle'])->name('payment.buyBundle');
    Route::post('/add-all-available-dates', [AdminController::class, 'postAllAddAvailableDates'])->name('addAllAvailableDates.post');
    Route::get('drop-dates/{date}', [AdminController::class, 'deleteAvailableDates'])->name('deleteAvailableDates');
    Route::get('/all-booking-dates', [AdminController::class, 'showAllBookings'])->name('allBookedDate.get');
    Route::get('/booking-dates', [AdminController::class, 'showAllBookingsPost'])->name('allBookedDate.post');
    Route::get('/users-plans', [AdminController::class, 'usersPlans'])->name('usersPlans.get');
    Route::any('download-user-plan-xl', [AdminController::class, 'downloadUserPlanXL'])->name('downloadUserPlanXL');

    Route::get('/users-additional-plans', [AdminController::class, 'usersAdditionalPlans'])->name('usersPlans.additional.get');

    Route::get('/campaigns', [CampaignController::class, 'getAllCampaigns'])->name('campaigns.get');
    Route::get('/check-campaign-name', [CampaignController::class, 'checkCampaignName'])->name('checkName.get');
    Route::post('/add-campaign', [CampaignController::class, 'addCampaign'])->name('addCampaign.post');
    Route::post('/update-campaign', [CampaignController::class, 'updateCampaign'])->name('editCampaign.post');
    Route::get('/delete-campaign', [CampaignController::class, 'deleteCampaign'])->name('deleteCampaign.get');
    Route::get('/change-status-campaign', [CampaignController::class, 'changeStatus'])->name('changeStatusCampaign.get');

    Route::get('/fix-dates', [AdminController::class, 'updateDateFormat'])->name('updateDateFormat.get');
    //services
    Route::get('/other-services', [AdminController::class, 'otherServices'])->name('otherServices.get');
    Route::post('/other-services', [AdminController::class, 'saveOtherService'])->name('otherServices.post');
    Route::get('/other-services/{slug}', [AdminController::class, 'editOtherServices'])->name('otherservices.edit');
    Route::get('/remove-services/{id}', [AdminController::class, 'deleteOtherServices'])->name('otherservices.delete');
    Route::put('/other-services', [AdminController::class, 'saveOtherService'])->name('otherservices.update');
    Route::get('/services-purchased', [AdminController::class, 'purchasedServices'])->name('purchasedServices.get');

    Route::get('/educational-services', [AdminController::class, 'educationalServices'])->name('educationalServices.get');
    Route::post('/educational-services', [AdminController::class, 'saveEducationalService'])->name('EducationServices.post');
    Route::get('/educational-services/{slug}', [AdminController::class, 'editEducationalServices'])->name('educationalservices.edit');
    Route::get('/remove-educational-services/{id}', [AdminController::class, 'deleteEducationalServices'])->name('educationalservices.delete');

    Route::post('/educational-save-services', [AdminController::class, 'saveEducationalService'])->name('educationalservices.update');

    Route::get('/addUserProfile', [UserProfileController::class, 'addUserProfileView'])->name('addUserProfile.get');
    Route::post('/addUserProfile', [UserProfileController::class, 'addUserProfile'])->name('addUserProfile.post');
    Route::post('updateUserProfile', [UserProfileController::class, 'updateUserProfile'])->name('updateUserProfile.post');
    Route::get('delete-user-profile', [UserProfileController::class, 'deleteUserProfile'])->name('deleteUserProfile.get');
    Route::get('/changeUserProfileStatus', [UserProfileController::class, 'changeUserProfileStatus'])->name('changeUserProfileStatus.get');
    Route::get('/education-service-author', [EducationServiceAuthorController::class, 'index'])->name('educationService.index');
    Route::post('/education-service-author', [EducationServiceAuthorController::class, 'store'])->name('educationService.store');
    //assessment configuration links
    Route::get('/batch', [BatchController::class, 'getAllBatches'])->name('getAllBatches.get');
    Route::post('/batch', [BatchController::class, 'addbatch'])->name('addBatch.post');
    Route::get('/get-batch', [BatchController::class, 'getBatchDetail'])->name('batch.get');
    Route::get('get-all-categories', [BatchController::class, 'getAllBatchCategories'])->name('getAllBatchCategories.get');
    Route::post('/editBatch', [BatchController::class, 'editbatch'])->name('editBatch.post');
    Route::post('/deleteBatch', [BatchController::class, 'deletebatch'])->name('deleteBatch.post');
    Route::get('/clone-batch', [BatchController::class, 'cloneBatch'])->name('cloneBatch.get');
    Route::get('/copy-categories', [BatchController::class, 'copyCategoryIntoBatch'])->name('copyCategories.get');

    Route::get('/category', [CategoryController::class, 'getAllCategories'])->name('getAllCategories.get');
    Route::post('/addCategory', [CategoryController::class, 'addCategory'])->name('addCategory.post');
    Route::post('/editCategory', [CategoryController::class, 'updateCategory'])->name('editCategory.post');
    Route::post('/deleteCategory', [CategoryController::class, 'deleteCategory'])->name('deleteCategory.post');

    Route::get('/allocateCategoryToBatch', [BatchController::class, 'allocateCategoryToBatch'])->name('allocateCategoryToBatch');
    Route::get('/updateBatchCategory', [BatchController::class, 'updateBatchCategory'])->name('updateBatchCategory.post');
    Route::get('/getBatchCategories', [BatchController::class, 'getBatchCategories'])->name('getBatchCategories.get');

    Route::get('/modifyQuestions', [QuestionController::class, 'modifyQuestions'])->name('modifyQuestions.get');
    Route::get('/getBatchcategoryQuestions', [QuestionController::class, 'getBatchCategoryQuestions'])->name('getBatchCategoryQuestions.get');
    Route::post('/updateQuestion', [QuestionController::class, 'updateQuestion'])->name('updateQuestion.post');
    Route::post('/deleteQuestion', [QuestionController::class, 'deleteQuestion'])->name('deleteQuestion.post');

    Route::get('/scoreCalculation', [QuestionController::class, 'scoreCalculation'])->name('scoreCalculation.get');

    Route::get('/ratingImages', [ReportCharacteristicController::class, 'getAllRatingImages'])->name('ratingImages.get');
    Route::post('/scoreRatingPictureUpload', [ReportCharacteristicController::class, 'scoreRatingPictureUpload'])->name('scoreRatingPictureUpload.post');
    Route::delete('/scoreRatingPictureUpload', [ReportCharacteristicController::class, 'deleteRatingImage'])->name('scoreRatingPictureUpload.post');
    Route::get('/delete-rating-picture/{id}', [ReportCharacteristicController::class, 'deleteRatingImage'])->name('deleteRatingImage.get');

    Route::get('/score-calculation', [ReportCharacteristicController::class, 'scoreCalculation'])->name('scoreCalculation.get');
    Route::get('/get-batchcategory-reportcharacteristics', [ReportCharacteristicController::class, 'getBatchCategoryReportCharacteristics'])->name('getBatchCategoryReportCharacteristics.get');
    Route::post('/save-reportcharacteristic', [ReportCharacteristicController::class, 'saveReportCharacteristic'])->name('saveReportCharacteristic.post');
    Route::get('/delete-reportcharacteristic', [ReportCharacteristicController::class, 'deleteReportCharacteristic'])->name('deleteReportCharacteristic.get');

    Route::post('/save-calculation-step', [BatchController::class, 'saveCalulcationStep'])->name('saveCalculationStep.post');

    Route::get('/report-order', [ReportCharacteristicController::class, 'reportOrder'])->name('reportOrder.get');
    Route::get('/get-unique-category', [BatchController::class, 'getBatchUniqueCategory'])->name('categoryReportOrder.get');
    Route::post('/save-report-order', [ReportCharacteristicController::class, 'saveReportOrder'])->name('reportOrder.post');

    Route::get('/regenerate-report/{assessment_id}', [AdminController::class, 'regenerateReprot'])->name('regenerateReport');

    Route::any('download-score-xl-reports', [DataExportController::class, 'downloadScoreXL'])->name('downloadScoreXL');
    
    Route::any('download-user-assessment-xl-reports', [DataExportController::class, 'downloadXL'])->name('downloadXL');
    Route::prefix('psychologist')->name('psychologist.')->group(function () {
        Route::get('all', [PsychologistController::class, 'index'])->name('all.get');

        Route::get('edit/{id}', [PsychologistController::class, 'edit'])->name('edit.get');
        Route::post('edit', [PsychologistController::class, 'update'])->name('edit.post');

        Route::get('add', [PsychologistController::class, 'create'])->name('add.get');
        Route::post('add', [PsychologistController::class, 'store'])->name('add.post');
        Route::get('add-dates', [PsychologistController::class, 'addDates'])->name('addDates.get');

        Route::get('delete/{id}', [PsychologistController::class, 'destroy'])->name('delete.get');
        Route::get('all-appointments', [PsychologistController::class, 'allPsychologistAppointment'])->name('allAppointments.get');
    });

    Route::get('languages', [LanguageController::class, 'index'])->name('languages.get');
    Route::post('languages', [LanguageController::class, 'store'])->name('language.post');
    Route::put('language/update/{id}', [LanguageController::class, 'update'])->name('language.put');
    Route::delete('language/delete/{id}', [LanguageController::class, 'destroy'])->name('language.delete');

    Route::get('cities', [CityController::class, 'index'])->name('city.get');
    Route::post('cities', [CityController::class, 'store'])->name('city.post');
    Route::put('city/update/{id}', [CityController::class, 'update'])->name('city.put');
    Route::delete('city/delete/{id}', [CityController::class, 'destroy'])->name('city.delete');

    Route::get('specializations', [SpecializationController::class, 'index'])->name('specializations.get');
    Route::post('specializations', [SpecializationController::class, 'store'])->name('specialization.post');
    Route::put('specialization/update/{id}', [SpecializationController::class, 'update'])->name('specialization.put');
    Route::delete('specialization/delete/{id}', [SpecializationController::class, 'destroy'])->name('specialization.delete');

    Route::get('expert-levels', [ExpertLevelController::class, 'index'])->name('expertLevels.get');
    Route::get('add-expert-level', [ExpertLevelController::class, 'create'])->name('addExpertLevel.get');
    Route::post('save-expert-level', [ExpertLevelController::class, 'store'])->name('addExpertLevel.post');
    Route::get('edit-expert-level/{id}', [ExpertLevelController::class, 'edit'])->name('editExpertLevel.get');
    Route::post('expert-level/update', [ExpertLevelController::class, 'update'])->name('updateExpertLevel.post');
    Route::delete('expert-level/delete/{id}', [ExpertLevelController::class, 'destroy'])->name('expertLevel.delete');

    //admin coupon route start

    Route::prefix('coupon')->name('coupon.')->group(function () {
        Route::get('delete', [CouponController::class, 'deleteCoupons'])->name('delete');
        Route::get('', [CouponController::class, 'showCoupons'])->name('show');
        Route::get('add', [CouponController::class, 'showCouponsForm'])->name('add');
        Route::post('', [CouponController::class, 'storeCoupons'])->name('post-store');
        Route::get('{id}/edit', [CouponController::class, 'editCoupons'])->name('edit');
        Route::post('{id}', [CouponController::class, 'updateCoupons'])->name('update');
        Route::get('user', [CouponController::class, 'viewCouponUser'])->name('coupon-user');
    });

    //admin coupon route end

    //admin HappiVOICE prompt route start

    Route::prefix('prompt')->name('prompt.')->group(function () {
        Route::get('delete', [PromptController::class, 'deletePrompts'])->name('delete');
        Route::get('', [PromptController::class, 'showPrompts'])->name('show');
        Route::get('add', [PromptController::class, 'showPromptsForm'])->name('add');
        Route::post(
            '',
            [PromptController::class, 'storePrompts']
        )->name('post-store');
        Route::get('{id}/edit', [PromptController::class, 'editPrompts'])->name('edit');
        Route::post('{id}', [PromptController::class, 'updatePrompts'])->name('update');
    });

    //admin HappiVOICE prompt route end

    //admin HappiVOICE score route start

    Route::prefix('score')->name('score.')->group(function () {
        Route::get('', [ScoreController::class, 'getScoreList'])->name('all');
    });

    //admin HappiVOICE score route end

    //admin ChatBot route start

    Route::prefix('chat-bot')->as('chat-bot.')->group(function () {
        Route::resource('discussion-topics', DiscussionTopicsController::class);
        Route::match(['GET', 'POST'], 'suicidal-thoughts', [SuicidalThoughtsController::class, 'update'])->name('suicidal-thoughts');

        // Recommendations
        Route::resource('recommendation-categories', RecommendationCategoriesController::class)->except('show');
        Route::resource('recommendations', RecommendationsController::class)->except('show');

        // ChatBot Assessments Starts

        // Categories
        Route::match(['GET', 'POST'], 'categories/import', [ChatBotCategoryController::class, 'import'])->name('categories.import');
        Route::resource('categories', ChatBotCategoryController::class)->except(['show']);

        // Questions
        Route::match(['GET', 'POST'], 'questions/import', [ChatBotQuestionController::class, 'import'])->name('questions.import');
        Route::resource('questions', ChatBotQuestionController::class)->except('show');

        // Report characteristics
        Route::resource('report-characteristics', ChatBotReportCharacteristicController::class)->except('show');

        // Assessments
        Route::post('assessments/download', [ChatBotAssessmentController::class, 'download'])->name('assessments.download');
        Route::resource('assessments', ChatBotAssessmentController::class)->only('index');

        // ChatBot Assessments Ends

        // Video: Square Breathing
        Route::get('/square-breathing', function () {
            asset('videos/square-breathing.mp4');
        });
    });

    //admin ChatBot route end

    //testing route start
    Route::any('/bot-test', [AssessmentController::class, 'BotAssessment']);
    Route::any('assessment/{assessment_id}', function ($assessmentId) {
        $as = new AssessmentService;
        $as->forAssessment($assessmentId);
        $as->calculateScore();
        dd($as->scoreArray);
    });
    //testing routes end
});
/* Admin Routes end  */

/**
 * Log
 */
Route::get('/clear-log', function () {
    $disc = Storage::disk('logs');

    collect($disc->allFiles())->each(function ($file) use ($disc) {
        if (str_ends_with($file, '.log')) {
            $disc->delete($file);
        }
    });

    return response('Logs cleared successfully!');
});

/**
 * Debug
 */
Route::get('/temp/email/show/{email}', function ($email) {
    $users = DB::table('users')->where('email', $email)->get();
    return response()->json($users);
});

Route::get('/temp/email/delete/{email}', function ($email) {
    DB::table('users')->where('email', $email)->delete();
});

Route::get('/temp/mobile/show/{mobile}', function ($mobile) {
    $users = DB::table('users')->where('mobile', $mobile)->get();
    return response()->json($users);
});

Route::get('/temp/mobile/delete/{mobile}', function ($mobile) {
    $user = DB::table('users')->where('mobile', $mobile)->first();

    if ($user) {
        $bundle_statuses = [];
        $bundle_statuses = DB::table('bundle_statuses')->where('user_id', $user->id)->get();

        foreach ($bundle_statuses as $bundle_status) {
            DB::table('token_plans')->where('bundle_status_id', $bundle_status->id)->delete();
        }

        DB::table('bundle_statuses')->where('user_id', $user->id)->delete();
        DB::table('users')->where('id', $user->id)->delete();
    }
});

Route::get('/temp/mobile/update/{old}/{new}', function ($old, $new) {
    DB::table('users')->where('mobile', $old)->update(['mobile' => $new]);
});
