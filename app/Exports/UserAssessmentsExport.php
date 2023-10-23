<?php

namespace App\Exports;

use App\Models\UserAssessment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Log;

class UserAssessmentsExport implements FromCollection, WithMapping, WithHeadings
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
        // Log::info($newRequest);
        $userAssessment = UserAssessment::with(['assessment_with_trashed', 'user', 'user.block', 'user.country']);
        $assignedDistrict = '';
        $assignedCountry = '';
        $assignedState = '';
        $assignedcadre = '';
        // Log::info($newRequest);

        if (\Auth::user()->role_type == 'country_type' && (\Auth::user()->roles[0]['id'] == 1 || \Auth::user()->roles[0]['id'] == 2)) {
            // $assignedCountry = \Auth::user()->country;
            // $assignedState = \Auth::user()->state;
            // $assignedCadre = \Auth::user()->cadre;
            // $assignedDistrict = \Auth::user()->district;
        } elseif (\Auth::user()->role_type == 'country_type') {
            $assignedCountry = \Auth::user()->country;
            $assignedcadre = \Auth::user()->cadre;
        } elseif (\Auth::user()->role_type == 'state_type') {
            $assignedState = \Auth::user()->state;
            $assignedcadre = \Auth::user()->cadre;
        } else {
            $assignedDistrict = \Auth::user()->district;
            $assignedcadre = \Auth::user()->cadre;
        }

        if ($assignedCountry != '' && $assignedCountry > 0 && $newRequest->has('country_id') && $newRequest['country_id'] == NULL) {
            $userAssessment = $userAssessment->whereHas('user.country', function ($q) use ($assignedCountry) {
                $q->whereIn('id', explode(',', $assignedCountry));
            });
        }
        if ($assignedState != '' && $assignedState > 0 && $newRequest->has('state_id') && $newRequest['state_id'] == NULL) {
            $userAssessment = $userAssessment->whereHas('user.state', function ($q) use ($assignedState) {
                $q->whereIn('id', explode(',', $assignedState));
            });
        }
        if ($assignedDistrict != '' && $assignedDistrict > 0 && $newRequest->has('district_id') && $newRequest['district_id'] == NULL) {
            $userAssessment = $userAssessment->whereHas('user.district', function ($q) use ($assignedDistrict) {
                $q->whereIn('id', explode(',', $assignedDistrict));
            });
        }
        if ($assignedcadre != '' && $assignedcadre > 0 && $newRequest->has('cadre_id') && $newRequest['cadre_id'] == NULL) {
            // Log::info('insdie cadre state of user -----');
            $userAssessment = $userAssessment->whereHas('user.cadre', function ($q) use ($assignedcadre) {
                $q->whereIn('id', explode(',', $assignedcadre));
            });
        }

        if ($newRequest->has('assessment_id') && $newRequest['assessment_id'] != NULL && $newRequest['assessment_id'] != 'null') {
            $userAssessment =  $userAssessment->where('assessment_id', $newRequest->assessment_id);
        }
        if ($newRequest->has('subscriber_id') && $newRequest['subscriber_id'] != NULL && $newRequest['subscriber_id'] != 'null') {
            $userAssessment =  $userAssessment->where('user_id', $newRequest->subscriber_id);
        }
        if ($newRequest->has('cadre_id') && $newRequest['cadre_id'] != NULL && $newRequest['cadre_id'] != 'null') {
            // Log::info('inside cadre id details --->');
            // $userAssessment =  $userAssessment->join('assessments', 'assessments.id', '=', 'user_assessments.assessment_id')
            //     ->whereRaw("find_in_set('" . $newRequest->cadre_id . "',assessments.cadre_id)");
            $userAssessment = $userAssessment->whereHas('user', function ($q) use ($newRequest) {
                $q->whereRaw("find_in_set('" . $newRequest->cadre_id . "',cadre_id)");
            });
        }
        if ($newRequest->has('country_id') && $newRequest['country_id'] != NULL && $newRequest['country_id'] != 'null') {
            $userAssessment =  $userAssessment->whereHas('user', function ($q) use ($newRequest) {
                $q->where('country_id', $newRequest->country_id);
            });
        }
        if ($newRequest->has('state_id') && $newRequest['state_id'] != NULL && $newRequest['state_id'] != 'null') {

            $userAssessment =  $userAssessment->whereHas('user', function ($q) use ($newRequest) {
                $q->where('state_id', $newRequest->state_id);
            });
        }
        if ($newRequest->has('district_id') && $newRequest['district_id'] != NULL && $newRequest['district_id'] != 'null') {

            $userAssessment =  $userAssessment->whereHas('user', function ($q) use ($newRequest) {
                $q->where('district_id', $newRequest->district_id);
            });
        }
        if ($newRequest->has('block_id') && $newRequest['block_id'] != NULL && $newRequest['block_id'] != 'null') {

            $userAssessment =  $userAssessment->whereHas('user', function ($q) use ($newRequest) {
                $q->where('block_id', $newRequest->block_id);
            });
        }
        if ($newRequest->has('health_facility_id') && $newRequest['health_facility_id'] != NULL && $newRequest['health_facility_id'] != 'null') {

            $userAssessment =  $userAssessment->whereHas('user', function ($q) use ($newRequest) {
                $q->where('health_facility_id', $newRequest->health_facility_id);
            });
        }
        if ($newRequest->has('todayDate') && $newRequest['todayDate'] != NULL && $newRequest['todayDate'] != 'null') {
            // $userAssessment =  $userAssessment->whereDate('created_at', Carbon::createFromFormat('d/m/Y', $newRequest->todayDate)->format('Y-m-d'));
            $userAssessment =  $userAssessment->whereDate('created_at', date('Y-m-d', strtotime($newRequest->todayDate)));
        }
        return $userAssessment->orderBy('user_assessments.created_at', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.user-assessment.columns.id'),
            trans('admin.user-assessment.columns.assessment_id'),
            trans('admin.user-assessment.columns.user_id'),
            trans('admin.user-assessment.columns.total_marks'),
            trans('admin.user-assessment.columns.obtained_marks'),
            trans('admin.user-assessment.columns.attempted'),
            trans('admin.user-assessment.columns.right_answers'),
            trans('admin.user-assessment.columns.wrong_answers'),
            trans('admin.user-assessment.columns.skipped'),
            trans('admin.user-assessment.columns.cadre'),
            "Country",
            trans('admin.user-assessment.columns.state'),
            trans('admin.user-assessment.columns.district'),
            trans('admin.user-assessment.columns.block'),
            trans('admin.user-assessment.columns.health_facility'),
            trans('admin.user-assessment.columns.assesment_submit_date'),
        ];
    }

    /**
     * @param UserAssessment $userAssessment
     * @return array
     *
     */
    public function map($userAssessment): array
    {
        $this->i++;
        return [
            // $userAssessment->id,
            $this->i,
            $userAssessment->assessment_with_trashed->assessment_title,
            $userAssessment->user->name,
            $userAssessment->total_marks,
            $userAssessment->obtained_marks,
            $userAssessment->attempted,
            $userAssessment->right_answers,
            $userAssessment->wrong_answers,
            $userAssessment->skipped,
            optional($userAssessment->user->cadre)->title,
            optional($userAssessment->user->country)->title,
            optional($userAssessment->user->state)->title,
            optional($userAssessment->user->district)->title,
            optional($userAssessment->user->block)->title,
            optional($userAssessment->user->health_facility)->health_facility_code,
            $userAssessment->created_at
        ];
    }
}
