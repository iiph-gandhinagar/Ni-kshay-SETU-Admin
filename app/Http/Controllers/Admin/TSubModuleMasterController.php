<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TSubModuleMaster\BulkDestroyTSubModuleMaster;
use App\Http\Requests\Admin\TSubModuleMaster\DestroyTSubModuleMaster;
use App\Http\Requests\Admin\TSubModuleMaster\IndexTSubModuleMaster;
use App\Http\Requests\Admin\TSubModuleMaster\StoreTSubModuleMaster;
use App\Http\Requests\Admin\TSubModuleMaster\UpdateTSubModuleMaster;
use App\Models\TSubModuleMaster;
use App\Models\TModuleMaster;
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

class TSubModuleMasterController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTSubModuleMaster $request
     * @return array|Factory|View
     */
    public function index(IndexTSubModuleMaster $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(TSubModuleMaster::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name', 'module_id', 'existing_module_ref'],

            // set columns to searchIn
            ['id', 'name'],

            function ($q) use ($request) {
                $q->with(['modules']);
            }
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.t-sub-module-master.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.t-sub-module-master.create');

        return view('admin.t-sub-module-master.create', ['modules' => TModuleMaster::get()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTSubModuleMaster $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTSubModuleMaster $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['module_id'] = $sanitized['module_id']['id'];

        // Store the TSubModuleMaster
        $tSubModuleMaster = TSubModuleMaster::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/t-sub-module-masters'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/t-sub-module-masters');
    }

    /**
     * Display the specified resource.
     *
     * @param TSubModuleMaster $tSubModuleMaster
     * @throws AuthorizationException
     * @return void
     */
    public function show(TSubModuleMaster $tSubModuleMaster)
    {
        $this->authorize('admin.t-sub-module-master.show', $tSubModuleMaster);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TSubModuleMaster $tSubModuleMaster
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(TSubModuleMaster $tSubModuleMaster)
    {
        $this->authorize('admin.t-sub-module-master.edit', $tSubModuleMaster);

        $tSubModuleMaster['module_id'] = TModuleMaster::where('id', $tSubModuleMaster['module_id'])->get();
        return view('admin.t-sub-module-master.edit', [
            'tSubModuleMaster' => $tSubModuleMaster,
            'modules' => TModuleMaster::get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTSubModuleMaster $request
     * @param TSubModuleMaster $tSubModuleMaster
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTSubModuleMaster $request, TSubModuleMaster $tSubModuleMaster)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['module_id'] = $sanitized['module_id']['id'];

        // Update changed values TSubModuleMaster
        $tSubModuleMaster->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/t-sub-module-masters'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/t-sub-module-masters');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTSubModuleMaster $request
     * @param TSubModuleMaster $tSubModuleMaster
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTSubModuleMaster $request, TSubModuleMaster $tSubModuleMaster)
    {
        $tSubModuleMaster->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTSubModuleMaster $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTSubModuleMaster $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('tSubModuleMasters')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
