<?php

namespace App\Exports;

use App\Models\SurveyMasterQuestion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SurveyMasterQuestionsExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return SurveyMasterQuestion::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.survey-master-question.columns.active'),
            trans('admin.survey-master-question.columns.id'),
            trans('admin.survey-master-question.columns.option1'),
            trans('admin.survey-master-question.columns.option2'),
            trans('admin.survey-master-question.columns.option3'),
            trans('admin.survey-master-question.columns.option4'),
            trans('admin.survey-master-question.columns.order_index'),
            trans('admin.survey-master-question.columns.question'),
            trans('admin.survey-master-question.columns.survey_master_id'),
            trans('admin.survey-master-question.columns.type'),
        ];
    }

    /**
     * @param SurveyMasterQuestion $surveyMasterQuestion
     * @return array
     *
     */
    public function map($surveyMasterQuestion): array
    {
        return [
            $surveyMasterQuestion->active,
            $surveyMasterQuestion->id,
            $surveyMasterQuestion->option1,
            $surveyMasterQuestion->option2,
            $surveyMasterQuestion->option3,
            $surveyMasterQuestion->option4,
            $surveyMasterQuestion->order_index,
            $surveyMasterQuestion->question,
            $surveyMasterQuestion->survey_master_id,
            $surveyMasterQuestion->type,
        ];
    }
}
