<?php

namespace App\Exports;

use App\Models\SubscriberActivity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class SubscriberActivitiesExport implements FromCollection, WithMapping, WithHeadings
{
    protected $request;
    protected $i = 0;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $newRquest = $this->request;
        // Log::info($newRquest);
        $subscriberActivity = SubscriberActivity::with(['user']);
        $assignedState = \Auth::user()->state;
        if ($assignedState != '' && $assignedState > 0) {
            $subscriberActivity->whereHas('user', function ($q) use ($assignedState) {
                $q->where('state_id', $assignedState);
            });
        }

        if ($newRquest->has('subscriber_id') && $newRquest['subscriber_id'] != NULL && $newRquest['subscriber_id'] != 'null') {
            $subscriberActivity = $subscriberActivity->where('user_id', $newRquest->subscriber_id);
        }
        if ($newRquest->has('action') && $newRquest['action'] != NULL && $newRquest['action'] != 'null') {
            $subscriberActivity = $subscriberActivity->where('action', $newRquest->action);
        }
        if ($newRquest->has('plateform') && $newRquest['plateform'] != NULL && $newRquest['plateform'] != 'null') {
            $subscriberActivity = $subscriberActivity->where('plateform', $newRquest->plateform);
        }
        if ($newRquest->has('cadre_id') && $newRquest['cadre_id'] != NULL && $newRquest['cadre_id'] != 'null') {
            $subscriberActivity =  $subscriberActivity->whereHas('user', function ($q) use ($newRquest) {
                $q->where('cadre_id', $newRquest->cadre_id);
            });
        }
        if ($newRquest->has('country_id') && $newRquest['country_id'] != NULL && $newRquest['country_id'] != 'null') {
            $subscriberActivity =  $subscriberActivity->whereHas('user', function ($q) use ($newRquest) {
                $q->where('country_id', $newRquest->country_id);
            });
        }
        if ($newRquest->has('state_id') && $newRquest['state_id'] != NULL && $newRquest['state_id'] != 'null') {

            $subscriberActivity =  $subscriberActivity->whereHas('user', function ($q) use ($newRquest) {
                $q->where('state_id', $newRquest->state_id);
            });
        }
        if ($newRquest->has('todayDate') && $newRquest['todayDate'] != NULL && $newRquest['todayDate'] != 'null') {
            // $subscriberActivity =  $subscriberActivity->whereDate('created_at', Carbon::createFromFormat('d/m/Y', $newRquest->todayDate)->format('Y-m-d'));
            $subscriberActivity = $subscriberActivity->whereDate('created_at', date('Y-m-d', strtotime($newRquest->todayDate)));
        }
        return $subscriberActivity->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.subscriber-activity.columns.id'),
            trans('admin.subscriber-activity.columns.user_id'),
            "Cadre",
            "Country",
            "State",
            trans('admin.subscriber-activity.columns.action'),
            trans('admin.subscriber-activity.columns.ip_address'),
            'Created_at',
        ];
    }

    /**
     * @param SubscriberActivity $subscriberActivity
     * @return array
     *
     */
    public function map($subscriberActivity): array
    {
        $this->i++;
        return [
            // $subscriberActivity->id,
            $this->i,
            $subscriberActivity->user->name,
            $subscriberActivity->user->cadre && $subscriberActivity->user->cadre->title ? $subscriberActivity->user->cadre->title : '',
            $subscriberActivity->user->country && $subscriberActivity->user->country->title ? $subscriberActivity->user->country->title : '',
            $subscriberActivity->user->state && $subscriberActivity->user->state->title ? $subscriberActivity->user->state->title : '',
            $subscriberActivity->action,
            $subscriberActivity->ip_address,
            $subscriberActivity->created_at,
        ];
    }
}
