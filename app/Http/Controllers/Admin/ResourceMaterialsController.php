<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResourceMaterial\BulkDestroyResourceMaterial;
use App\Http\Requests\Admin\ResourceMaterial\DestroyResourceMaterial;
use App\Http\Requests\Admin\ResourceMaterial\IndexResourceMaterial;
use App\Http\Requests\Admin\ResourceMaterial\StoreResourceMaterial;
use App\Http\Requests\Admin\ResourceMaterial\UpdateResourceMaterial;
use App\Jobs\sendNotification;
use App\Models\AutomaticNotification;
use App\Models\ResourceMaterial;
use App\Models\State;
use App\Models\Cadre;
use App\Models\Country;
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

class ResourceMaterialsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexResourceMaterial $request
     * @return array|Factory|View
     */
    public function index(IndexResourceMaterial $request)
    {
        $state = State::get(['id', 'title']);

        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/resource-material-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/resource-material-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/resource-material-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ResourceMaterial::class)
            ->modifyQuery(function ($query) use ($request) {
                $query->with(['parent_master', 'user', 'media']);
                if (\Auth::user()->roles[0]['id'] == 10 && $request['master'] != 0) {
                    $query->where('created_by', \Auth::user()->id);
                }
                if (isset($request['master']) && $request['master'] != '') {
                    $query->where('parent_id', $request['master']);
                }

                $assignedState = '';
                $assignedCadre = '';
                if (\Auth::user()->role_type == 'country_type' && (\Auth::user()->roles[0]['id'] == 1 || \Auth::user()->roles[0]['id'] == 2)) {
                    // $assignedCountry = \Auth::user()->country;
                    // $assignedState = \Auth::user()->state;
                    // $assignedCadre = \Auth::user()->cadre;
                    // $assignedDistrict = \Auth::user()->district;
                } elseif (\Auth::user()->role_type == 'state_type') {
                    $assignedCadre = \Auth::user()->cadre;
                    $assignedState = \Auth::user()->state;
                    // Log::info($assignedCadre);
                    // Log::info($assignedState);
                    // $query->whereRaw("find_in_set('" . $assignedState . "',state)");
                    // $query->whereRaw("find_in_set('" . $assignedCadre . "',cadre)");
                    $query->whereRaw("substr('" . $assignedState . "',state)")->whereRaw("substr('" . $assignedCadre . "',cadre)")->orWhere('cadre', $assignedCadre);
                }
            })->processRequestAndGet(
                // pass the request with params
                $request,

                // set columns to query
                ['id', 'title', 'type_of_materials', 'country_id', 'state', 'cadre', 'parent_id', 'icon_type', 'index', 'created_by', 'created_at'],

                // set columns to searchIn
                ['id', 'title', 'type_of_materials', 'state', 'cadre', 'icon_type']
            );
        // $this->setTranslate();
        if ($request->ajax()) {
            $request->session()->pull('resource_material_notification');
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/resource-material-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/resource-material-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/resource-material-search')];
        }
        return view('admin.resource-material.index', ['data' => $data, 'state' => $state, 'message' => session('resource_material_notification'), 'search' => session(\Str::slug($request->getPathInfo()) . '/resource-material-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.resource-material.create');
        $masterData = \StateWiseFilterData::getStateWiseMasterData();
        $state = $masterData['state'];
        $cadre = $masterData['cadres'];
        $country = $masterData['country'];
        $folderList = ResourceMaterial::where('type_of_materials', 'folder')->with('parent_folder')->orderBy('parent_id', 'asc')->get();
        $parentTitle = '';
        if (isset($_REQUEST['master']) && $_REQUEST['master'] != '' && $_REQUEST['master'] > 0) {
            $parentTitle = ResourceMaterial::where('id', $_REQUEST['master'])->pluck('title')[0];
        } else if (isset($_REQUEST['master']) && $_REQUEST['master'] != '' && $_REQUEST['master'] == 0) {
            $parentTitle = 'Root';
        } else {
            $parentTitle = '';
        }

        if (\Auth::user()->roles[0]['id'] == 10) {
            $cadre = Cadre::whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get(['id', 'title', 'cadre_type']);
        }

        return view('admin.resource-material.create', [
            'state' => $state,
            'cadre' => $cadre,
            'folderList' => $folderList,
            'parent_title' => $parentTitle,
            'country' => $country,
            'user_state' => \Auth::user()->state
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreResourceMaterial $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreResourceMaterial $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($request['country_id']) && $request['country_id'] != '') {
            if (count($request['country_id']) > 0) {
                $sanitized['country_id'] = $request['country_id']['id'];
            } else {
                $sanitized['country_id'] = 0;
            }
        } else {
            $sanitized['country_id'] = 0;
        }
        $sanitized['cadre'] = implode(",", $sanitized['cadre']);
        $sanitized['state'] = implode(",", $sanitized['state']);
        $sanitized['created_by'] = \Auth::user()->id;

        // Store the ResourceMaterial
        ResourceMaterial::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/resource-materials?master=' . $request['parent_id']), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/resource-materials?master=' . $request['parent_id']);
    }

    /**
     * Display the specified resource.
     *
     * @param ResourceMaterial $resourceMaterial
     * @throws AuthorizationException
     * @return void
     */
    public function show(ResourceMaterial $resourceMaterial)
    {
        $this->authorize('admin.resource-material.show', $resourceMaterial);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ResourceMaterial $resourceMaterial
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(ResourceMaterial $resourceMaterial)
    {
        if (\Auth::user()->roles[0]['id'] == 3 || \Auth::user()->roles[0]['id'] == 10) {
            $resourceMaterial->load('user');
            if ($resourceMaterial->user->roles[0]->id != 3 || $resourceMaterial->user->roles[0]->id != 10) {
                abort(403);
            } else {
                $this->authorize('admin.resource-material.edit', $resourceMaterial);
            }
        } else {
            $this->authorize('admin.resource-material.edit', $resourceMaterial);
        }
        $masterData = \StateWiseFilterData::getStateWiseMasterData();
        $state = $masterData['state'];
        $cadres = $masterData['cadres'];
        $country = $masterData['country'];
        $folderList = ResourceMaterial::where('type_of_materials', 'folder')->with('parent_folder')->orderBy('parent_id', 'asc')->get();
        if (\Auth::user()->roles[0]['id'] == 10) {
            $cadres = Cadre::whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get(['id', 'title', 'cadre_type']);
        }

        //needed to show multiselect selected value
        if (isset($resourceMaterial['cadre']) && $resourceMaterial['cadre'] != "") {
            $resourceMaterial['cadre'] = explode(',', $resourceMaterial['cadre']);
        }
        if (isset($resourceMaterial['country_id']) && $resourceMaterial['country_id'] != "") {
            $resourceMaterial['country_id'] = $resourceMaterial['country_id'];
            $resourceMaterial['country_id'] = Country::where('id', $resourceMaterial['country_id'])->get(['id', 'title']);
        }
        if (isset($resourceMaterial['state']) && $resourceMaterial['state'] != "") {
            $resourceMaterial['state'] = explode(',', $resourceMaterial['state']);
        }
        $resourceMaterial['all_cadres'] = $cadres;
        $resourceMaterial['all_states'] = $state;

        return view('admin.resource-material.edit', [
            'resourceMaterial' => $resourceMaterial,
            'state' => $state,
            'cadre' => $cadres,
            'folderList' => $folderList,
            'country' =>  $country,
            'user_state' => \Auth::user()->state
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateResourceMaterial $request
     * @param ResourceMaterial $resourceMaterial
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateResourceMaterial $request, ResourceMaterial $resourceMaterial)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
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
        $sanitized['cadre'] = implode(",", $sanitized['cadre']);
        $sanitized['state'] = implode(",", $sanitized['state']);

        // Update changed values ResourceMaterial
        $resourceMaterial->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/resource-materials?master=' . $request['parent_id']),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/resource-materials?master=' . $request['parent_id']);
    }

    public function sendInitialInvitation(Request $request, ResourceMaterial $resourceMaterial)
    {
        $message = "";
        try {

            if ($resourceMaterial->country_id != 0) {
                $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $resourceMaterial->cadre . "')")
                    ->whereRaw("find_in_set(country_id,'" . $resourceMaterial->country_id . "')")->pluck('id');
            } else {
                $subscriber = Subscriber::whereRaw("find_in_set(cadre_id,'" . $resourceMaterial->cadre . "')")
                    ->whereRaw("find_in_set(state_id,'" . $resourceMaterial->state . "')")->pluck('id');
            }

            $notification['title'] = "New Resource Material Added";
            $notification['description'] = "$resourceMaterial->title";
            $material_parentid = ResourceMaterial::where('parent_id', $resourceMaterial->parent_id)->get(['title', 'type_of_materials'])[0];
            $device_id = UserDeviceToken::whereIn('user_id', $subscriber)->get('notification_token'); //$subscriber
            if (isset($device_id) && count($device_id) > 0) {
                $notification['type'] = "Resource Material";
                $notification['subscriber_id'] = implode(',', $subscriber->toArray());
                $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/Materials/$resourceMaterial->parent_id/$material_parentid->type_of_materials/$material_parentid->title";
                $notification['created_by'] = \Auth::user()->id;
                $notification['status'] = 'Pending';
                $userNotification = AutomaticNotification::create($notification);
                // $response = SendNotificationController::resourceMaterial($notification,$device_id,Config::get('app.GENERAL.frontend_url')."/Materials/$resourceMaterial->parent_id/$material_parentid->type_of_materials/$material_parentid->title");
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

            session(['resource_material_notification' => $message]);
            if ($request->ajax()) {
                session(['resource_material_notification' => $message]);
                return [
                    'redirect' => url('admin/resource-materials?master=' . $resourceMaterial['parent_id']),
                    'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
                ];
            }
            return redirect('admin/resource-materials?master=' . $resourceMaterial['parent_id'])->with('message', $message); //
        } catch (Exception $e) {
            Log::error($e);
            Log::info("some error in processing case definitions function");
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyResourceMaterial $request
     * @param ResourceMaterial $resourceMaterial
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyResourceMaterial $request, ResourceMaterial $resourceMaterial)
    {
        $resourceMaterial->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyResourceMaterial $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyResourceMaterial $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('resource_materials')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }


    // public function setTranslate()
    // {
    //     $resource_material = ResourceMaterial::get();

    //     foreach ($resource_material as $key => $resource_materials) {
    //         $resource_materials = ResourceMaterial::find($resource_materials->id);
    //         $resource_materials->setTranslation('title_json', 'en', $resource_material[$key]->title)->save();
    //     }
    // }
}