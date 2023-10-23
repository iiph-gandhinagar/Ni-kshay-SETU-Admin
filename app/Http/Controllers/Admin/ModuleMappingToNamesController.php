<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModuleMappingToName\BulkDestroyModuleMappingToName;
use App\Http\Requests\Admin\ModuleMappingToName\DestroyModuleMappingToName;
use App\Http\Requests\Admin\ModuleMappingToName\IndexModuleMappingToName;
use App\Http\Requests\Admin\ModuleMappingToName\StoreModuleMappingToName;
use App\Http\Requests\Admin\ModuleMappingToName\UpdateModuleMappingToName;
use App\Models\ModuleMappingToName;
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

class ModuleMappingToNamesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexModuleMappingToName $request
     * @return array|Factory|View
     */
    public function index(IndexModuleMappingToName $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ModuleMappingToName::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'module_name', 'mapping_name'],

            // set columns to searchIn
            ['id', 'module_name', 'mapping_name']
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

        return view('admin.module-mapping-to-name.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.module-mapping-to-name.create');

        return view('admin.module-mapping-to-name.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreModuleMappingToName $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreModuleMappingToName $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the ModuleMappingToName
        $moduleMappingToName = ModuleMappingToName::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/module-mapping-to-names'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/module-mapping-to-names');
    }

    /**
     * Display the specified resource.
     *
     * @param ModuleMappingToName $moduleMappingToName
     * @throws AuthorizationException
     * @return void
     */
    public function show(ModuleMappingToName $moduleMappingToName)
    {
        $this->authorize('admin.module-mapping-to-name.show', $moduleMappingToName);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ModuleMappingToName $moduleMappingToName
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(ModuleMappingToName $moduleMappingToName)
    {
        $this->authorize('admin.module-mapping-to-name.edit', $moduleMappingToName);


        return view('admin.module-mapping-to-name.edit', [
            'moduleMappingToName' => $moduleMappingToName,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateModuleMappingToName $request
     * @param ModuleMappingToName $moduleMappingToName
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateModuleMappingToName $request, ModuleMappingToName $moduleMappingToName)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values ModuleMappingToName
        $moduleMappingToName->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/module-mapping-to-names'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/module-mapping-to-names');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyModuleMappingToName $request
     * @param ModuleMappingToName $moduleMappingToName
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyModuleMappingToName $request, ModuleMappingToName $moduleMappingToName)
    {
        $moduleMappingToName->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyModuleMappingToName $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyModuleMappingToName $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('moduleMappingToNames')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
