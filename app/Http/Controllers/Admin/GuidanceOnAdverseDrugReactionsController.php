<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GuidanceOnAdverseDrugReaction\BulkDestroyGuidanceOnAdverseDrugReaction;
use App\Http\Requests\Admin\GuidanceOnAdverseDrugReaction\DestroyGuidanceOnAdverseDrugReaction;
use App\Http\Requests\Admin\GuidanceOnAdverseDrugReaction\IndexGuidanceOnAdverseDrugReaction;
use App\Http\Requests\Admin\GuidanceOnAdverseDrugReaction\StoreGuidanceOnAdverseDrugReaction;
use App\Http\Requests\Admin\GuidanceOnAdverseDrugReaction\UpdateGuidanceOnAdverseDrugReaction;
use App\Jobs\sendNotification;
use App\Models\AutomaticNotification;
use App\Models\Cadre;
use App\Models\GuidanceOnAdverseDrugReaction;
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

class GuidanceOnAdverseDrugReactionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexGuidanceOnAdverseDrugReaction $request
     * @return array|Factory|View
     */

    public function getMasterNodes()
    {
        $this->authorize('admin.guidance-on-adverse-drug-reaction.index');
        $data = GuidanceOnAdverseDrugReaction::where('parent_id', 0)->orderBy('index')->get();
        return view('admin.guidance-on-adverse-drug-reaction.master-nodes', ['data' => $data]);
    }

    public function index(IndexGuidanceOnAdverseDrugReaction $request)
    {
        $state = State::get(['id', 'title']);
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(GuidanceOnAdverseDrugReaction::class)
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
            $request->session()->pull('guidance_notification');
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }
        return view('admin.guidance-on-adverse-drug-reaction.index', ['data' => $data, 'state' => $state, 'message' => session('guidance_notification')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.guidance-on-adverse-drug-reaction.create');

        $state = State::get(['id', 'title']);
        $cadre = Cadre::get(['id', 'title']);
        return view('admin.guidance-on-adverse-drug-reaction.create', [
            'state' => $state,
            'cadre' => $cadre,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreGuidanceOnAdverseDrugReaction $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreGuidanceOnAdverseDrugReaction $request)
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
        // Store the GuidanceOnAdverseDrugReaction
        $guidanceOnAdverseDrugReaction = GuidanceOnAdverseDrugReaction::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/guidance-on-adverse-drug-reactions?master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/guidance-on-adverse-drug-reactions?master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']);
    }

    /**
     * Display the specified resource.
     *
     * @param GuidanceOnAdverseDrugReaction $guidanceOnAdverseDrugReaction
     * @throws AuthorizationException
     * @return void
     */
    public function show(GuidanceOnAdverseDrugReaction $guidanceOnAdverseDrugReaction)
    {
        $this->authorize('admin.guidance-on-adverse-drug-reaction.show', $guidanceOnAdverseDrugReaction);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param GuidanceOnAdverseDrugReaction $guidanceOnAdverseDrugReaction
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(GuidanceOnAdverseDrugReaction $guidanceOnAdverseDrugReaction)
    {
        $this->authorize('admin.guidance-on-adverse-drug-reaction.edit', $guidanceOnAdverseDrugReaction);
        $state = State::get(['id', 'title']);
        $cadres = Cadre::get(['id', 'title']);
        if (isset($guidanceOnAdverseDrugReaction['state_id']) && $guidanceOnAdverseDrugReaction['state_id'] != "") {
            $guidanceOnAdverseDrugReaction['state_id'] = explode(',', $guidanceOnAdverseDrugReaction['state_id']);
        } else {
            $guidanceOnAdverseDrugReaction['state_id'] = [];
        }
        if (isset($guidanceOnAdverseDrugReaction['cadre_id']) && $guidanceOnAdverseDrugReaction['cadre_id'] != "") {
            $guidanceOnAdverseDrugReaction['cadre_id'] = explode(',', $guidanceOnAdverseDrugReaction['cadre_id']);
        } else {
            $guidanceOnAdverseDrugReaction['cadre_id'] = [];
        }

        $guidanceOnAdverseDrugReaction['all_cadres'] = $cadres;
        $guidanceOnAdverseDrugReaction['all_states'] = $state;

        return view('admin.guidance-on-adverse-drug-reaction.edit', [
            'guidanceOnAdverseDrugReaction' => $guidanceOnAdverseDrugReaction,
            'state' => $state,
            'cadre' => $cadres,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateGuidanceOnAdverseDrugReaction $request
     * @param GuidanceOnAdverseDrugReaction $guidanceOnAdverseDrugReaction
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateGuidanceOnAdverseDrugReaction $request, GuidanceOnAdverseDrugReaction $guidanceOnAdverseDrugReaction)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($sanitized['state_id']) && isset($sanitized['state_id'][0]) && $sanitized['state_id'][0] != '') {
            $sanitized['state_id'] = implode(",", $sanitized['state_id']);
        }

        if (isset($sanitized['cadre_id']) && isset($sanitized['cadre_id'][0]) && $sanitized['cadre_id'][0] != '') {
            $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);
        }
        // Update changed values GuidanceOnAdverseDrugReaction
        $guidanceOnAdverseDrugReaction->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/guidance-on-adverse-drug-reactions?master=' . $request['parent_id']),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/guidance-on-adverse-drug-reactions?master=' . $request['parent_id']);
    }

    public function sendInitialInvitation(Request $request, GuidanceOnAdverseDrugReaction $guidanceOnAdverseDrugReaction)
    {
        $message = "";
        try {
            if ($guidanceOnAdverseDrugReaction->state_id != '' && $guidanceOnAdverseDrugReaction->cadre_id != '') {
                $state = State::count();
                $cadre = Cadre::count();
                $explode_state = count(explode(',', $guidanceOnAdverseDrugReaction->state_id));
                $explode_cadre = count(explode(',', $guidanceOnAdverseDrugReaction->cadre_id));
                if ($state == $explode_state && $cadre == $explode_cadre) {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $guidanceOnAdverseDrugReaction->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $guidanceOnAdverseDrugReaction->state_id . "')")
                        ->orWhere('country_id', 1)
                        ->pluck('id');
                } else {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $guidanceOnAdverseDrugReaction->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $guidanceOnAdverseDrugReaction->state_id . "')")
                        ->pluck('id');
                }
            } elseif ($guidanceOnAdverseDrugReaction->state_id != '' && $guidanceOnAdverseDrugReaction->cadre_id == '') {
                $subscriber = Subscriber::whereRaw("find_in_set(state_id,'" . $guidanceOnAdverseDrugReaction->state_id . "')")
                    // ->toSql();
                    ->pluck('id');
            } else {
                $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $guidanceOnAdverseDrugReaction->cadre_id . "')")
                    // ->toSql();
                    ->pluck('id');
            }

            $notification['title'] = "New Module";
            $notification['description'] = "$guidanceOnAdverseDrugReaction->title";

            $device_id = UserDeviceToken::whereIn('user_id', $subscriber)->get('notification_token'); //$subscriber
            if (isset($device_id) && count($device_id) > 0) {
                $notification['type'] = "Guidance on ADR";
                $notification['subscriber_id'] = implode(',', $subscriber->toArray());
                $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/AlgorithmList/TITLE_GUIDANCE_ON_ADR/Guidance on ADR/null";
                $notification['created_by'] = \Auth::user()->id;
                $notification['status'] = 'Pending';
                $userNotification = AutomaticNotification::create($notification);
                // $response = SendNotificationController::newModules($notification,$device_id,Config::get('app.GENERAL.frontend_url')."/AlgorithmList/TITLE_GUIDANCE_ON_ADR/Guidance on ADR/null");
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
            GuidanceOnAdverseDrugReaction::where('id', $guidanceOnAdverseDrugReaction->id)->update(['send_initial_notification' => 1]);
            session(['guidance_notification' => $message]);
            if ($request->ajax()) {
                session(['guidance_notification' => $message]);
                return [
                    'redirect' => url('admin/guidance-on-adverse-drug-reactions?master=0&master_node_id=0'),
                    'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
                ];
            }

            return redirect('admin/guidance-on-adverse-drug-reactions?master=0&master_node_id=0')->with('message', $message);
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing Guidance on adr algorithm function");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyGuidanceOnAdverseDrugReaction $request
     * @param GuidanceOnAdverseDrugReaction $guidanceOnAdverseDrugReaction
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyGuidanceOnAdverseDrugReaction $request, GuidanceOnAdverseDrugReaction $guidanceOnAdverseDrugReaction)
    {
        $guidanceOnAdverseDrugReaction->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyGuidanceOnAdverseDrugReaction $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyGuidanceOnAdverseDrugReaction $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('guidanceOnAdverseDrugReactions')->whereIn('id', $bulkChunk)
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
        return view('admin.guidance-on-adverse-drug-reaction.org-chart');
    }

    public function setTranslate()
    {
        $guidanceOnAdverseDrugReaction = GuidanceOnAdverseDrugReaction::get();

        foreach ($guidanceOnAdverseDrugReaction as $key => $guidanceOnAdverseDrugReactions) {
            $guidanceOnAdverseDrugReactions = GuidanceOnAdverseDrugReaction::find($guidanceOnAdverseDrugReactions->id);
            $guidanceOnAdverseDrugReactions->setTranslation('header', 'en', $guidanceOnAdverseDrugReaction[$key]->header)->save();
            $guidanceOnAdverseDrugReactions->setTranslation('sub_header', 'en', $guidanceOnAdverseDrugReaction[$key]->sub_header)->save();
            // $guidanceOnAdverseDrugReactions->setTranslation('title_value_json', 'en', $guidanceOnAdverseDrugReaction[$key]->title)->save();
            // $guidanceOnAdverseDrugReactions->setTranslation('description_value_json', 'en', $guidanceOnAdverseDrugReaction[$key]->description)->save();
        }
    }
}