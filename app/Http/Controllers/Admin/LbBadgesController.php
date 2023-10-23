<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LbBadge\BulkDestroyLbBadge;
use App\Http\Requests\Admin\LbBadge\DestroyLbBadge;
use App\Http\Requests\Admin\LbBadge\IndexLbBadge;
use App\Http\Requests\Admin\LbBadge\StoreLbBadge;
use App\Http\Requests\Admin\LbBadge\UpdateLbBadge;
use App\Models\LbBadge;
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

class LbBadgesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexLbBadge $request
     * @return array|Factory|View
     */
    public function index(IndexLbBadge $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/lb-badge-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/lb-badge-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/lb-badge-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(LbBadge::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'level_id', 'badge', 'created_at'],

            // set columns to searchIn
            ['id', 'badge', 'lb_levels.level'],
            function ($query) use ($request) {
                $query->with(['lb_level']);

                $query->leftJoin('lb_levels', 'lb_levels.id', '=', 'lb_badges.level_id');
            }
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/lb-badge-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/lb-badge-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/lb-badge-search')];
        }

        return view('admin.lb-badge.index', ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/lb-badge-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.lb-badge.create');

        $lb_level = LbLevel::get(['id', 'level']);

        return view('admin.lb-badge.create', ['lb_level' => $lb_level]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLbBadge $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreLbBadge $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['level_id'] = $request['level_id']['id'];
        // Store the LbBadge
        $lbBadge = LbBadge::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/lb-badges'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/lb-badges');
    }

    /**
     * Display the specified resource.
     *
     * @param LbBadge $lbBadge
     * @throws AuthorizationException
     * @return void
     */
    public function show(LbBadge $lbBadge)
    {
        $this->authorize('admin.lb-badge.show', $lbBadge);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LbBadge $lbBadge
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(LbBadge $lbBadge)
    {
        $this->authorize('admin.lb-badge.edit', $lbBadge);
        $lb_level = LbLevel::get(['id', 'level']);

        $lbBadge['level_id'] = LbLevel::where('id', $lbBadge->level_id)->get(['id', 'level']);

        return view('admin.lb-badge.edit', [
            'lbBadge' => $lbBadge,
            'lb_level' => $lb_level,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLbBadge $request
     * @param LbBadge $lbBadge
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateLbBadge $request, LbBadge $lbBadge)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($sanitized['level_id']) && isset($sanitized['level_id'][0]) && $sanitized['level_id'][0] != '') {
            $sanitized['level_id'] = $sanitized['level_id'][0]['id'];
        } elseif (isset($sanitized['level_id']) && $sanitized['level_id'] != '' && $sanitized['level_id']['id']) {
            $sanitized['level_id'] = $sanitized['level_id']['id'];
        }

        // Update changed values LbBadge
        $lbBadge->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/lb-badges'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/lb-badges');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyLbBadge $request
     * @param LbBadge $lbBadge
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyLbBadge $request, LbBadge $lbBadge)
    {
        $lbBadge->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyLbBadge $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyLbBadge $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('lbBadges')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
