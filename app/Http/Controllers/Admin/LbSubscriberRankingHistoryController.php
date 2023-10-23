<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LbSubscriberRankingHistoryExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LbSubscriberRankingHistory\BulkDestroyLbSubscriberRankingHistory;
use App\Http\Requests\Admin\LbSubscriberRankingHistory\DestroyLbSubscriberRankingHistory;
use App\Http\Requests\Admin\LbSubscriberRankingHistory\IndexLbSubscriberRankingHistory;
use App\Http\Requests\Admin\LbSubscriberRankingHistory\StoreLbSubscriberRankingHistory;
use App\Http\Requests\Admin\LbSubscriberRankingHistory\UpdateLbSubscriberRankingHistory;
use App\Models\LbBadge;
use App\Models\LbLevel;
use App\Models\LbSubscriberRankingHistory;
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

class LbSubscriberRankingHistoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexLbSubscriberRankingHistory $request
     * @return array|Factory|View
     */
    public function index(IndexLbSubscriberRankingHistory $request)
    {
        $level = LbLevel::get(['id','level']);
        $badge = LbBadge::get(['id','badge','level_id']);
        $subscriber = Subscriber::get(['id','name']);
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(LbSubscriberRankingHistory::class)->modifyQuery(function ($query) use ($request) {
            if ($request->has('level_id')) {
                $query->where('lb_subscriber_ranking_history.level_id', $request->level_id);
            }
            if ($request->has('badge_id')) {
                $query->where('lb_subscriber_ranking_history.badge_id', $request->badge_id);
            }
            if ($request->has('subscriber_id')) {
                $query->where('lb_subscriber_ranking_history.subscriber_id', $request->subscriber_id);
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['App_opended_count', 'badge_id', 'chatbot_usage_count', 'id', 'lb_subscriber_rankings_id', 'level_id', 'mins_spent_count', 'resource_material_accessed_count', 'sub_module_usage_count', 'subscriber_id','created_at'],

            // set columns to searchIn
            ['id', 'mins_spent_count', 'sub_module_usage_count','lb_levels.level','lb_badges.badge','subscribers.name'],
            function ($query) use($request){
                $query->with(['lb_level','lb_badge','lb_task_list','user']);

                $query->leftJoin('subscribers', 'subscribers.id', '=', 'lb_subscriber_ranking_history.subscriber_id');
                $query->leftJoin('lb_levels', 'lb_levels.id', '=', 'lb_subscriber_ranking_history.level_id');
                $query->leftJoin('lb_badges', 'lb_badges.id', '=', 'lb_subscriber_ranking_history.badge_id');
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

        return view('admin.lb-subscriber-ranking-history.index', ['data' => $data,'level' => $level,'badge' => $badge,'subscriber' => $subscriber]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.lb-subscriber-ranking-history.create');

        return view('admin.lb-subscriber-ranking-history.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLbSubscriberRankingHistory $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreLbSubscriberRankingHistory $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the LbSubscriberRankingHistory
        $lbSubscriberRankingHistory = LbSubscriberRankingHistory::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/lb-subscriber-ranking-histories'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/lb-subscriber-ranking-histories');
    }

    /**
     * Display the specified resource.
     *
     * @param LbSubscriberRankingHistory $lbSubscriberRankingHistory
     * @throws AuthorizationException
     * @return void
     */
    public function show(LbSubscriberRankingHistory $lbSubscriberRankingHistory)
    {
        $this->authorize('admin.lb-subscriber-ranking-history.show', $lbSubscriberRankingHistory);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LbSubscriberRankingHistory $lbSubscriberRankingHistory
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(LbSubscriberRankingHistory $lbSubscriberRankingHistory)
    {
        $this->authorize('admin.lb-subscriber-ranking-history.edit', $lbSubscriberRankingHistory);


        return view('admin.lb-subscriber-ranking-history.edit', [
            'lbSubscriberRankingHistory' => $lbSubscriberRankingHistory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLbSubscriberRankingHistory $request
     * @param LbSubscriberRankingHistory $lbSubscriberRankingHistory
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateLbSubscriberRankingHistory $request, LbSubscriberRankingHistory $lbSubscriberRankingHistory)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values LbSubscriberRankingHistory
        $lbSubscriberRankingHistory->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/lb-subscriber-ranking-histories'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/lb-subscriber-ranking-histories');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyLbSubscriberRankingHistory $request
     * @param LbSubscriberRankingHistory $lbSubscriberRankingHistory
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyLbSubscriberRankingHistory $request, LbSubscriberRankingHistory $lbSubscriberRankingHistory)
    {
        $lbSubscriberRankingHistory->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyLbSubscriberRankingHistory $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyLbSubscriberRankingHistory $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('lbSubscriberRankingHistories')->whereIn('id', $bulkChunk)
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
        return Excel::download(new LbSubscriberRankingHistoryExport($request), 'lbSubscriberRankingHistories.csv');
    }
}
