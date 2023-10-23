<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaticModule\BulkDestroyStaticModule;
use App\Http\Requests\Admin\StaticModule\DestroyStaticModule;
use App\Http\Requests\Admin\StaticModule\IndexStaticModule;
use App\Http\Requests\Admin\StaticModule\StoreStaticModule;
use App\Http\Requests\Admin\StaticModule\UpdateStaticModule;
use App\Models\StaticModule;
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

class StaticModuleController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexStaticModule $request
     * @return array|Factory|View
     */
    public function index(IndexStaticModule $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/static-module-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/static-module-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/static-module-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(StaticModule::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title', 'description', 'order_index', 'active','created_at'],

            // set columns to searchIn
            ['id', 'title', 'slug', 'description']
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/static-module-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/static-module-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/static-module-search')];
        }

        return view('admin.static-module.index', ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/static-module-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.static-module.create');

        return view('admin.static-module.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStaticModule $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreStaticModule $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the StaticModule
        $staticModule = StaticModule::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/static-modules'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/static-modules');
    }

    /**
     * Display the specified resource.
     *
     * @param StaticModule $staticModule
     * @throws AuthorizationException
     * @return void
     */
    public function show(StaticModule $staticModule)
    {
        $this->authorize('admin.static-module.show', $staticModule);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param StaticModule $staticModule
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(StaticModule $staticModule)
    {
        $this->authorize('admin.static-module.edit', $staticModule);


        return view('admin.static-module.edit', [
            'staticModule' => $staticModule,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStaticModule $request
     * @param StaticModule $staticModule
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateStaticModule $request, StaticModule $staticModule)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values StaticModule
        $staticModule->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/static-modules'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/static-modules');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyStaticModule $request
     * @param StaticModule $staticModule
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyStaticModule $request, StaticModule $staticModule)
    {
        $staticModule->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyStaticModule $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyStaticModule $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('staticModules')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
