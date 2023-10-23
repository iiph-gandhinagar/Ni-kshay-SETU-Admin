<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Assessment;
use Log;

class AssessmentQuestionController extends BaseController
{
    public function getAllAssessmentQuestions(Request $request, $assessmentId)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);

        $assessmentQuestions = Assessment::where('id', $assessmentId)->with(['assessment_questions' => function ($q) {
            $q->orderBy('order_index');
        }])->get();

        $success = true;
        return ['status' => $success, 'data' => $assessmentQuestions, 'code' => 200];
    }
}
