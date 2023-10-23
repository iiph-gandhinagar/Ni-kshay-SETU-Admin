<?php

namespace App\Exports;

use App\Models\ChatQuestionHit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ChatQuestionHitsExport implements FromCollection, WithMapping, WithHeadings
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
        $newRquest = $this->request;
        $chatQuestionHit = ChatQuestionHit::with(['user', 'questions_with_trashed']);
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
            $chatQuestionHit =  $chatQuestionHit->whereHas('user', function ($q) use ($assignedCountry) {
                $q->whereIn('country_id', explode(',', $assignedCountry));
            });
        }
        if ($assignedState != '' && $assignedState > 0) {
            $chatQuestionHit =  $chatQuestionHit->whereHas('user.state', function ($q) use ($assignedState) {
                $q->whereIn('id', explode(',', $assignedState));
            });
        }
        if ($assignedDistrict != '' && $assignedDistrict > 0) {
            $chatQuestionHit =  $chatQuestionHit->whereHas('user.district', function ($q) use ($assignedDistrict) {
                $q->whereIn('id', explode(',', $assignedDistrict));
            });
        }
        if ($assignedCadre != '' && $assignedCadre > 0) {
            $chatQuestionHit =  $chatQuestionHit->whereHas('user.cadre', function ($q) use ($assignedCadre) {
                $q->whereIn('id', explode(',', $assignedCadre));
            });
        }

        if ($newRquest->has('subscriber_id') && $newRquest['subscriber_id'] != NULL && $newRquest['subscriber_id'] != 'null') {
            $chatQuestionHit =  $chatQuestionHit->where('subscriber_id', $newRquest->subscriber_id);
        }
        if ($newRquest->has('category') && $newRquest['category'] != NULL && $newRquest['category'] != 'null') {
            $chatQuestionHit =  $chatQuestionHit->whereHas('questions_with_trashed', function ($q) use ($newRquest) {
                $q->where('category', $newRquest->category);
            });
        }
        return $chatQuestionHit->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.chat-question-hit.columns.id'),
            trans('admin.chat-question-hit.columns.question_id'),
            trans('admin.chat-question-hit.columns.subscriber_id'),
            'Created_at',
        ];
    }

    /**
     * @param ChatQuestionHit $chatQuestionHit
     * @return array
     *
     */
    public function map($chatQuestionHit): array
    {
        return [
            $chatQuestionHit->id,
            $chatQuestionHit->questions_with_trashed->question,
            $chatQuestionHit->user->name,
            $chatQuestionHit->created_at,
        ];
    }
}
