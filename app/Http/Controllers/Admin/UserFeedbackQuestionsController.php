<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserFeedbackQuestion\BulkDestroyUserFeedbackQuestion;
use App\Http\Requests\Admin\UserFeedbackQuestion\DestroyUserFeedbackQuestion;
use App\Http\Requests\Admin\UserFeedbackQuestion\IndexUserFeedbackQuestion;
use App\Http\Requests\Admin\UserFeedbackQuestion\StoreUserFeedbackQuestion;
use App\Http\Requests\Admin\UserFeedbackQuestion\UpdateUserFeedbackQuestion;
use App\Models\UserFeedbackQuestion;
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

class UserFeedbackQuestionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexUserFeedbackQuestion $request
     * @return array|Factory|View
     */
    public function index(IndexUserFeedbackQuestion $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/user-feedback-question-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/user-feedback-question-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/user-feedback-question-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(UserFeedbackQuestion::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'feedback_question', 'feedback_description', 'feedback_value', 'feedback_time', 'feedback_type', 'feedback_days', 'is_active', 'created_at'],

            // set columns to searchIn
            ['id', 'feedback_question', 'feedback_description', 'feedback_value', 'feedback_time', 'feedback_type']
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/user-feedback-question-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/user-feedback-question-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/user-feedback-question-search')];
        }

        return view('admin.user-feedback-question.index', ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/user-feedback-question-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.user-feedback-question.create');

        return view('admin.user-feedback-question.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserFeedbackQuestion $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreUserFeedbackQuestion $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['feedback_value'] = $request->feedback_type == "repeat" ? 0 : $request->feedback_value;

        // Store the UserFeedbackQuestion
        $userFeedbackQuestion = UserFeedbackQuestion::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/user-feedback-questions'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/user-feedback-questions');
    }

    /**
     * Display the specified resource.
     *
     * @param UserFeedbackQuestion $userFeedbackQuestion
     * @throws AuthorizationException
     * @return void
     */
    public function show(UserFeedbackQuestion $userFeedbackQuestion)
    {
        $this->authorize('admin.user-feedback-question.show', $userFeedbackQuestion);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param UserFeedbackQuestion $userFeedbackQuestion
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(UserFeedbackQuestion $userFeedbackQuestion)
    {
        $this->authorize('admin.user-feedback-question.edit', $userFeedbackQuestion);


        return view('admin.user-feedback-question.edit', [
            'userFeedbackQuestion' => $userFeedbackQuestion,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserFeedbackQuestion $request
     * @param UserFeedbackQuestion $userFeedbackQuestion
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateUserFeedbackQuestion $request, UserFeedbackQuestion $userFeedbackQuestion)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['feedback_value'] = $request->feedback_type == "repeat" ? 0 : $request->feedback_value;

        // Update changed values UserFeedbackQuestion
        $userFeedbackQuestion->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/user-feedback-questions'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/user-feedback-questions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyUserFeedbackQuestion $request
     * @param UserFeedbackQuestion $userFeedbackQuestion
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyUserFeedbackQuestion $request, UserFeedbackQuestion $userFeedbackQuestion)
    {
        $userFeedbackQuestion->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyUserFeedbackQuestion $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyUserFeedbackQuestion $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('userFeedbackQuestions')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
