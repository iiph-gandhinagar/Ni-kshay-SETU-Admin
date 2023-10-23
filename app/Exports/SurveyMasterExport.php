<?php

namespace App\Exports;

use App\Models\SurveyMaster;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SurveyMasterExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return SurveyMaster::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.survey-master.columns.active'),
            trans('admin.survey-master.columns.id'),
            trans('admin.survey-master.columns.order_index'),
            trans('admin.survey-master.columns.title'),
        ];
    }

    /**
     * @param SurveyMaster $surveyMaster
     * @return array
     *
     */
    public function map($surveyMaster): array
    {
        return [
            $surveyMaster->active,
            $surveyMaster->id,
            $surveyMaster->order_index,
            $surveyMaster->title,
        ];
    }
}
