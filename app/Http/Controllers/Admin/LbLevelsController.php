<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LbLevel\BulkDestroyLbLevel;
use App\Http\Requests\Admin\LbLevel\DestroyLbLevel;
use App\Http\Requests\Admin\LbLevel\IndexLbLevel;
use App\Http\Requests\Admin\LbLevel\StoreLbLevel;
use App\Http\Requests\Admin\LbLevel\UpdateLbLevel;
use App\Models\LbLevel;
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

class LbLevelsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexLbLevel $request
     * @return array|Factory|View
     */
    public function index(IndexLbLevel $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(LbLevel::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'level', 'content','created_at'],

            // set columns to searchIn
            ['id', 'level', 'content']
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

        return view('admin.lb-level.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.lb-level.create');

        return view('admin.lb-level.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLbLevel $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreLbLevel $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the LbLevel
        $lbLevel = LbLevel::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/lb-levels'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/lb-levels');
    }

    /**
     * Display the specified resource.
     *
     * @param LbLevel $lbLevel
     * @throws AuthorizationException
     * @return void
     */
    public function show(LbLevel $lbLevel)
    {
        $this->authorize('admin.lb-level.show', $lbLevel);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LbLevel $lbLevel
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(LbLevel $lbLevel)
    {
        $this->authorize('admin.lb-level.edit', $lbLevel);


        return view('admin.lb-level.edit', [
            'lbLevel' => $lbLevel,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLbLevel $request
     * @param LbLevel $lbLevel
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateLbLevel $request, LbLevel $lbLevel)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values LbLevel
        $lbLevel->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/lb-levels'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/lb-levels');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyLbLevel $request
     * @param LbLevel $lbLevel
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyLbLevel $request, LbLevel $lbLevel)
    {
        $lbLevel->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyLbLevel $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyLbLevel $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('lbLevels')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
