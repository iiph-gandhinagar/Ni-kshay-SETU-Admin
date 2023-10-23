<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TreatmentAlgorithm\BulkDestroyTreatmentAlgorithm;
use App\Http\Requests\Admin\TreatmentAlgorithm\DestroyTreatmentAlgorithm;
use App\Http\Requests\Admin\TreatmentAlgorithm\IndexTreatmentAlgorithm;
use App\Http\Requests\Admin\TreatmentAlgorithm\StoreTreatmentAlgorithm;
use App\Http\Requests\Admin\TreatmentAlgorithm\UpdateTreatmentAlgorithm;
use App\Jobs\sendNotification;
use App\Models\AutomaticNotification;
use App\Models\Cadre;
use App\Models\State;
use App\Models\Subscriber;
use App\Models\TreatmentAlgorithm;
use App\Models\UserDeviceToken;
use Brackets\AdminListing\Facades\AdminListing;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Log;
use Config;

class TreatmentAlgorithmsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTreatmentAlgorithm $request
     * @return array|Factory|View
     */

    public function getMasterNodes()
    {
        $this->authorize('admin.treatment-algorithm.index');
        $data = TreatmentAlgorithm::where('parent_id', 0)->orderBy('index')->get();
        return view('admin.treatment-algorithm.master-nodes', ['data' => $data]);
    }

    public function index(IndexTreatmentAlgorithm $request)
    {
        $state = State::get(['id', 'title']);
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(TreatmentAlgorithm::class)
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

                function ($q) use ($request) {
                    // $q->where('activated',1);
                    // if(isset($request['master']) && $request['master'] != ''){
                    //     $q->where('parent_id',$request['master'])->orderBy('index');
                    // }
                }
            );
        // $this->setTranslate();
        if ($request->ajax()) {
            $request->session()->pull('treatment_notification');
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }
        return view('admin.treatment-algorithm.index', ['data' => $data, 'state' => $state, 'message' => session('treatment_notification')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create(Request $request)
    {
        $this->authorize('admin.treatment-algorithm.create');
        $parentNodes = [];
        if (isset($request['master']) && $request['master'] != '') {
            $parentNodes = TreatmentAlgorithm::where('id', $request['master'])->get();
        }
        $state = State::get(['id', 'title']);
        $cadre = Cadre::get(['id', 'title']);


        return view('admin.treatment-algorithm.create', [
            'parentNodes' => $parentNodes, 'state' => $state,
            'cadre' => $cadre
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTreatmentAlgorithm $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTreatmentAlgorithm $request)
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
        // Store the TreatmentAlgorithm
        $treatmentAlgorithm = TreatmentAlgorithm::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/treatment-algorithms?master=' . $request['parent_id']), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/treatment-algorithms?master=' . $request['parent_id']);
    }

    /**
     * Display the specified resource.
     *
     * @param TreatmentAlgorithm $treatmentAlgorithm
     * @throws AuthorizationException
     * @return void
     */
    public function show(TreatmentAlgorithm $treatmentAlgorithm)
    {
        $this->authorize('admin.treatment-algorithm.show', $treatmentAlgorithm);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TreatmentAlgorithm $treatmentAlgorithm
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(TreatmentAlgorithm $treatmentAlgorithm)
    {
        $this->authorize('admin.treatment-algorithm.edit', $treatmentAlgorithm);

        $state = State::get(['id', 'title']);
        $cadres = Cadre::get(['id', 'title']);
        if (isset($treatmentAlgorithm['state_id']) && $treatmentAlgorithm['state_id'] != "") {
            $treatmentAlgorithm['state_id'] = explode(',', $treatmentAlgorithm['state_id']);
        } else {
            $treatmentAlgorithm['state_id'] = [];
        }
        if (isset($treatmentAlgorithm['cadre_id']) && $treatmentAlgorithm['cadre_id'] != "") {
            $treatmentAlgorithm['cadre_id'] = explode(',', $treatmentAlgorithm['cadre_id']);
        } else {
            $treatmentAlgorithm['cadre_id'] = [];
        }

        $treatmentAlgorithm['all_cadres'] = $cadres;
        $treatmentAlgorithm['all_states'] = $state;

        return view('admin.treatment-algorithm.edit', [
            'treatmentAlgorithm' => $treatmentAlgorithm,
            'state' => $state,
            'cadre' => $cadres,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTreatmentAlgorithm $request
     * @param TreatmentAlgorithm $treatmentAlgorithm
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTreatmentAlgorithm $request, TreatmentAlgorithm $treatmentAlgorithm)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($sanitized['state_id']) && isset($sanitized['state_id'][0]) && $sanitized['state_id'][0] != '') {
            $sanitized['state_id'] = implode(",", $sanitized['state_id']);
        }

        if (isset($sanitized['cadre_id']) && isset($sanitized['cadre_id'][0]) && $sanitized['cadre_id'][0] != '') {
            $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);
        }
        // Update changed values TreatmentAlgorithm
        $treatmentAlgorithm->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/treatment-algorithms?master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/treatment-algorithms?master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']);
    }

    public function sendInitialInvitation(Request $request, TreatmentAlgorithm $treatmentAlgorithm)
    {
        $message = "";
        try {
            if ($treatmentAlgorithm->state_id != '' && $treatmentAlgorithm->cadre_id != '') {
                $state = State::count();
                $cadre = Cadre::count();
                $explode_state = count(explode(',', $treatmentAlgorithm->state_id));
                $explode_cadre = count(explode(',', $treatmentAlgorithm->cadre_id));
                if ($state == $explode_state && $cadre == $explode_cadre) {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $treatmentAlgorithm->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $treatmentAlgorithm->state_id . "')")
                        ->orWhere('country_id', 1)
                        ->pluck('id');
                } else {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $treatmentAlgorithm->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $treatmentAlgorithm->state_id . "')")
                        ->pluck('id');
                }
            } elseif ($treatmentAlgorithm->state_id != '' && $treatmentAlgorithm->cadre_id == '') {
                $subscriber = Subscriber::whereRaw("find_in_set(state_id,'" . $treatmentAlgorithm->state_id . "')")
                    // ->toSql();
                    ->pluck('id');
            } else {
                $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $treatmentAlgorithm->cadre_id . "')")
                    // ->toSql();
                    ->pluck('id');
            }

            $notification['title'] = "New Module";
            $notification['description'] = "$treatmentAlgorithm->title";

            $device_id = UserDeviceToken::whereIn('user_id', $subscriber)->get('notification_token'); //$subscriber
            if (isset($device_id) && count($device_id) > 0) {
                $notification['type'] = "Treatment Algorithm";
                $notification['subscriber_id'] = implode(',', $subscriber->toArray());
                $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/AlgorithmList/TITLE_TREATMENT_ALGORITHM/Treatment Algorithm/null";
                $notification['created_by'] = \Auth::user()->id;
                $notification['status'] = 'Pending';
                $userNotification = AutomaticNotification::create($notification);
                // $response = SendNotificationController::newModules($notification,$device_id,Config::get('app.GENERAL.frontend_url')."/AlgorithmList/TITLE_TREATMENT_ALGORITHM/Treatment Algorithm/null");
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
            TreatmentAlgorithm::where('id', $treatmentAlgorithm->id)->update(['send_initial_notification' => 1]);
            session(['treatment_notification' => $message]);
            if ($request->ajax()) {
                session(['treatment_notification' => $message]);
                session(['message' => $message]);
                return [
                    'redirect' => url('admin/treatment-algorithms?master=0&master_node_id=0'),
                    'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
                ];
            }

            return redirect('admin/treatment-algorithms?master=0&master_node_id=0')->with('message', $message);
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing Treatment algorithm function");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTreatmentAlgorithm $request
     * @param TreatmentAlgorithm $treatmentAlgorithm
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTreatmentAlgorithm $request, TreatmentAlgorithm $treatmentAlgorithm)
    {
        $treatmentAlgorithm->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTreatmentAlgorithm $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTreatmentAlgorithm $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('treatmentAlgorithms')->whereIn('id', $bulkChunk)
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
        return view('admin.treatment-algorithm.org-chart');
    }

    public function setTranslate()
    {
        $treatmentAlgorithm = TreatmentAlgorithm::get();

        foreach ($treatmentAlgorithm as $key => $treatmentAlgorithms) {
            $treatmentAlgorithms = TreatmentAlgorithm::find($treatmentAlgorithms->id);
            $treatmentAlgorithms->setTranslation('header', 'en', $treatmentAlgorithm[$key]->header)->save();
            $treatmentAlgorithms->setTranslation('sub_header', 'en', $treatmentAlgorithm[$key]->sub_header)->save();
            // $treatmentAlgorithms->setTranslation('title_value_json', 'en', $treatmentAlgorithm[$key]->title)->save();
            // $treatmentAlgorithms->setTranslation('description_value_json', 'en', $treatmentAlgorithm[$key]->description)->save();
        }
    }
}