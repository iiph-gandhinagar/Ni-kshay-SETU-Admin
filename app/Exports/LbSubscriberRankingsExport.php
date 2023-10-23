<?php

namespace App\Exports;

use App\Models\LbSubscriberRanking;
use App\Models\LbTaskList;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LbSubscriberRankingsExport implements FromCollection, WithMapping, WithHeadings
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
        $lbSubscriberRanking =  LbSubscriberRanking::with(['lb_level', 'lb_badge', 'lb_task_list', 'user']);
        if ($newRequest->has('level_id') && $newRequest['level_id'] != NULL && $newRequest['level_id'] != 'null') {
            $lbSubscriberRanking =  $lbSubscriberRanking->where('lb_subscriber_rankings.level_id', $newRequest->level_id);
        }
        if ($newRequest->has('subscriber_id') && $newRequest['subscriber_id'] != NULL && $newRequest['subscriber_id'] != 'null') {
            $lbSubscriberRanking =  $lbSubscriberRanking->where('lb_subscriber_rankings.subscriber_id', $newRequest->subscriber_id);
        }
        if ($newRequest->has('badge_id') && $newRequest['badge_id'] != NULL && $newRequest['badge_id'] != 'null') {
            $lbSubscriberRanking =  $lbSubscriberRanking->where('lb_subscriber_rankings.badge_id', $newRequest->badge_id);
        }
        return $lbSubscriberRanking = $lbSubscriberRanking->orderBy('lb_subscriber_rankings.created_at', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.lb-subscriber-ranking.columns.id'),
            trans('admin.lb-subscriber-ranking.columns.subscriber_id'),
            trans('admin.lb-subscriber-ranking.columns.level_id'),
            trans('admin.lb-subscriber-ranking.columns.badge_id'),
            trans('admin.lb-subscriber-ranking.columns.mins_spent_count'),
            trans('admin.lb-subscriber-ranking.columns.sub_module_usage_count'),
            trans('admin.lb-subscriber-ranking.columns.App_opended_count'),
            trans('admin.lb-subscriber-ranking.columns.chatbot_usage_count'),
            trans('admin.lb-subscriber-ranking.columns.resource_material_accessed_count'),
            trans('admin.lb-subscriber-ranking.columns.total_task_count'),
            "percentage",
            "created At"
        ];
    }

    /**
     * @param LbSubscriberRanking $lbSubscriberRanking
     * @return array
     *
     */
    public function map($lbSubscriberRanking): array
    {
        // Log::info($lbSubscriberRanking);
        return [
            $lbSubscriberRanking->id,
            $lbSubscriberRanking->user->name,
            $lbSubscriberRanking->level_id != 6 ? $lbSubscriberRanking->lb_level->level : "Expert",
            $lbSubscriberRanking->badge_id != 16 ? $lbSubscriberRanking->lb_badge->badge : "Gold",
            $lbSubscriberRanking->mins_spent_count,
            $lbSubscriberRanking->sub_module_usage_count,
            $lbSubscriberRanking->App_opended_count,
            $lbSubscriberRanking->chatbot_usage_count,
            $lbSubscriberRanking->resource_material_accessed_count,
            $lbSubscriberRanking->total_task_count,
            round(($lbSubscriberRanking->total_task_count * 100) / LbTaskList::count(), 2),
            $lbSubscriberRanking->created_at,
        ];
    }
}
