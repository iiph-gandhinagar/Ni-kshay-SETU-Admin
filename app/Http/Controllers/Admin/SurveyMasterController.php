<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SurveyMasterExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SurveyMaster\BulkDestroySurveyMaster;
use App\Http\Requests\Admin\SurveyMaster\DestroySurveyMaster;
use App\Http\Requests\Admin\SurveyMaster\IndexSurveyMaster;
use App\Http\Requests\Admin\SurveyMaster\StoreSurveyMaster;
use App\Http\Requests\Admin\SurveyMaster\UpdateSurveyMaster;
use App\Models\AutomaticNotification;
use App\Models\Cadre;
use App\Models\Country;
use App\Models\District;
use App\Models\State;
use App\Models\Subscriber;
use App\Models\SurveyMaster;
use App\Models\SurveyMasterQuestion;
use App\Models\UserDeviceToken;
use Brackets\AdminListing\Facades\AdminListing;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;
use Log;
use Config;

class SurveyMasterController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexSurveyMaster $request
     * @return array|Factory|View
     */
    public function index(IndexSurveyMaster $request)
    {
        $survey_question = SurveyMasterQuestion::get(['id', 'survey_master_id']);
        $state = State::get(['id', 'title']);
        $districts = District::get(['id', 'title']);
        $cadre = Cadre::get(['id', 'title']);
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(SurveyMaster::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title', 'country_id', 'cadre_id', 'state_id', 'district_id', 'cadre_type', 'order_index', 'active', 'created_at', 'send_initial_notification'],

            // set columns to searchIn
            ['id', 'title', 'country_id', 'cadre_id', 'state_id', 'district_id', 'cadre_type'],
            function ($query) use ($request) {
                // $query->with(['survey_history']);
                $assignedState = \Auth::user()->state;
                if ($assignedState != '' && $assignedState > 0) {
                    $query->whereRaw("find_in_set('" . $assignedState . "',state_id)");
                }
            }
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.survey-master.index', [
            'data' => $data,
            'state' => $state,
            'districts' => $districts,
            'cadre' => $cadre,
            'survey_question' => $survey_question
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.survey-master.create');
        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithSubscriber();
        $states = $masterData['state'];
        $districts = $masterData['district'];
        $cadres = $masterData['cadres'];
        $country = Country::get(['id', 'title']);

        return view('admin.survey-master.create', [
            'cadre' => $cadres,
            'state' => $states,
            'district' => $districts,
            'country' => $country,
            'user_state' => \Auth::user()->state
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSurveyMaster $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreSurveyMaster $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        $sanitized['cadre_id'] = array_pluck($sanitized['cadre_id'], 'id');
        $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);

        if (isset($request['country_id']) && $request['country_id'] != '') {

            $sanitized['country_id'] = $request['country_id']['id'];
        } else {
            $sanitized['country_id'] = 0;
        }

        $sanitized['state_id'] = array_pluck($sanitized['state_id'], 'id');
        $sanitized['state_id'] = implode(",", $sanitized['state_id']);

        $sanitized['district_id'] = array_pluck($sanitized['district_id'], 'id');
        $sanitized['district_id'] = implode(",", $sanitized['district_id']);
        // Store the SurveyMaster
        $surveyMaster = SurveyMaster::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/survey-masters'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/survey-masters');
    }

    /**
     * Display the specified resource.
     *
     * @param SurveyMaster $surveyMaster
     * @throws AuthorizationException
     * @return void
     */
    public function show(SurveyMaster $surveyMaster)
    {
        $this->authorize('admin.survey-master.show', $surveyMaster);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param SurveyMaster $surveyMaster
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(SurveyMaster $surveyMaster)
    {

        if (\Auth::user()->roles[0]['id'] == 3) {
            $surveyMaster->load('user');
            if ($surveyMaster->user->roles[0]->id != 3) {
                abort(403);
            } else {
                $this->authorize('admin.survey-master.edit', $surveyMaster);
            }
        } else {
            $this->authorize('admin.survey-master.edit', $surveyMaster);
        }
        $cadres = Cadre::get(['id', 'title', 'cadre_type']);
        $country = Country::get(['id', 'title']);
        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithSubscriber();
        $states = $masterData['state'];
        $districts = $masterData['district'];

        //needed to show multiselect selected value
        if (isset($surveyMaster['cadre_id']) && $surveyMaster['cadre_id'] != "") {
            $assignedCadres = explode(',', $surveyMaster['cadre_id']);
            $surveyMaster['cadre_id'] = Cadre::whereIn('id', $assignedCadres)->get(['id', 'title', 'cadre_type']);
        }

        if (isset($surveyMaster['country_id']) && $surveyMaster['country_id'] != "") {
            $surveyMaster['country_id'] = $surveyMaster['country_id'];
            $surveyMaster['country_id'] = Country::where('id', $surveyMaster['country_id'])->get(['id', 'title']);
        }

        if (isset($surveyMaster['state_id']) && $surveyMaster['state_id'] != "") {
            $assignedStates = explode(',', $surveyMaster['state_id']);
            $surveyMaster['state_id'] = State::whereIn('id', $assignedStates)->get();
        }

        if (isset($surveyMaster['district_id']) && $surveyMaster['district_id'] != "") {
            $assignedDistricts = explode(',', $surveyMaster['district_id']);
            $surveyMaster['district_id'] = District::whereIn('id', $assignedDistricts)->get();
        } else {
            $surveyMaster['district_id'] = [];
        }

        $surveyMaster['all_cadres'] = $cadres;
        $surveyMaster['all_states'] = $states;

        return view('admin.survey-master.edit', [
            'surveyMaster' => $surveyMaster,
            'cadre' => $cadres,
            'state' => $states,
            'district' => $districts,
            'country' => $country,
            'user_state' => \Auth::user()->state
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSurveyMaster $request
     * @param SurveyMaster $surveyMaster
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateSurveyMaster $request, SurveyMaster $surveyMaster)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        //arrray to string for multiple cadres
        $sanitized['cadre_id'] = array_pluck($sanitized['cadre_id'], 'id');
        $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);

        if (isset($request['country_id'])) {
            if ($request['country_id'] == NULL) {
                $sanitized['country_id'] = 0;
            } else {
                $country_id = collect($request['country_id'])->pluck('id');
                if (is_numeric($country_id[0]) && $country_id[0] > 0) {
                    $sanitized['country_id'] = $country_id[0];
                } else {
                    $sanitized['country_id'] = $request['country_id']['id'];
                }
            }
        }

        $sanitized['state_id'] = array_pluck($sanitized['state_id'], 'id');
        $sanitized['state_id'] = implode(",", $sanitized['state_id']);

        $sanitized['district_id'] = array_pluck($sanitized['district_id'], 'id');
        $sanitized['district_id'] = implode(",", $sanitized['district_id']);
        // Update changed values SurveyMaster
        $surveyMaster->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/survey-masters'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/survey-masters');
    }

    public function sendInitialInvitation(Request $request, SurveyMaster $surveyMaster)
    {
        $message = "";
        try {
            if ($surveyMaster->country_id != 0) {
                $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $surveyMaster->cadre_id . "')")
                    ->whereRaw("find_in_set(country_id, ?)", [$surveyMaster->country_id])
                    ->pluck('id');
            } elseif ($surveyMaster->district_id != '') {
                $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $surveyMaster->cadre_id . "')")
                    ->whereRaw("find_in_set(district_id, ?)", [$surveyMaster->district_id])
                    // ->toSql();
                    ->pluck('id');
            } else {
                $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $surveyMaster->cadre_id . "')")
                    ->whereRaw("find_in_set(state_id,'" . $surveyMaster->state_id . "')")
                    // ->toSql();
                    ->pluck('id');
            }

            $notification['title'] = "New Survey";
            $notification['description'] = "$surveyMaster->title";

            $device_id = UserDeviceToken::whereIn('user_id', $subscriber)->get('notification_token');
            if (isset($device_id) && count($device_id) > 0) {
                $notification['type'] = "Survey";
                $notification['subscriber_id'] = implode(',', $subscriber->toArray());
                $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/SurveyFormList";
                $notification['created_by'] = \Auth::user()->id;
                AutomaticNotification::create($notification);
                $response = SendNotificationController::surveyForms($notification, $device_id);
                if (isset($response['error'])) {
                    $message = "User Not Found";
                } else {
                    $successCount = isset($response['successFullCount']) && $response['successFullCount'] > 0 ? $response['successFullCount'] : 0;
                    $failCount = isset($response['failedCount']) && $response['failedCount'] > 0 ? $response['failedCount'] : 0;
                    $message = "You have successfully added notification. Your notification is successfully send to " . $successCount . " Subscribers and Failed for " . $failCount . " Subscribers.";
                }
            }
            SurveyMaster::where('id', $surveyMaster->id)->update(['send_initial_notification' => 1]);

            if ($request->ajax()) {
                session(['message' => $message]);
                return [
                    'redirect' => url('admin/survey-masters'),
                    'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
                ];
            }

            return redirect('admin/survey-masters')->with('message', $message);
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing survey function");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroySurveyMaster $request
     * @param SurveyMaster $surveyMaster
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroySurveyMaster $request, SurveyMaster $surveyMaster)
    {
        $surveyMaster->delete();
        SurveyMasterQuestion::where('survey_master_id', $surveyMaster->id)->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroySurveyMaster $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroySurveyMaster $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('surveyMasters')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    /**
     * Export entities
     *
     * @return BinaryFileResponse|null
     */
    public function export(): ?BinaryFileResponse
    {
        return Excel::download(app(SurveyMasterExport::class), 'surveyMasters.xlsx');
    }
}