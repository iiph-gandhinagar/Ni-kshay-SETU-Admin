<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LatentTbInfection\BulkDestroyLatentTbInfection;
use App\Http\Requests\Admin\LatentTbInfection\DestroyLatentTbInfection;
use App\Http\Requests\Admin\LatentTbInfection\IndexLatentTbInfection;
use App\Http\Requests\Admin\LatentTbInfection\StoreLatentTbInfection;
use App\Http\Requests\Admin\LatentTbInfection\UpdateLatentTbInfection;
use App\Jobs\sendNotification;
use App\Models\AutomaticNotification;
use App\Models\Cadre;
use App\Models\LatentTbInfection;
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

class LatentTbInfectionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexLatentTbInfection $request
     * @return array|Factory|View
     */

    public function getMasterNodes()
    {
        $this->authorize('admin.latent-tb-infection.index');
        $data = LatentTbInfection::where('parent_id', 0)->orderBy('index')->get();
        return view('admin.latent-tb-infection.master-nodes', ['data' => $data]);
    }

    public function index(IndexLatentTbInfection $request)
    {
        $state = State::get(['id', 'title']);
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(LatentTbInfection::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'node_type', 'is_expandable', 'has_options', 'parent_id', 'master_node_id', 'index', 'state_id', 'cadre_id', 'title', 'description', 'time_spent', 'redirect_algo_type', 'redirect_node_id', 'header', 'sub_header', 'activated', 'send_initial_notification', 'created_at'],

            // set columns to searchIn
            ['id', 'node_type', 'title', 'description', 'time_spent', 'redirect_algo_type', 'header', 'sub_header'],

            function ($q) use ($request) {
                // $q->where('activated',1);
                if (isset($request['master']) && $request['master'] != '') {
                    $q->where('parent_id', $request['master'])->orderBy('index', 'desc');
                }
            }
        );
        // $this->setTranslate();
        if ($request->ajax()) {
            $request->session()->pull('latent_tb_notification');
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }
        return view('admin.latent-tb-infection.index', ['data' => $data, 'state' => $state, 'message' => session('latent_tb_notification')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.latent-tb-infection.create');
        $state = State::get(['id', 'title']);
        $cadre = Cadre::get(['id', 'title']);
        return view('admin.latent-tb-infection.create', [
            'state' => $state,
            'cadre' => $cadre,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLatentTbInfection $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreLatentTbInfection $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        if ($request['redirect_node_id'] == null || $request['redirect_node_id'] == NULL) {
            $sanitized['redirect_node_id'] = 0;
        } else {
            $sanitized['redirect_node_id'] = $request['redirect_node_id'];
        }
        $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);
        $sanitized['state_id'] = implode(",", $sanitized['state_id']);
        // Store the LatentTbInfection
        $latentTbInfection = LatentTbInfection::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/latent-tb-infections?master=' . $request['parent_id']), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/latent-tb-infections?master=' . $request['parent_id']);
    }

    /**
     * Display the specified resource.
     *
     * @param LatentTbInfection $latentTbInfection
     * @throws AuthorizationException
     * @return void
     */
    public function show(LatentTbInfection $latentTbInfection)
    {
        $this->authorize('admin.latent-tb-infection.show', $latentTbInfection);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LatentTbInfection $latentTbInfection
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(LatentTbInfection $latentTbInfection)
    {
        $this->authorize('admin.latent-tb-infection.edit', $latentTbInfection);
        $state = State::get(['id', 'title']);
        $cadres = Cadre::get(['id', 'title']);
        if (isset($latentTbInfection['state_id']) && $latentTbInfection['state_id'] != "") {
            $latentTbInfection['state_id'] = explode(',', $latentTbInfection['state_id']);
        } else {
            $latentTbInfection['state_id'] = [];
        }
        if (isset($latentTbInfection['cadre_id']) && $latentTbInfection['cadre_id'] != "") {
            $latentTbInfection['cadre_id'] = explode(',', $latentTbInfection['cadre_id']);
        } else {
            $latentTbInfection['cadre_id'] = [];
        }

        $latentTbInfection['all_cadres'] = $cadres;
        $latentTbInfection['all_states'] = $state;

        return view('admin.latent-tb-infection.edit', [
            'latentTbInfection' => $latentTbInfection,
            'state' => $state,
            'cadre' => $cadres,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLatentTbInfection $request
     * @param LatentTbInfection $latentTbInfection
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateLatentTbInfection $request, LatentTbInfection $latentTbInfection)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);
        $sanitized['state_id'] = implode(",", $sanitized['state_id']);


        // Update changed values LatentTbInfection
        $latentTbInfection->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/latent-tb-infections?master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/latent-tb-infections?master=' . $request['parent_id'] . '&master_node_id=' . $request['master_node_id']);
    }

    public function sendInitialInvitation(Request $request, LatentTbInfection $latentTbInfection)
    {
        $message = "";
        try {
            if ($latentTbInfection->state_id != '' && $latentTbInfection->cadre_id != '') {
                $state = State::count();
                $cadre = Cadre::count();
                $explode_state = count(explode(',', $latentTbInfection->state_id));
                $explode_cadre = count(explode(',', $latentTbInfection->cadre_id));
                if ($state == $explode_state && $cadre == $explode_cadre) {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $latentTbInfection->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $latentTbInfection->state_id . "')")
                        ->orWhere('country_id', 1)
                        ->pluck('id');
                } else {
                    $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $latentTbInfection->cadre_id . "')")
                        ->whereRaw("find_in_set(state_id,'" . $latentTbInfection->state_id . "')")
                        ->pluck('id');
                }
            } elseif ($latentTbInfection->state_id != '' && $latentTbInfection->cadre_id == '') {
                $subscriber = Subscriber::whereRaw("find_in_set(state_id,'" . $latentTbInfection->state_id . "')")
                    // ->toSql();
                    ->pluck('id');
            } else {
                $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $latentTbInfection->cadre_id . "')")
                    // ->toSql();
                    ->pluck('id');
            }

            $notification['title'] = "New Module";
            $notification['description'] = "$latentTbInfection->title";

            $device_id = UserDeviceToken::whereIn('user_id', $subscriber)->get('notification_token'); //$subscriber
            if (isset($device_id) && count($device_id) > 0) {
                $notification['type'] = "Latent TB Infection";
                $notification['subscriber_id'] = implode(',', $subscriber->toArray());
                $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/AlgorithmList/TITLE_LATENT_TB_INFECTION/Latent TB Infection/null";
                $notification['created_by'] = \Auth::user()->id;
                $notification['status'] = 'Pending';
                $userNotification = AutomaticNotification::create($notification);
                // $response = SendNotificationController::newModules($notification,$device_id,Config::get('app.GENERAL.frontend_url')."/AlgorithmList/TITLE_LATENT_TB_INFECTION/Latent TB Infection/null");
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
            LatentTbInfection::where('id', $latentTbInfection->id)->update(['send_initial_notification' => 1]);

            session(['latent_tb_notification' => $message]);
            if ($request->ajax()) {
                session(['latent_tb_notification' => $message]);
                return [
                    'redirect' => url('admin/latent-tb-infections?master=0&master_node_id=0'),
                    'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
                ];
            }
            return redirect('admin/latent-tb-infections?master=0&master_node_id=0')->with('message', $message);
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing Latent tb infection algorithm function");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyLatentTbInfection $request
     * @param LatentTbInfection $latentTbInfection
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyLatentTbInfection $request, LatentTbInfection $latentTbInfection)
    {
        $latentTbInfection->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyLatentTbInfection $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyLatentTbInfection $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('latentTbInfections')->whereIn('id', $bulkChunk)
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
        return view('admin.latent-tb-infection.org-chart');
    }

    public function setTranslate()
    {
        $latentTbInfection = LatentTbInfection::get();

        foreach ($latentTbInfection as $key => $latentTbInfections) {
            $latentTbInfections = LatentTbInfection::find($latentTbInfections->id);
            $latentTbInfections->setTranslation('header', 'en', $latentTbInfection[$key]->header)->save();
            $latentTbInfections->setTranslation('sub_header', 'en', $latentTbInfection[$key]->sub_header)->save();
            // $latentTbInfections->setTranslation('title_value_json', 'en', $latentTbInfection[$key]->title)->save();
            // $latentTbInfections->setTranslation('description_value_json', 'en', $latentTbInfection[$key]->description)->save();
        }
    }
}