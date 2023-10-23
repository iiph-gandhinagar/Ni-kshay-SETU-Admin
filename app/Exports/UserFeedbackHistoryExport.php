<?php

namespace App\Exports;

use App\Models\UserFeedbackHistory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserFeedbackHistoryExport implements FromCollection, WithMapping, WithHeadings
{
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
        $userFeedbackHistory =  UserFeedbackHistory::with(['user', 'feedback_question']);
        if ($newRequest->has('rating_id') && $newRequest['rating_id'] != NULL && $newRequest['rating_id'] != 'null') {
            $userFeedbackHistory =  $userFeedbackHistory->where('user_feedback_history.ratings', $newRequest->rating_id);
        }
        if ($newRequest->has('subscriber_id') && $newRequest['subscriber_id'] != NULL && $newRequest['subscriber_id'] != 'null') {
            $userFeedbackHistory =  $userFeedbackHistory->where('user_feedback_history.subscriber_id', $newRequest->subscriber_id);
        }
        return $userFeedbackHistory = $userFeedbackHistory->orderBy('user_feedback_history.created_at', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.user-feedback-history.columns.id'),
            trans('admin.user-feedback-history.columns.subscriber_id'),
            trans('admin.user-feedback-history.columns.feedback_id'),
            trans('admin.user-feedback-history.columns.ratings'),
            trans('admin.user-feedback-history.columns.review'),
            trans('admin.user-feedback-history.columns.skip'),
            "Created At"
        ];
    }

    /**
     * @param UserFeedbackHistory $userFeedbackHistory
     * @return array
     *
     */
    public function map($userFeedbackHistory): array
    {
        return [
            $userFeedbackHistory->id,
            $userFeedbackHistory->user->name,
            $userFeedbackHistory->feedback_question->feedback_question,
            $userFeedbackHistory->ratings,
            $userFeedbackHistory->review,
            $userFeedbackHistory->skip,
            $userFeedbackHistory->created_at,
        ];
    }
}
