<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DynamicAlgorithm\BulkDestroyDynamicAlgorithm;
use App\Http\Requests\Admin\DynamicAlgorithm\DestroyDynamicAlgorithm;
use App\Http\Requests\Admin\DynamicAlgorithm\IndexDynamicAlgorithm;
use App\Http\Requests\Admin\DynamicAlgorithm\StoreDynamicAlgorithm;
use App\Http\Requests\Admin\DynamicAlgorithm\UpdateDynamicAlgorithm;
use App\Jobs\sendNotification;
use App\Models\AutomaticNotification;
use App\Models\Cadre;
use App\Models\DynamicAlgorithm;
use App\Models\DynamicAlgoMaster;
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
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Log;
use Config;

class DynamicAlgorithmController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexDynamicAlgorithm $request
     * @return array|Factory|View
     */

    public function getAlgorithmTitle($key)
    {
        $dynamicAlgoMaster = DynamicAlgoMaster::with('app_config')->where('id', $key)->get()[0];
        return $dynamicAlgoMaster['app_config']['value_json'];
    }

    public function getMasterNodes(Request $request)
    {
        $data = [];
        $algoTitle = ' Dynamic Algorithm';
        if ($request['key'] && $request['key'] != '') {
            $data = DynamicAlgorithm::where('parent_id', 0)->where('algo_key', $request['key'])->orderBy('index')->get();
            $algoTitle = $this->getAlgorithmTitle($request['key']);
        }
        return view('admin.dynamic-algorithm.master-nodes', ['data' => $data, 'algoTitle' => $algoTitle]);
    }

    public function index(IndexDynamicAlgorithm $request)
    {
        $state = State::get(['id', 'title']);
        if ($request['key'] && $request['key'] != '') {
            // create and AdminListing instance for a specific model and
            $data = AdminListing::create(DynamicAlgorithm::class)
                ->modifyQuery(function ($query) use ($request) {
                    if (isset($request['master']) && $request['master'] != '' && $request->orderDirection == "") {
                        $query->where('algo_key', $request['key']);
                        $query->where('parent_id', $request['master']);
                        $query->orderBy('index', 'DESC');
                    }
                    if (isset($request['master']) && $request['master'] != '' && $request->orderDirection == "asc") {
                        $query->where('algo_key', $request['key']);
                        $query->where('parent_id', $request['master']);
                        $query->orderBy('index', 'DESC');
                    }
                    if (isset($request['master']) && $request['master'] != '' && $request->orderDirection == "desc") {
                        $query->where('algo_key', $request['key']);
                        $query->where('parent_id', $request['master']);
                        $query->orderBy('index', 'DESC');
                    }
                })
                ->processRequestAndGet(
                    // pass the request with params
                    $request,

                    // set columns to query
                    ['id', 'algo_key', 'title', 'node_type', 'is_expandable', 'has_options', 'parent_id', 'master_node_id', 'state_id', 'cadre_id', 'index', 'description', 'redirect_algo_type', 'redirect_node_id', 'header', 'sub_header', 'activated', 'send_initial_notification'],

                    // set columns to searchIn
                    ['id', 'algo_key', 'title', 'node_type', 'description', 'redirect_algo_type', 'header', 'sub_header']
                );
            // $this->setTranslate();
            $algoTitle = $this->getAlgorithmTitle($request['key']);
        } else {
            $data = [];
            $algoTitle = ' Dynamic Algorithm';
        }

        if ($request->ajax()) {
            $request->session()->pull('dynamic_notification');
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'algoTitle' => $algoTitle];
        }
        return view('admin.dynamic-algorithm.index', ['data' => $data, 'algoTitle' => $algoTitle, 'state' => $state, 'message' => session('dynamic_notification')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create(Request $request)
    {
        $this->authorize('admin.dynamic-algorithm.create');
        $algoTitle = $this->getAlgorithmTitle($request['key']);
        $state = State::get(['id', 'title']);
        $cadre = Cadre::get(['id', 'title']);

        return view('admin.dynamic-algorithm.create', [
            'algoTitle' => $algoTitle, 'state' => $state,
            'cadre' => $cadre,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDynamicAlgorithm $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreDynamicAlgorithm $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($sanitized['state_id']) && isset($sanitized['state_id'][0]) && $sanitized['state_id'][0] != '') {
            $sanitized['state_id'] = implode(",", $sanitized['state_id']);
        }

        if (isset($sanitized['cadre_id']) && isset($sanitized['cadre_id'][0]) && $sanitized['cadre_id'][0] != '') {
            $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);
        }
        // Store the DynamicAlgorithm
        $dynamicAlgorithm = DynamicAlgorithm::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/dynamic-algorithms?key=' . $request['algo_key'] . '&master=' . $request['parent_id']), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/dynamic-algorithms?key=' . $request['algo_key'] . '&master=' . $request['parent_id']);
    }

    /**
     * Display the specified resource.
     *
     * @param DynamicAlgorithm $dynamicAlgorithm
     * @throws AuthorizationException
     * @return void
     */
    public function show(DynamicAlgorithm $dynamicAlgorithm)
    {
        $this->authorize('admin.dynamic-algorithm.show', $dynamicAlgorithm);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param DynamicAlgorithm $dynamicAlgorithm
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(DynamicAlgorithm $dynamicAlgorithm)
    {
        $this->authorize('admin.dynamic-algorithm.edit', $dynamicAlgorithm);
        $state = State::get(['id', 'title']);
        $cadres = Cadre::get(['id', 'title']);
        if (isset($dynamicAlgorithm['state_id']) && $dynamicAlgorithm['state_id'] != "") {
            $dynamicAlgorithm['state_id'] = explode(',', $dynamicAlgorithm['state_id']);
        } else {
            $dynamicAlgorithm['state_id'] = [];
        }
        if (isset($dynamicAlgorithm['cadre_id']) && $dynamicAlgorithm['cadre_id'] != "") {
            $dynamicAlgorithm['cadre_id'] = explode(',', $dynamicAlgorithm['cadre_id']);
        } else {
            $dynamicAlgorithm['cadre_id'] = [];
        }

        $dynamicAlgorithm['all_cadres'] = $cadres;
        $dynamicAlgorithm['all_states'] = $state;

        return view('admin.dynamic-algorithm.edit', [
            'dynamicAlgorithm' => $dynamicAlgorithm,
            'state' => $state,
            'cadre' => $cadres,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDynamicAlgorithm $request
     * @param DynamicAlgorithm $dynamicAlgorithm
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateDynamicAlgorithm $request, DynamicAlgorithm $dynamicAlgorithm)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($sanitized['state_id']) && isset($sanitized['state_id'][0]) && $sanitized['state_id'][0] != '') {
            $sanitized['state_id'] = implode(",", $sanitized['state_id']);
        }

        if (isset($sanitized['cadre_id']) && isset($sanitized['cadre_id'][0]) && $sanitized['cadre_id'][0] != '') {
            $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);
        }

        // Update changed values DynamicAlgorithm
        $dynamicAlgorithm->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/dynamic-algorithms?key=' . $request['algo_key'] . '&master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded')
            ];
        }

        return redirect('admin/dynamic-algorithms?key=' . $request['algo_key'] . '&master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']);
    }

    public function sendInitialInvitation(Request $request, DynamicAlgorithm $dynamicAlgorithm)
    {
        $message = "";
        try {
            if ($dynamicAlgorithm->state_id != '' && $dynamicAlgorithm->cadre_id != '') {
                $state = State::count();
                $cadre = Cadre::count();
                $explode_state = count(explode(',', $dynamicAlgorithm->state_id));
                $explode_cadre = count(explode(',', $dynamicAlgorithm->cadre_id));
                if ($state == $explode_state && $cadre == $explode_cadre) {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $dynamicAlgorithm->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $dynamicAlgorithm->state_id . "')")
                        ->orWhere('country_id', 1)
                        ->pluck('id');
                } else {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $dynamicAlgorithm->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $dynamicAlgorithm->state_id . "')")
                        ->pluck('id');
                }
            } elseif ($dynamicAlgorithm->state_id != '' && $dynamicAlgorithm->cadre_id == '') {
                $subscriber = Subscriber::whereRaw("find_in_set(state_id,'" . $dynamicAlgorithm->state_id . "')")
                    // ->toSql();
                    ->pluck('id');
            } else {
                $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $dynamicAlgorithm->cadre_id . "')")
                    // ->toSql();
                    ->pluck('id');
            }

            $notification['title'] = "New Module";
            $notification['description'] = "$dynamicAlgorithm->title";
            $dynamic_algo_master = DynamicAlgoMaster::where('id', $dynamicAlgorithm->algo_key)->get(['name'])[0];
            $device_id = UserDeviceToken::whereIn('user_id', $subscriber)->get('notification_token');
            if (isset($device_id) && count($device_id) > 0) {
                $notification['type'] = "Dynamic";
                $notification['subscriber_id'] = implode(',', $subscriber->toArray());
                $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/AlgorithmList/$dynamic_algo_master->name/Dynamic/$dynamicAlgorithm->algo_key";
                $notification['created_by'] = \Auth::user()->id;
                $notification['status'] = 'Pending';
                $userNotification = AutomaticNotification::create($notification);
                // $response = SendNotificationController::newModules($notification,$device_id,Config::get('app.GENERAL.frontend_url')."/AlgorithmList/$dynamic_algo_master->name/Dynamic/$dynamicAlgorithm->algo_key");
                dispatch(new sendNotification($notification, $subscriber, $notification['type'], $notification['linking_url'], $userNotification['id'], 'true'));
                $message = "Notification queued. Check status later.";
                // if (isset($response['error'])) {
                //     $message = "User Not Found";
                // } else {
                //     $successCount = isset($response['successFullCount']) && $response['successFullCount'] > 0 ? $response['successFullCount'] : 0;
                //     $failCount = isset($response['failedCount']) && $response['failedCount'] > 0 ? $response['failedCount'] : 0;
                //     $message = "You have successfully added notification. Your notification is successfully send to " . $successCount . " Subscribers and Failed for " . $failCount . " Subscribers.";
                // }
            }
            DynamicAlgorithm::where('id', $dynamicAlgorithm->id)->update(['send_initial_notification' => 1]);

            session(['dynamic_notification' => $message]);
            if ($request->ajax()) {
                session(['dynamic_notification' => $message]);
                return [
                    'redirect' => url('admin/dynamic-algorithms?key=' . $dynamicAlgorithm->algo_key . '&master=0&master_node_id=0'),
                    'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
                ];
            }
            return redirect('admin/dynamic-algorithms?key=' . $dynamicAlgorithm->algo_key . '&master=0&master_node_id=0')->with('message', $message);
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing Cgc Algorithm function");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyDynamicAlgorithm $request
     * @param DynamicAlgorithm $dynamicAlgorithm
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyDynamicAlgorithm $request, DynamicAlgorithm $dynamicAlgorithm)
    {
        $dynamicAlgorithm->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyDynamicAlgorithm $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyDynamicAlgorithm $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('dynamicAlgorithms')->whereIn('id', $bulkChunk)
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
        return view('admin.dynamic-algorithm.org-chart');
    }

    public function setTranslate()
    {
        $dynamicAlgorithm = DynamicAlgorithm::get();

        foreach ($dynamicAlgorithm as $key => $dynamicAlgorithms) {
            $dynamicAlgorithms = DynamicAlgorithm::find($dynamicAlgorithms->id);
            $dynamicAlgorithms->setTranslation('header', 'en', $dynamicAlgorithm[$key]->header)->save();
            $dynamicAlgorithms->setTranslation('sub_header', 'en', $dynamicAlgorithm[$key]->sub_header)->save();
            $dynamicAlgorithms->setTranslation('title', 'en', $dynamicAlgorithm[$key]->title)->save();
            $dynamicAlgorithms->setTranslation('description', 'en', $dynamicAlgorithm[$key]->description)->save();
        }
    }
}
