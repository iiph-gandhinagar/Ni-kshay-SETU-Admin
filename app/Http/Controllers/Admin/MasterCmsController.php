<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MasterCm\BulkDestroyMasterCm;
use App\Http\Requests\Admin\MasterCm\DestroyMasterCm;
use App\Http\Requests\Admin\MasterCm\IndexMasterCm;
use App\Http\Requests\Admin\MasterCm\StoreMasterCm;
use App\Http\Requests\Admin\MasterCm\UpdateMasterCm;
use App\Models\MasterCm;
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

class MasterCmsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexMasterCm $request
     * @return array|Factory|View
     */
    public function index(IndexMasterCm $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(MasterCm::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title', 'description'],

            // set columns to searchIn
            ['id', 'title', 'description']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.master-cm.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.master-cm.create');

        return view('admin.master-cm.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMasterCm $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreMasterCm $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the MasterCm
        $masterCm = MasterCm::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/master-cms'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/master-cms');
    }

    /**
     * Display the specified resource.
     *
     * @param MasterCm $masterCm
     * @throws AuthorizationException
     * @return void
     */
    public function show(MasterCm $masterCm)
    {
        $this->authorize('admin.master-cm.show', $masterCm);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MasterCm $masterCm
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(MasterCm $masterCm)
    {
        $this->authorize('admin.master-cm.edit', $masterCm);


        return view('admin.master-cm.edit', [
            'masterCm' => $masterCm,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateMasterCm $request
     * @param MasterCm $masterCm
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateMasterCm $request, MasterCm $masterCm)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values MasterCm
        $masterCm->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/master-cms'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/master-cms');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyMasterCm $request
     * @param MasterCm $masterCm
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyMasterCm $request, MasterCm $masterCm)
    {
        $masterCm->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyMasterCm $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyMasterCm $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('masterCms')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
