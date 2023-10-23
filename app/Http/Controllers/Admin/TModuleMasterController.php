<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TModuleMaster\BulkDestroyTModuleMaster;
use App\Http\Requests\Admin\TModuleMaster\DestroyTModuleMaster;
use App\Http\Requests\Admin\TModuleMaster\IndexTModuleMaster;
use App\Http\Requests\Admin\TModuleMaster\StoreTModuleMaster;
use App\Http\Requests\Admin\TModuleMaster\UpdateTModuleMaster;
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

class TModuleMasterController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTModuleMaster $request
     * @return array|Factory|View
     */
    public function index(IndexTModuleMaster $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(TModuleMaster::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'name','created_at'],

            // set columns to searchIn
            ['id', 'name']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.t-module-master.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.t-module-master.create');

        return view('admin.t-module-master.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTModuleMaster $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTModuleMaster $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the TModuleMaster
        $tModuleMaster = TModuleMaster::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/t-module-masters'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/t-module-masters');
    }

    /**
     * Display the specified resource.
     *
     * @param TModuleMaster $tModuleMaster
     * @throws AuthorizationException
     * @return void
     */
    public function show(TModuleMaster $tModuleMaster)
    {
        $this->authorize('admin.t-module-master.show', $tModuleMaster);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TModuleMaster $tModuleMaster
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(TModuleMaster $tModuleMaster)
    {
        $this->authorize('admin.t-module-master.edit', $tModuleMaster);


        return view('admin.t-module-master.edit', [
            'tModuleMaster' => $tModuleMaster,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTModuleMaster $request
     * @param TModuleMaster $tModuleMaster
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTModuleMaster $request, TModuleMaster $tModuleMaster)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values TModuleMaster
        $tModuleMaster->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/t-module-masters'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/t-module-masters');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTModuleMaster $request
     * @param TModuleMaster $tModuleMaster
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTModuleMaster $request, TModuleMaster $tModuleMaster)
    {
        $tModuleMaster->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTModuleMaster $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTModuleMaster $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('tModuleMasters')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
