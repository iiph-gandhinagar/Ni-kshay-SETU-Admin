<?php

namespace App\Observers;

use App\Models\Assessment;
use App\Models\AssessmentEnrollment;
use App\Models\Subscriber;
use Exception;
use Log;

class AssessmentObserver
{
    /**
     * Handle the Assessment "created" event.
     *
     * @param  \App\Models\Assessment  $assessment
     * @return void
     */
    public function created(Assessment $assessment)
    {
        try {
            if ($assessment->activated == 1 && $assessment->assessment_type == "planned") {
                $suitable_subscribers = Subscriber::whereRaw("find_in_set(state_id, ?)", [$assessment['state_id']])
                    ->whereRaw("find_in_set(cadre_id, ?)", [$assessment['cadre_id']])
                    ->orWhereRaw("find_in_set(country_id, ?)", [$assessment['country_id']])
                    ->orWhereRaw("find_in_set(district_id, ?)", [$assessment['district_id']])
                    ->get(['cadre_id', 'id', 'district_id', 'state_id', 'country_id']);
                $newRequest['assessment_id'] = $assessment->id;
                $newRequest['response'] = "Requested";
                $newRequest['send_inital_invitation'] = 0;
                foreach ($suitable_subscribers as $user) {
                    $newRequest['user_id'] = $user->id;
                    AssessmentEnrollment::create($newRequest);
                }
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    /**
     * Handle the Assessment "updated" event.
     *
     * @param  \App\Models\Assessment  $assessment
     * @return void
     */
    public function updated(Assessment $assessment)
    {
        try {
            if (($assessment->isDirty('activated')  || $assessment->isDirty('assessment_type')) && $assessment->activated == 1 && $assessment->assessment_type == "planned") {
                $existing_user = AssessmentEnrollment::where('assessment_id', $assessment->id)->count();
                if ($existing_user > 0) {
                    AssessmentEnrollment::where('assessment_id', $assessment->id)->delete();
                    $suitable_subscribers = Subscriber::whereRaw("find_in_set(state_id, ?)", [$assessment['state_id']])
                        ->whereRaw("find_in_set(cadre_id, ?)", [$assessment['cadre_id']])
                        ->orWhereRaw("find_in_set(country_id, ?)", [$assessment['country_id']])
                        ->orWhereRaw("find_in_set(district_id, ?)", [$assessment['district_id']])
                        // ->toSql();
                        ->get(['cadre_id', 'id', 'district_id', 'state_id', 'country_id']);
                    $newRequest['assessment_id'] = $assessment->id;
                    $newRequest['response'] = "Requested";
                    $newRequest['send_inital_invitation'] = 0;
                    foreach ($suitable_subscribers as $user) {
                        $newRequest['user_id'] = $user->id;
                        AssessmentEnrollment::create($newRequest);
                    }
                } else {
                    $suitable_subscribers = Subscriber::whereRaw("find_in_set(state_id, ?)", [$assessment['state_id']])
                        ->whereRaw("find_in_set(cadre_id, ?)", [$assessment['cadre_id']])
                        ->orWhereRaw("find_in_set(country_id, ?)", [$assessment['country_id']])
                        ->whereRaw("find_in_set(district_id, ?)", [$assessment['district_id']])
                        // ->toSql();
                        ->get(['cadre_id', 'id', 'district_id', 'state_id', 'country_id']);
                    $newRequest['assessment_id'] = $assessment->id;
                    $newRequest['response'] = "Requested";
                    $newRequest['send_inital_invitation'] = 0;
                    foreach ($suitable_subscribers as $user) {
                        $newRequest['user_id'] = $user->id;
                        AssessmentEnrollment::create($newRequest);
                    }
                }
            } else if ($assessment->activated == 0 && $assessment->assessment_type == "planned") {
                Assessment::where('id', $assessment->id)->update(['initial_invitation' => 0]);
            } else {
                Assessment::where('id', $assessment->id)->update(['initial_invitation' => 1]);
            }
        } catch (Exception $e) {
            Log::error($e);
        }
    }

    /**
     * Handle the Assessment "deleted" event.
     *
     * @param  \App\Models\Assessment  $assessment
     * @return void
     */
    public function deleted(Assessment $assessment)
    {
        //
    }

    /**
     * Handle the Assessment "restored" event.
     *
     * @param  \App\Models\Assessment  $assessment
     * @return void
     */
    public function restored(Assessment $assessment)
    {
        //
    }

    /**
     * Handle the Assessment "force deleted" event.
     *
     * @param  \App\Models\Assessment  $assessment
     * @return void
     */
    public function forceDeleted(Assessment $assessment)
    {
        //
    }
}
