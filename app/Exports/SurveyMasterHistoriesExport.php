<?php

namespace App\Exports;

use App\Models\SurveyMasterHistory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SurveyMasterHistoriesExport implements FromCollection, WithMapping, WithHeadings
{
    protected $answer;
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    /**
     * @return Collection
     */
    public function collection()
    {
        $newRequest = $this->request;
        $surveyMasterHistory =  SurveyMasterHistory::with(['survey_master', 'user', 'survey_master_question']);
        if ($newRequest->has('survey_id') && $newRequest['survey_id'] != NULL && $newRequest['survey_id'] != 'null') {
            $surveyMasterHistory =  $surveyMasterHistory->where('survey_master_histories.survey_id', $newRequest->survey_id);
        }
        if ($newRequest->has('subscriber_id') && $newRequest['subscriber_id'] != NULL && $newRequest['subscriber_id'] != 'null') {
            $surveyMasterHistory =  $surveyMasterHistory->where('survey_master_histories.user_id', $newRequest->subscriber_id);
        }
        return $surveyMasterHistory = $surveyMasterHistory->orderBy('survey_master_histories.created_at', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.survey-master-history.columns.id'),
            trans('admin.survey-master-history.columns.user_id'),
            trans('admin.survey-master-history.columns.survey_id'),
            trans('admin.survey-master-history.columns.survey_question_id'),
            trans('admin.survey-master-history.columns.answer'),
            "Created At"
        ];
    }

    /**
     * @param SurveyMasterHistory $surveyMasterHistory
     * @return array
     *
     */
    public function map($surveyMasterHistory): array
    {
        if ($surveyMasterHistory->answer == "option1") {
            $this->answer = $surveyMasterHistory->survey_master_question->option1;
        } else if ($surveyMasterHistory->answer == "option2") {
            $this->answer = $surveyMasterHistory->survey_master_question->option2;
        } else if ($surveyMasterHistory->answer == "option3") {
            $this->answer = $surveyMasterHistory->survey_master_question->option3;
        } else {
            $this->answer = $surveyMasterHistory->answer;
        }
        // Log::info($this->answer);
        return [
            $surveyMasterHistory->id,
            $surveyMasterHistory->user->name,
            $surveyMasterHistory->survey_master->title,
            $surveyMasterHistory->survey_master_question->question,
            $this->answer,
            $surveyMasterHistory->created_at
        ];
    }
}
