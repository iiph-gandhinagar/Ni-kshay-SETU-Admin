<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CaseDefinition\BulkDestroyCaseDefinition;
use App\Http\Requests\Admin\CaseDefinition\DestroyCaseDefinition;
use App\Http\Requests\Admin\CaseDefinition\IndexCaseDefinition;
use App\Http\Requests\Admin\CaseDefinition\StoreCaseDefinition;
use App\Http\Requests\Admin\CaseDefinition\UpdateCaseDefinition;
use App\Jobs\sendNotification;
use App\Models\AutomaticNotification;
use App\Models\Cadre;
use App\Models\CaseDefinition;
use App\Models\DiagnosesAlgorithm;
use App\Models\GuidanceOnAdverseDrugReaction;
use App\Models\LatentTbInfection;
use App\Models\State;
use App\Models\Subscriber;
use App\Models\TreatmentAlgorithm;
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
use Illuminate\View\View;
use Log;
use Config;

class CaseDefinitionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCaseDefinition $request
     * @return array|Factory|View
     */
    public function getMasterNodes()
    {
        $this->authorize('admin.case-definition.index');
        $data = CaseDefinition::where('parent_id', 0)->orderBy('index')->get();
        return view('admin.case-definition.master-nodes', ['data' => $data]);
    }
    public function index(IndexCaseDefinition $request)
    {
        $state = State::get(['id', 'title']);
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(CaseDefinition::class)
            ->modifyQuery(function ($query) use ($request) {
                if (isset($request['master']) && $request['master'] != '' && $request->orderDirection == "") {
                    $query->where('parent_id', $request['master']);
                    $query->orderBy('index', 'DESC');
                }
                if (isset($request['master']) && $request['master'] != '' && $request->orderDirection == "asc") {
                    $query->where('parent_id', $request['master']);
                    $query->orderBy('index', 'DESC');
                }
                if (isset($request['master']) && $request['master'] != '' && $request->orderDirection == "desc") {
                    $query->where('parent_id', $request['master']);
                    $query->orderBy('index', 'DESC');
                }
            })
            ->processRequestAndGet(
                // pass the request with params
                $request,

                // set columns to query
                ['id', 'node_type', 'is_expandable', 'has_options', 'parent_id', 'master_node_id', 'state_id', 'cadre_id', 'index', 'title', 'description', 'time_spent', 'redirect_algo_type', 'redirect_node_id', 'header', 'sub_header', 'activated', 'send_initial_notification'],

                // set columns to searchIn
                ['id', 'node_type', 'title', 'description', 'time_spent', 'redirect_algo_type', 'header', 'sub_header'],
                // function ($q) use($request) {
                //     $q->where('activated',1);
                // }
            );
        // $this->setTranslate();
        if ($request->ajax()) {
            $request->session()->pull('case_def_notification');
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }
        return view('admin.case-definition.index', ['data' => $data, 'state' => $state, 'message' => session('case_def_notification')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.case-definition.create');
        $state = State::get(['id', 'title']);
        $cadre = Cadre::get(['id', 'title']);
        return view('admin.case-definition.create', [
            'state' => $state,
            'cadre' => $cadre,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCaseDefinition $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCaseDefinition $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($sanitized['state_id']) && isset($sanitized['state_id'][0]) && $sanitized['state_id'][0] != '') {
            $sanitized['state_id'] = implode(",", $sanitized['state_id']);
        }

        if (isset($sanitized['cadre_id']) && isset($sanitized['cadre_id'][0]) && $sanitized['cadre_id'][0] != '') {
            $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);
        }
        // Store the CaseDefinition
        $caseDefinition = CaseDefinition::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/case-definitions?master=' . $request['parent_id']), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/case-definitions?master=' . $request['parent_id']);
    }

    /**
     * Display the specified resource.
     *
     * @param CaseDefinition $caseDefinition
     * @throws AuthorizationException
     * @return void
     */
    public function show(CaseDefinition $caseDefinition)
    {
        $this->authorize('admin.case-definition.show', $caseDefinition);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CaseDefinition $caseDefinition
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(CaseDefinition $caseDefinition)
    {
        $this->authorize('admin.case-definition.edit', $caseDefinition);
        $state = State::get(['id', 'title']);
        $cadres = Cadre::get(['id', 'title']);
        if (isset($caseDefinition['state_id']) && $caseDefinition['state_id'] != "") {
            $caseDefinition['state_id'] = explode(',', $caseDefinition['state_id']);
        } else {
            $caseDefinition['state_id'] = [];
        }
        if (isset($caseDefinition['cadre_id']) && $caseDefinition['cadre_id'] != "") {
            $caseDefinition['cadre_id'] = explode(',', $caseDefinition['cadre_id']);
        } else {
            $caseDefinition['cadre_id'] = [];
        }

        $caseDefinition['all_cadres'] = $cadres;
        $caseDefinition['all_states'] = $state;

        return view('admin.case-definition.edit', [
            'caseDefinition' => $caseDefinition,
            'state' => $state,
            'cadre' => $cadres,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCaseDefinition $request
     * @param CaseDefinition $caseDefinition
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCaseDefinition $request, CaseDefinition $caseDefinition)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($sanitized['state_id']) && isset($sanitized['state_id'][0]) && $sanitized['state_id'][0] != '') {
            $sanitized['state_id'] = implode(",", $sanitized['state_id']);
        }

        if (isset($sanitized['cadre_id']) && isset($sanitized['cadre_id'][0]) && $sanitized['cadre_id'][0] != '') {
            $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);
        }
        // Update changed values CaseDefinition
        $caseDefinition->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/case-definitions?master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/case-definitions?master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']);
    }

    public function sendInitialInvitation(Request $request, CaseDefinition $caseDefinition)
    {
        $message = "";
        try {
            if ($caseDefinition->state_id != '' && $caseDefinition->cadre_id != '') {
                $state = State::count();
                $cadre = Cadre::count();
                $explode_state = count(explode(',', $caseDefinition->state_id));
                $explode_cadre = count(explode(',', $caseDefinition->cadre_id));
                if ($state == $explode_state && $cadre == $explode_cadre) {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $caseDefinition->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $caseDefinition->state_id . "')")
                        ->orWhere('country_id', 1)
                        ->pluck('id');
                } else {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $caseDefinition->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $caseDefinition->state_id . "')")
                        ->pluck('id');
                }
            } elseif ($caseDefinition->state_id != '' && $caseDefinition->cadre_id == '') {
                $subscriber = Subscriber::whereRaw("find_in_set(state_id,'" . $caseDefinition->state_id . "')")
                    // ->toSql();
                    ->pluck('id');
            } else {
                $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $caseDefinition->cadre_id . "')")
                    // ->toSql();
                    ->pluck('id');
            }

            $notification['title'] = "New Module";
            $notification['description'] = "$caseDefinition->title";

            $device_id = UserDeviceToken::whereIn('user_id', $subscriber)->get('notification_token'); //$subscriber
            if (isset($device_id) && count($device_id) > 0) {
                $notification['type'] = "Case Definition";
                $notification['subscriber_id'] = implode(',', $subscriber->toArray());
                $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/AlgorithmList/TITLE_CASE_DEFINITION/Case Definition/null";
                $notification['created_by'] = \Auth::user()->id;
                $notification['status'] = 'Pending';
                $userNotification = AutomaticNotification::create($notification);
                // $response = SendNotificationController::newModules($notification,$device_id,Config::get('app.GENERAL.frontend_url')."/AlgorithmList/TITLE_CASE_DEFINITION/Case Definition/null");
                dispatch(new sendNotification($notification, $subscriber, 'Algorithm', $notification['linking_url'], $userNotification['id'], 'true'));
                $message = "Notification queued. Check status later.";
                // if (isset($response['error'])) {
                //     $message = "User Not Found";
                // } else {
                //     $successCount = isset($response['successFullCount']) && $response['successFullCount'] > 0 ? $response['successFullCount'] : 0;
                //     $failCount = isset($response['failedCount']) && $response['failedCount'] > 0 ? $response['failedCount'] : 0;
                //     $message = "You have successfully added notification. Your notification is successfully send to " . $successCount . " Subscribers and Failed for " . $failCount . " Subscribers.";
                // }
            }
            CaseDefinition::where('id', $caseDefinition->id)->update(['send_initial_notification' => 1]);

            session(['case_def_notification' => $message]);
            if ($request->ajax()) {
                session(['case_def_notification' => $message]);
                return [
                    'redirect' => url('admin/case-definitions?master=0&master_node_id=0'),
                    'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
                ];
            }
            return redirect('admin/case-definitions?master=0&master_node_id=0')->with('message', $message);
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing case definitions function");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCaseDefinition $request
     * @param CaseDefinition $caseDefinition
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCaseDefinition $request, CaseDefinition $caseDefinition)
    {
        $caseDefinition->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCaseDefinition $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCaseDefinition $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('caseDefinitions')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    public function getTreeViewData()
    {
        return view('admin.case-definition.org-chart');
    }

    public function setTranslate()
    {
        $caseDefinition = CaseDefinition::get();

        foreach ($caseDefinition as $key => $caseDefinitions) {
            $caseDefinitions = CaseDefinition::find($caseDefinitions->id);
            $caseDefinitions->setTranslation('header', 'en', $caseDefinition[$key]->header)->save();
            $caseDefinitions->setTranslation('sub_header', 'en', $caseDefinition[$key]->sub_header)->save();
            // $caseDefinitions->setTranslation('title_value_json', 'en', $caseDefinition[$key]->title)->save();
            // $caseDefinitions->setTranslation('description_value_json', 'en', $caseDefinition[$key]->description)->save();
        }
    }

    public function getMasterNodeByType($type)
    {
        $masterNodes = [];
        if ($type == 'Case Definition') {
            $masterNodes = CaseDefinition::where('parent_id', 0)->get(['id', 'title']);
        } else if ($type == 'Diagnosis Algorithm') {
            $masterNodes = DiagnosesAlgorithm::where('parent_id', 0)->get(['id', 'title']);
        } else if ($type == 'Guidance on ADR') {
            $masterNodes = GuidanceOnAdverseDrugReaction::where('parent_id', 0)->get(['id', 'title']);
        } else if ($type == 'Latent TB Infection') {
            $masterNodes = LatentTbInfection::where('parent_id', 0)->get(['id', 'title']);
        } else if ($type == 'Treatment Algorithm') {
            $masterNodes = TreatmentAlgorithm::where('parent_id', 0)->get(['id', 'title']);
        }

        return $masterNodes;
    }
}