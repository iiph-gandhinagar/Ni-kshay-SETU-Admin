<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LbSubscriberRankingsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LbSubscriberRanking\BulkDestroyLbSubscriberRanking;
use App\Http\Requests\Admin\LbSubscriberRanking\DestroyLbSubscriberRanking;
use App\Http\Requests\Admin\LbSubscriberRanking\IndexLbSubscriberRanking;
use App\Http\Requests\Admin\LbSubscriberRanking\StoreLbSubscriberRanking;
use App\Http\Requests\Admin\LbSubscriberRanking\UpdateLbSubscriberRanking;
use App\Models\LbBadge;
use App\Models\LbLevel;
use App\Models\LbSubscriberRanking;
use App\Models\Subscriber;
use Brackets\AdminListing\Facades\AdminListing;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;

class LbSubscriberRankingsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexLbSubscriberRanking $request
     * @return array|Factory|View
     */
    public function index(IndexLbSubscriberRanking $request)
    {
        $level = LbLevel::get(['id','level']);
        $badge = LbBadge::get(['id','badge','level_id']);
        $subscriber = Subscriber::get(['id','name']);
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(LbSubscriberRanking::class)->modifyQuery(function ($query) use ($request) {
            if ($request->has('level_id')) {
                $query->where('lb_subscriber_rankings.level_id', $request->level_id);
            }
            if ($request->has('badge_id')) {
                $query->where('lb_subscriber_rankings.badge_id', $request->badge_id);
            }
            if ($request->has('subscriber_id')) {
                $query->where('lb_subscriber_rankings.subscriber_id', $request->subscriber_id);
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['App_opended_count', 'badge_id', 'chatbot_usage_count', 'id', 'level_id', 'mins_spent_count', 'resource_material_accessed_count', 'sub_module_usage_count', 'subscriber_id', 'total_task_count','created_at'],

            // set columns to searchIn
            ['id', 'mins_spent_count', 'sub_module_usage_count','lb_levels.level','lb_badges.badge','subscribers.name'],
            function ($query) use($request){
                $query->with(['lb_level','lb_badge','lb_task_list','user']);

                $query->leftJoin('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id');
                $query->leftJoin('lb_levels', 'lb_levels.id', '=', 'lb_subscriber_rankings.level_id');
                $query->leftJoin('lb_badges', 'lb_badges.id', '=', 'lb_subscriber_rankings.badge_id');
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

        return view('admin.lb-subscriber-ranking.index', ['data' => $data,'level' => $level,'badge' => $badge,'subscriber' => $subscriber]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.lb-subscriber-ranking.create');

        return view('admin.lb-subscriber-ranking.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLbSubscriberRanking $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreLbSubscriberRanking $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the LbSubscriberRanking
        $lbSubscriberRanking = LbSubscriberRanking::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/lb-subscriber-rankings'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/lb-subscriber-rankings');
    }

    /**
     * Display the specified resource.
     *
     * @param LbSubscriberRanking $lbSubscriberRanking
     * @throws AuthorizationException
     * @return void
     */
    public function show(LbSubscriberRanking $lbSubscriberRanking)
    {
        $this->authorize('admin.lb-subscriber-ranking.show', $lbSubscriberRanking);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LbSubscriberRanking $lbSubscriberRanking
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(LbSubscriberRanking $lbSubscriberRanking)
    {
        $this->authorize('admin.lb-subscriber-ranking.edit', $lbSubscriberRanking);


        return view('admin.lb-subscriber-ranking.edit', [
            'lbSubscriberRanking' => $lbSubscriberRanking,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLbSubscriberRanking $request
     * @param LbSubscriberRanking $lbSubscriberRanking
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateLbSubscriberRanking $request, LbSubscriberRanking $lbSubscriberRanking)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values LbSubscriberRanking
        $lbSubscriberRanking->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/lb-subscriber-rankings'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/lb-subscriber-rankings');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyLbSubscriberRanking $request
     * @param LbSubscriberRanking $lbSubscriberRanking
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyLbSubscriberRanking $request, LbSubscriberRanking $lbSubscriberRanking)
    {
        $lbSubscriberRanking->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyLbSubscriberRanking $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyLbSubscriberRanking $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('lbSubscriberRankings')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    /**
     * Export entities
     *
     * @return BinaryFileResponse|null
     */
    public function export(Request $request): ?BinaryFileResponse
    {
        return Excel::download(new LbSubscriberRankingsExport($request), 'lbSubscriberRankings.csv');
    }
}
