<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\API\AppController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\StateDistrictBlockController;
use App\Http\Controllers\API\AssessmentController;
use App\Http\Controllers\API\AssessmentQuestionController;
use App\Http\Controllers\API\UserAssessmentResultController;
use App\Http\Controllers\API\MaterialController;
use App\Http\Controllers\API\CgcInterventionController;
use App\Http\Controllers\API\ScreeningController;
use App\Http\Controllers\API\DiagnosesAlgorithmsController;
use App\Http\Controllers\API\TreatmentAlgorithmsController;
use App\Http\Controllers\API\GuidanceOnAdverseDrugReactionsController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\CaseDefinitionsController;
use App\Http\Controllers\API\LatentTbInfectionsController;
use App\Http\Controllers\API\EnquiryController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\SubscriberActivitiesController;
use App\Http\Controllers\API\CgcInterventionsAlgorithmsController;
use App\Http\Controllers\API\DifferentialCareAlgorithmsController;
use App\Http\Controllers\API\DynamicAlgorithmsController;
use App\Http\Controllers\API\AppConfigController;
use App\Http\Controllers\API\TourController;
use App\Http\Controllers\API\TrainingController;
use App\Http\Controllers\API\TranslateController;
use App\Http\Controllers\API\TvDashboardController;
use App\Http\Controllers\API\StaticBlogController;
use App\Http\Controllers\API\StaticContentController;
use App\Http\Controllers\API\StaticReleaseController;
use App\Http\Controllers\API\LeaderBoardController;
use App\Http\Controllers\API\UserFeedbackController;
use App\Http\Controllers\API\FlashNewsController;
use App\Http\Controllers\API\MasterSearchController;
use App\Http\Controllers\API\CertificateController;
use App\Http\Controllers\API\SurveyController;
use App\Http\Middleware\ApiAuth;
use App\Http\Controllers\API\AutomaticNotificationController;
use App\Http\Controllers\API\UserAppVersionController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('translate', [TranslateController::class, 'translate']);
Route::get('translate-training-tag', [TranslateController::class, 'translate_training_tag']);
Route::get('translate-chat-question', [TranslateController::class, 'translate_chat_question']);
Route::get('translate-case-def-algo', [TranslateController::class, 'translate_case_definition']);
Route::get('translate-cgc-algo', [TranslateController::class, 'translate_cgc_algo']);
Route::get('translate-diagnosis-algo', [TranslateController::class, 'translate_diagnosis_algo']);
Route::get('translate-differential-care-algo', [TranslateController::class, 'translate_differential_care_algo']);
Route::get('translate-guidance-on-adr-algo', [TranslateController::class, 'translate_guidance_on_adr_algo']);
Route::get('translate-latent-tb-algo', [TranslateController::class, 'translate_latent_tb_algo']);
Route::get('translate-treatment-algo', [TranslateController::class, 'translate_treatment_algo']);
Route::get('translate-dynamic-algo', [TranslateController::class, 'translate_dynamic_algo']);
Route::get('translate-app-config', [TranslateController::class, 'translate_app_config']);
Route::get('translate-screening-tool', [TranslateController::class, 'translate_screening_tool']);
/* start api Register User ,login,password-reser and forot-password*/


Route::get('get-app-config', [AppController::class, 'getConfiguration']);
Route::post('store-user', [RegisterController::class, 'store']);
Route::post('store-user-v2', [RegisterController::class, 'storeV2']);
Route::post('login', [RegisterController::class, 'login']);
Route::post('forgotPassword', [RegisterController::class, 'forgotPassword']);

/* start api registration state_id,district_id,block_id,cadre_id,health_facility */
Route::get('get-all-state', [StateDistrictBlockController::class, 'getAllStates']);
Route::get('get-all-country', [StateDistrictBlockController::class, 'getAllCountry']);
Route::get('get-district-by-state/{state_id}', [StateDistrictBlockController::class, 'getAllDistrict']);
Route::get('get-block-by-district/{district_id}', [StateDistrictBlockController::class, 'getAllBlock']);
Route::get('get-all-cadre/{type}', [StateDistrictBlockController::class, 'getAllcadre']);
Route::get('get-all-cadre-type', [StateDistrictBlockController::class, 'getAllCadreType']);
Route::get('get-health-by-block/{block_id}', [StateDistrictBlockController::class, 'getAllHealth']);

Route::get('get-all-symptoms', [ScreeningController::class, 'getAllSymptoms']);
Route::post('verified-forogot-password-otp', [RegisterController::class, 'verifyForgotPasswordOtp']);


Route::post('check-health-status', [AppConfigController::class, 'getCheckHealthStatus']);

// Route::get('get-all-pdfs',[MaterialController::class,'getAllPdfs']);
// Route::get('get-all-videos',[MaterialController::class,'getAllVideos']);
// Route::get('get-all-documents',[MaterialController::class,'getAllDocument']);
// Route::get('get-all-images',[MaterialController::class,'getAllImages']);
// Route::get('get-all-ppts',[MaterialController::class,'getAllPpts']);

Route::middleware([ApiAuth::class])->group(function () {
    /* APIs for User Paswword reset and get User */
    Route::post('store-user-device-token', [RegisterController::class, 'storeUserDeviceToken']);
    Route::post('password-reset', [RegisterController::class, 'resetPassword']);
    Route::get('get-user', [RegisterController::class, 'getUser']);
    Route::get('get-user-v2', [RegisterController::class, 'getUserV2']);
    Route::get('get-user-v3', [RegisterController::class, 'getUserV3']);
    Route::post('update-user-details', [RegisterController::class, 'updateUserDetails']);
    Route::post('update-user-details-v2', [RegisterController::class, 'updateUserDetailsV2']);
    Route::post('remove-notification-token', [RegisterController::class, 'removeNotificationToken']);

    /* APIs for Assessment Details */
    Route::get('get-all-assessment', [AssessmentController::class, 'getAllAssessment']);
    Route::get('get-all-past-assessment', [AssessmentController::class, 'getPastAssessment']);
    Route::get('get-all-future-assessment', [AssessmentController::class, 'getFutureAssessments']);
    Route::get('get-assessment-performace', [AssessmentController::class, 'getAssessmentPerformance']);
    Route::post('store-assessment-enrollnment', [AssessmentController::class, 'storeAssessmentEnrollnment']);
    Route::get('get-assessment-with-assessmentquestions/{assessmentId}', [AssessmentQuestionController::class, 'getAllAssessmentQuestions']);
    Route::post('store-user-assessment-result', [UserAssessmentResultController::class, 'store']);
    Route::post('get-subscriber-assessment-details', [UserAssessmentResultController::class, 'getSubscriberAssessmentDetails']);
    Route::post('store-complete-assessment', [UserAssessmentResultController::class, 'completeAssessment']);
    Route::get('get-user-result/{assessmnent_id}', [UserAssessmentResultController::class, 'getUserResult']);

    /* APIs for User Activity */
    Route::post('store-user-activity', [SubscriberActivitiesController::class, 'storeUserActivity']);

    /* APIs for CGC Intervention */
    Route::get('get-all-chapters', [CgcInterventionController::class, 'getAllchapters']);
    Route::get('get-chapter-by-id/{chapterId}', [CgcInterventionController::class, 'getChaptersById']);

    /* APIs for User screening */
    Route::post('store-user-screening', [ScreeningController::class, 'storeScreening']);

    /* APIs for Resource Material and Filter */
    Route::get('get-material/{type}', [MaterialController::class, 'getAllMaterial']);
    Route::get('get-root-folders', [MaterialController::class, 'getAllRootFolders']);
    Route::get('get-files-by-parent/{parentId}', [MaterialController::class, 'getFilesByParentId']);
    Route::get('get-health-facilities', [StateDistrictBlockController::class, 'getFilterData']);

    /* APIs for User Enquiry  */
    Route::post('store-user-enquiry', [EnquiryController::class, 'storeEnquiry']);

    /* APIs for Otp generate and verify*/
    Route::get('generate-otp', [NotificationController::class, 'sendRegistrationOtp']);
    Route::post('verified-otp', [NotificationController::class, 'verifyRegisterOtp']);

    /* APIs for Patient Management Tool Screen */
    Route::get('get-diagnoses-algorithms-master-nodes', [DiagnosesAlgorithmsController::class, 'getMasterNodes']);
    Route::get('get-diagnoses-algorithms-master-nodes-v2', [DiagnosesAlgorithmsController::class, 'getMasterNodesV2']);
    Route::get('get-diagnoses-algorithms-dependent-nodes/{masterNodeId}', [DiagnosesAlgorithmsController::class, 'getDependentNodes']);


    Route::get('get-treatment-algorithms-master-nodes', [TreatmentAlgorithmsController::class, 'getMasterNodes']);
    Route::get('get-treatment-algorithms-master-nodes-v2', [TreatmentAlgorithmsController::class, 'getMasterNodesV2']);
    Route::get('get-treatment-algorithms-dependent-nodes/{masterNodeId}', [TreatmentAlgorithmsController::class, 'getDependentNodes']);

    Route::get('get-guidance-on-adverse-drug-reactions-master-nodes', [GuidanceOnAdverseDrugReactionsController::class, 'getMasterNodes']);
    Route::get('get-guidance-on-adverse-drug-reactions-master-nodes-v2', [GuidanceOnAdverseDrugReactionsController::class, 'getMasterNodesV2']);
    Route::get('get-guidance-on-adverse-drug-reactions-dependent-nodes/{masterNodeId}', [GuidanceOnAdverseDrugReactionsController::class, 'getDependentNodes']);

    Route::get('get-latent-tb-infection-master-nodes', [LatentTbInfectionsController::class, 'getMasterNodes']);
    Route::get('get-latent-tb-infection-master-nodes-v2', [LatentTbInfectionsController::class, 'getMasterNodesV2']);
    Route::get('get-latent-tb-infection-dependent-nodes/{masterNodeId}', [LatentTbInfectionsController::class, 'getDependentNodes']);
    Route::get('get-latent-tb-infection-all-nodes', [LatentTbInfectionsController::class, 'getAllNodes']);

    Route::get('get-cgc-interventions-algorithms-master-nodes', [CgcInterventionsAlgorithmsController::class, 'getMasterNodes']);
    Route::get('get-cgc-interventions-algorithms-master-nodes-v2', [CgcInterventionsAlgorithmsController::class, 'getMasterNodesV2']);
    Route::get('get-cgc-interventions-algorithms-dependent-nodes/{masterNodeId}', [CgcInterventionsAlgorithmsController::class, 'getDependentNodes']);

    Route::get('get-differential-care-algorithms-master-nodes', [DifferentialCareAlgorithmsController::class, 'getMasterNodes']);
    Route::get('get-differential-care-algorithms-master-nodes-v2', [DifferentialCareAlgorithmsController::class, 'getMasterNodesV2']);
    Route::get('get-differential-care-algorithms-dependent-nodes/{masterNodeId}', [DifferentialCareAlgorithmsController::class, 'getDependentNodes']);

    /* APIs for Case difinition */
    Route::get('get-case-definitions-master-nodes', [CaseDefinitionsController::class, 'getMasterNodes']);
    Route::get('get-case-definitions-master-nodes-v2', [CaseDefinitionsController::class, 'getMasterNodesV2']);
    Route::get('get-case-definitions-dependent-nodes/{masterNodeId}', [CaseDefinitionsController::class, 'getDependentNodes']);
    // Route::get('get-case-definitions-dependent-nodes-v2/{masterNodeId}', [CaseDefinitionsController::class, 'getDependentNodesV2']);

    /* Routes for Patient Management Tool Screen */
    Route::get('get-dynamic-algorithms-master-nodes/{key}', [DynamicAlgorithmsController::class, 'getMasterNodes']);
    Route::get('get-dynamic-algorithms-master-nodes-v2/{key}', [DynamicAlgorithmsController::class, 'getMasterNodesV2']);
    Route::get('get-dynamic-algorithms-dependent-nodes/{key}/{masterNodeId}', [DynamicAlgorithmsController::class, 'getDependentNodes']);
    Route::get('get-dynamic-algo-group-by-section', [DynamicAlgorithmsController::class, 'getDynamicAlgoMasterGroupBySection']);

    /* APIs for Chat Module */
    Route::get('get-keywords', [ChatController::class, 'getTopKeywords']);
    Route::get('get-questions-by-keyword/{keyword}', [ChatController::class, 'getQuestionsByKeyword']);
    Route::get('get-questions-by-keyword-v2/{keyword}', [ChatController::class, 'getQuestionsByKeywordV2']);
    Route::get('get-questions-by-keyword-v3/{keyword}', [ChatController::class, 'getQuestionsByKeywordV3']);
    Route::get('search-by-keyword/{keyword}', [ChatController::class, 'serachQuestionsByKeyword']);
    Route::get('search-by-keyword-v2', [ChatController::class, 'serachQuestionsByKeywordV2']);
    Route::get('search-by-keyword-v2/{keyword}', [ChatController::class, 'serachQuestionsByKeywordV2']);
    Route::post('get-text-to-speech', [ChatController::class, 'getTextToSpeech']);
    Route::post('submit-question-hit', [ChatController::class, 'storeQuestionHit']);
    Route::post('submit-feedback', [ChatController::class, 'submitFeedback']);

    /* API for Differntial care module store patient details */
    Route::post('store-patient-details', [DifferentialCareAlgorithmsController::class, 'storePatientDetails']);

    /* APIs for get tag with master details of all field */
    Route::get('get-tag-with-master-data', [TrainingController::class, 'getTagWithMasterData']);

    /* APIs for new Dashboard */

    Route::get('get-dashboard-data', [DashboardController::class, 'getDashboardDataWithFilters']);

    /* Leader Board APIs Start */
    Route::get('get-leaderboard-details', [LeaderBoardController::class, 'leaderBoardInformation']);
    Route::get('get-leaderboard-task-list', [LeaderBoardController::class, 'leaderBoardTasks']);
    Route::get('get-leaderboard-achivements', [LeaderBoardController::class, 'leaderBoardAchivements']);
    Route::post('store-sub-module-usage', [LeaderBoardController::class, 'storeSubModule']);
    /* Leader Board APIs End */

    /* User Feedback APIs Start */
    Route::get('get-feedback-details', [UserFeedbackController::class, 'getFeedbackDetails']);
    Route::post('store-feedback-details', [UserFeedbackController::class, 'storeFeedback']);
    /* User Feedback APIs End */

    /* Master Search APIs Start -----------------------------------------------------------------------------------------*/
    Route::get('get-master-search', [MasterSearchController::class, 'getMasterSearchResult']);
    Route::get('get-module-master-search', [MasterSearchController::class, 'getModuleSearch']);
    Route::get('get-sub-module-master-search', [MasterSearchController::class, 'getSubModuleSearch']);
    Route::get('get-resource-material-master-search', [MasterSearchController::class, 'getResourceMaterialSearch']);
    Route::get('get-chat-question-master-search', [MasterSearchController::class, 'getChatQuestionSearch']);
    /* Master Search APIs End -----------------------------------------------------------------------------------------*/

    /* Certificate Pdf APIs Start--------------------------------------------------------------------------------------- */
    Route::get('get-all-certificates', [CertificateController::class, 'getAllCertificateDetails']);
    Route::get('get-certificate-pdf/{assessment_id}', [CertificateController::class, 'getCertificate']);
    /* Certificate Pdf APIs End--------------------------------------------------------------------------------------- */

    /* Survey Forms APIs Start -----------------------------------------------------------------------------------------*/

    Route::get('get-survey-forms', [SurveyController::class, 'getSurveyDetails']);
    Route::get('get-survey-by-id/{survey_id}', [SurveyController::class, 'getSurveyQuestionsById']);
    Route::post('store-survey-details', [SurveyController::class, 'storeSurveyDetails']);
    /* Survey Forms APIs End -----------------------------------------------------------------------------------------*/

    /* Home Data APIs Start------------------------------------------------------------------------------------------------------ */
    Route::get('get-app-home-data', [FlashNewsController::class, 'getAllHomeData']);
    Route::get('get-module-usage', [FlashNewsController::class, 'getModuleUsage']);
    Route::get('get-recently-added', [FlashNewsController::class, 'getRecentlyAdded']);
    /* Home Data APIs End ------------------------------------------------------------------------------------------------------ */

    /* Automatic Notification APIs Start ------------------------------------------------------------------------------------------ */
    Route::get('get-all-automatic-notification', [AutomaticNotificationController::class, 'getNotification']);
    /* Automatic Notification APIs End ------------------------------------------------------------------------------------------ */
});
Route::get('get-all-tags', [TrainingController::class, 'getAllTags']);

Route::get('get-all-tour', [TourController::class, 'activeTourDetail']);
Route::get('get-all-tour-slides', [TourController::class, 'activeTourSlide']);
Route::get('get-all-dashboard-data', [TvDashboardController::class, 'getDashboardDetails']);

/* -------------------------------------- Static Website Content APIS ---------------------------- */

/*  Statci Blogs APIs Start ----------------------------------------------------------------------------- */
Route::get('get-all-blogs', [StaticBlogController::class, 'getAllBlogs']);
Route::get('get-blogs-details/{slug}', [StaticBlogController::class, 'getBlogsDetails']);
Route::get('get-similar-blogs-details/{slug}', [StaticBlogController::class, 'getSimilarBlogs']);

/*  Statci Blogs APIs END -------------------------------------------------------------------------------- */

/* Static FAQ APIs Start --------------------------------------------------------------------------------- */
Route::get('get-all-faq', [StaticContentController::class, 'getFAQs']);
Route::post('store-static-enquiry', [StaticContentController::class, 'storeStaticEnquiry']);
/* Static FAQ APIs END ----------------------------------------------------------------------------------- */

/* Static App Config APIs Start --------------------------------------------------------------------------------- */
Route::get('get-all-app-config', [StaticContentController::class, 'getStaticAppConfig']);
/* Static App Config APIs END ----------------------------------------------------------------------------------- */

/* Static Dashboard APIs Start --------------------------------------------------------------------------------- */
Route::get('get-dashboard-details', [TvDashboardController::class, 'staticDashboardData']);
/* Static Dashboard APIs END ----------------------------------------------------------------------------------- */

/* Static Home Data APIs Start --------------------------------------------------------------------------------- */
Route::get('get-home-data', [StaticContentController::class, 'getHomeData']);
/* Static Dashboard APIs END ----------------------------------------------------------------------------------- */

/* Static Whats new APIs Start --------------------------------------------------------------------------------- */
Route::get('get-static-release', [StaticReleaseController::class, 'getStaticRelease']);
Route::get('get-static-modules', [StaticReleaseController::class, 'staticModules']);
Route::get('get-static-modules-by-slug/{slug}', [StaticReleaseController::class, 'getStaticModuleBySlug']);
Route::get('get-static-resource-material', [StaticReleaseController::class, 'staticresourceMaterial']);
/* Static Whats new APIs END ----------------------------------------------------------------------------------- */

/* Script For First Time Entry for Existing Subscribers in lb_subscriber_ranking Start--------------------------------*/

// Route::get('store-leaderboard-details-existing-user',[LeaderBoardController::class,'leaderBoardEntry']);
Route::get('get-level-wise-badges-information', [LeaderBoardController::class, 'leaderBoardOverview']);
/* Script For First Time Entry for Existing Subscribers in lb_subscriber_ranking End--------------------------------*/

/* Flash News APIs Start -------------------------------------------------------------------------------------------- */
Route::get('get-all-flash-news', [FlashNewsController::class, 'getAllFlashNews']);
Route::get('get-all-similar-apps', [FlashNewsController::class, 'getSimilarApps']);
/* Flash News APIs End -------------------------------------------------------------------------------------------- */

/* Set Master Id in Modules APIs -------------------------------------------------------------------------------------*/
Route::get('get-set-master-node', [DiagnosesAlgorithmsController::class, 'setMasterNodeId']);
/* Set Master Id in Modules APIs -------------------------------------------------------------------------------------*/


/* Script For Update User App Version Start-------------------------------------------------------------------------------- */
Route::get('script-to-update-plateform', [UserAppVersionController::class, 'scriptToUpdatePlatform']);
    /* Script For Update User App Version End-------------------------------------------------------------------------------- */
