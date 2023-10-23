<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaticResourceMaterial\BulkDestroyStaticResourceMaterial;
use App\Http\Requests\Admin\StaticResourceMaterial\DestroyStaticResourceMaterial;
use App\Http\Requests\Admin\StaticResourceMaterial\IndexStaticResourceMaterial;
use App\Http\Requests\Admin\StaticResourceMaterial\StoreStaticResourceMaterial;
use App\Http\Requests\Admin\StaticResourceMaterial\UpdateStaticResourceMaterial;
use App\Models\StaticResourceMaterial;
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

class StaticResourceMaterialsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexStaticResourceMaterial $request
     * @return array|Factory|View
     */
    public function index(IndexStaticResourceMaterial $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/static-resource-material-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/static-resource-material-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/static-resource-material-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(StaticResourceMaterial::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['active', 'id', 'order_index', 'title', 'type_of_materials','created_at'],

            // set columns to searchIn
            ['id', 'title', 'type_of_materials']
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/static-resource-material-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/static-resource-material-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/static-resource-material-search')];
        }

        return view('admin.static-resource-material.index', ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/static-resource-material-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.static-resource-material.create');

        return view('admin.static-resource-material.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStaticResourceMaterial $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreStaticResourceMaterial $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the StaticResourceMaterial
        $staticResourceMaterial = StaticResourceMaterial::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/static-resource-materials'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/static-resource-materials');
    }

    /**
     * Display the specified resource.
     *
     * @param StaticResourceMaterial $staticResourceMaterial
     * @throws AuthorizationException
     * @return void
     */
    public function show(StaticResourceMaterial $staticResourceMaterial)
    {
        $this->authorize('admin.static-resource-material.show', $staticResourceMaterial);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param StaticResourceMaterial $staticResourceMaterial
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(StaticResourceMaterial $staticResourceMaterial)
    {
        $this->authorize('admin.static-resource-material.edit', $staticResourceMaterial);


        return view('admin.static-resource-material.edit', [
            'staticResourceMaterial' => $staticResourceMaterial,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStaticResourceMaterial $request
     * @param StaticResourceMaterial $staticResourceMaterial
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateStaticResourceMaterial $request, StaticResourceMaterial $staticResourceMaterial)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values StaticResourceMaterial
        $staticResourceMaterial->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/static-resource-materials'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/static-resource-materials');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyStaticResourceMaterial $request
     * @param StaticResourceMaterial $staticResourceMaterial
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyStaticResourceMaterial $request, StaticResourceMaterial $staticResourceMaterial)
    {
        $staticResourceMaterial->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyStaticResourceMaterial $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyStaticResourceMaterial $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('staticResourceMaterials')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
