<?php

namespace App\Exports;

use App\Models\LbSubscriberRankingHistory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LbSubscriberRankingHistoryExport implements FromCollection, WithMapping, WithHeadings
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
        $lbSubscriberRankingHistory =  LbSubscriberRankingHistory::with(['lb_level', 'lb_badge', 'lb_task_list', 'user']);
        if ($newRequest->has('level_id') && $newRequest['level_id'] != NULL && $newRequest['level_id'] != 'null') {
            $lbSubscriberRankingHistory =  $lbSubscriberRankingHistory->where('lb_subscriber_ranking_history.level_id', $newRequest->level_id);
        }
        if ($newRequest->has('subscriber_id') && $newRequest['subscriber_id'] != NULL && $newRequest['subscriber_id'] != 'null') {
            $lbSubscriberRankingHistory =  $lbSubscriberRankingHistory->where('lb_subscriber_ranking_history.subscriber_id', $newRequest->subscriber_id);
        }
        if ($newRequest->has('badge_id') && $newRequest['badge_id'] != NULL && $newRequest['badge_id'] != 'null') {
            $lbSubscriberRankingHistory =  $lbSubscriberRankingHistory->where('lb_subscriber_ranking_history.badge_id', $newRequest->badge_id);
        }
        return $lbSubscriberRankingHistory = $lbSubscriberRankingHistory->orderBy('lb_subscriber_ranking_history.created_at', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.lb-subscriber-ranking-history.columns.id'),
            trans('admin.lb-subscriber-ranking-history.columns.subscriber_id'),
            trans('admin.lb-subscriber-ranking-history.columns.level_id'),
            trans('admin.lb-subscriber-ranking-history.columns.badge_id'),
            trans('admin.lb-subscriber-ranking-history.columns.mins_spent_count'),
            trans('admin.lb-subscriber-ranking-history.columns.sub_module_usage_count'),
            trans('admin.lb-subscriber-ranking-history.columns.App_opended_count'),
            trans('admin.lb-subscriber-ranking-history.columns.chatbot_usage_count'),
            trans('admin.lb-subscriber-ranking-history.columns.resource_material_accessed_count'),
            "created At"
        ];
    }

    /**
     * @param LbSubscriberRankingHistory $lbSubscriberRankingHistory
     * @return array
     *
     */
    public function map($lbSubscriberRankingHistory): array
    {
        // Log::info($lbSubscriberRankingHistory);
        return [
            $lbSubscriberRankingHistory->id,
            $lbSubscriberRankingHistory->user->name,
            $lbSubscriberRankingHistory->level_id != 6 ? $lbSubscriberRankingHistory->lb_level->level : "Expert",
            $lbSubscriberRankingHistory->badge_id != 16 ? $lbSubscriberRankingHistory->lb_badge->badge : "Gold",
            $lbSubscriberRankingHistory->mins_spent_count,
            $lbSubscriberRankingHistory->sub_module_usage_count,
            $lbSubscriberRankingHistory->App_opended_count,
            $lbSubscriberRankingHistory->chatbot_usage_count,
            $lbSubscriberRankingHistory->resource_material_accessed_count,
            $lbSubscriberRankingHistory->created_at,
        ];
    }
}
