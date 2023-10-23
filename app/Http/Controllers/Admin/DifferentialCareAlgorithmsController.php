<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DifferentialCareAlgorithm\BulkDestroyDifferentialCareAlgorithm;
use App\Http\Requests\Admin\DifferentialCareAlgorithm\DestroyDifferentialCareAlgorithm;
use App\Http\Requests\Admin\DifferentialCareAlgorithm\IndexDifferentialCareAlgorithm;
use App\Http\Requests\Admin\DifferentialCareAlgorithm\StoreDifferentialCareAlgorithm;
use App\Http\Requests\Admin\DifferentialCareAlgorithm\UpdateDifferentialCareAlgorithm;
use App\Jobs\sendNotification;
use App\Models\AutomaticNotification;
use App\Models\Cadre;
use App\Models\DifferentialCareAlgorithm;
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

class DifferentialCareAlgorithmsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexDifferentialCareAlgorithm $request
     * @return array|Factory|View
     */


    public function getMasterNodes()
    {
        $this->authorize('admin.differential-care-algorithm.index');
        $data = DifferentialCareAlgorithm::where('parent_id', 0)->orderBy('index')->get();
        return view('admin.differential-care-algorithm.master-nodes', ['data' => $data]);
    }

    public function index(IndexDifferentialCareAlgorithm $request)
    {
        $state = State::get(['id', 'title']);
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(DifferentialCareAlgorithm::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title', 'node_type', 'is_expandable', 'has_options', 'parent_id', 'master_node_id', 'state_id', 'cadre_id', 'description', 'time_spent', 'index', 'redirect_algo_type', 'redirect_node_id', 'header', 'sub_header', 'activated', 'send_initial_notification'],

            // set columns to searchIn
            ['id', 'title', 'node_type', 'description', 'time_spent', 'redirect_algo_type', 'header', 'sub_header'],
            function ($q) use ($request) {
                // $q->where('activated',1);
                if (isset($request['master']) && $request['master'] != '') {
                    $q->where('parent_id', $request['master'])->orderBy('index', 'desc');
                }
            }
        );
        // $this->setTranslate();

        if ($request->ajax()) {
            $request->session()->pull('differential_care_notification');
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }
        return view('admin.differential-care-algorithm.index', ['data' => $data, 'state' => $state, 'message' => session('differential_care_notification')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.differential-care-algorithm.create');
        $state = State::get(['id', 'title']);
        $cadre = Cadre::get(['id', 'title']);
        return view('admin.differential-care-algorithm.create', [
            'state' => $state,
            'cadre' => $cadre,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDifferentialCareAlgorithm $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreDifferentialCareAlgorithm $request)
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
        // Store the DifferentialCareAlgorithm
        $differentialCareAlgorithm = DifferentialCareAlgorithm::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/differential-care-algorithms?master=' . $request['parent_id']), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/differential-care-algorithms?master=' . $request['parent_id']);
    }

    /**
     * Display the specified resource.
     *
     * @param DifferentialCareAlgorithm $differentialCareAlgorithm
     * @throws AuthorizationException
     * @return void
     */
    public function show(DifferentialCareAlgorithm $differentialCareAlgorithm)
    {
        $this->authorize('admin.differential-care-algorithm.show', $differentialCareAlgorithm);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param DifferentialCareAlgorithm $differentialCareAlgorithm
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(DifferentialCareAlgorithm $differentialCareAlgorithm)
    {
        $this->authorize('admin.differential-care-algorithm.edit', $differentialCareAlgorithm);
        $state = State::get(['id', 'title']);
        $cadres = Cadre::get(['id', 'title']);
        if (isset($differentialCareAlgorithm['state_id']) && $differentialCareAlgorithm['state_id'] != "") {
            $differentialCareAlgorithm['state_id'] = explode(',', $differentialCareAlgorithm['state_id']);
        } else {
            $differentialCareAlgorithm['state_id'] = [];
        }
        if (isset($differentialCareAlgorithm['cadre_id']) && $differentialCareAlgorithm['cadre_id'] != "") {
            $differentialCareAlgorithm['cadre_id'] = explode(',', $differentialCareAlgorithm['cadre_id']);
        } else {
            $differentialCareAlgorithm['cadre_id'] = [];
        }

        $differentialCareAlgorithm['all_cadres'] = $cadres;
        $differentialCareAlgorithm['all_states'] = $state;

        return view('admin.differential-care-algorithm.edit', [
            'differentialCareAlgorithm' => $differentialCareAlgorithm,
            'state' => $state,
            'cadre' => $cadres,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDifferentialCareAlgorithm $request
     * @param DifferentialCareAlgorithm $differentialCareAlgorithm
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateDifferentialCareAlgorithm $request, DifferentialCareAlgorithm $differentialCareAlgorithm)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($sanitized['state_id']) && isset($sanitized['state_id'][0]) && $sanitized['state_id'][0] != '') {
            $sanitized['state_id'] = implode(",", $sanitized['state_id']);
        }

        if (isset($sanitized['cadre_id']) && isset($sanitized['cadre_id'][0]) && $sanitized['cadre_id'][0] != '') {
            $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);
        }

        // Update changed values DifferentialCareAlgorithm
        $differentialCareAlgorithm->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/differential-care-algorithms?master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/differential-care-algorithms?master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']);
    }

    public function sendInitialInvitation(Request $request, DifferentialCareAlgorithm $differentialCareAlgorithm)
    {
        $message = "";
        try {
            if ($differentialCareAlgorithm->state_id != '' && $differentialCareAlgorithm->cadre_id != '') {
                $state = State::count();
                $cadre = Cadre::count();
                $explode_state = count(explode(',', $differentialCareAlgorithm->state_id));
                $explode_cadre = count(explode(',', $differentialCareAlgorithm->cadre_id));
                if ($state == $explode_state && $cadre == $explode_cadre) {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $differentialCareAlgorithm->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $differentialCareAlgorithm->state_id . "')")
                        ->orWhere('country_id', 1)
                        ->pluck('id');
                } else {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $differentialCareAlgorithm->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $differentialCareAlgorithm->state_id . "')")
                        ->pluck('id');
                }
            } elseif ($differentialCareAlgorithm->state_id != '' && $differentialCareAlgorithm->cadre_id == '') {
                $subscriber = Subscriber::whereRaw("find_in_set(state_id,'" . $differentialCareAlgorithm->state_id . "')")->orWhere('country_id', 1)
                    // ->toSql();
                    ->pluck('id');
            } else {
                $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $differentialCareAlgorithm->cadre_id . "')")->orWhere('country_id', 1)
                    // ->toSql();
                    ->pluck('id');
            }

            $notification['title'] = "New Module";
            $notification['description'] = "$differentialCareAlgorithm->title";

            $device_id = UserDeviceToken::whereIn('user_id', $subscriber)->get('notification_token'); //$subscriber
            if (isset($device_id) && count($device_id) > 0) {
                $notification['type'] = "Differentiated Care Of TB Patients";
                $notification['subscriber_id'] = implode(',', $subscriber->toArray());
                $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/AlgorithmList/TITLE_DIFFERENTIANTED_CARE/Differentiated Care Of TB Patients/null";
                $notification['created_by'] = \Auth::user()->id;
                $notification['status'] = 'Pending';
                $userNotification = AutomaticNotification::create($notification);
                // $response = SendNotificationController::newModules($notification,$device_id,Config::get('app.GENERAL.frontend_url')."/AlgorithmList/TITLE_DIFFERENTIANTED_CARE/Differentiated Care Of TB Patients/null");
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
            DifferentialCareAlgorithm::where('id', $differentialCareAlgorithm->id)->update(['send_initial_notification' => 1]);

            session(['differential_care_notification' => $message]);
            if ($request->ajax()) {
                session(['differential_care_notification' => $message]);
                return [
                    'redirect' => url('admin/differential-care-algorithms?master=0&master_node_id=0'),
                    'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
                ];
            }
            return redirect('admin/differential-care-algorithms?master=0&master_node_id=0')->with('message', $message);
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing Differential care algorithm function");
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyDifferentialCareAlgorithm $request
     * @param DifferentialCareAlgorithm $differentialCareAlgorithm
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyDifferentialCareAlgorithm $request, DifferentialCareAlgorithm $differentialCareAlgorithm)
    {
        $differentialCareAlgorithm->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyDifferentialCareAlgorithm $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyDifferentialCareAlgorithm $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('differentialCareAlgorithms')->whereIn('id', $bulkChunk)
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
        return view('admin.differential-care-algorithm.org-chart');
    }

    public function setTranslate()
    {
        $differentialCareAlgorithms = DifferentialCareAlgorithm::get();

        foreach ($differentialCareAlgorithms as $key => $differentialCareAlgorithm) {
            $differentialCareAlgorithm = DifferentialCareAlgorithm::find($differentialCareAlgorithm->id);
            $differentialCareAlgorithm->setTranslation('header', 'en', $differentialCareAlgorithms[$key]->header)->save();
            $differentialCareAlgorithm->setTranslation('sub_header', 'en', $differentialCareAlgorithms[$key]->sub_header)->save();
            // $differentialCareAlgorithm->setTranslation('title_value_json', 'en', $differentialCareAlgorithms[$key]->title)->save();
            // $differentialCareAlgorithm->setTranslation('description_value_json', 'en', $differentialCareAlgorithms[$key]->description)->save();
        }
    }
}