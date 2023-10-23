<?php

namespace App\Exports;

use App\Models\AssessmentQuestion;
use App\Models\UserAssessment;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AssessmentResultExport implements FromView
{
    protected $request;
    protected $flag;
    protected $i = 0;

    public function __construct($request, $flag)
    {
        $this->request = $request;
        $this->flag = $flag;
    }

    public function view(): View
    {
        $assessment_question = AssessmentQuestion::where('assessment_id', $this->request->id)->get();
        // Log::info($this->request);

        if ($this->flag == 1) {
            return view('exports.export-assessment-question-result', [
                'assessment_question' => $assessment_question,
                'assessment' => $this->request
            ]);
        } else {

            $assessment_quiz_result = UserAssessment::where('assessment_id', $this->request->id)->with(['user'  => function ($q) {
                $q->with(['state', 'district', 'block', 'health_facility', 'cadre']);
            }, 'assessment_with_trashed', 'assessment_user_quiz_answer' => function ($q) {
                $q->orderBy('question_id');
            }])->orderBy('user_assessments.created_at', 'DESC')->get();
            // Log::info("query fetched for assessment quiz result");
            // Log::info($assessment_quiz_result);

            return view('exports.export-assessment-quiz-result', [
                'assessment_question' => $assessment_question,
                'assessment_quiz_result' => $assessment_quiz_result,
            ]);
        }
    }
}
