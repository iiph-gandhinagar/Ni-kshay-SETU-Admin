<?php

namespace App\Exports;

use App\Models\UserFeedbackDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserFeedbackDetailsExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return UserFeedbackDetail::with(['user', 'feedback_question'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.user-feedback-detail.columns.id'),
            trans('admin.user-feedback-detail.columns.subscriber_id'),
            trans('admin.user-feedback-detail.columns.feedback_id'),
            trans('admin.user-feedback-detail.columns.ratings'),
            trans('admin.user-feedback-detail.columns.review'),
            "created At"
        ];
    }

    /**
     * @param UserFeedbackDetail $userFeedbackDetail
     * @return array
     *
     */
    public function map($userFeedbackDetail): array
    {
        return [
            $userFeedbackDetail->id,
            $userFeedbackDetail->user->name,
            $userFeedbackDetail->feedback_question->feedback_question,
            $userFeedbackDetail->ratings,
            $userFeedbackDetail->review,
            $userFeedbackDetail->created_at,
        ];
    }
}
