<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DiagnosesAlgorithm\BulkDestroyDiagnosesAlgorithm;
use App\Http\Requests\Admin\DiagnosesAlgorithm\DestroyDiagnosesAlgorithm;
use App\Http\Requests\Admin\DiagnosesAlgorithm\IndexDiagnosesAlgorithm;
use App\Http\Requests\Admin\DiagnosesAlgorithm\StoreDiagnosesAlgorithm;
use App\Http\Requests\Admin\DiagnosesAlgorithm\UpdateDiagnosesAlgorithm;
use App\Jobs\sendNotification;
use App\Models\AutomaticNotification;
use App\Models\Cadre;
use App\Models\DiagnosesAlgorithm;
use App\Models\State;
use App\Models\Subscriber;
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

class DiagnosesAlgorithmsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexDiagnosesAlgorithm $request
     * @return array|Factory|View
     */

    public function getMasterNodes()
    {
        $this->authorize('admin.diagnoses-algorithm.index');
        $data = DiagnosesAlgorithm::where('parent_id', 0)->orderBy('index')->get();
        return view('admin.diagnoses-algorithm.master-nodes', ['data' => $data]);
    }

    public function index(IndexDiagnosesAlgorithm $request)
    {
        $state = State::get(['id', 'title']);
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(DiagnosesAlgorithm::class)
            ->modifyQuery(function ($query) use ($request) {
                if (isset($request['master']) && $request['master'] != '' && $request->orderDirection == "") {
                    $query->where('parent_id', $request['master']);
                    $query->orderBy('index', 'DESC');
                }
                if (isset($request['master']) && $request['master'] != '') {
                    $query->where('parent_id', $request['master']);
                    $query->orderBy('index', 'DESC');
                }
                if (isset($request['master']) && $request['master'] != '') {
                    $query->where('parent_id', $request['master']);
                    $query->orderBy('index', 'DESC');
                }
            })
            ->processRequestAndGet(
                // pass the request with params
                $request,

                // set columns to query
                ['id', 'node_type', 'is_expandable', 'has_options', 'parent_id', 'master_node_id', 'state_id', 'cadre_id', 'index', 'title', 'description', 'time_spent', 'redirect_algo_type', 'redirect_node_id', 'header', 'sub_header', 'activated', 'created_at', 'send_initial_notification'],

                // set columns to searchIn
                ['id', 'node_type', 'title', 'description', 'time_spent', 'redirect_algo_type', 'header', 'sub_header', 'master_node_id', 'created_at'],
                // function ($q) use($request) {
                //     $q->where('activated',1);
                // }
            );
        // $this->setTranslate();
        if ($request->ajax()) {
            $request->session()->pull('diagnosis_notification');
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }
        return view('admin.diagnoses-algorithm.index', ['data' => $data, 'state' => $state, 'message' => session('diagnosis_notification')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.diagnoses-algorithm.create');

        $state = State::get(['id', 'title']);
        $cadre = Cadre::get(['id', 'title']);

        return view('admin.diagnoses-algorithm.create', [
            'state' => $state,
            'cadre' => $cadre,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDiagnosesAlgorithm $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreDiagnosesAlgorithm $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        if ($request['redirect_node_id'] == null || $request['redirect_node_id'] == NULL) {
            $sanitized['redirect_node_id'] = 0;
        } else {
            $sanitized['redirect_node_id'] = $request['redirect_node_id'];
        }
        if (isset($sanitized['state_id']) && isset($sanitized['state_id'][0]) && $sanitized['state_id'][0] != '') {
            $sanitized['state_id'] = implode(",", $sanitized['state_id']);
        }

        if (isset($sanitized['cadre_id']) && isset($sanitized['cadre_id'][0]) && $sanitized['cadre_id'][0] != '') {
            $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);
        }

        // Store the DiagnosesAlgorithm
        $diagnosesAlgorithm = DiagnosesAlgorithm::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/diagnoses-algorithms?master=' . $request['parent_id']), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/diagnoses-algorithms?master=' . $request['parent_id']);
    }

    /**
     * Display the specified resource.
     *
     * @param DiagnosesAlgorithm $diagnosesAlgorithm
     * @throws AuthorizationException
     * @return void
     */
    public function show(DiagnosesAlgorithm $diagnosesAlgorithm)
    {
        $this->authorize('admin.diagnoses-algorithm.show', $diagnosesAlgorithm);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param DiagnosesAlgorithm $diagnosesAlgorithm
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(DiagnosesAlgorithm $diagnosesAlgorithm)
    {
        $this->authorize('admin.diagnoses-algorithm.edit', $diagnosesAlgorithm);
        $state = State::get(['id', 'title']);
        $cadres = Cadre::get(['id', 'title']);
        if (isset($diagnosesAlgorithm['state_id']) && $diagnosesAlgorithm['state_id'] != "") {
            $diagnosesAlgorithm['state_id'] = explode(',', $diagnosesAlgorithm['state_id']);
        } else {
            $diagnosesAlgorithm['state_id'] = [];
        }
        if (isset($diagnosesAlgorithm['cadre_id']) && $diagnosesAlgorithm['cadre_id'] != "") {
            $diagnosesAlgorithm['cadre_id'] = explode(',', $diagnosesAlgorithm['cadre_id']);
        } else {
            $diagnosesAlgorithm['cadre_id'] = [];
        }

        $diagnosesAlgorithm['all_cadres'] = $cadres;
        $diagnosesAlgorithm['all_states'] = $state;

        return view('admin.diagnoses-algorithm.edit', [
            'diagnosesAlgorithm' => $diagnosesAlgorithm,
            'state' => $state,
            'cadre' => $cadres,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDiagnosesAlgorithm $request
     * @param DiagnosesAlgorithm $diagnosesAlgorithm
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateDiagnosesAlgorithm $request, DiagnosesAlgorithm $diagnosesAlgorithm)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($sanitized['state_id']) && isset($sanitized['state_id'][0]) && $sanitized['state_id'][0] != '') {
            $sanitized['state_id'] = implode(",", $sanitized['state_id']);
        }

        if (isset($sanitized['cadre_id']) && isset($sanitized['cadre_id'][0]) && $sanitized['cadre_id'][0] != '') {
            $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);
        }

        // Update changed values DiagnosesAlgorithm
        $diagnosesAlgorithm->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/diagnoses-algorithms?master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/diagnoses-algorithms?master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']);
    }

    public function sendInitialInvitation(Request $request, DiagnosesAlgorithm $diagnosesAlgorithm)
    {
        $message = "";
        try {
            if ($diagnosesAlgorithm->state_id != '' && $diagnosesAlgorithm->cadre_id != '') {
                $state = State::count();
                $cadre = Cadre::count();
                $explode_state = count(explode(',', $diagnosesAlgorithm->state_id));
                $explode_cadre = count(explode(',', $diagnosesAlgorithm->cadre_id));
                if ($state == $explode_state && $cadre == $explode_cadre) {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $diagnosesAlgorithm->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $diagnosesAlgorithm->state_id . "')")
                        ->orWhere('country_id', 1)
                        ->pluck('id');
                } else {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $diagnosesAlgorithm->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $diagnosesAlgorithm->state_id . "')")
                        ->pluck('id');
                }
            } elseif ($diagnosesAlgorithm->state_id != '' && $diagnosesAlgorithm->cadre_id == '') {
                $subscriber = Subscriber::whereRaw("find_in_set(state_id,'" . $diagnosesAlgorithm->state_id . "')")
                    // ->toSql();
                    ->pluck('id');
            } else {
                $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $diagnosesAlgorithm->cadre_id . "')")
                    // ->toSql();
                    ->pluck('id');
            }

            $notification['title'] = "New Module";
            $notification['description'] = "$diagnosesAlgorithm->title";

            $device_id = UserDeviceToken::whereIn('user_id', $subscriber)->get('notification_token'); //$subscriber
            if (isset($device_id) && count($device_id) > 0) {
                $notification['type'] = "Diagnosis Algorithm";
                $notification['subscriber_id'] = implode(',', $subscriber->toArray());
                $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/AlgorithmList/TITLE_DIAGNOSIS_ALGORITHM/Diagnosis Algorithm/null";
                $notification['created_by'] = \Auth::user()->id;
                $notification['status'] = 'Pending';
                $userNotification = AutomaticNotification::create($notification);
                // $response = SendNotificationController::newModules($notification,$device_id,Config::get('app.GENERAL.frontend_url')."/AlgorithmList/TITLE_DIAGNOSIS_ALGORITHM/Diagnosis Algorithm/null");
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
            DiagnosesAlgorithm::where('id', $diagnosesAlgorithm->id)->update(['send_initial_notification' => 1]);
            session(['diagnosis_notification' => $message]);
            if ($request->ajax()) {
                session(['diagnosis_notification' => $message]);
                return [
                    'redirect' => url('admin/diagnoses-algorithms?master=0&master_node_id=0'),
                    'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
                ];
            }

            return redirect('admin/diagnoses-algorithms?master=0&master_node_id=0')->with('message', $message);
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing Diagnosis algorithm function");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyDiagnosesAlgorithm $request
     * @param DiagnosesAlgorithm $diagnosesAlgorithm
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyDiagnosesAlgorithm $request, DiagnosesAlgorithm $diagnosesAlgorithm)
    {
        $diagnosesAlgorithm->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyDiagnosesAlgorithm $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyDiagnosesAlgorithm $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('diagnosesAlgorithms')->whereIn('id', $bulkChunk)
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
        return view('admin.diagnoses-algorithm.org-chart');
    }

    public function setTranslate()
    {
        $diagnosesAlgorithm = DiagnosesAlgorithm::get();

        foreach ($diagnosesAlgorithm as $key => $diagnosesAlgorithms) {
            $diagnosesAlgorithms = DiagnosesAlgorithm::find($diagnosesAlgorithms->id);
            $diagnosesAlgorithms->setTranslation('header', 'en', $diagnosesAlgorithm[$key]->header)->save();
            $diagnosesAlgorithms->setTranslation('sub_header', 'en', $diagnosesAlgorithm[$key]->sub_header)->save();
            // $diagnosesAlgorithms->setTranslation('title_value_json', 'en', $diagnosesAlgorithm[$key]->title)->save();
            // $diagnosesAlgorithms->setTranslation('description_value_json', 'en', $diagnosesAlgorithm[$key]->description)->save();
        }
    }
}