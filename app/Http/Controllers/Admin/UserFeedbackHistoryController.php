<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserFeedbackHistoryExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserFeedbackHistory\BulkDestroyUserFeedbackHistory;
use App\Http\Requests\Admin\UserFeedbackHistory\DestroyUserFeedbackHistory;
use App\Http\Requests\Admin\UserFeedbackHistory\IndexUserFeedbackHistory;
use App\Http\Requests\Admin\UserFeedbackHistory\StoreUserFeedbackHistory;
use App\Http\Requests\Admin\UserFeedbackHistory\UpdateUserFeedbackHistory;
use App\Models\Subscriber;
use App\Models\UserFeedbackHistory;
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
use Log;

class UserFeedbackHistoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexUserFeedbackHistory $request
     * @return array|Factory|View
     */
    public function index(IndexUserFeedbackHistory $request)
    {
        $subscriber = Subscriber::get(['id', 'name']);
        $ratings = UserFeedbackHistory::orderby('ratings', 'desc')->distinct()->get(['ratings']);
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(UserFeedbackHistory::class)->modifyQuery(function ($query) use ($request) {
            if ($request->has('subscriber_id')) {
                $query->where('user_feedback_history.subscriber_id', $request->subscriber_id);
            }
            if ($request->has('rating_id')) {
                $query->where('user_feedback_history.ratings', $request->rating_id);
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['feedback_id', 'id', 'ratings', 'skip', 'subscriber_id', 'created_at'],

            // set columns to searchIn
            ['id', 'review'],
            function ($query) {
                $query->with(['user', 'feedback_question']);
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

        return view('admin.user-feedback-history.index', ['data' => $data, 'subscriber' => $subscriber, 'rating' => $ratings]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.user-feedback-history.create');

        return view('admin.user-feedback-history.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserFeedbackHistory $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreUserFeedbackHistory $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the UserFeedbackHistory
        $userFeedbackHistory = UserFeedbackHistory::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/user-feedback-histories'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/user-feedback-histories');
    }

    /**
     * Display the specified resource.
     *
     * @param UserFeedbackHistory $userFeedbackHistory
     * @throws AuthorizationException
     * @return void
     */
    public function show(UserFeedbackHistory $userFeedbackHistory)
    {
        $this->authorize('admin.user-feedback-history.show', $userFeedbackHistory);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param UserFeedbackHistory $userFeedbackHistory
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(UserFeedbackHistory $userFeedbackHistory)
    {
        $this->authorize('admin.user-feedback-history.edit', $userFeedbackHistory);


        return view('admin.user-feedback-history.edit', [
            'userFeedbackHistory' => $userFeedbackHistory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserFeedbackHistory $request
     * @param UserFeedbackHistory $userFeedbackHistory
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateUserFeedbackHistory $request, UserFeedbackHistory $userFeedbackHistory)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values UserFeedbackHistory
        $userFeedbackHistory->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/user-feedback-histories'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/user-feedback-histories');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyUserFeedbackHistory $request
     * @param UserFeedbackHistory $userFeedbackHistory
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyUserFeedbackHistory $request, UserFeedbackHistory $userFeedbackHistory)
    {
        $userFeedbackHistory->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyUserFeedbackHistory $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyUserFeedbackHistory $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('userFeedbackHistories')->whereIn('id', $bulkChunk)
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
        return Excel::download(new UserFeedbackHistoryExport($request), 'userFeedbackHistories.csv');
    }
}
