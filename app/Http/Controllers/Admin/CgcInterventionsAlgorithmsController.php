<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CgcInterventionsAlgorithm\BulkDestroyCgcInterventionsAlgorithm;
use App\Http\Requests\Admin\CgcInterventionsAlgorithm\DestroyCgcInterventionsAlgorithm;
use App\Http\Requests\Admin\CgcInterventionsAlgorithm\IndexCgcInterventionsAlgorithm;
use App\Http\Requests\Admin\CgcInterventionsAlgorithm\StoreCgcInterventionsAlgorithm;
use App\Http\Requests\Admin\CgcInterventionsAlgorithm\UpdateCgcInterventionsAlgorithm;
use App\Jobs\sendNotification;
use App\Models\AutomaticNotification;
use App\Models\Cadre;
use App\Models\CgcInterventionsAlgorithm;
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

class CgcInterventionsAlgorithmsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexCgcInterventionsAlgorithm $request
     * @return array|Factory|View
     */
    public function getMasterNodes()
    {
        $this->authorize('admin.cgc-interventions-algorithm.index');
        $data = CgcInterventionsAlgorithm::where('parent_id', 0)->orderBy('index')->get();
        return view('admin.cgc-interventions-algorithm.master-nodes', ['data' => $data]);
    }
    public function index(IndexCgcInterventionsAlgorithm $request)
    {
        $state = State::get(['id', 'title']);
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(CgcInterventionsAlgorithm::class)
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
                ['id', 'title', 'node_type', 'is_expandable', 'has_options', 'parent_id', 'master_node_id', 'state_id', 'cadre_id', 'description', 'time_spent', 'index', 'redirect_algo_type', 'redirect_node_id', 'header', 'sub_header', 'activated', 'send_initial_notification'],

                // set columns to searchIn
                ['id', 'title', 'node_type', 'description', 'time_spent', 'redirect_algo_type', 'header', 'sub_header'],
                // function ($q) use($request) {
                //     $q->where('activated',1);
                // }
            );
        // $this->setTranslate();

        if ($request->ajax()) {
            $request->session()->pull('cgc_algo_notification');
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }
        return view('admin.cgc-interventions-algorithm.index', ['data' => $data, 'state' => $state, 'message' => session('cgc_algo_notification')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.cgc-interventions-algorithm.create');
        $state = State::get(['id', 'title']);
        $cadre = Cadre::get(['id', 'title']);
        return view('admin.cgc-interventions-algorithm.create', [
            'state' => $state,
            'cadre' => $cadre,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCgcInterventionsAlgorithm $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreCgcInterventionsAlgorithm $request)
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
        // Store the CgcInterventionsAlgorithm
        $cgcInterventionsAlgorithm = CgcInterventionsAlgorithm::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/cgc-interventions-algorithms?master=' . $request['parent_id']), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/cgc-interventions-algorithms?master=' . $request['parent_id']);
    }

    /**
     * Display the specified resource.
     *
     * @param CgcInterventionsAlgorithm $cgcInterventionsAlgorithm
     * @throws AuthorizationException
     * @return void
     */
    public function show(CgcInterventionsAlgorithm $cgcInterventionsAlgorithm)
    {
        $this->authorize('admin.cgc-interventions-algorithm.show', $cgcInterventionsAlgorithm);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CgcInterventionsAlgorithm $cgcInterventionsAlgorithm
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(CgcInterventionsAlgorithm $cgcInterventionsAlgorithm)
    {
        $this->authorize('admin.cgc-interventions-algorithm.edit', $cgcInterventionsAlgorithm);
        $state = State::get(['id', 'title']);
        $cadres = Cadre::get(['id', 'title']);
        if (isset($cgcInterventionsAlgorithm['state_id']) && $cgcInterventionsAlgorithm['state_id'] != "") {
            $cgcInterventionsAlgorithm['state_id'] = explode(',', $cgcInterventionsAlgorithm['state_id']);
        } else {
            $cgcInterventionsAlgorithm['state_id'] = [];
        }
        if (isset($cgcInterventionsAlgorithm['cadre_id']) && $cgcInterventionsAlgorithm['cadre_id'] != "") {
            $cgcInterventionsAlgorithm['cadre_id'] = explode(',', $cgcInterventionsAlgorithm['cadre_id']);
        } else {
            $cgcInterventionsAlgorithm['cadre_id'] = [];
        }

        $cgcInterventionsAlgorithm['all_cadres'] = $cadres;
        $cgcInterventionsAlgorithm['all_states'] = $state;

        return view('admin.cgc-interventions-algorithm.edit', [
            'cgcInterventionsAlgorithm' => $cgcInterventionsAlgorithm,
            'state' => $state,
            'cadre' => $cadres,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCgcInterventionsAlgorithm $request
     * @param CgcInterventionsAlgorithm $cgcInterventionsAlgorithm
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateCgcInterventionsAlgorithm $request, CgcInterventionsAlgorithm $cgcInterventionsAlgorithm)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($sanitized['state_id']) && isset($sanitized['state_id'][0]) && $sanitized['state_id'][0] != '') {
            $sanitized['state_id'] = implode(",", $sanitized['state_id']);
        }

        if (isset($sanitized['cadre_id']) && isset($sanitized['cadre_id'][0]) && $sanitized['cadre_id'][0] != '') {
            $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);
        }
        // Update changed values CgcInterventionsAlgorithm
        $cgcInterventionsAlgorithm->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/cgc-interventions-algorithms?master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/cgc-interventions-algorithms?master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']);
    }

    public function sendInitialInvitation(Request $request, CgcInterventionsAlgorithm $cgcInterventionsAlgorithm)
    {
        $message = "";
        try {
            if ($cgcInterventionsAlgorithm->state_id != '' && $cgcInterventionsAlgorithm->cadre_id != '') {
                $state = State::count();
                $cadre = Cadre::count();
                $explode_state = count(explode(',', $cgcInterventionsAlgorithm->state_id));
                $explode_cadre = count(explode(',', $cgcInterventionsAlgorithm->cadre_id));
                if ($state == $explode_state && $cadre == $explode_cadre) {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $cgcInterventionsAlgorithm->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $cgcInterventionsAlgorithm->state_id . "')")
                        ->orWhere('country_id', 1)
                        ->pluck('id');
                } else {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $cgcInterventionsAlgorithm->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $cgcInterventionsAlgorithm->state_id . "')")
                        ->pluck('id');
                }
            } elseif ($cgcInterventionsAlgorithm->state_id != '' && $cgcInterventionsAlgorithm->cadre_id == '') {
                $subscriber = Subscriber::whereRaw("find_in_set(state_id,'" . $cgcInterventionsAlgorithm->state_id . "')")
                    ->pluck('id');
            } else {
                $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $cgcInterventionsAlgorithm->cadre_id . "')")
                    ->pluck('id');
            }

            $notification['title'] = "New Module";
            $notification['description'] = "$cgcInterventionsAlgorithm->title";

            $device_id = UserDeviceToken::whereIn('user_id', $subscriber)->get('notification_token'); //$subscriber
            if (isset($device_id) && count($device_id) > 0) {
                $notification['type'] = "NTEP";
                $notification['subscriber_id'] = implode(',', $subscriber->toArray());
                $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/Algorithms/TITLE_CGC_INTERVENTION/NTEP";
                $notification['created_by'] = \Auth::user()->id;
                $notification['status'] = 'Pending';
                $userNotification = AutomaticNotification::create($notification);
                // $response = SendNotificationController::newModules($notification,$device_id,Config::get('app.GENERAL.frontend_url')."/Algorithms/TITLE_CGC_INTERVENTION/NTEP");
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
            CgcInterventionsAlgorithm::where('id', $cgcInterventionsAlgorithm->id)->update(['send_initial_notification' => 1]);

            session(['cgc_algo_notification' => $message]);
            if ($request->ajax()) {
                session(['cgc_algo_notification' => $message]);
                return [
                    'redirect' => url('admin/cgc-interventions-algorithms?master=0&master_node_id=0'),
                    'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
                ];
            }
            return redirect('admin/cgc-interventions-algorithms?master=0&master_node_id=0')->with('message', $message);
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing Cgc Algorithm function");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyCgcInterventionsAlgorithm $request
     * @param CgcInterventionsAlgorithm $cgcInterventionsAlgorithm
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyCgcInterventionsAlgorithm $request, CgcInterventionsAlgorithm $cgcInterventionsAlgorithm)
    {
        $cgcInterventionsAlgorithm->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyCgcInterventionsAlgorithm $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyCgcInterventionsAlgorithm $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('cgcInterventionsAlgorithms')->whereIn('id', $bulkChunk)
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
        return view('admin.cgc-interventions-algorithm.org-chart');
    }

    public function setTranslate()
    {
        $cgcInterventionsAlgorithms = CgcInterventionsAlgorithm::get();

        foreach ($cgcInterventionsAlgorithms as $key => $cgcInterventionsAlgorithm) {
            $cgcInterventionsAlgorithm = CgcInterventionsAlgorithm::find($cgcInterventionsAlgorithm->id);
            $cgcInterventionsAlgorithm->setTranslation('header', 'en', $cgcInterventionsAlgorithms[$key]->header)->save();
            $cgcInterventionsAlgorithm->setTranslation('sub_header', 'en', $cgcInterventionsAlgorithms[$key]->sub_header)->save();
            // $cgcInterventionsAlgorithm->setTranslation('title_value_json', 'en', $cgcInterventionsAlgorithms[$key]->title)->save();
            // $cgcInterventionsAlgorithm->setTranslation('description_value_json', 'en', $cgcInterventionsAlgorithms[$key]->description)->save();
        }
    }
}
