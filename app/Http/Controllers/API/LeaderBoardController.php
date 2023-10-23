<?php

namespace App\Http\Controllers\API;

use App\Helpers\RequestHelpers;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\CaseDefinition;
use App\Models\CgcInterventionsAlgorithm;
use App\Models\DiagnosesAlgorithm;
use App\Models\DifferentialCareAlgorithm;
use App\Models\GuidanceOnAdverseDrugReaction;
use App\Models\LatentTbInfection;
use App\Models\LbBadge;
use App\Models\LbLevel;
use App\Models\LbSubModuleUsage;
use App\Models\LbSubscriberRanking;
use App\Models\LbSubscriberRankingHistory;
use App\Models\LbTaskList;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Models\TreatmentAlgorithm;
use App\Models\UserAssessment;
use Log;
use Config;

class LeaderBoardController extends BaseController
{
    public function leaderBoardEntry(Request $request) //script for existing user data entry not general api
    {
        // $lang = $request->header('lang');

        // if($lang == NULL){
        //     $lang = 'en';
        // }
        // app()->setLocale($lang);

        $subscriber = Subscriber::get(['id', 'name']);
        foreach ($subscriber as $user) {
            $newRequest['subscriber_id'] = $user->id;
            $newRequest['level_id'] = 1;
            $newRequest['badge_id'] = 2;
            $newRequest['sub_module_usage_count'] = 1;
            $newRequest['mins_spent_count'] = 0;
            $newRequest['App_opended_count'] = 2;
            $newRequest['chatbot_usage_count'] = 0;
            $newRequest['resource_material_accessed_count'] = 0;
            $newRequest['total_task_count'] = 2;
            $ranking = LbSubscriberRanking::create($newRequest);


            $processing_request['subscriber_id'] = $user->id;
            $processing_request['lb_subscriber_rankings_id'] = $ranking->id;
            $processing_request['level_id'] = 1;
            $processing_request['badge_id'] = 1;
            $processing_request['sub_module_usage_count'] = 1;
            $processing_request['mins_spent_count'] = 0;
            $processing_request['App_opended_count'] = 2;
            $processing_request['chatbot_usage_count'] = 0;
            $processing_request['resource_material_accessed_count'] = 0;
            LbSubscriberRankingHistory::create($processing_request);
        }

        $success = true;
        return ['status' => $success, 'data' => 'Store leader board data successfully', 'code' => 200];
    }

    public function leaderBoardInformation(Request $request)
    {
        $paginationParams = RequestHelpers::getPaginationParams($request);
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }

        app()->setLocale($lang);

        // $newRequest = $request->all();
        $user = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id'])[0];
        // $subscriber = Subscriber::with(['media'])->where('id', '!=', $user->id)->where('cadre_id',$user->cadre_id)->with(['cadre'])->orderBy("created_at", "desc")->get();
        $lbTaskList = LbTaskList::sum('total_task');
        $subscribers = LbSubscriberRanking::with(['lb_level', 'lb_badge', 'user.cadre', 'user.media'])
            ->leftJoin('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
            ->where('subscribers.cadre_id', $user->cadre_id)
            ->orderBy('total_task_count', 'desc')
            ->paginate($paginationParams['per_page']);

        // ->get(['lb_subscriber_rankings.id','level_id','badge_id','total_task_count','subscribers.id',DB::raw("(total_task_count * 100)/$lbTaskList as count_data")]);
        foreach ($subscribers as $user) {
            // $leaderboard_detail = LbSubscriberRanking::with(['lb_level', 'lb_badge'])->where('subscriber_id', $user->id)->get();
            if (isset($user['user']['media']) && isset($user['user']['media'][0])) {
                $user['user']['media'][0] = isset($user['user']['media'][0]) ? [
                    "origin" => $user['user']['media'][0],
                    "thumb_60" => $user['user']['media'][0]->hasGeneratedConversion('thumb_60') ?  $user['user']['media'][0]->getPath('thumb_60') : '',
                    "thumb_100" => $user['user']['media'][0]->hasGeneratedConversion('thumb_100') ? $user['user']['media'][0]->getPath('thumb_100') : '',
                ] : [];
            }
            $user['percentage'] = $user->total_task_count * 100 / $lbTaskList;
            // if (isset($leaderboard_detail) && count($leaderboard_detail) > 0) {
            //     // $user['level'] = $leaderboard_detail[0]['lb_level']['level'];
            //     $user->percentage = $subscribers->total_task_count * 100 / $lbTaskList;
            // } else {
            //     // $user['level'] = "Begineer";
            //     $user['percentage'] = 0;
            // }
        }
        $success = true;
        return ['status' => $success, 'data' => $subscribers, 'code' => 200];
    }

    public function leaderBoardTasks(Request $request)
    {
        // $lang = $request->header('lang');

        // if($lang == NULL){
        //     $lang = 'en';
        // }

        // app()->setLocale($lang);

        // if (Cache::has('db_config')) {
        //     $dbConfig = Cache::get('db_config');

        // } else {

        //     $dbConfig = AppConfig::all();
        //     Cache::put('db_config', $dbConfig, Config::get('app.GENERAL.app_config_cache_time_out'));
        // }
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get();
        $lb_subscriber_ranking = LbSubscriberRanking::with(['lb_task_list'])->where('subscriber_id', $subscriber[0]['id'])->get();
        $newRequest['total_task_completed'] = $lb_subscriber_ranking[0]['total_task_count'];
        $newRequest['total_tasks'] = LbTaskList::sum('total_task');
        $newRequest['total_task_pending'] = $newRequest['total_tasks'] - $newRequest['total_task_completed'];
        $newRequest['achive_badge_details'] = LbSubscriberRankingHistory::with(['lb_level', 'lb_badge', 'lb_task_list'])->where('subscriber_id', $subscriber[0]['id'])->get()->groupBy('lb_level.level');
        $newRequest['current_badge_details'] = $lb_subscriber_ranking;
        $newRequest['task_list_data'] = LbTaskList::with(['lb_level', 'lb_badge'])
            ->orderByRaw("FIELD(level , '1','2','3','4','5') ASC")
            ->get()
            ->groupby('lb_level.level');

        $success = true;
        return ['status' => $success, 'data' => $newRequest, 'code' => 200];
    }

    public function leaderBoardAchivements(Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }

        app()->setLocale($lang);
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get();
        $lb_subscriber_ranking = LbSubscriberRanking::with(['lb_level', 'lb_badge'])->where('subscriber_id', $subscriber[0]['id'])->get();
        $newRequest['level'] = isset($lb_subscriber_ranking) && $lb_subscriber_ranking[0]['level_id'] < 6  ? $lb_subscriber_ranking[0]['lb_level']['level'] : "Expert Level";
        $newRequest['total_bronze_medal'] = LbBadge::where('badge', 'like', '%Bronze%')->count();
        $newRequest['achive_bronze_medal'] = LbSubscriberRankingHistory::where('subscriber_id', $subscriber[0]['id'])->with(['lb_level', 'lb_badge' => function ($query) {
            $query->where('badge', 'like', '%Bronze%');
        }])->whereHas('lb_badge', function ($query) {
            $query->where('badge', 'like', '%Bronze%');
        })->count();

        $newRequest['total_silver_medal'] = LbBadge::where('badge', 'like', '%Silver%')->count();
        $newRequest['achive_silver_medal'] = LbSubscriberRankingHistory::where('subscriber_id', $subscriber[0]['id'])->with(['lb_level', 'lb_badge' => function ($query) {
            $query->where('badge', 'like', '%Silver%');
        }])->whereHas('lb_badge', function ($query) {
            $query->where('badge', 'like', '%Silver%');
        })->count();

        $newRequest['total_gold_medal'] = LbBadge::where('badge', 'like', '%Gold%')->count();
        $newRequest['achive_gold_medal'] = LbSubscriberRankingHistory::where('subscriber_id', $subscriber[0]['id'])->with(['lb_level', 'lb_badge' => function ($query) {
            $query->where('badge', 'like', '%Gold%');
        }])->whereHas('lb_badge', function ($query) {
            $query->where('badge', 'like', '%Gold%');
        })->count();

        $newRequest['assessment_completion'] = UserAssessment::with(['assessment_with_trashed'])->where('user_id', $subscriber[0]['id'])->count();

        $success = true;
        return ['status' => $success, 'data' => $newRequest, 'code' => 200];
    }

    public function leaderBoardOverview(Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }

        app()->setLocale($lang);

        // if (Cache::has('db_config')) {
        //     $dbConfig = Cache::get('db_config');

        // } else {

        //     $dbConfig = AppConfig::all();
        //     Cache::put('db_config', $dbConfig, Config::get('app.GENERAL.app_config_cache_time_out'));
        // }
        $level = LbLevel::get();
        $success = true;
        return ['status' => $success, 'data' => $level, 'code' => 200];
    }

    public function storeSubModule(Request $request)
    { /* LbSubModuleUsageObserver used to update complete flag */
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get();
        $raw_data = $request->all();
        $return_ids = collect([]);
        foreach ($raw_data as $data) {
            // $return_ids = array_push($return_ids,$data['id']);
            $return_ids->push($data['id']);
            if ($request['activity_type'] != "app_usage") {
                $exist_record = LbSubModuleUsage::where('subscriber_id', $subscriber[0]['id'])->where('module_id', $data['module'])->where('sub_module', $data['sub_module_id'])->get();
                $app_usage = LbSubscriberRanking::where('subscriber_id', $subscriber[0]['id'])->get();
                if (count($exist_record) > 0) {
                    $modelId = LbSubModuleUsage::where('subscriber_id', $subscriber[0]['id'])->where('module_id', $data['module'])->where('sub_module', $data['sub_module_id'])->get(['id']);
                    if (isset($modelId[0]) && $modelId[0]['id']) {
                        $model = LbSubModuleUsage::findOrFail($modelId[0]['id']);
                        if ($model) {
                            $model->update(['mins_spent' => $exist_record[0]['mins_spent'] + $data['time']]);
                        }
                        LbSubscriberRanking::where('subscriber_id', $subscriber[0]['id'])->update(['mins_spent_count' => $app_usage[0]['mins_spent_count'] + $data['time']]);
                    }
                } else {
                    $total_time = 0;
                    if ($data['module'] == "Case Definition") {
                        $total_time = CaseDefinition::where('id', $data['sub_module_id'])->get(['time_spent'])[0]->time_spent;
                    } elseif ($data['module'] == "Diagnosis Algorithm") {
                        $total_time = DiagnosesAlgorithm::where('id', $data['sub_module_id'])->get(['time_spent'])[0]->time_spent;
                    } elseif ($data['module'] == "Guidance on ADR") {
                        $total_time = GuidanceOnAdverseDrugReaction::where('id', $data['sub_module_id'])->get(['time_spent'])[0]->time_spent;
                    } elseif ($data['module'] == "Treatment Algorithm") {
                        $total_time = TreatmentAlgorithm::where('id', $data['sub_module_id'])->get(['time_spent'])[0]->time_spent;
                    } elseif ($data['module'] == "Latent TB Infection") {
                        $total_time = LatentTbInfection::where('id', $data['sub_module_id'])->get(['time_spent'])[0]->time_spent;
                    } elseif ($data['module'] == "Differentiated Care Of TB Patients") {
                        $total_time = DifferentialCareAlgorithm::where('id', $data['sub_module_id'])->get(['time_spent'])[0]->time_spent;
                    } elseif ($data['module'] == "NTEP Intervention") {
                        $total_time = CgcInterventionsAlgorithm::where('id', $data['sub_module_id'])->get(['time_spent'])[0]->time_spent;
                    }

                    $newRequest['subscriber_id'] = $subscriber[0]['id'];
                    $newRequest['module_id'] = $data['module'];
                    $newRequest['sub_module'] = $data['sub_module_id'];
                    $newRequest['total_time'] = $total_time;
                    $newRequest['mins_spent'] = $data['time'];
                    $newRequest['completed_flag'] = 0;
                    LbSubModuleUsage::create($newRequest);
                    LbSubscriberRanking::where('subscriber_id', $subscriber[0]['id'])->update(['mins_spent_count' => $app_usage[0]['mins_spent_count'] + $data['time']]);
                }
            } else {
                $exist_record = LbSubModuleUsage::where('subscriber_id', $subscriber[0]['id'])->where('module_id', $data['module'])->get();
                $mins_spent_count = LbSubscriberRanking::where('subscriber_id', $subscriber[0]['id'])->get();
                if (count($exist_record) > 0) {
                    $modelId = LbSubModuleUsage::where('subscriber_id', $subscriber[0]['id'])->where('module_id', $data['module'])->get(['id']);
                    if (isset($modelId[0]) && $modelId[0]['id']) {
                        $model = LbSubModuleUsage::findOrFail($modelId[0]['id']);
                        if ($model) {
                            $model->update(['mins_spent' => $exist_record[0]['mins_spent'] + $data['time']]);
                        }
                        LbSubscriberRanking::where('subscriber_id', $subscriber[0]['id'])->update(['mins_spent_count' => $mins_spent_count[0]['mins_spent_count'] + $data['time']]);
                    }
                } else {
                    $newRequest['subscriber_id'] = $subscriber[0]['id'];
                    $newRequest['module_id'] = $data['module'];
                    $newRequest['sub_module'] = $data['sub_module_id'];
                    $newRequest['total_time'] = 0;
                    $newRequest['mins_spent'] = $data['time'];
                    $newRequest['completed_flag'] = 1;
                    LbSubModuleUsage::create($newRequest);
                    LbSubscriberRanking::where('subscriber_id', $subscriber[0]['id'])->update(['mins_spent_count' => $mins_spent_count[0]['mins_spent_count'] + $data['time']]);
                }
            }
        }

        $success = true;
        return ['status' => $success, 'data' => $return_ids, 'code' => 200];
    }
}
