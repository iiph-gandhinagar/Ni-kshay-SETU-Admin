<?php

namespace App\Exports;

use App\Models\Subscriber;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubscribersExport implements FromCollection, WithMapping, WithHeadings
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
        $subscriber = Subscriber::with(['health_facility', 'state', 'district', 'block', 'cadre', 'lb_subscriber_rankings']); //->withCount(['subscriber_activities'])
        $assignedState = \Auth::user()->state;
        if ($assignedState != '' && $assignedState > 0) {
            $subscriber = $subscriber->where('state_id', $assignedState);
        }
        if ($newRequest->has('cadre_id') && $newRequest['cadre_id'] != 'null' && $newRequest['cadre_id'] != NULL) {
            // Log::info("inside cadre id");
            $subscriber =  $subscriber->where('cadre_id', $newRequest->cadre_id);
        }
        if ($newRequest->has('state_id') && $newRequest['state_id'] != NULL && $newRequest['state_id'] != 'null') {
            // Log::info("inside state id");
            $subscriber = $subscriber->where('state_id', $newRequest->state_id);
        }
        if ($newRequest->has('district_id') && $newRequest['district_id'] != NULL && $newRequest['district_id'] != 'null') {
            // Log::info("inside district id");
            $subscriber = $subscriber->where('district_id', $newRequest->district_id);
        }
        if ($newRequest->has('block_id') && $newRequest['block_id'] != NULL && $newRequest['block_id'] != 'null') {
            // Log::info("inside block id");
            $subscriber = $subscriber->where('block_id', $newRequest->block_id);
        }
        if ($newRequest->has('health_facility_id') && $newRequest['health_facility_id'] != NULL && $newRequest['health_facility_id'] != 'null') {
            // Log::info("inside health_facility  id");
            $subscriber = $subscriber->where('health_facility_id', $newRequest->health_facility_id);
        }
        if ($newRequest->has('app_version') && $newRequest['app_version'] != NULL && $newRequest['app_version'] != 'null') {
            // Log::info("inside app_version");
            $subscriber = $subscriber->whereHas('user_app_version', function ($q) use ($newRequest) {
                $q->where('app_version', $newRequest->app_version);
            });
        }
        if ($newRequest->has('from_date') && $newRequest['from_date'] != NULL && $newRequest['from_date'] != 'null') {
            $subscriber =  $subscriber->whereDate('created_at', '>=', date('Y-m-d', strtotime($newRequest->from_date)));
        }
        if ($newRequest->has('to_date') && $newRequest['to_date'] != NULL && $newRequest['to_date'] != 'null') {
            $subscriber = $subscriber->whereDate('created_at', '<=', date('Y-m-d', strtotime($newRequest->to_date)));
        }
        return $subscriber->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.subscriber.columns.id'),
            // trans('admin.subscriber.columns.api_token'),
            trans('admin.subscriber.columns.name'),
            trans('admin.subscriber.columns.phone_no'),
            trans('admin.subscriber.columns.cadre_type'),
            trans('admin.subscriber.columns.is_verified'),
            "Country",
            trans('admin.subscriber.columns.cadre_id'),
            trans('admin.subscriber.columns.block_id'),
            trans('admin.subscriber.columns.district_id'),
            trans('admin.subscriber.columns.state_id'),
            trans('admin.subscriber.columns.health_facility_id'),
            'Level',
            'Badge',
            'Minute Spent',
            'Sub Module Usage Count',
            'App Opened Count',
            'Chatbot Usage',
            'Resource Material Usage',
            'Total Task',
            'App Performance Percentage',
            'Created_at',
            // 'No. of Visits',
        ];
    }

    /**
     * @param Subscriber $subscriber
     * @return array
     *
     */
    public function map($subscriber): array
    {
        return [
            $subscriber->id,
            // $subscriber->api_token,
            $subscriber->name,
            $subscriber->phone_no,
            $subscriber->cadre_type,
            $subscriber->is_verified,
            $subscriber->country && $subscriber->country->title ? $subscriber->country->title : '',
            $subscriber->cadre && $subscriber->cadre->title ? $subscriber->cadre->title : '',
            $subscriber->block && $subscriber->block->title ? $subscriber->block->title : '',
            $subscriber->district && $subscriber->district->title ? $subscriber->district->title : '',
            $subscriber->state && $subscriber->state->title ? $subscriber->state->title : '',
            $subscriber->health_facility && $subscriber->health_facility->health_facility_code ? $subscriber->health_facility->health_facility_code : '',
            $subscriber->lb_subscriber_rankings && $subscriber->lb_subscriber_rankings->lb_level && $subscriber->lb_subscriber_rankings->level_id < 6 ? $subscriber->lb_subscriber_rankings->lb_level->level : "Expert",
            $subscriber->lb_subscriber_rankings && $subscriber->lb_subscriber_rankings->lb_badge && $subscriber->lb_subscriber_rankings->badge_id < 16 ? $subscriber->lb_subscriber_rankings->lb_badge->badge : "Gold",
            $subscriber->lb_subscriber_rankings && $subscriber->lb_subscriber_rankings->mins_spent_count ? $subscriber->lb_subscriber_rankings->mins_spent_count : '',
            $subscriber->lb_subscriber_rankings && $subscriber->lb_subscriber_rankings->sub_module_usage_count ? $subscriber->lb_subscriber_rankings->sub_module_usage_count : '',
            $subscriber->lb_subscriber_rankings && $subscriber->lb_subscriber_rankings->App_opended_count ? $subscriber->lb_subscriber_rankings->App_opended_count : '',
            $subscriber->lb_subscriber_rankings && $subscriber->lb_subscriber_rankings->chatbot_usage_count ? $subscriber->lb_subscriber_rankings->chatbot_usage_count : '',
            $subscriber->lb_subscriber_rankings && $subscriber->lb_subscriber_rankings->resource_material_accessed_count ? $subscriber->lb_subscriber_rankings->resource_material_accessed_count : '',
            $subscriber->lb_subscriber_rankings && $subscriber->lb_subscriber_rankings->total_task_count ? $subscriber->lb_subscriber_rankings->total_task_count : '',
            $subscriber->lb_subscriber_rankings && $subscriber->lb_subscriber_rankings->total_task_count ? ($subscriber->lb_subscriber_rankings->total_task_count * 100) / 64 . "%" : '',
            $subscriber->created_at,
            // $subscriber->subscriber_activities_count ? $subscriber->subscriber_activities_count : '',
        ];
    }
}
