<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\LbSubscriberRanking;
use App\Models\Subscriber;
use App\Models\UserFeedbackDetail;
use App\Models\UserFeedbackHistory;
use App\Models\UserFeedbackQuestion;
use Illuminate\Http\Request;
use Log;
use Carbon\Carbon;
use Exception;
use DB;
// use Config;

class UserFeedbackController extends BaseController
{
    public function getFeedbackDetails(Request $request)
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
        //feedback_question_skip == 0 --->menu feedback questions and feedback_question_skip ==1 popup
        $user_id = Subscriber::where('api_token', $request->bearerToken())->get('id');
        $feedback_details = UserFeedbackQuestion::with(['media'])->where('is_active', 1)->get();
        $feedback_question = collect([]);
        foreach ($feedback_details as $feedback) {
            if ($feedback->feedback_type == "no_repeat") {
                $feedback_response = UserFeedbackHistory::where('subscriber_id', $user_id[0]['id'])->where('feedback_id', $feedback->id)->orderBy('created_at', 'desc')->limit(1)->get();
                if (count($feedback_response) > 0) {

                    if (count($feedback_response) > 0 && $feedback_response[0]['skip'] == 1) {
                        if (isset($request['feedback_question_skip']) && $request['feedback_question_skip'] == 0) {
                            $feedback_question->push($feedback);
                        } else {
                            if (date('Y-m-d', strtotime($feedback_response[0]['created_at'])) != date('Y-m-d')) {
                                $feedback_question->push($feedback);
                            }
                        }
                    }
                } else {
                    $feedback_question->push($feedback);
                }
            } else if ($feedback->feedback_type == "repeat") {
                $feedback_response = UserFeedbackHistory::where('subscriber_id', $user_id[0]['id'])->where('feedback_id', $feedback->id)->orderBy('created_at', 'desc')->limit(1)->get();
                if (count($feedback_response) > 0) {
                    if (date('Y-m-d', strtotime($feedback_response[0]['created_at'])) <= Carbon::now()->subDays($feedback['feedback_days'])) {
                        $feedback_question->push($feedback);
                    } else if (date('Y-m-d', strtotime($feedback_response[0]['created_at'])) >= Carbon::now()->subDays($feedback['feedback_days']) && $feedback_response[0]['skip'] == 1) { // && date('Y-m-d', strtotime($feedback_response[0]['created_at'])) != date('Y-m-d')
                        if (isset($request['feedback_question_skip']) && $request['feedback_question_skip'] == 0) {
                            $feedback_question->push($feedback);
                        } else {
                            if (date('Y-m-d', strtotime($feedback_response[0]['created_at'])) != date('Y-m-d')) {
                                $feedback_question->push($feedback);
                            }
                        }
                        // $feedback_question->push($feedback);
                    }
                } else {
                    $feedback_question->push($feedback);
                }
            }
        }

        $final_feedback_questions = collect([]);
        $count = LbSubscriberRanking::where('subscriber_id', $user_id[0]['id'])->get(['App_opended_count', 'chatbot_usage_count']);
        $app_count_criteria = 0;
        $module_count_criteria = 0;
        $module_time_criteria = 0;
        $chatbot_count_criteria = 0;
        foreach ($feedback_question as $fq) {
            if ($fq->feedback_question == "User Inteface") {
                $app_count_criteria = $fq->feedback_value;
            } else if ($fq->feedback_question == "Module Content") {
                $module_count_criteria = $fq->feedback_value;
                $module_count_criteria = $fq->feedback_value;
            } else if ($fq->feedback_question == "Chatbot") {
                $chatbot_count_criteria = $fq->feedback_value;
            }
        }
        $subscriber_id = $user_id[0]['id'];
        $get_module_usage = DB::select("SELECT module_id,sum(mins_spent) FROM `lb_sub_module_usages` where subscriber_id = $subscriber_id GROUP BY module_id HAVING sum(mins_spent)/60 > $module_time_criteria");
        // $module = 4;

        if ($count[0]['App_opended_count'] >= $app_count_criteria && $count[0]['chatbot_usage_count'] >= $chatbot_count_criteria && count($get_module_usage) >= $module_count_criteria) {
            $success = true;
            return ['status' => $success, 'data' => $feedback_question, 'code' => 200];
        } else {
            $success = true;
            return ['status' => $success, 'data' => [], 'code' => 200];
        }
    }

    public function storeFeedback(Request $request)
    {
        $user_id = Subscriber::where('api_token', $request->bearerToken())->get(['id']);
        $feedback_response = $request->all();
        foreach ($feedback_response['payload']['ratings'] as $response) {

            $newRequest['subscriber_id'] = $user_id[0]['id'];
            $newRequest['feedback_id'] = $response['id'];
            $newRequest['ratings'] = $response['rating'];
            $newRequest['review'] = isset($feedback_response['payload']['review']) ? (string) $feedback_response['payload']['review'] : "";
            $newRequest['skip'] = $response['skip'];
            $find = UserFeedbackDetail::where('subscriber_id', $user_id[0]['id'])->count();
            try {
                if ($find > 0) {
                    UserFeedbackDetail::where('subscriber_id', $user_id[0]['id'])->update(['feedback_id' => $response['id'], 'ratings' => $response['rating'], 'review' => isset($feedback_response['payload']['review']) ? (string) $feedback_response['payload']['review'] : '']);
                    UserFeedbackHistory::create($newRequest);
                } else {

                    UserFeedbackDetail::create($newRequest);
                    UserFeedbackHistory::create($newRequest);
                }
            } catch (Exception $e) {
                Log::error("error --->" . $e);
                $success = false;
                return ['status' => $success, 'data' => "Issue", 'code' => 400];
            }
        }

        $success = true;
        return ['status' => $success, 'data' => "Thank You For Your Response", 'code' => 200];
    }
}
