<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DynamicAlgoMaster\BulkDestroyDynamicAlgoMaster;
use App\Http\Requests\Admin\DynamicAlgoMaster\DestroyDynamicAlgoMaster;
use App\Http\Requests\Admin\DynamicAlgoMaster\IndexDynamicAlgoMaster;
use App\Http\Requests\Admin\DynamicAlgoMaster\StoreDynamicAlgoMaster;
use App\Http\Requests\Admin\DynamicAlgoMaster\UpdateDynamicAlgoMaster;
use App\Models\DynamicAlgoMaster;
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

class DynamicAlgoMasterController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexDynamicAlgoMaster $request
     * @return array|Factory|View
     */
    public function index(IndexDynamicAlgoMaster $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(DynamicAlgoMaster::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'section', 'active'],

            // set columns to searchIn
            ['id', 'name', 'section']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.dynamic-algo-master.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.dynamic-algo-master.create');

        return view('admin.dynamic-algo-master.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDynamicAlgoMaster $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreDynamicAlgoMaster $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the DynamicAlgoMaster
        $dynamicAlgoMaster = DynamicAlgoMaster::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/dynamic-algo-masters'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/dynamic-algo-masters');
    }

    /**
     * Display the specified resource.
     *
     * @param DynamicAlgoMaster $dynamicAlgoMaster
     * @throws AuthorizationException
     * @return void
     */
    public function show(DynamicAlgoMaster $dynamicAlgoMaster)
    {
        $this->authorize('admin.dynamic-algo-master.show', $dynamicAlgoMaster);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param DynamicAlgoMaster $dynamicAlgoMaster
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(DynamicAlgoMaster $dynamicAlgoMaster)
    {
        $this->authorize('admin.dynamic-algo-master.edit', $dynamicAlgoMaster);


        return view('admin.dynamic-algo-master.edit', [
            'dynamicAlgoMaster' => $dynamicAlgoMaster,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDynamicAlgoMaster $request
     * @param DynamicAlgoMaster $dynamicAlgoMaster
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateDynamicAlgoMaster $request, DynamicAlgoMaster $dynamicAlgoMaster)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values DynamicAlgoMaster
        $dynamicAlgoMaster->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/dynamic-algo-masters'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/dynamic-algo-masters');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyDynamicAlgoMaster $request
     * @param DynamicAlgoMaster $dynamicAlgoMaster
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyDynamicAlgoMaster $request, DynamicAlgoMaster $dynamicAlgoMaster)
    {
        $dynamicAlgoMaster->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyDynamicAlgoMaster $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyDynamicAlgoMaster $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('dynamicAlgoMasters')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
