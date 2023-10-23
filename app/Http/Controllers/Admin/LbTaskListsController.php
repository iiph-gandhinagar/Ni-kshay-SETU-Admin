<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LbTaskList\BulkDestroyLbTaskList;
use App\Http\Requests\Admin\LbTaskList\DestroyLbTaskList;
use App\Http\Requests\Admin\LbTaskList\IndexLbTaskList;
use App\Http\Requests\Admin\LbTaskList\StoreLbTaskList;
use App\Http\Requests\Admin\LbTaskList\UpdateLbTaskList;
use App\Models\LbBadge;
use App\Models\LbLevel;
use App\Models\LbTaskList;
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

class LbTaskListsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexLbTaskList $request
     * @return array|Factory|View
     */
    public function index(IndexLbTaskList $request)
    {
        $level = LbLevel::get(['id', 'level']);
        $badge = LbBadge::get(['id', 'badge', 'level_id']);

        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(LbTaskList::class)->modifyQuery(function ($query) use ($request) {
            if ($request->has('level_id')) {
                $query->where('level_id', $request->level_id);
            }
            if ($request->has('badge_id')) {
                $query->where('badges', $request->badge_id);
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'level', 'badges', 'mins_spent', 'sub_module_usage_count', 'App_opended_count', 'chatbot_usage_count', 'resource_material_accessed_count', 'total_task', 'created_at'],

            // set columns to searchIn
            ['id', 'mins_spent', 'sub_module_usage_count', 'lb_levels.level', 'lb_badges.badge'],
            function ($query) use ($request) {
                $query->with(['lb_level', 'lb_badge']);

                $query->leftJoin('lb_levels', 'lb_levels.id', '=', 'lb_task_lists.level');
                $query->leftJoin('lb_badges', 'lb_badges.id', '=', 'lb_task_lists.badges');
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

        return view('admin.lb-task-list.index', ['data' => $data, 'level' => $level, 'badge' => $badge]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.lb-task-list.create');
        $lb_level = LbLevel::get(['id', 'level']);
        $lb_badge = LbBadge::get(['id', 'badge', 'level_id']);
        return view('admin.lb-task-list.create', [
            'lb_level' => $lb_level,
            'lb_badge' => $lb_badge,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLbTaskList $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreLbTaskList $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['level'] = $request['level']['id'];
        $sanitized['badges'] = $request['badges']['id'];

        // Store the LbTaskList
        $lbTaskList = LbTaskList::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/lb-task-lists'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/lb-task-lists');
    }

    /**
     * Display the specified resource.
     *
     * @param LbTaskList $lbTaskList
     * @throws AuthorizationException
     * @return void
     */
    public function show(LbTaskList $lbTaskList)
    {
        $this->authorize('admin.lb-task-list.show', $lbTaskList);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LbTaskList $lbTaskList
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(LbTaskList $lbTaskList)
    {
        $this->authorize('admin.lb-task-list.edit', $lbTaskList);
        $lb_level = LbLevel::get(['id', 'level']);
        $lb_badge = LbBadge::get(['id', 'badge', 'level_id']);

        $lbTaskList['level'] = LbLevel::where('id', $lbTaskList->level)->get(['id', 'level']);
        $lbTaskList['badges'] = LbBadge::where('id', $lbTaskList->badges)->get(['id', 'badge', 'level_id']);

        return view('admin.lb-task-list.edit', [
            'lbTaskList' => $lbTaskList,
            'lb_level' => $lb_level,
            'lb_badge' => $lb_badge,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLbTaskList $request
     * @param LbTaskList $lbTaskList
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateLbTaskList $request, LbTaskList $lbTaskList)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($sanitized['level']) && isset($sanitized['level'][0]) && $sanitized['level'][0] != '') {
            $sanitized['level'] = $sanitized['level'][0]['id'];
        } elseif (isset($sanitized['level']) && $sanitized['level'] != '' && $sanitized['level']['id']) {
            $sanitized['level'] = $sanitized['level']['id'];
        }

        if (isset($sanitized['badges']) && isset($sanitized['badges'][0]) && $sanitized['badges'][0] != '') {
            $sanitized['badges'] = $sanitized['badges'][0]['id'];
        } elseif (isset($sanitized['badges']) && $sanitized['badges'] != '' && $sanitized['badges']['id']) {
            $sanitized['badges'] = $sanitized['badges']['id'];
        }

        // Update changed values LbTaskList
        $lbTaskList->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/lb-task-lists'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/lb-task-lists');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyLbTaskList $request
     * @param LbTaskList $lbTaskList
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyLbTaskList $request, LbTaskList $lbTaskList)
    {
        $lbTaskList->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyLbTaskList $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyLbTaskList $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('lbTaskLists')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
