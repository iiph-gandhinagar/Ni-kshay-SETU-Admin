<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\AssessmentEnrollment;
use App\Models\AssessmentQuestion;
use App\Models\UserAssessment;
use App\Models\Subscriber;
use Log;
use Validator;

class AssessmentController extends BaseController
{
    public function getAllAssessment(Request $request)
    {
        $lang = $request->header('lang');
        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);

        $currentSubscriber = Subscriber::where('api_token', $request->bearerToken())->get(['cadre_id', 'id', 'district_id', 'state_id', 'country_id'])[0];
        // $assessment = Assessment::where('activated',1)->withCount(['user_assessment_result' => function($q) use($currentSubscriber){
        //                             $q->where('user_id',$currentSubscriber['id']);
        //                         },'assessment_questions'])->with(['user_assessment_result' => function($q) use($currentSubscriber){
        //                             $q->where('user_id',$currentSubscriber['id']);
        //                         }])
        //                         ->whereRaw("find_in_set('".$currentSubscriber['cadre_id']."',cadre_id)")
        //                         ->get();
        if ($currentSubscriber['country_id'] != 0) {
            $assessment = Assessment::where('activated', 1)->withCount(['user_assessment_result' => function ($q) use ($currentSubscriber) {
                $q->where('user_id', $currentSubscriber['id']);
            }, 'assessment_questions'])->with(['user_assessment_result' => function ($q) use ($currentSubscriber) {
                $q->where('user_id', $currentSubscriber['id']);
            }])
                ->whereRaw("find_in_set('" . $currentSubscriber['cadre_id'] . "',cadre_id)")
                ->whereRaw("find_in_set('" . $currentSubscriber['country_id'] . "',country_id)")
                ->get();
        } elseif ($currentSubscriber['district_id'] != 0) {
            $assessment = Assessment::where('activated', 1)->withCount(['user_assessment_result' => function ($q) use ($currentSubscriber) {
                $q->where('user_id', $currentSubscriber['id']);
            }, 'assessment_questions'])->with(['user_assessment_result' => function ($q) use ($currentSubscriber) {
                $q->where('user_id', $currentSubscriber['id']);
            }])
                ->whereRaw("find_in_set('" . $currentSubscriber['cadre_id'] . "',cadre_id)")
                ->whereRaw("find_in_set('" . $currentSubscriber['district_id'] . "',district_id)")
                ->get();
        } else {
            $assessment = Assessment::where('activated', 1)->withCount(['user_assessment_result' => function ($q) use ($currentSubscriber) {
                $q->where('user_id', $currentSubscriber['id']);
            }, 'assessment_questions'])->with(['user_assessment_result' => function ($q) use ($currentSubscriber) {
                $q->where('user_id', $currentSubscriber['id']);
            }])
                ->whereRaw("find_in_set('" . $currentSubscriber['cadre_id'] . "',cadre_id)")
                ->whereRaw("find_in_set('" . $currentSubscriber['state_id'] . "',state_id)")
                ->get();
        }
        $success = true;
        return ['status' => $success, 'data' => $assessment, 'code' => 200];
    }

    public function getPastAssessment(Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $ids = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id']);

        $assessment = UserAssessment::where('user_id', $ids[0]['id'])
            ->with(['assessment_with_trashed'])
            ->where('is_calculated', 1)
            ->where('created_at', '<', date('Y-m-d H:i:s'))
            ->distinct()
            ->get(['assessment_id', 'obtained_marks', 'created_at']);
        foreach ($assessment as $key => $ass) {

            $assessment[$key]['assessment_question_count'] = AssessmentQuestion::where('assessment_id', $ass->assessment_id)->count();
        }
        $success = true;
        return ['status' => $success, 'data' => $assessment, 'code' => 200];
    }

    public function getFutureAssessments(Request $request)
    {
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'state_id', 'cadre_id', 'district_id', 'country_id'])[0];
        $assessment = collect([]);
        if ($subscriber['country_id'] != 0) {

            $assessment = Assessment::where('activated', 1)->withCount(['assessment_questions'])->where('assessment_type', 'planned')
                ->where('from_date', '>=', date('Y-m-d H:i:s'))
                ->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
                ->whereRaw("find_in_set('" . $subscriber['country_id'] . "',country_id)")
                ->get(['id', 'time_to_complete', 'assessment_title', 'assessment_type', 'activated']);
        } elseif ($subscriber['district_id'] != 0) {

            $assessment = Assessment::where('activated', 1)->withCount(['assessment_questions'])->where('assessment_type', 'planned')
                ->where('from_date', '>=', date('Y-m-d H:i:s'))
                ->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
                ->whereRaw("find_in_set('" . $subscriber['district_id'] . "',district_id)")
                // ->toSql();
                ->get(['id', 'time_to_complete', 'assessment_title', 'assessment_type', 'activated']);
        } else {

            $assessment = Assessment::where('activated', 1)->withCount(['assessment_questions'])->where('assessment_type', 'planned')
                ->where('from_date', '>=', date('Y-m-d H:i:s'))
                ->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
                ->whereRaw("find_in_set('" . $subscriber['state_id'] . "',state_id)")
                ->get(['id', 'time_to_complete', 'assessment_title', 'assessment_type', 'activated']);
        }

        foreach ($assessment as $key => $ass) {
            $enrollnment_response = AssessmentEnrollment::where('assessment_id', $ass->id)->where('user_id', $subscriber['id'])->get(['response'])[0];
            $assessment[$key]['response'] = $enrollnment_response['response'];
        }
        $success = true;
        return ['status' => $success, 'data' => $assessment, 'code' => 200];
    }

    public function getAssessmentPerformance(Request $request)
    {
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'state_id', 'cadre_id', 'district_id', 'country_id'])[0];
        $assessment = collect([]);
        // $planned_assessment = collect([]);
        if ($subscriber['country_id'] != 0) {
            $assessment = Assessment::where('activated', 1)->where('assessment_type', 'real_time')
                ->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
                ->whereRaw("find_in_set('" . $subscriber['country_id'] . "',country_id)")
                ->get(['id', 'time_to_complete', 'assessment_title', 'assessment_type', 'activated']);

            $planned_assessment = Assessment::where('activated', 1)->where('assessment_type', 'planned')
                ->whereDate('to_date', '<=', date('Y-m-d H:i:s'))
                ->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
                ->whereRaw("find_in_set('" . $subscriber['country_id'] . "',country_id)")
                ->get(['id', 'time_to_complete', 'assessment_title', 'assessment_type', 'activated']);
        } elseif ($subscriber['district_id'] != 0) {
            $assessment = Assessment::where('activated', 1)->where('assessment_type', 'real_time')
                ->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
                ->whereRaw("find_in_set('" . $subscriber['district_id'] . "',district_id)")
                ->get(['id', 'time_to_complete', 'assessment_title', 'assessment_type', 'activated']);

            $planned_assessment = Assessment::where('activated', 1)->where('assessment_type', 'planned')
                ->whereDate('to_date', '<=', date('Y-m-d H:i:s'))
                ->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
                ->whereRaw("find_in_set('" . $subscriber['district_id'] . "',district_id)")
                // ->toSql();
                ->get(['id', 'time_to_complete', 'assessment_title', 'assessment_type', 'activated']);
        } else {
            $assessment = Assessment::where('activated', 1)->where('assessment_type', 'real_time')
                ->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
                ->whereRaw("find_in_set('" . $subscriber['state_id'] . "',state_id)")
                ->get(['id', 'time_to_complete', 'assessment_title', 'assessment_type', 'activated']);

            $planned_assessment = Assessment::where('activated', 1)->where('assessment_type', 'planned')
                ->whereDate('to_date', '<=', date('Y-m-d H:i:s'))
                ->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
                ->whereRaw("find_in_set('" . $subscriber['state_id'] . "',state_id)")
                ->get(['id', 'time_to_complete', 'assessment_title', 'assessment_type', 'activated']);
        }

        // $assessment->push($planned_assessment);
        $assessment = array_merge($assessment->toArray(), $planned_assessment->toArray());
        $complete_assessment = UserAssessment::where('user_id', $subscriber['id'])->whereIn('assessment_id', collect($assessment)->pluck('id'))->get();
        $sum = 0;
        foreach ($complete_assessment as $complete) {
            $sum = $sum + ($complete->obtained_marks / $complete->total_marks);
        }
        if (isset($sum) && $sum > 0) {
            $avrage = round(($sum / collect($assessment)->count()) * 100, 2);
        } else {
            $avrage = 0;
        }


        $newRequest['total_assessment_count'] = collect($assessment)->count();
        $newRequest['complete_assessment'] = count($complete_assessment);
        $newRequest['accuracy'] = $avrage;
        $newRequest['certificate'] = UserAssessment::with(['assessment_with_trashed'])->where('user_id', $subscriber['id'])->count();

        $success = true;
        return ['status' => $success, 'data' => $newRequest, 'code' => 200];
    }

    public function storeAssessmentEnrollnment(Request $request)
    {
        $newRequest = $request->all();

        $rules = [
            'assessment_id' => 'required',
            'response' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {
            $id = Subscriber::where('api_token', $request->bearerToken())->get(['id'])[0]->id;
            $assessmentEnroll = AssessmentEnrollment::where('assessment_id', $request['assessment_id'])->where('user_id', $id)->update(['response' => $request['response']]);
            $success = true;
            return ['status' => $success, 'data' => "Your response stored successfully!!", 'code' => 200];
        }
    }
}
