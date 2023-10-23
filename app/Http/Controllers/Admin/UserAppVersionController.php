<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserAppVersion\BulkDestroyUserAppVersion;
use App\Http\Requests\Admin\UserAppVersion\DestroyUserAppVersion;
use App\Http\Requests\Admin\UserAppVersion\IndexUserAppVersion;
use App\Http\Requests\Admin\UserAppVersion\StoreUserAppVersion;
use App\Http\Requests\Admin\UserAppVersion\UpdateUserAppVersion;
use App\Models\UserAppVersion;
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
use App\Helpers\RequestHelpers;
use Log;

class UserAppVersionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexUserAppVersion $request
     * @return array|Factory|View
     */
    public function index(IndexUserAppVersion $request)
    {
        $paginationParams = RequestHelpers::getPaginationParams($request);
        // create and AdminListing instance for a specific model and
        $data = UserAppVersion::selectRaw('app_version,count(user_id) as user_count,current_plateform')
            ->groupBy('app_version', 'current_plateform')
            ->orderBy("app_version", "desc")
            ->paginate($paginationParams['per_page']);

        $subscriber_not_login = DB::select("select count(*) as Subscriber_count from subscribers where id not in(select user_id from user_app_version)");
        $subscriber_count_array = json_decode(json_encode($subscriber_not_login), true);
        $subscriber_count = $subscriber_count_array[0]['Subscriber_count'];
        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'subscriber_count' => $subscriber_count];
        }

        return view('admin.user-app-version.index', ['data' => $data, 'subscriber_count' => $subscriber_count]);
    }

    public function overAllListing(IndexUserAppVersion $request)
    {
        $plateform = collect(['mobile-app', 'iphone-app', 'web']);
        $app_version = UserAppVersion::distinct('app_version')->get('app_version');
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        $data = AdminListing::create(UserAppVersion::class)->modifyQuery(function ($query) use ($request) {
            if ($request->has('current_plateform')) {
                $query->where('current_plateform', $request->current_plateform);
            }
            if ($request->has('app_version')) {
                $query->where('app_version', $request->app_version);
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['user_id', 'user_name', 'app_version', 'current_plateform', 'has_ios', 'has_android', 'has_web', 'created_at'],

            // set columns to searchIn
            ['user_id', 'user_name', 'app_version', 'current_plateform', 'has_ios', 'has_android', 'has_web'], //
            function ($query) use ($request) {
                $query->with(['user']);
            }
        );
        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'plateform' => $plateform, 'app_version' => $app_version];
        }
        return view('admin.user-app-version.user-app-version', ['data' => $data, 'plateform' => $plateform, 'app_version' => $app_version]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.user-app-version.create');

        return view('admin.user-app-version.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserAppVersion $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreUserAppVersion $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the UserAppVersion
        $userAppVersion = UserAppVersion::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/user-app-versions'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/user-app-versions');
    }

    /**
     * Display the specified resource.
     *
     * @param UserAppVersion $userAppVersion
     * @throws AuthorizationException
     * @return void
     */
    public function show(UserAppVersion $userAppVersion)
    {
        $this->authorize('admin.user-app-version.show', $userAppVersion);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param UserAppVersion $userAppVersion
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(UserAppVersion $userAppVersion)
    {
        $this->authorize('admin.user-app-version.edit', $userAppVersion);


        return view('admin.user-app-version.edit', [
            'userAppVersion' => $userAppVersion,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserAppVersion $request
     * @param UserAppVersion $userAppVersion
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateUserAppVersion $request, UserAppVersion $userAppVersion)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values UserAppVersion
        $userAppVersion->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/user-app-versions'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/user-app-versions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyUserAppVersion $request
     * @param UserAppVersion $userAppVersion
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyUserAppVersion $request, UserAppVersion $userAppVersion)
    {
        $userAppVersion->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyUserAppVersion $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyUserAppVersion $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('userAppVersions')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
