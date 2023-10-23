<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaticAppConfig\BulkDestroyStaticAppConfig;
use App\Http\Requests\Admin\StaticAppConfig\DestroyStaticAppConfig;
use App\Http\Requests\Admin\StaticAppConfig\IndexStaticAppConfig;
use App\Http\Requests\Admin\StaticAppConfig\StoreStaticAppConfig;
use App\Http\Requests\Admin\StaticAppConfig\UpdateStaticAppConfig;
use App\Models\StaticAppConfig;
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

class StaticAppConfigController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexStaticAppConfig $request
     * @return array|Factory|View
     */
    public function index(IndexStaticAppConfig $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(StaticAppConfig::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'key', 'type', 'value_json','created_at'],

            // set columns to searchIn
            ['id', 'key', 'type', 'value_json']
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
            return ['data' => $data];
        }

        return view('admin.static-app-config.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.static-app-config.create');

        return view('admin.static-app-config.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStaticAppConfig $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreStaticAppConfig $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the StaticAppConfig
        $staticAppConfig = StaticAppConfig::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/static-app-configs'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/static-app-configs');
    }

    /**
     * Display the specified resource.
     *
     * @param StaticAppConfig $staticAppConfig
     * @throws AuthorizationException
     * @return void
     */
    public function show(StaticAppConfig $staticAppConfig)
    {
        $this->authorize('admin.static-app-config.show', $staticAppConfig);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param StaticAppConfig $staticAppConfig
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(StaticAppConfig $staticAppConfig)
    {
        $this->authorize('admin.static-app-config.edit', $staticAppConfig);


        return view('admin.static-app-config.edit', [
            'staticAppConfig' => $staticAppConfig,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStaticAppConfig $request
     * @param StaticAppConfig $staticAppConfig
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateStaticAppConfig $request, StaticAppConfig $staticAppConfig)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values StaticAppConfig
        $staticAppConfig->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/static-app-configs'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/static-app-configs');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyStaticAppConfig $request
     * @param StaticAppConfig $staticAppConfig
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyStaticAppConfig $request, StaticAppConfig $staticAppConfig)
    {
        $staticAppConfig->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyStaticAppConfig $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyStaticAppConfig $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('staticAppConfigs')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
