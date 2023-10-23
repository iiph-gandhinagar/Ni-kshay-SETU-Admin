<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Subscriber;
use App\Models\SurveyMaster;
use App\Models\SurveyMasterHistory;
use App\Models\SurveyMasterQuestion;
use Illuminate\Http\Request;
use Log;

class SurveyController extends BaseController
{
    public function getSurveyDetails(Request $request)
    {
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'state_id', 'cadre_id', 'country_id', 'district_id'])[0];
        $survey_master = SurveyMaster::where('active', 1)->whereRaw("find_in_set('" . $subscriber->cadre_id . "',cadre_id)")
            ->whereRaw("find_in_set('" . $subscriber->state_id . "',state_id)")
            ->orWhereRaw("find_in_set(country_id, ?)", [$subscriber->country_id])
            ->orWhereRaw("find_in_set(district_id, ?)", [$subscriber->district_id])
            ->get(['id', 'title']);
        $surveys = collect();
        foreach ($survey_master as $survey) {
            $exist_survey = SurveyMasterHistory::where('survey_id', $survey->id)->where('user_id', $subscriber['id'])->count();
            if ($exist_survey == 0) {
                $surveys->push($survey);
            }
        }
        // $done_survey_lists = SurveyMasterHistory::with(['survey_master'])->where('user_id',$subscriber)->get()->groupby('survey_master.title');
        $done_survey_lists = SurveyMaster::whereHas('survey_history', function ($query) use ($subscriber) {
            $query->where('user_id', $subscriber['id']);
        })->where('active', 1)->get();
        $final_surveys['survey_list'] = $surveys;
        $final_surveys['dont_survey_list'] = $done_survey_lists;
        $success = true;
        return ['status' => $success, 'data' => $final_surveys, 'code' => 200];
    }

    public function getSurveyQuestionsById(Request $request, $survey_id)
    {
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get(['id'])[0]->id;
        $get_count = SurveyMaster::where('id', $survey_id)->count();
        if ($get_count > 0) {
            $survey_questions = [];
        }
        $survey_questions = SurveyMasterQuestion::where('survey_master_id', $survey_id)->get(['id', 'question', 'option1', 'option2', 'option3', 'option4', 'survey_master_id', 'type']);
        $success = true;
        return ['status' => $success, 'data' => $survey_questions, 'code' => 200];
    }

    public function storeSurveyDetails(Request $request)
    {
        $newRequest = $request->all();
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get(['id'])[0]->id;
        $result = collect();
        collect($newRequest)->map(function ($item) use ($subscriber, $result) {
            $result->push(['survey_id' => $item['survey_id'], 'survey_question_id' => $item['survey_question_id'], 'answer' => $item['answer'], 'user_id' => $subscriber, 'created_at' => now(), 'updated_at' => now()]);
            return $result;
        });
        SurveyMasterHistory::insert($result->toArray());
        $success = true;
        return ['status' => $success, 'data' => "survey stored successfully!", 'code' => 200];
    }
}
