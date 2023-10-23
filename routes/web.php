<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DiagnosesAlgorithmsController;
use App\Http\Controllers\API\TreatmentAlgorithmsController;
use App\Http\Controllers\API\GuidanceOnAdverseDrugReactionsController;
use App\Http\Controllers\API\CaseDefinitionsController;
use App\Http\Controllers\API\LatentTbInfectionsController;
use App\Http\Controllers\API\CgcInterventionsAlgorithmsController;
use App\Http\Controllers\API\DifferentialCareAlgorithmsController;
use App\Http\Controllers\API\DynamicAlgorithmsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SubscribersController;
use App\Http\Controllers\API\GraphController;
use Brackets\AdminAuth\Http\Controllers\AdminHomepageController;

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

// Route::get('/', function () {
//     // return redirect()->to('/admin'); 
//     view('dashboard');
//     // Route::get('/dashboard',[ReactDashboardController::class,'react-dashboard'])->name('react-dashboard');
// });

Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::get('/', function () {
        return view('dashboard');
    });

    Route::get('/admin', function () {
        return view('dashboard');
    });
    // Route::get('/admin/design', function () {
    //     return view('redirect-page');
    // });
    Route::get('/admin/home', [AdminHomepageController::class, 'index']);
    // Route::get('/admin', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('get-role-wise-state', [GraphController::class, 'index']);
    Route::get('get-dashboard-data', [DashboardController::class, 'getDashboardDataWithFilters']);
    Route::get('get-district-block-data', [DashboardController::class, 'getDistrictBlockData']);
    Route::get('get-block-health-data', [DashboardController::class, 'getBlockHealthData']);
    Route::get('get-health-data', [DashboardController::class, 'getHealthData']);
    Route::get('get-district-data', [DashboardController::class, 'getDistrictData']);
    Route::get('get-block-data', [DashboardController::class, 'getBlockData']);
    Route::get('/export-cadre-wise-subscribers', [DashboardController::class, 'exportCadreWiseSubscriber'])->name('exportCadreWiseSubscriber');
    Route::get('/export-module-usage', [DashboardController::class, 'exportModuleUsage'])->name('exportModuleUsage');

    Route::get('/get_states', [DashboardController::class, 'getStates'])->name('getStates');
    Route::get('/get_indicator_values', [DashboardController::class, 'getIndicatorValues'])->name('getIndicatorValues');
    Route::get('/get_latest_timestamp', [DashboardController::class, 'getLatestTimestamp'])->name('getLatestTimestamp');
    Route::get('/get_latest_timestamp_dist', [DashboardController::class, 'getLatestTimestampDist'])->name('getLatestTimestampDist');
    Route::get('/get_map_data', [DashboardController::class, 'getMapData'])->name('getMapData');
    Route::get('/get_state_lowscore', [DashboardController::class, 'getStateLowerScore'])->name('getStateLowerScore');
    Route::get('/get_state_highscore', [DashboardController::class, 'getStateHighScore'])->name('getStateHighScore');
    Route::get('/get_state_highchange', [DashboardController::class, 'getStateHighChange'])->name('getStateHighChange');
    Route::get('/get_state_lowchange', [DashboardController::class, 'getStateLowChange'])->name('getStateLowChange');
    Route::get('/get_district_map_data', [DashboardController::class, 'getDistrictMapData'])->name('getDistrictMapData');
    Route::get('/get_districts', [DashboardController::class, 'getDistricts'])->name('getDistricts');

    Route::get('/get-master-node-by-type/{type}', [App\Http\Controllers\Admin\CaseDefinitionsController::class, 'getMasterNodeByType'])->name('getMasterNodeByType');
    Route::get('/get-distirct/{type}', [App\Http\Controllers\Admin\SubscribersController::class, 'getDistrict'])->name('getDistrict');
    Route::get('/get-block/{state_id}/{district_id}', [App\Http\Controllers\Admin\SubscribersController::class, 'getBlock'])->name('getBlock');
    Route::get('/get-health-facility/{state_id}/{district_id}/{block_id}', [App\Http\Controllers\Admin\SubscribersController::class, 'getHealthFacility'])->name('getHealthFacility');

    /* New Dashboard Data Api Start-------------------------------------------------------------------------------------------------- */
    Route::get('get-count-list-data', [GraphController::class, 'countListGraph']);
    Route::get('get-usage-count', [GraphController::class, 'usageCountData']);
    Route::get('get-subscribers-count', [GraphController::class, 'subscriberCount']);
    Route::get('get-cadre-wise-subscriber-count', [GraphController::class, 'cadreWiseSubscriber']);
    Route::get('get-module-wise-subscriber-count', [GraphController::class, 'moduleUsage']);
    Route::get('get-leaderboard-count', [GraphController::class, 'leaderboardLevels']);
    Route::get('get-chatquestion-hit-count', [GraphController::class, 'chatQuestionHits']);
    Route::get('get-chatkeyword-hit-count', [GraphController::class, 'chatkeywordHits']);
    Route::get('get-user-feedback-details', [GraphController::class, 'userFeedback']);
    Route::get('get-assessment-submission-details', [GraphController::class, 'assessmentSubmission']);
    Route::get('get-app-opened-count', [GraphController::class, 'appOpenedCountWeek']);
    Route::get('/export-leader-board', [DashboardController::class, 'exportLeaderboard'])->name('exportLeaderboard');
    Route::get('/export-chatbot-question', [DashboardController::class, 'exportChatQuestion'])->name('exportChatQuestion');
    Route::get('/export-chatbot-keyword', [DashboardController::class, 'exportChatKeyword'])->name('exportChatKeyword');
    Route::get('/export-app-opened-count-3-to-5', [DashboardController::class, 'exportAppOpenedCount3to5'])->name('exportAppOpenedCount3to5');
    Route::get('/export-app-opened-count-5-to-7', [DashboardController::class, 'exportAppOpenedCount5to7'])->name('exportAppOpenedCount5to7');
    Route::get('/export-app-opened-count-7-to-9', [DashboardController::class, 'exportAppOpenedCount7to9'])->name('exportAppOpenedCount7to9');
    Route::get('/export-app-opened-count-10', [DashboardController::class, 'exportAppOpenedCount10'])->name('exportAppOpenedCount10');
    Route::get('/get-user-name', [GraphController::class, 'getUserName'])->name('getUserName');

    /* New Dashboard Data Api END-------------------------------------------------------------------------------------------------- */
});


// Route::get('/get-diagnoses/{lang}', [App\Http\Controllers\Admin\ChatQuestionsController::class, 'getDiagnoese'])->name('getDiagnoese');
// Route::get('/get-case-definition/{lang}', [App\Http\Controllers\Admin\ChatQuestionsController::class, 'getCaseDefinition'])->name('getCaseDefinition');
// Route::get('/get-treatment/{lang}', [App\Http\Controllers\Admin\ChatQuestionsController::class, 'getTreatment'])->name('getTreatment');
// Route::get('/get-guidance-on-adr/{lang}', [App\Http\Controllers\Admin\ChatQuestionsController::class, 'getGuidanceOnAdr'])->name('getGuidanceOnAdr');
// Route::get('/get-latent-tb/{lang}', [App\Http\Controllers\Admin\ChatQuestionsController::class, 'getLatentTb'])->name('getLatentTb');
// Route::get('/get-differential-care/{lang}', [App\Http\Controllers\Admin\ChatQuestionsController::class, 'getDifferentialCare'])->name('getDifferentialCare');

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('admin-users')->name('admin-users/')->group(static function () {
            Route::get('/',                                             'AdminUsersController@index')->name('index');
            Route::get('/create',                                       'AdminUsersController@create')->name('create');
            Route::post('/',                                            'AdminUsersController@store')->name('store');
            Route::get('/{adminUser}/impersonal-login',                 'AdminUsersController@impersonalLogin')->name('impersonal-login');
            Route::get('/{adminUser}/edit',                             'AdminUsersController@edit')->name('edit');
            Route::post('/{adminUser}',                                 'AdminUsersController@update')->name('update');
            Route::delete('/{adminUser}',                               'AdminUsersController@destroy')->name('destroy');
            Route::get('/{adminUser}/resend-activation',                'AdminUsersController@resendActivationEmail')->name('resendActivationEmail');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::get('/profile',                                      'ProfileController@editProfile')->name('edit-profile');
        Route::post('/profile',                                     'ProfileController@updateProfile')->name('update-profile');
        Route::get('/password',                                     'ProfileController@editPassword')->name('edit-password');
        Route::post('/password',                                    'ProfileController@updatePassword')->name('update-password');
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('cadres')->name('cadres/')->group(static function () {
            Route::get('/',                                             'CadreController@index')->name('index');
            Route::get('/create',                                       'CadreController@create')->name('create');
            Route::post('/',                                            'CadreController@store')->name('store');
            Route::get('/{cadre}/edit',                                 'CadreController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CadreController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{cadre}',                                     'CadreController@update')->name('update');
            Route::delete('/{cadre}',                                   'CadreController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('states')->name('states/')->group(static function () {
            Route::get('/',                                             'StateController@index')->name('index');
            Route::get('/create',                                       'StateController@create')->name('create');
            Route::post('/',                                            'StateController@store')->name('store');
            Route::get('/{state}/edit',                                 'StateController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'StateController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{state}',                                     'StateController@update')->name('update');
            Route::delete('/{state}',                                   'StateController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('districts')->name('districts/')->group(static function () {
            Route::get('/',                                             'DistrictsController@index')->name('index');
            Route::get('/create',                                       'DistrictsController@create')->name('create');
            Route::post('/',                                            'DistrictsController@store')->name('store');
            Route::get('/{district}/edit',                              'DistrictsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'DistrictsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{district}',                                  'DistrictsController@update')->name('update');
            Route::delete('/{district}',                                'DistrictsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('blocks')->name('blocks/')->group(static function () {
            Route::get('/',                                             'BlocksController@index')->name('index');
            Route::get('/create',                                       'BlocksController@create')->name('create');
            Route::post('/',                                            'BlocksController@store')->name('store');
            Route::get('/{block}/edit',                                 'BlocksController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'BlocksController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{block}',                                     'BlocksController@update')->name('update');
            Route::delete('/{block}',                                   'BlocksController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('assessments')->name('assessments/')->group(static function () {
            Route::get('/',                                             'AssessmentsController@index')->name('index');
            Route::get('/create',                                       'AssessmentsController@create')->name('create');
            Route::post('/',                                            'AssessmentsController@store')->name('store');
            Route::get('/{assessment}/edit',                            'AssessmentsController@edit')->name('edit');
            Route::get('/{assessment}/send-initial-invitation',         'AssessmentsController@sendInitialInvitation')->name('sendInitialInvitation');
            Route::post('/{assessment}/activeflag',                     'AssessmentsController@activeFlag')->name('active-flag');
            Route::get('/{assessment}/copy',                            'AssessmentsController@copy')->name('copy');
            Route::get('/{assessment}/report',                          'AssessmentsController@report')->name('report');
            Route::get('/{assessment}/assessment-question',             'AssessmentsController@assessmentQuestion')->name('assessmentQuestion');
            Route::post('/bulk-destroy',                                'AssessmentsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{assessment}',                                'AssessmentsController@update')->name('update');
            Route::delete('/{assessment}',                              'AssessmentsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('assessment-questions')->name('assessment-questions/')->group(static function () {
            Route::get('/',                                             'AssessmentQuestionsController@index')->name('index');
            Route::get('/create',                                       'AssessmentQuestionsController@create')->name('create');
            Route::post('/',                                            'AssessmentQuestionsController@store')->name('store');
            Route::get('/{assessmentQuestion}/edit',                    'AssessmentQuestionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AssessmentQuestionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{assessmentQuestion}',                        'AssessmentQuestionsController@update')->name('update');
            Route::delete('/{assessmentQuestion}',                      'AssessmentQuestionsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('user-assessments')->name('user-assessments/')->group(static function () {
            Route::get('/',                                             'UserAssessmentsController@index')->name('index');
            // Route::get('/create',                                       'UserAssessmentsController@create')->name('create');
            // Route::post('/',                                            'UserAssessmentsController@store')->name('store');
            // Route::get('/{userAssessment}/edit',                        'UserAssessmentsController@edit')->name('edit');
            // Route::post('/bulk-destroy',                                'UserAssessmentsController@bulkDestroy')->name('bulk-destroy');
            // Route::post('/{userAssessment}',                            'UserAssessmentsController@update')->name('update');
            // Route::delete('/{userAssessment}',                          'UserAssessmentsController@destroy')->name('destroy');
            Route::get('/export',                                       'UserAssessmentsController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('resource-materials')->name('resource-materials/')->group(static function () {
            // Route::get('/master-nodes',                                 'ResourceMaterialsController@getMasterNodes')->name('master-nodes');
            Route::get('/',                                             'ResourceMaterialsController@index')->name('index');
            Route::get('/create',                                       'ResourceMaterialsController@create')->name('create');
            Route::post('/',                                            'ResourceMaterialsController@store')->name('store');
            Route::get('/{resourceMaterial}/edit',                      'ResourceMaterialsController@edit')->name('edit');
            Route::get('/{resourceMaterial}/send-initial-invitation',   'ResourceMaterialsController@sendInitialInvitation')->name('sendInitialInvitation');
            Route::post('/bulk-destroy',                                'ResourceMaterialsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{resourceMaterial}',                          'ResourceMaterialsController@update')->name('update');
            Route::delete('/{resourceMaterial}',                        'ResourceMaterialsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('cgc-interventions')->name('cgc-interventions/')->group(static function () {
            Route::get('/',                                             'CgcInterventionsController@index')->name('index');
            Route::get('/create',                                       'CgcInterventionsController@create')->name('create');
            Route::post('/',                                            'CgcInterventionsController@store')->name('store');
            Route::get('/{cgcIntervention}/edit',                       'CgcInterventionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CgcInterventionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{cgcIntervention}',                           'CgcInterventionsController@update')->name('update');
            Route::delete('/{cgcIntervention}',                         'CgcInterventionsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('symptoms')->name('symptoms/')->group(static function () {
            Route::get('/',                                             'SymptomsController@index')->name('index');
            Route::get('/create',                                       'SymptomsController@create')->name('create');
            Route::post('/',                                            'SymptomsController@store')->name('store');
            Route::get('/{symptom}/edit',                               'SymptomsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'SymptomsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{symptom}',                                   'SymptomsController@update')->name('update');
            Route::delete('/{symptom}',                                 'SymptomsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('diagnoses-algorithms')->name('diagnoses-algorithms/')->group(static function () {
            Route::get('/master-nodes',                                 'DiagnosesAlgorithmsController@getMasterNodes')->name('master-nodes');
            Route::get('/org-chart',                                    'DiagnosesAlgorithmsController@getTreeViewData')->name('org-chart');
            Route::get('/',                                             'DiagnosesAlgorithmsController@index')->name('index');
            Route::get('/create',                                       'DiagnosesAlgorithmsController@create')->name('create');
            Route::post('/',                                            'DiagnosesAlgorithmsController@store')->name('store');
            Route::get('/{diagnosesAlgorithm}/edit',                    'DiagnosesAlgorithmsController@edit')->name('edit');
            Route::get('/{diagnosesAlgorithm}/send-initial-invitation', 'DiagnosesAlgorithmsController@sendInitialInvitation')->name('sendInitialInvitation');
            Route::post('/bulk-destroy',                                'DiagnosesAlgorithmsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{diagnosesAlgorithm}',                        'DiagnosesAlgorithmsController@update')->name('update');
            Route::delete('/{diagnosesAlgorithm}',                      'DiagnosesAlgorithmsController@destroy')->name('destroy');
        });
        Route::prefix('health-facilities')->name('health-facilities/')->group(static function () {
            Route::get('/',                                             'HealthFacilitiesController@index')->name('index');
            Route::get('/create',                                       'HealthFacilitiesController@create')->name('create');
            Route::post('/',                                            'HealthFacilitiesController@store')->name('store');
            Route::get('/{healthFacility}/edit',                        'HealthFacilitiesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'HealthFacilitiesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{healthFacility}',                            'HealthFacilitiesController@update')->name('update');
            Route::delete('/{healthFacility}',                          'HealthFacilitiesController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('treatment-algorithms')->name('treatment-algorithms/')->group(static function () {
            Route::get('/master-nodes',                                 'TreatmentAlgorithmsController@getMasterNodes')->name('master-nodes');
            Route::get('/org-chart',                                    'TreatmentAlgorithmsController@getTreeViewData')->name('org-chart');
            Route::get('/',                                             'TreatmentAlgorithmsController@index')->name('index');
            Route::get('/create',                                       'TreatmentAlgorithmsController@create')->name('create');
            Route::post('/',                                            'TreatmentAlgorithmsController@store')->name('store');
            Route::get('/{treatmentAlgorithm}/edit',                    'TreatmentAlgorithmsController@edit')->name('edit');
            Route::get('/{treatmentAlgorithm}/send-initial-invitation', 'TreatmentAlgorithmsController@sendInitialInvitation')->name('sendInitialInvitation');
            Route::post('/bulk-destroy',                                'TreatmentAlgorithmsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{treatmentAlgorithm}',                        'TreatmentAlgorithmsController@update')->name('update');
            Route::delete('/{treatmentAlgorithm}',                      'TreatmentAlgorithmsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('guidance-on-adverse-drug-reactions')->name('guidance-on-adverse-drug-reactions/')->group(static function () {
            Route::get('/master-nodes',                                 'GuidanceOnAdverseDrugReactionsController@getMasterNodes')->name('master-nodes');
            Route::get('/org-chart',                                    'GuidanceOnAdverseDrugReactionsController@getTreeViewData')->name('org-chart');
            Route::get('/',                                             'GuidanceOnAdverseDrugReactionsController@index')->name('index');
            Route::get('/create',                                       'GuidanceOnAdverseDrugReactionsController@create')->name('create');
            Route::post('/',                                            'GuidanceOnAdverseDrugReactionsController@store')->name('store');
            Route::get('/{guidanceOnAdverseDrugReaction}/edit',         'GuidanceOnAdverseDrugReactionsController@edit')->name('edit');
            Route::get('/{guidanceOnAdverseDrugReaction}/send-initial-invitation', 'GuidanceOnAdverseDrugReactionsController@sendInitialInvitation')->name('sendInitialInvitation');
            Route::post('/bulk-destroy',                                'GuidanceOnAdverseDrugReactionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{guidanceOnAdverseDrugReaction}',             'GuidanceOnAdverseDrugReactionsController@update')->name('update');
            Route::delete('/{guidanceOnAdverseDrugReaction}',           'GuidanceOnAdverseDrugReactionsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('case-definitions')->name('case-definitions/')->group(static function () {
            Route::get('/master-nodes',                                 'CaseDefinitionsController@getMasterNodes')->name('master-nodes');
            Route::get('/org-chart',                                    'CaseDefinitionsController@getTreeViewData')->name('org-chart');
            Route::get('/',                                             'CaseDefinitionsController@index')->name('index');
            Route::get('/create',                                       'CaseDefinitionsController@create')->name('create');
            Route::post('/',                                            'CaseDefinitionsController@store')->name('store');
            Route::get('/{caseDefinition}/edit',                        'CaseDefinitionsController@edit')->name('edit');
            Route::get('/{caseDefinition}/send-initial-invitation',     'CaseDefinitionsController@sendInitialInvitation')->name('sendInitialInvitation');
            Route::post('/bulk-destroy',                                'CaseDefinitionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{caseDefinition}',                            'CaseDefinitionsController@update')->name('update');
            Route::delete('/{caseDefinition}',                          'CaseDefinitionsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('latent-tb-infections')->name('latent-tb-infections/')->group(static function () {
            Route::get('/master-nodes',                                 'LatentTbInfectionsController@getMasterNodes')->name('master-nodes');
            Route::get('/org-chart',                                    'LatentTbInfectionsController@getTreeViewData')->name('org-chart');
            Route::get('/',                                             'LatentTbInfectionsController@index')->name('index');
            Route::get('/create',                                       'LatentTbInfectionsController@create')->name('create');
            Route::post('/',                                            'LatentTbInfectionsController@store')->name('store');
            Route::get('/{latentTbInfection}/edit',                     'LatentTbInfectionsController@edit')->name('edit');
            Route::get('/{latentTbInfection}/send-initial-invitation',  'LatentTbInfectionsController@sendInitialInvitation')->name('sendInitialInvitation');
            Route::post('/bulk-destroy',                                'LatentTbInfectionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{latentTbInfection}',                         'LatentTbInfectionsController@update')->name('update');
            Route::delete('/{latentTbInfection}',                       'LatentTbInfectionsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('chat-keywords')->name('chat-keywords/')->group(static function () {
            Route::get('/',                                             'ChatKeywordsController@index')->name('index');
            Route::get('/create',                                       'ChatKeywordsController@create')->name('create');
            Route::post('/',                                            'ChatKeywordsController@store')->name('store');
            Route::get('/{chatKeyword}/edit',                           'ChatKeywordsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ChatKeywordsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{chatKeyword}',                               'ChatKeywordsController@update')->name('update');
            Route::delete('/{chatKeyword}',                             'ChatKeywordsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('chat-questions')->name('chat-questions/')->group(static function () {
            Route::get('/chat-question-vs-tag',                         'ChatQuestionsController@getQuestionVsTags')->name('chat-question-vs-tag');
            Route::get('/chat-question-tag-list/{questionId}',          'ChatQuestionsController@getTagsByQuestion')->name('chat-question-tag-list');
            Route::get('/',                                             'ChatQuestionsController@index')->name('index');
            Route::get('/create',                                       'ChatQuestionsController@create')->name('create');
            Route::post('/',                                            'ChatQuestionsController@store')->name('store');
            Route::get('/{chatQuestion}/edit',                          'ChatQuestionsController@edit')->name('edit');
            Route::get('/{chatQuestion}/tag',                           'ChatQuestionsController@addTag')->name('addTag');
            Route::post('/bulk-destroy',                                'ChatQuestionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{chatQuestion}',                              'ChatQuestionsController@update')->name('update');
            Route::delete('/{chatQuestion}',                            'ChatQuestionsController@destroy')->name('destroy');
            Route::get('/export',                                       'ChatQuestionsController@export')->name('export');
            Route::get('/export-marathi',                               'ChatQuestionsController@exportMarathi')->name('exportMarathi');
        });
    });
});

Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('chat-question-hits')->name('chat-question-hits/')->group(static function () {
            Route::get('/',                                             'ChatQuestionHitsController@index')->name('index');
            // Route::get('/create',                                       'ChatQuestionHitsController@create')->name('create');
            // Route::post('/',                                            'ChatQuestionHitsController@store')->name('store');
            // Route::get('/{chatQuestionHit}/edit',                       'ChatQuestionHitsController@edit')->name('edit');
            // Route::post('/bulk-destroy',                                'ChatQuestionHitsController@bulkDestroy')->name('bulk-destroy');
            // Route::post('/{chatQuestionHit}',                           'ChatQuestionHitsController@update')->name('update');
            // Route::delete('/{chatQuestionHit}',                         'ChatQuestionHitsController@destroy')->name('destroy');
            Route::get('/export',                                       'ChatQuestionHitsController@export')->name('export');
        });
    });
});

/* Routes for Patient Management Tool Screen */
Route::get('get-diagnoses-algorithms-master-nodes', [DiagnosesAlgorithmsController::class, 'getMasterNodes']);
Route::get('get-diagnoses-algorithms-dependent-nodes/{masterNodeId}', [DiagnosesAlgorithmsController::class, 'getDependentNodes']);

Route::get('get-treatment-algorithms-master-nodes', [TreatmentAlgorithmsController::class, 'getMasterNodes']);
Route::get('get-treatment-algorithms-dependent-nodes/{masterNodeId}', [TreatmentAlgorithmsController::class, 'getDependentNodes']);

Route::get('get-guidance-on-adverse-drug-reactions-master-nodes', [GuidanceOnAdverseDrugReactionsController::class, 'getMasterNodes']);
Route::get('get-guidance-on-adverse-drug-reactions-dependent-nodes/{masterNodeId}', [GuidanceOnAdverseDrugReactionsController::class, 'getDependentNodes']);

Route::get('get-latent-tb-infection-master-nodes', [LatentTbInfectionsController::class, 'getMasterNodes']);
Route::get('get-latent-tb-infection-dependent-nodes/{masterNodeId}', [LatentTbInfectionsController::class, 'getDependentNodes']);

/* Routes for Case difinition */
Route::get('get-case-definitions-master-nodes', [CaseDefinitionsController::class, 'getMasterNodes']);
Route::get('get-case-definitions-dependent-nodes/{masterNodeId}', [CaseDefinitionsController::class, 'getDependentNodes']);

Route::get('get-cgc-interventions-algorithms-master-nodes', [CgcInterventionsAlgorithmsController::class, 'getMasterNodes']);
Route::get('get-cgc-interventions-algorithms-dependent-nodes/{masterNodeId}', [CgcInterventionsAlgorithmsController::class, 'getDependentNodes']);

Route::get('get-differential-care-algorithms-master-nodes', [DifferentialCareAlgorithmsController::class, 'getMasterNodes']);
Route::get('get-differential-care-algorithms-dependent-nodes/{masterNodeId}', [DifferentialCareAlgorithmsController::class, 'getDependentNodes']);

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('subscribers')->name('subscribers/')->group(static function () {
            Route::get('/',                                             'SubscribersController@index')->name('index');
            // Route::get('/create',                                       'SubscribersController@create')->name('create');
            // Route::post('/',                                            'SubscribersController@store')->name('store');
            Route::get('/{subscriber}/edit',                            'SubscribersController@edit')->name('edit');
            Route::get('/{subscriber}/sendOtp',                          'SubscribersController@sendForogtOtp')->name('sendOtp');
            // Route::post('/bulk-destroy',                                'SubscribersController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{subscriber}',                                'SubscribersController@update')->name('update');
            // Route::delete('/{subscriber}',                              'SubscribersController@destroy')->name('destroy');
            Route::get('/export',                                       'SubscribersController@export')->name('export');
        });
    });
});

Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('enquiries')->name('enquiries/{?date=}')->group(static function () {
            Route::get('/',                                             'EnquiriesController@index')->name('index');
            // Route::get('/create',                                       'EnquiriesController@create')->name('create');
            // Route::post('/',                                            'EnquiriesController@store')->name('store');
            // Route::get('/{enquiry}/edit',                               'EnquiriesController@edit')->name('edit');
            // Route::post('/bulk-destroy',                                'EnquiriesController@bulkDestroy')->name('bulk-destroy');
            // Route::post('/{enquiry}',                                   'EnquiriesController@update')->name('update');
            // Route::delete('/{enquiry}',                                 'EnquiriesController@destroy')->name('destroy');
            Route::get('/export',                                       'EnquiriesController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('app-configs')->name('app-configs/')->group(static function () {
            Route::get('/',                                             'AppConfigController@index')->name('index');
            Route::get('/create',                                       'AppConfigController@create')->name('create');
            Route::post('/',                                            'AppConfigController@store')->name('store');
            Route::get('/{appConfig}/edit',                             'AppConfigController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AppConfigController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{appConfig}',                                 'AppConfigController@update')->name('update');
            Route::delete('/{appConfig}',                               'AppConfigController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('subscriber-activities')->name('subscriber-activities/')->group(static function () {
            Route::get('/',                                             'SubscriberActivitiesController@index')->name('index');
            // Route::get('/create',                                       'SubscriberActivitiesController@create')->name('create');
            // Route::post('/',                                            'SubscriberActivitiesController@store')->name('store');
            // Route::get('/{subscriberActivity}/edit',                    'SubscriberActivitiesController@edit')->name('edit');
            // Route::post('/bulk-destroy',                                'SubscriberActivitiesController@bulkDestroy')->name('bulk-destroy');
            // Route::post('/{subscriberActivity}',                        'SubscriberActivitiesController@update')->name('update');
            // Route::delete('/{subscriberActivity}',                      'SubscriberActivitiesController@destroy')->name('destroy');
            Route::get('/export',                                       'SubscriberActivitiesController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('chat-keyword-hits')->name('chat-keyword-hits/')->group(static function () {
            Route::get('/',                                             'ChatKeywordHitsController@index')->name('index');
            Route::get('/create',                                       'ChatKeywordHitsController@create')->name('create');
            Route::post('/',                                            'ChatKeywordHitsController@store')->name('store');
            Route::get('/{chatKeywordHit}/edit',                        'ChatKeywordHitsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ChatKeywordHitsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{chatKeywordHit}',                            'ChatKeywordHitsController@update')->name('update');
            Route::delete('/{chatKeywordHit}',                          'ChatKeywordHitsController@destroy')->name('destroy');
            Route::get('/export',                                       'ChatKeywordHitsController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('master-cms')->name('master-cms/')->group(static function () {
            Route::get('/',                                             'MasterCmsController@index')->name('index');
            Route::get('/create',                                       'MasterCmsController@create')->name('create');
            Route::post('/',                                            'MasterCmsController@store')->name('store');
            Route::get('/{masterCm}/edit',                              'MasterCmsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'MasterCmsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{masterCm}',                                  'MasterCmsController@update')->name('update');
            Route::delete('/{masterCm}',                                'MasterCmsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('cgc-interventions-algorithms')->name('cgc-interventions-algorithms/')->group(static function () {
            Route::get('/master-nodes',                                 'CgcInterventionsAlgorithmsController@getMasterNodes')->name('master-nodes');
            Route::get('/org-chart',                                    'CgcInterventionsAlgorithmsController@getTreeViewData')->name('org-chart');
            Route::get('/',                                             'CgcInterventionsAlgorithmsController@index')->name('index');
            Route::get('/create',                                       'CgcInterventionsAlgorithmsController@create')->name('create');
            Route::post('/',                                            'CgcInterventionsAlgorithmsController@store')->name('store');
            Route::get('/{cgcInterventionsAlgorithm}/edit',             'CgcInterventionsAlgorithmsController@edit')->name('edit');
            Route::get('/{cgcInterventionsAlgorithm}/send-initial-invitation',  'CgcInterventionsAlgorithmsController@sendInitialInvitation')->name('sendInitialInvitation');
            Route::post('/bulk-destroy',                                'CgcInterventionsAlgorithmsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{cgcInterventionsAlgorithm}',                 'CgcInterventionsAlgorithmsController@update')->name('update');
            Route::delete('/{cgcInterventionsAlgorithm}',               'CgcInterventionsAlgorithmsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('differential-care-algorithms')->name('differential-care-algorithms/')->group(static function () {
            Route::get('/master-nodes',                                 'DifferentialCareAlgorithmsController@getMasterNodes')->name('master-nodes');
            Route::get('/org-chart',                                    'DifferentialCareAlgorithmsController@getTreeViewData')->name('org-chart');
            Route::get('/',                                             'DifferentialCareAlgorithmsController@index')->name('index');
            Route::get('/create',                                       'DifferentialCareAlgorithmsController@create')->name('create');
            Route::post('/',                                            'DifferentialCareAlgorithmsController@store')->name('store');
            Route::get('/{differentialCareAlgorithm}/edit',             'DifferentialCareAlgorithmsController@edit')->name('edit');
            Route::get('/{differentialCareAlgorithm}/send-initial-invitation',  'DifferentialCareAlgorithmsController@sendInitialInvitation')->name('sendInitialInvitation');
            Route::post('/bulk-destroy',                                'DifferentialCareAlgorithmsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{differentialCareAlgorithm}',                 'DifferentialCareAlgorithmsController@update')->name('update');
            Route::delete('/{differentialCareAlgorithm}',               'DifferentialCareAlgorithmsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('patient-assessments')->name('patient-assessments/')->group(static function () {
            Route::get('/',                                             'PatientAssessmentsController@index')->name('index');
            // Route::get('/create',                                       'PatientAssessmentsController@create')->name('create');
            // Route::post('/',                                            'PatientAssessmentsController@store')->name('store');
            // Route::get('/{patientAssessment}/edit',                     'PatientAssessmentsController@edit')->name('edit');
            // Route::post('/bulk-destroy',                                'PatientAssessmentsController@bulkDestroy')->name('bulk-destroy');
            // Route::post('/{patientAssessment}',                         'PatientAssessmentsController@update')->name('update');
            // Route::delete('/{patientAssessment}',                       'PatientAssessmentsController@destroy')->name('destroy');
            Route::get('/export',                                       'PatientAssessmentsController@export')->name('export');
        });
    });
});

Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::post('get_district', [SubscribersController::class, 'getDistrict']);
});


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('module-mapping-to-names')->name('module-mapping-to-names/')->group(static function () {
            Route::get('/',                                             'ModuleMappingToNamesController@index')->name('index');
            Route::get('/create',                                       'ModuleMappingToNamesController@create')->name('create');
            Route::post('/',                                            'ModuleMappingToNamesController@store')->name('store');
            Route::get('/{moduleMappingToName}/edit',                   'ModuleMappingToNamesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ModuleMappingToNamesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{moduleMappingToName}',                       'ModuleMappingToNamesController@update')->name('update');
            Route::delete('/{moduleMappingToName}',                     'ModuleMappingToNamesController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('user-notifications')->name('user-notifications/')->group(static function () {
            Route::get('/',                                             'UserNotificationsController@index')->name('index');
            Route::get('/create',                                       'UserNotificationsController@create')->name('create');
            Route::post('/',                                            'UserNotificationsController@store')->name('store');
            // Route::get('/{userNotification}/edit',                      'UserNotificationsController@edit')->name('edit');
            // Route::post('/bulk-destroy',                                'UserNotificationsController@bulkDestroy')->name('bulk-destroy');
            // Route::post('/{userNotification}',                          'UserNotificationsController@update')->name('update');
            // Route::delete('/{userNotification}',                        'UserNotificationsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('roles')->name('roles/')->group(static function () {
            Route::get('/',                                             'RolesController@index')->name('index');
            Route::get('/create',                                       'RolesController@create')->name('create');
            Route::post('/',                                            'RolesController@store')->name('store');
            Route::get('/{role}/edit',                                  'RolesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'RolesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{role}',                                      'RolesController@update')->name('update');
            Route::delete('/{role}',                                    'RolesController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('message-notifications')->name('message-notifications/')->group(static function () {
            Route::get('/',                                             'MessageNotificationsController@index')->name('index');
            Route::get('/create',                                       'MessageNotificationsController@create')->name('create');
            Route::post('/',                                            'MessageNotificationsController@store')->name('store');
            // Route::get('/{messageNotification}/edit',                   'MessageNotificationsController@edit')->name('edit');
            // Route::post('/bulk-destroy',                                'MessageNotificationsController@bulkDestroy')->name('bulk-destroy');
            // Route::post('/{messageNotification}',                       'MessageNotificationsController@update')->name('update');
            // Route::delete('/{messageNotification}',                     'MessageNotificationsController@destroy')->name('destroy');
            Route::get('/export',                                       'MessageNotificationsController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('chatbot-activities')->name('chatbot-activities/')->group(static function () {
            Route::get('/',                                             'ChatbotActivityController@index')->name('index');
            Route::get('/create',                                       'ChatbotActivityController@create')->name('create');
            Route::post('/',                                            'ChatbotActivityController@store')->name('store');
            Route::get('/{chatbotActivity}/edit',                       'ChatbotActivityController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ChatbotActivityController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{chatbotActivity}',                           'ChatbotActivityController@update')->name('update');
            Route::delete('/{chatbotActivity}',                         'ChatbotActivityController@destroy')->name('destroy');
        });
    });
});


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('app-management-flags')->name('app-management-flags/')->group(static function () {
            Route::get('/',                                             'AppManagementFlagsController@index')->name('index');
            Route::get('/create',                                       'AppManagementFlagsController@create')->name('create');
            Route::post('/',                                            'AppManagementFlagsController@store')->name('store');
            Route::get('/{appManagementFlag}/edit',                     'AppManagementFlagsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AppManagementFlagsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{appManagementFlag}',                         'AppManagementFlagsController@update')->name('update');
            Route::delete('/{appManagementFlag}',                       'AppManagementFlagsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('dynamic-algo-masters')->name('dynamic-algo-masters/')->group(static function () {
            Route::get('/',                                             'DynamicAlgoMasterController@index')->name('index');
            Route::get('/create',                                       'DynamicAlgoMasterController@create')->name('create');
            Route::post('/',                                            'DynamicAlgoMasterController@store')->name('store');
            Route::get('/{dynamicAlgoMaster}/edit',                     'DynamicAlgoMasterController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'DynamicAlgoMasterController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{dynamicAlgoMaster}',                         'DynamicAlgoMasterController@update')->name('update');
            Route::delete('/{dynamicAlgoMaster}',                       'DynamicAlgoMasterController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('t-module-masters')->name('t-module-masters/')->group(static function () {
            Route::get('/',                                             'TModuleMasterController@index')->name('index');
            Route::get('/create',                                       'TModuleMasterController@create')->name('create');
            Route::post('/',                                            'TModuleMasterController@store')->name('store');
            Route::get('/{tModuleMaster}/edit',                         'TModuleMasterController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'TModuleMasterController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{tModuleMaster}',                             'TModuleMasterController@update')->name('update');
            Route::delete('/{tModuleMaster}',                           'TModuleMasterController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('dynamic-algorithms')->name('dynamic-algorithms/')->group(static function () {
            Route::get('/master-nodes',                                 'DynamicAlgorithmController@getMasterNodes')->name('master-nodes');
            Route::get('/org-chart',                                    'DynamicAlgorithmController@getTreeViewData')->name('org-chart');
            Route::get('/',                                             'DynamicAlgorithmController@index')->name('index');
            Route::get('/create',                                       'DynamicAlgorithmController@create')->name('create');
            Route::post('/',                                            'DynamicAlgorithmController@store')->name('store');
            Route::get('/{dynamicAlgorithm}/edit',                      'DynamicAlgorithmController@edit')->name('edit');
            Route::get('/{dynamicAlgorithm}/send-initial-invitation',   'DynamicAlgorithmController@sendInitialInvitation')->name('sendInitialInvitation');
            Route::post('/bulk-destroy',                                'DynamicAlgorithmController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{dynamicAlgorithm}',                          'DynamicAlgorithmController@update')->name('update');
            Route::delete('/{dynamicAlgorithm}',                        'DynamicAlgorithmController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('t-sub-module-masters')->name('t-sub-module-masters/')->group(static function () {
            Route::get('/',                                             'TSubModuleMasterController@index')->name('index');
            Route::get('/create',                                       'TSubModuleMasterController@create')->name('create');
            Route::post('/',                                            'TSubModuleMasterController@store')->name('store');
            Route::get('/{tSubModuleMaster}/edit',                      'TSubModuleMasterController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'TSubModuleMasterController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{tSubModuleMaster}',                          'TSubModuleMasterController@update')->name('update');
            Route::delete('/{tSubModuleMaster}',                        'TSubModuleMasterController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('role-has-permissions')->name('role-has-permissions/')->group(static function () {
            Route::get('/',                                             'RoleHasPermissionsController@index')->name('index');
            Route::get('/create',                                       'RoleHasPermissionsController@create')->name('create');
            Route::post('/',                                            'RoleHasPermissionsController@store')->name('store');
            Route::get('/{roleHasPermission}/edit',                     'RoleHasPermissionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'RoleHasPermissionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{roleHasPermission}',                         'RoleHasPermissionsController@update')->name('update');
            Route::delete('/{roleHasPermission}',                       'RoleHasPermissionsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('t-training-tags')->name('t-training-tags/')->group(static function () {
            Route::get('/',                                             'TTrainingTagController@index')->name('index');
            Route::get('/create',                                       'TTrainingTagController@create')->name('create');
            Route::post('/',                                            'TTrainingTagController@store')->name('store');
            Route::get('/{tTrainingTag}/edit',                          'TTrainingTagController@edit')->name('edit');
            Route::get('/{tTrainingTag}/copy',                          'TTrainingTagController@copy')->name('copy');
            Route::post('/bulk-destroy',                                'TTrainingTagController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{tTrainingTag}',                              'TTrainingTagController@update')->name('update');
            Route::get('/tagcount',                            'TTrainingTagController@tagcount')->name('tagcount');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('permissions')->name('permissions/')->group(static function () {
            Route::get('/',                                             'PermissionsController@index')->name('index');
            Route::get('/create',                                       'PermissionsController@create')->name('create');
            Route::post('/',                                            'PermissionsController@store')->name('store');
            Route::get('/{permission}/edit',                            'PermissionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'PermissionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{permission}',                                'PermissionsController@update')->name('update');
            Route::delete('/{permission}',                              'PermissionsController@destroy')->name('destroy');
        });
    });
});

/* Routes for dynamic-algorithm */
Route::get('get-dynamic-algorithms-master-nodes/{key}', [DynamicAlgorithmsController::class, 'getMasterNodes']);
Route::get('get-dynamic-algorithms-dependent-nodes/{key}/{masterNodeId}', [DynamicAlgorithmsController::class, 'getDependentNodes']);


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('countries')->name('countries/')->group(static function () {
            Route::get('/',                                             'CountryController@index')->name('index');
            Route::get('/create',                                       'CountryController@create')->name('create');
            Route::post('/',                                            'CountryController@store')->name('store');
            Route::get('/{country}/edit',                               'CountryController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'CountryController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{country}',                                   'CountryController@update')->name('update');
            Route::delete('/{country}',                                 'CountryController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('user-app-versions')->name('user-app-versions/')->group(static function () {
            Route::get('/',                                             'UserAppVersionController@index')->name('index');
            Route::get('/overall-app-version',                          'UserAppVersionController@overAllListing')->name('overAllListing');
            // Route::get('/create',                                       'UserAppVersionController@create')->name('create');
            // Route::post('/',                                            'UserAppVersionController@store')->name('store');
            // Route::get('/{userAppVersion}/edit',                        'UserAppVersionController@edit')->name('edit');
            // Route::post('/bulk-destroy',                                'UserAppVersionController@bulkDestroy')->name('bulk-destroy');
            // Route::post('/{userAppVersion}',                            'UserAppVersionController@update')->name('update');
            // Route::delete('/{userAppVersion}',                          'UserAppVersionController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('tours')->name('tours/')->group(static function () {
            Route::get('/',                                             'ToursController@index')->name('index');
            Route::get('/create',                                       'ToursController@create')->name('create');
            Route::post('/',                                            'ToursController@store')->name('store');
            Route::get('/{tour}/edit',                                  'ToursController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'ToursController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{tour}',                                      'ToursController@update')->name('update');
            Route::delete('/{tour}',                                    'ToursController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('tour-slides')->name('tour-slides/')->group(static function () {
            Route::get('/',                                             'TourSlidesController@index')->name('index');
            Route::get('/create',                                       'TourSlidesController@create')->name('create');
            Route::post('/',                                            'TourSlidesController@store')->name('store');
            Route::get('/{tourSlide}/edit',                             'TourSlidesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'TourSlidesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{tourSlide}',                                 'TourSlidesController@update')->name('update');
            Route::delete('/{tourSlide}',                               'TourSlidesController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('static-blogs')->name('static-blogs/')->group(static function () {
            Route::get('/',                                             'StaticBlogsController@index')->name('index');
            Route::get('/create',                                       'StaticBlogsController@create')->name('create');
            Route::post('/',                                            'StaticBlogsController@store')->name('store');
            Route::get('/{staticBlog}/edit',                            'StaticBlogsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'StaticBlogsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{staticBlog}',                                'StaticBlogsController@update')->name('update');
            Route::delete('/{staticBlog}',                              'StaticBlogsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('static-faqs')->name('static-faqs/')->group(static function () {
            Route::get('/',                                             'StaticFaqController@index')->name('index');
            Route::get('/create',                                       'StaticFaqController@create')->name('create');
            Route::post('/',                                            'StaticFaqController@store')->name('store');
            Route::get('/{staticFaq}/edit',                             'StaticFaqController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'StaticFaqController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{staticFaq}',                                 'StaticFaqController@update')->name('update');
            Route::delete('/{staticFaq}',                               'StaticFaqController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('static-app-configs')->name('static-app-configs/')->group(static function () {
            Route::get('/',                                             'StaticAppConfigController@index')->name('index');
            Route::get('/create',                                       'StaticAppConfigController@create')->name('create');
            Route::post('/',                                            'StaticAppConfigController@store')->name('store');
            Route::get('/{staticAppConfig}/edit',                       'StaticAppConfigController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'StaticAppConfigController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{staticAppConfig}',                           'StaticAppConfigController@update')->name('update');
            Route::delete('/{staticAppConfig}',                         'StaticAppConfigController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('key-features')->name('key-features/')->group(static function () {
            Route::get('/',                                             'KeyFeaturesController@index')->name('index');
            Route::get('/create',                                       'KeyFeaturesController@create')->name('create');
            Route::post('/',                                            'KeyFeaturesController@store')->name('store');
            Route::get('/{keyFeature}/edit',                            'KeyFeaturesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'KeyFeaturesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{keyFeature}',                                'KeyFeaturesController@update')->name('update');
            Route::delete('/{keyFeature}',                              'KeyFeaturesController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('static-testimonials')->name('static-testimonials/')->group(static function () {
            Route::get('/',                                             'StaticTestimonialsController@index')->name('index');
            Route::get('/create',                                       'StaticTestimonialsController@create')->name('create');
            Route::post('/',                                            'StaticTestimonialsController@store')->name('store');
            Route::get('/{staticTestimonial}/edit',                     'StaticTestimonialsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'StaticTestimonialsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{staticTestimonial}',                         'StaticTestimonialsController@update')->name('update');
            Route::delete('/{staticTestimonial}',                       'StaticTestimonialsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('static-releases')->name('static-releases/')->group(static function () {
            Route::get('/',                                             'StaticReleasesController@index')->name('index');
            Route::get('/create',                                       'StaticReleasesController@create')->name('create');
            Route::post('/',                                            'StaticReleasesController@store')->name('store');
            Route::get('/{staticRelease}/edit',                         'StaticReleasesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'StaticReleasesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{staticRelease}',                             'StaticReleasesController@update')->name('update');
            Route::delete('/{staticRelease}',                           'StaticReleasesController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('static-enquiries')->name('static-enquiries/')->group(static function () {
            Route::get('/',                                             'StaticEnquiriesController@index')->name('index');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('static-what-we-dos')->name('static-what-we-dos/')->group(static function () {
            Route::get('/',                                             'StaticWhatWeDoController@index')->name('index');
            Route::get('/create',                                       'StaticWhatWeDoController@create')->name('create');
            Route::post('/',                                            'StaticWhatWeDoController@store')->name('store');
            Route::get('/{staticWhatWeDo}/edit',                        'StaticWhatWeDoController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'StaticWhatWeDoController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{staticWhatWeDo}',                            'StaticWhatWeDoController@update')->name('update');
            Route::delete('/{staticWhatWeDo}',                          'StaticWhatWeDoController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('static-modules')->name('static-modules/')->group(static function () {
            Route::get('/',                                             'StaticModuleController@index')->name('index');
            Route::get('/create',                                       'StaticModuleController@create')->name('create');
            Route::post('/',                                            'StaticModuleController@store')->name('store');
            Route::get('/{staticModule}/edit',                          'StaticModuleController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'StaticModuleController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{staticModule}',                              'StaticModuleController@update')->name('update');
            Route::delete('/{staticModule}',                            'StaticModuleController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('static-resource-materials')->name('static-resource-materials/')->group(static function () {
            Route::get('/',                                             'StaticResourceMaterialsController@index')->name('index');
            Route::get('/create',                                       'StaticResourceMaterialsController@create')->name('create');
            Route::post('/',                                            'StaticResourceMaterialsController@store')->name('store');
            Route::get('/{staticResourceMaterial}/edit',                'StaticResourceMaterialsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'StaticResourceMaterialsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{staticResourceMaterial}',                    'StaticResourceMaterialsController@update')->name('update');
            Route::delete('/{staticResourceMaterial}',                  'StaticResourceMaterialsController@destroy')->name('destroy');
        });
    });
});
// Route::post('upload', 'CKEditorController@upload')->name('image-upload');

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('lb-levels')->name('lb-levels/')->group(static function () {
            Route::get('/',                                             'LbLevelsController@index')->name('index');
            Route::get('/create',                                       'LbLevelsController@create')->name('create');
            Route::post('/',                                            'LbLevelsController@store')->name('store');
            Route::get('/{lbLevel}/edit',                               'LbLevelsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'LbLevelsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{lbLevel}',                                   'LbLevelsController@update')->name('update');
            Route::delete('/{lbLevel}',                                 'LbLevelsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('lb-badges')->name('lb-badges/')->group(static function () {
            Route::get('/',                                             'LbBadgesController@index')->name('index');
            Route::get('/create',                                       'LbBadgesController@create')->name('create');
            Route::post('/',                                            'LbBadgesController@store')->name('store');
            Route::get('/{lbBadge}/edit',                               'LbBadgesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'LbBadgesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{lbBadge}',                                   'LbBadgesController@update')->name('update');
            Route::delete('/{lbBadge}',                                 'LbBadgesController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('lb-task-lists')->name('lb-task-lists/')->group(static function () {
            Route::get('/',                                             'LbTaskListsController@index')->name('index');
            Route::get('/create',                                       'LbTaskListsController@create')->name('create');
            Route::post('/',                                            'LbTaskListsController@store')->name('store');
            Route::get('/{lbTaskList}/edit',                            'LbTaskListsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'LbTaskListsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{lbTaskList}',                                'LbTaskListsController@update')->name('update');
            Route::delete('/{lbTaskList}',                              'LbTaskListsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('lb-subscriber-rankings')->name('lb-subscriber-rankings/')->group(static function () {
            Route::get('/',                                             'LbSubscriberRankingsController@index')->name('index');
            // Route::get('/create',                                       'LbSubscriberRankingsController@create')->name('create');
            // Route::post('/',                                            'LbSubscriberRankingsController@store')->name('store');
            // Route::get('/{lbSubscriberRanking}/edit',                            'LbSubscriberRankingsController@edit')->name('edit');
            // Route::post('/bulk-destroy',                                'LbSubscriberRankingsController@bulkDestroy')->name('bulk-destroy');
            // Route::post('/{lbSubscriberRanking}',                       'LbSubscriberRankingsController@update')->name('update');
            // Route::delete('/{lbSubscriberRanking}',                     'LbSubscriberRankingsController@destroy')->name('destroy');
            Route::get('/export',                                          'LbSubscriberRankingsController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('lb-subscriber-ranking-histories')->name('lb-subscriber-ranking-histories/')->group(static function () {
            Route::get('/',                                             'LbSubscriberRankingHistoryController@index')->name('index');
            // Route::get('/create',                                       'LbSubscriberRankingHistoryController@create')->name('create');
            // Route::post('/',                                            'LbSubscriberRankingHistoryController@store')->name('store');
            // Route::get('/{lbSubscriberRankingHistory}/edit',            'LbSubscriberRankingHistoryController@edit')->name('edit');
            // Route::post('/bulk-destroy',                                'LbSubscriberRankingHistoryController@bulkDestroy')->name('bulk-destroy');
            // Route::post('/{lbSubscriberRankingHistory}',                'LbSubscriberRankingHistoryController@update')->name('update');
            // Route::delete('/{lbSubscriberRankingHistory}',              'LbSubscriberRankingHistoryController@destroy')->name('destroy');
            Route::get('/export',                                          'LbSubscriberRankingHistoryController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('lb-sub-module-usages')->name('lb-sub-module-usages/')->group(static function () {
            Route::get('/',                                             'LbSubModuleUsagesController@index')->name('index');
            Route::get('/create',                                       'LbSubModuleUsagesController@create')->name('create');
            Route::post('/',                                            'LbSubModuleUsagesController@store')->name('store');
            Route::get('/{lbSubModuleUsage}/edit',                      'LbSubModuleUsagesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'LbSubModuleUsagesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{lbSubModuleUsage}',                          'LbSubModuleUsagesController@update')->name('update');
            Route::delete('/{lbSubModuleUsage}',                        'LbSubModuleUsagesController@destroy')->name('destroy');
        });
    });
});
// Route::post('upload', 'CKEditorController@upload')->name('image-upload');


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('user-feedback-questions')->name('user-feedback-questions/')->group(static function () {
            Route::get('/',                                             'UserFeedbackQuestionsController@index')->name('index');
            Route::get('/create',                                       'UserFeedbackQuestionsController@create')->name('create');
            Route::post('/',                                            'UserFeedbackQuestionsController@store')->name('store');
            Route::get('/{userFeedbackQuestion}/edit',                  'UserFeedbackQuestionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'UserFeedbackQuestionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{userFeedbackQuestion}',                      'UserFeedbackQuestionsController@update')->name('update');
            Route::delete('/{userFeedbackQuestion}',                    'UserFeedbackQuestionsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('user-feedback-details')->name('user-feedback-details/')->group(static function () {
            Route::get('/',                                             'UserFeedbackDetailsController@index')->name('index');
            Route::get('/create',                                       'UserFeedbackDetailsController@create')->name('create');
            Route::post('/',                                            'UserFeedbackDetailsController@store')->name('store');
            Route::get('/{userFeedbackDetail}/edit',                    'UserFeedbackDetailsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'UserFeedbackDetailsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{userFeedbackDetail}',                        'UserFeedbackDetailsController@update')->name('update');
            Route::delete('/{userFeedbackDetail}',                      'UserFeedbackDetailsController@destroy')->name('destroy');
            Route::get('/export',                                       'UserFeedbackDetailsController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('user-feedback-histories')->name('user-feedback-histories/')->group(static function () {
            Route::get('/',                                             'UserFeedbackHistoryController@index')->name('index');
            Route::get('/create',                                       'UserFeedbackHistoryController@create')->name('create');
            Route::post('/',                                            'UserFeedbackHistoryController@store')->name('store');
            Route::get('/{userFeedbackHistory}/edit',                   'UserFeedbackHistoryController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'UserFeedbackHistoryController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{userFeedbackHistory}',                       'UserFeedbackHistoryController@update')->name('update');
            Route::delete('/{userFeedbackHistory}',                     'UserFeedbackHistoryController@destroy')->name('destroy');
            Route::get('/export',                                       'UserFeedbackHistoryController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('flash-news')->name('flash-news/')->group(static function () {
            Route::get('/',                                             'FlashNewsController@index')->name('index');
            Route::get('/create',                                       'FlashNewsController@create')->name('create');
            Route::post('/',                                            'FlashNewsController@store')->name('store');
            Route::get('/{flashNews}/edit',                             'FlashNewsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'FlashNewsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{flashNews}/activeflag',                     'FlashNewsController@activeFlag')->name('active-flag');
            Route::post('/{flashNews}',                                 'FlashNewsController@update')->name('update');
            Route::delete('/{flashNews}',                               'FlashNewsController@destroy')->name('destroy');
            Route::get('/export',                                       'FlashNewsController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('flash-similar-apps')->name('flash-similar-apps/')->group(static function () {
            Route::get('/',                                             'FlashSimilarAppsController@index')->name('index');
            Route::get('/create',                                       'FlashSimilarAppsController@create')->name('create');
            Route::post('/',                                            'FlashSimilarAppsController@store')->name('store');
            Route::get('/{flashSimilarApp}/edit',                       'FlashSimilarAppsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'FlashSimilarAppsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{flashSimilarApp}',                           'FlashSimilarAppsController@update')->name('update');
            Route::delete('/{flashSimilarApp}',                         'FlashSimilarAppsController@destroy')->name('destroy');
            Route::get('/export',                                       'FlashSimilarAppsController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('assessment-enrollments')->name('assessment-enrollments/')->group(static function () {
            Route::get('/',                                             'AssessmentEnrollmentsController@index')->name('index');
            Route::get('/create',                                       'AssessmentEnrollmentsController@create')->name('create');
            Route::post('/',                                            'AssessmentEnrollmentsController@store')->name('store');
            Route::get('/{assessmentEnrollment}/edit',                  'AssessmentEnrollmentsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AssessmentEnrollmentsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{assessmentEnrollment}',                      'AssessmentEnrollmentsController@update')->name('update');
            Route::delete('/{assessmentEnrollment}',                    'AssessmentEnrollmentsController@destroy')->name('destroy');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('survey-masters')->name('survey-masters/')->group(static function () {
            Route::get('/',                                             'SurveyMasterController@index')->name('index');
            Route::get('/create',                                       'SurveyMasterController@create')->name('create');
            Route::post('/',                                            'SurveyMasterController@store')->name('store');
            Route::get('/{surveyMaster}/edit',                          'SurveyMasterController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'SurveyMasterController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{surveyMaster}',                              'SurveyMasterController@update')->name('update');
            Route::delete('/{surveyMaster}',                            'SurveyMasterController@destroy')->name('destroy');
            Route::get('/export',                                       'SurveyMasterController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('survey-master-questions')->name('survey-master-questions/')->group(static function () {
            Route::get('/',                                             'SurveyMasterQuestionsController@index')->name('index');
            Route::get('/create',                                       'SurveyMasterQuestionsController@create')->name('create');
            Route::post('/',                                            'SurveyMasterQuestionsController@store')->name('store');
            Route::get('/{surveyMasterQuestion}/edit',                  'SurveyMasterQuestionsController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'SurveyMasterQuestionsController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{surveyMasterQuestion}',                      'SurveyMasterQuestionsController@update')->name('update');
            Route::delete('/{surveyMasterQuestion}',                    'SurveyMasterQuestionsController@destroy')->name('destroy');
            Route::get('/export',                                       'SurveyMasterQuestionsController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('survey-master-histories')->name('survey-master-histories/')->group(static function () {
            Route::get('/',                                             'SurveyMasterHistoriesController@index')->name('index');
            Route::get('/create',                                       'SurveyMasterHistoriesController@create')->name('create');
            Route::post('/',                                            'SurveyMasterHistoriesController@store')->name('store');
            Route::get('/{surveyMasterHistory}/edit',                   'SurveyMasterHistoriesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'SurveyMasterHistoriesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{surveyMasterHistory}',                       'SurveyMasterHistoriesController@update')->name('update');
            Route::delete('/{surveyMasterHistory}',                     'SurveyMasterHistoriesController@destroy')->name('destroy');
            Route::get('/export',                                       'SurveyMasterHistoriesController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('automatic-notifications')->name('automatic-notifications/')->group(static function () {
            Route::get('/',                                             'AutomaticNotificationsController@index')->name('index');
            // Route::get('/create',                                       'AutomaticNotificationsController@create')->name('create');
            // Route::post('/',                                            'AutomaticNotificationsController@store')->name('store');
            // Route::get('/{automaticNotification}/edit',                 'AutomaticNotificationsController@edit')->name('edit');
            // Route::post('/bulk-destroy',                                'AutomaticNotificationsController@bulkDestroy')->name('bulk-destroy');
            // Route::post('/{automaticNotification}',                     'AutomaticNotificationsController@update')->name('update');
            // Route::delete('/{automaticNotification}',                   'AutomaticNotificationsController@destroy')->name('destroy');
            Route::get('/export',                                       'AutomaticNotificationsController@export')->name('export');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('activity-logs')->name('activity-logs/')->group(static function () {
            Route::get('/',                                             'ActivityLogController@index')->name('index');
            // Route::get('/create',                                       'ActivityLogController@create')->name('create');
            // Route::post('/',                                            'ActivityLogController@store')->name('store');
            // Route::get('/{activityLog}/edit',                           'ActivityLogController@edit')->name('edit');
            // Route::post('/bulk-destroy',                                'ActivityLogController@bulkDestroy')->name('bulk-destroy');
            // Route::post('/{activityLog}',                               'ActivityLogController@update')->name('update');
            // Route::delete('/{activityLog}',                             'ActivityLogController@destroy')->name('destroy');
        });
    });
});


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin', 'XSS'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function () {
        Route::prefix('assessment-certificates')->name('assessment-certificates/')->group(static function () {
            Route::get('/',                                             'AssessmentCertificatesController@index')->name('index');
            Route::get('/create',                                       'AssessmentCertificatesController@create')->name('create');
            Route::post('/',                                            'AssessmentCertificatesController@store')->name('store');
            Route::get('/{assessmentCertificate}/edit',                 'AssessmentCertificatesController@edit')->name('edit');
            Route::post('/bulk-destroy',                                'AssessmentCertificatesController@bulkDestroy')->name('bulk-destroy');
            Route::post('/{assessmentCertificate}',                     'AssessmentCertificatesController@update')->name('update');
            Route::delete('/{assessmentCertificate}',                   'AssessmentCertificatesController@destroy')->name('destroy');
        });
    });
});
