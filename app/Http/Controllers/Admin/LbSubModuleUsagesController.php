<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LbSubModuleUsage\BulkDestroyLbSubModuleUsage;
use App\Http\Requests\Admin\LbSubModuleUsage\DestroyLbSubModuleUsage;
use App\Http\Requests\Admin\LbSubModuleUsage\IndexLbSubModuleUsage;
use App\Http\Requests\Admin\LbSubModuleUsage\StoreLbSubModuleUsage;
use App\Http\Requests\Admin\LbSubModuleUsage\UpdateLbSubModuleUsage;
use App\Models\LbSubModuleUsage;
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

class LbSubModuleUsagesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexLbSubModuleUsage $request
     * @return array|Factory|View
     */
    public function index(IndexLbSubModuleUsage $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(LbSubModuleUsage::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'subscriber_id','module_id', 'sub_module', 'total_time', 'mins_spent', 'completed_flag','created_at'],

            // set columns to searchIn
            ['id', 'sub_module', 'total_time', 'mins_spent'],
            function($query) use($request){
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
            return ['data' => $data];
        }

        return view('admin.lb-sub-module-usage.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.lb-sub-module-usage.create');

        return view('admin.lb-sub-module-usage.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLbSubModuleUsage $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreLbSubModuleUsage $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the LbSubModuleUsage
        $lbSubModuleUsage = LbSubModuleUsage::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/lb-sub-module-usages'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/lb-sub-module-usages');
    }

    /**
     * Display the specified resource.
     *
     * @param LbSubModuleUsage $lbSubModuleUsage
     * @throws AuthorizationException
     * @return void
     */
    public function show(LbSubModuleUsage $lbSubModuleUsage)
    {
        $this->authorize('admin.lb-sub-module-usage.show', $lbSubModuleUsage);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LbSubModuleUsage $lbSubModuleUsage
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(LbSubModuleUsage $lbSubModuleUsage)
    {
        $this->authorize('admin.lb-sub-module-usage.edit', $lbSubModuleUsage);


        return view('admin.lb-sub-module-usage.edit', [
            'lbSubModuleUsage' => $lbSubModuleUsage,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLbSubModuleUsage $request
     * @param LbSubModuleUsage $lbSubModuleUsage
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateLbSubModuleUsage $request, LbSubModuleUsage $lbSubModuleUsage)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values LbSubModuleUsage
        $lbSubModuleUsage->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/lb-sub-module-usages'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/lb-sub-module-usages');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyLbSubModuleUsage $request
     * @param LbSubModuleUsage $lbSubModuleUsage
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyLbSubModuleUsage $request, LbSubModuleUsage $lbSubModuleUsage)
    {
        $lbSubModuleUsage->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyLbSubModuleUsage $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyLbSubModuleUsage $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('lbSubModuleUsages')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
