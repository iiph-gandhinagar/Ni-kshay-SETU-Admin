<?php

namespace App\Exports;

use App\Models\ChatKeywordHit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ChatKeywordHitsExport implements FromCollection, WithMapping, WithHeadings
{
    protected $request;
    protected $i = 0;

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
        $chatKeywordHits = ChatKeywordHit::with(['user', 'keyword', 'user.cadre', 'user.state', 'user.district', 'user.health_facility', 'user.country']);
        $assignedDistrict = '';
        $assignedCountry = '';
        $assignedState = '';
        $assignedCadre = '';
        if (\Auth::user()->role_type == 'country_type' && (\Auth::user()->roles[0]['id'] == 1 || \Auth::user()->roles[0]['id'] == 2)) {
            // $assignedCountry = \Auth::user()->country;
            // $assignedState = \Auth::user()->state;
            // $assignedCadre = \Auth::user()->cadre;
            // $assignedDistrict = \Auth::user()->district;
        } elseif (\Auth::user()->role_type == 'country_type') {
            $assignedCountry = \Auth::user()->country;
            $assignedCadre = \Auth::user()->cadre;
        } elseif (\Auth::user()->role_type == 'state_type') {
            $assignedState = \Auth::user()->state;
            $assignedCadre = \Auth::user()->cadre;
        } else {
            $assignedDistrict = \Auth::user()->district;
            $assignedCadre = \Auth::user()->cadre;
        }


        if ($assignedCountry != '' && $assignedCountry > 0) {
            $chatKeywordHits = $chatKeywordHits->whereHas('user', function ($q) use ($assignedCountry) {
                $q->whereIn('country_id', explode(',', $assignedCountry));
            });
        }
        if ($assignedState != '' && $assignedState > 0) {
            $chatKeywordHits = $chatKeywordHits->whereHas('user.state', function ($q) use ($assignedState) {
                $q->whereIn('id', explode(',', $assignedState));
            });
        }
        if ($assignedDistrict != '' && $assignedDistrict > 0) {
            $chatKeywordHits = $chatKeywordHits->whereHas('user.district', function ($q) use ($assignedDistrict) {
                $q->whereIn('id', explode(',', $assignedDistrict));
            });
        }
        if ($assignedCadre != '' && $assignedCadre > 0) {
            $chatKeywordHits = $chatKeywordHits->whereHas('user.cadre', function ($q) use ($assignedCadre) {
                $q->whereIn('id', explode(',', $assignedCadre));
            });
        }
        if ($newRequest->has('subscriber_id') && $newRequest['subscriber_id'] != NULL && $newRequest['subscriber_id'] != 'null') {
            $chatKeywordHits = $chatKeywordHits->where('subscriber_id', $newRequest->subscriber_id);
        }
        if ($newRequest->has('cadre_id') && $newRequest['cadre_id'] != NULL && $newRequest['cadre_id'] != 'null') {
            // $chatKeywordHits =  $chatKeywordHits->where('cadre_id',$newRequest->cadre_id);

            $chatKeywordHits =  $chatKeywordHits->whereHas('user', function ($q) use ($newRequest) {
                $q->where('cadre_id', $newRequest->cadre_id);
            });
            // ->where('subscribers.cadre_id',$newRequest->cadre_id);
        }
        if ($newRequest->has('country_id') && $newRequest['country_id'] != NULL && $newRequest['country_id'] != 'null') {

            $chatKeywordHits =  $chatKeywordHits->whereHas('user', function ($q) use ($newRequest) {
                $q->where('country_id', $newRequest->country_id);
            });
            // ->where('subscribers.cadre_id',$newRequest->cadre_id);
        }
        if ($newRequest->has('state_id') && $newRequest['state_id'] != NULL && $newRequest['state_id'] != 'null') {
            // $chatKeywordHits = $chatKeywordHits->where('state_id', $newRequest->state_id);
            $chatKeywordHits =  $chatKeywordHits->whereHas('user', function ($q) use ($newRequest) {
                $q->where('state_id', $newRequest->state_id);
            });
        }
        if ($newRequest->has('district_id') && $newRequest['district_id'] != NULL && $newRequest['district_id'] != 'null') {
            // $chatKeywordHits = $chatKeywordHits->where('district_id', $newRequest->district_id);
            $chatKeywordHits =  $chatKeywordHits->whereHas('user', function ($q) use ($newRequest) {
                $q->where('district_id', $newRequest->district_id);
            });
        }
        if ($newRequest->has('block_id') && $newRequest['block_id'] != NULL && $newRequest['block_id'] != 'null') {
            // $chatKeywordHits = $chatKeywordHits->where('block_id', $newRequest->block_id);
            $chatKeywordHits =  $chatKeywordHits->whereHas('user', function ($q) use ($newRequest) {
                $q->where('block_id', $newRequest->block_id);
            });
        }
        if ($newRequest->has('health_facility_id') && $newRequest['health_facility_id'] != NULL && $newRequest['health_facility_id'] != 'null') {
            // $chatKeywordHits = $chatKeywordHits->where('health_facility_id', $newRequest->health_facility_id);
            $chatKeywordHits =  $chatKeywordHits->whereHas('user', function ($q) use ($newRequest) {
                $q->where('health_facility_id', $newRequest->health_facility_id);
            });
        }
        return $chatKeywordHits->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.chat-question-hit.columns.id'),
            trans('admin.chat-question-hit.columns.keyword_id'),
            trans('admin.chat-question-hit.columns.subscriber_id'),
            "Cadre",
            "Country",
            "State",
            "District",
            "Block",
            "Health Facility",
            'Created_at',
        ];
    }

    public function map($chatKeywordHits): array
    {
        $this->i++;
        return [
            // $chatKeywordHits->id,
            $this->i,
            $chatKeywordHits->keyword && $chatKeywordHits->keyword->title ? $chatKeywordHits->keyword->title : "",
            $chatKeywordHits->user->name,
            $chatKeywordHits->user->cadre && $chatKeywordHits->user->cadre->title ? $chatKeywordHits->user->cadre->title : '',
            $chatKeywordHits->user->country && $chatKeywordHits->user->country->title ? $chatKeywordHits->user->country->title : '',
            $chatKeywordHits->user->state && $chatKeywordHits->user->state->title ? $chatKeywordHits->user->state->title : '',
            $chatKeywordHits->user->district && $chatKeywordHits->user->district->title ? $chatKeywordHits->user->district->title : '',
            $chatKeywordHits->user->block && $chatKeywordHits->user->block->title ? $chatKeywordHits->user->block->title : '',
            $chatKeywordHits->user->health_facility && $chatKeywordHits->user->health_facility->health_facility_code ? $chatKeywordHits->user->health_facility->health_facility_code : '',
            $chatKeywordHits->created_at,
        ];
    }
}
