<?php

namespace App\Exports;

use App\Models\Enquiry;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class EnquiriesExport implements FromCollection, WithMapping, WithHeadings
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
        $enquiry =  Enquiry::with(['user', 'user.cadre', 'user.state', 'user.district', 'user.block', 'user.health_facility', 'user.country']);
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
            $enquiry = $enquiry->whereIn('subscribers.country_id', explode(',', $assignedCountry));
        }
        if ($assignedState != '' && $assignedState > 0) {
            $enquiry->whereHas('user.state', function ($q) use ($assignedState) {
                $q->where('id', $assignedState);
            });
        } else if ($newRequest->has('state')) {
            $enquiry = $enquiry->whereHas('user', function ($q) use ($newRequest) {
                $q->whereIn('state_id', explode(',', $newRequest->state));
            });
        }

        if ($assignedDistrict != '' && $assignedDistrict > 0) {
            $enquiry = $enquiry->whereHas('user.district', function ($q) use ($assignedDistrict) {
                $q->whereIn('id', explode(',', $assignedDistrict));
            });
        }
        if ($assignedCadre != '' && $assignedCadre > 0) {
            $enquiry = $enquiry->whereHas('user.cadre', function ($q) use ($assignedCadre) {
                $q->whereIn('id', explode(',', $assignedCadre));
            });
        }
        // if ($newRequest->has('todayDate') && $newRequest['todayDate'] != NULL && $newRequest['todayDate'] != 'null') {
        //     $enquiry =  $enquiry->whereDate('created_at', Carbon::createFromFormat('d/m/Y', $newRequest->todayDate)->format('Y-m-d'));
        // }
        // Log::info($enquiry);
        return $enquiry->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.enquiry.columns.id'),
            trans('admin.enquiry.columns.name'),
            trans('admin.enquiry.columns.phone'),
            "Cadre",
            "Country",
            "State",
            "District",
            "Block",
            "Health Facility",
            trans('admin.enquiry.columns.subject'),
            trans('admin.enquiry.columns.message'),
            'Created_at',
        ];
    }

    /**
     * @param Enquiry $enquiry
     * @return array
     *
     */
    public function map($enquiry): array
    {
        $this->i++;

        return [
            // $enquiry->id,
            $this->i,
            $enquiry->name,
            $enquiry->phone,
            $enquiry->user->country && $enquiry->user->country->title ? $enquiry->user->country->title : '',
            $enquiry->user->cadre && $enquiry->user->cadre->title ? $enquiry->user->cadre->title : '',
            $enquiry->user->state && $enquiry->user->state->title ? $enquiry->user->state->title : '',
            $enquiry->user->district && $enquiry->user->district->title ? $enquiry->user->district->title : '',
            $enquiry->user->block && $enquiry->user->block->title ? $enquiry->user->block->title : '',
            $enquiry->user->health_facility && $enquiry->user->health_facility->health_facility_code ? $enquiry->user->health_facility->health_facility_code : '',
            $enquiry->subject,
            $enquiry->message,
            $enquiry->created_at,
        ];
    }
}