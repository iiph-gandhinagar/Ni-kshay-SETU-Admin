<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserFeedbackDetailsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserFeedbackDetail\BulkDestroyUserFeedbackDetail;
use App\Http\Requests\Admin\UserFeedbackDetail\DestroyUserFeedbackDetail;
use App\Http\Requests\Admin\UserFeedbackDetail\IndexUserFeedbackDetail;
use App\Http\Requests\Admin\UserFeedbackDetail\StoreUserFeedbackDetail;
use App\Http\Requests\Admin\UserFeedbackDetail\UpdateUserFeedbackDetail;
use App\Models\UserFeedbackDetail;
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
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;

class UserFeedbackDetailsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexUserFeedbackDetail $request
     * @return array|Factory|View
     */
    public function index(IndexUserFeedbackDetail $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(UserFeedbackDetail::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['feedback_id', 'id', 'ratings', 'subscriber_id', 'created_at'],

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

        return view('admin.user-feedback-detail.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.user-feedback-detail.create');

        return view('admin.user-feedback-detail.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserFeedbackDetail $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreUserFeedbackDetail $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the UserFeedbackDetail
        $userFeedbackDetail = UserFeedbackDetail::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/user-feedback-details'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/user-feedback-details');
    }

    /**
     * Display the specified resource.
     *
     * @param UserFeedbackDetail $userFeedbackDetail
     * @throws AuthorizationException
     * @return void
     */
    public function show(UserFeedbackDetail $userFeedbackDetail)
    {
        $this->authorize('admin.user-feedback-detail.show', $userFeedbackDetail);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param UserFeedbackDetail $userFeedbackDetail
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(UserFeedbackDetail $userFeedbackDetail)
    {
        $this->authorize('admin.user-feedback-detail.edit', $userFeedbackDetail);


        return view('admin.user-feedback-detail.edit', [
            'userFeedbackDetail' => $userFeedbackDetail,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserFeedbackDetail $request
     * @param UserFeedbackDetail $userFeedbackDetail
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateUserFeedbackDetail $request, UserFeedbackDetail $userFeedbackDetail)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values UserFeedbackDetail
        $userFeedbackDetail->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/user-feedback-details'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/user-feedback-details');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyUserFeedbackDetail $request
     * @param UserFeedbackDetail $userFeedbackDetail
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyUserFeedbackDetail $request, UserFeedbackDetail $userFeedbackDetail)
    {
        $userFeedbackDetail->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyUserFeedbackDetail $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyUserFeedbackDetail $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('userFeedbackDetails')->whereIn('id', $bulkChunk)
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
    public function export(): ?BinaryFileResponse
    {
        return Excel::download(app(UserFeedbackDetailsExport::class), 'userFeedbackDetails.csv');
    }
}
