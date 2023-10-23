<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\AssessmentQuestion;
use App\Models\Subscriber;
use App\Models\UserAssessment;
use App\Models\UserAssessmentAnswer;
use App\Models\Assessment;
use Validator;

class UserAssessmentResultController extends BaseController
{
    public function store(Request $request)
    {
        // DB::info($request->all());
        $newRequest = $request->all();
        $rules = [
            'assessment_id' => 'required|gt:0|integer',
            'answers' => 'required|array',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {
            $user_id = Subscriber::where('api_token', $request->bearerToken())->get('id');

            $answer = AssessmentQuestion::where('assessment_id', $request['assessment_id'])
                ->where('id', $request['answers'][0]['question_id'])
                ->get(['correct_answer']);
            $newRequest['assessment_id'] = $request['assessment_id'];
            $newRequest['user_id'] = $user_id[0]['id'];
            $newRequest['question_id'] = $request['answers'][0]['question_id'];
            $newRequest['answer'] = $request['answers'][0]['answer'];
            $newRequest['is_correct'] = 0;
            $newRequest['is_submit'] = $request['answers'][0]['is_submit'];

            if (isset($answer[0]['correct_answer'])) {
                if ($answer[0]['correct_answer'] == $request['answers'][0]['answer']) {

                    $newRequest['is_correct'] = 1;
                }
            } else {
                $success = false;
                return ['status' => $success, 'data' => "not valid question id", 'code' => 400];
            }
            UserAssessmentAnswer::create($newRequest);
            $success = true;
            return ['status' => $success, 'data' => "Your Answer is Saved", 'code' => 200];
        }
    }

    public function getUserResult(Request $request, $assessment_id)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
            app()->setLocale($lang);

            $userId = Subscriber::where('api_token', $request->bearerToken())->get('id');
            $userAssessment = UserAssessment::where('user_id', $userId[0]['id'])->where('assessment_id', $assessment_id)->where('is_calculated', 0)->get();

            if (count($userAssessment) > 0) {
                $this->completeAssessment($userId[0]['id'], $assessment_id);
            }

            $userAssessment = Assessment::with(['user_assessment_result' => function ($q) use ($userId) {

                $q->where('user_id', $userId[0]['id']);
                $q->orderBy('created_at', 'DESC')->first();
            }])->where('id', $assessment_id)->withCount(['assessment_questions'])->withTrashed()->get();

            $success = true;
            return ['status' => $success, 'data' => $userAssessment, 'code' => 200];
        }
    }

    public function getSubscriberAssessmentDetails(Request $request)
    {
        $rules = [
            'assessment_id' => 'required|gt:0|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {
            $user_id = Subscriber::where('api_token', $request->bearerToken())->get('id');
            $is_available = UserAssessment::where('user_id', $user_id[0]['id'])->where('assessment_id', $request['assessment_id'])->get();
            $attemptedQuestions = UserAssessmentAnswer::where('user_id', $user_id[0]['id'])->where(
                'assessment_id',
                $request['assessment_id']
            )->get(['question_id', 'answer', 'is_submit']);

            $total_time = Assessment::where('id', $request['assessment_id'])
                ->withCount(['assessment_questions'])
                ->get(['time_to_complete'])[0];

            if (count($is_available) > 0) {
                $data =  Date("Y-m-d H:i:s", strtotime($total_time['time_to_complete'] . "min", strtotime($is_available[0]['created_at'])));

                if ($is_available[0]['is_calculated'] == 1) {
                    $is_assessment_attempted = 1;
                    $is_assessment_expired = 1;
                    $remaining_time = 0;
                } else {
                    if ($data < date("Y-m-d H:i:s")) {
                        $is_assessment_attempted = 1;
                        $is_assessment_expired = 1;
                        $remaining_time = 0;
                    } else {
                        $is_assessment_attempted = 0;
                        $is_assessment_expired = 0;
                        $remaining_time = (int)((strtotime($data) - strtotime(date('H:i:s'))) / 60 * 60);
                    }
                }
            } else {
                UserAssessment::create(['assessment_id' => $request['assessment_id'], 'user_id' => $user_id[0]['id'], 'total_time' => $total_time['time_to_complete'], 'total_marks' => $total_time['assessment_questions_count']]);
                $is_assessment_attempted = 0;
                $is_assessment_expired = 0;
                $remaining_time = ($total_time['time_to_complete']) * 60;
            }
            $result = new \stdClass;
            $result->is_assessment_attempted = $is_assessment_attempted;
            $result->is_assessment_expired = $is_assessment_expired;
            $result->remaining_time = $remaining_time;
            $result->answers = $attemptedQuestions;

            $success = true;
            return ['status' => $success, 'data' => $result, 'code' => 200];
        }
    }

    public function completeAssessment($userId, $assessment_id)
    {
        $userAssessmentResult = UserAssessmentAnswer::where('user_id', $userId)
            ->where('assessment_id', $assessment_id)
            ->get();

        $totalMarks = UserAssessment::where('assessment_id', $assessment_id)->where('user_id', $userId)->get(['total_marks', 'total_time', 'created_at']);
        if (isset($totalMarks) && count($totalMarks) > 0) {

            $data =  Date("Y-m-d H:i:s", strtotime($totalMarks[0]['total_time'] . "min", strtotime($totalMarks[0]['created_at'])));

            $is_correct = 0;
            $wrongAnswer = 0;
            // if ($data < date("Y-m-d H:i:s")) {
            for ($i = 0; $i < count($userAssessmentResult); $i++) {
                if ($userAssessmentResult[$i]['is_correct'] == 1) {
                    $is_correct = $is_correct + 1;
                }
                if ($userAssessmentResult[$i]['is_correct'] == 0 && $userAssessmentResult[$i]['answer'] != "null") {
                    $wrongAnswer = $wrongAnswer + 1;
                }
            }
            $newRequest['obtained_marks'] = $is_correct;
            $newRequest['attempted'] = $is_correct + $wrongAnswer;
            $newRequest['right_answers'] = $is_correct;
            $newRequest['wrong_answers'] = $wrongAnswer;
            $newRequest['skipped'] = $totalMarks[0]['total_marks'] - $newRequest['attempted'];
            $newRequest['is_calculated'] = 1;
            UserAssessment::where('user_id', $userId)->where('assessment_id', $assessment_id)->update($newRequest);
            return true;
            // } else {
            //     return false;
            // }
        } else {
            return false;
        }
    }
}