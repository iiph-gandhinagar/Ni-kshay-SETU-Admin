<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssessmentEnrollment\BulkDestroyAssessmentEnrollment;
use App\Http\Requests\Admin\AssessmentEnrollment\DestroyAssessmentEnrollment;
use App\Http\Requests\Admin\AssessmentEnrollment\IndexAssessmentEnrollment;
use App\Http\Requests\Admin\AssessmentEnrollment\StoreAssessmentEnrollment;
use App\Http\Requests\Admin\AssessmentEnrollment\UpdateAssessmentEnrollment;
use App\Models\AssessmentEnrollment;
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

class AssessmentEnrollmentsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAssessmentEnrollment $request
     * @return array|Factory|View
     */
    public function index(IndexAssessmentEnrollment $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AssessmentEnrollment::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'assessment_id', 'user_id', 'response', 'send_inital_invitation'],

            // set columns to searchIn
            ['id', 'response']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.assessment-enrollment.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.assessment-enrollment.create');

        return view('admin.assessment-enrollment.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAssessmentEnrollment $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAssessmentEnrollment $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the AssessmentEnrollment
        $assessmentEnrollment = AssessmentEnrollment::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/assessment-enrollments'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/assessment-enrollments');
    }

    /**
     * Display the specified resource.
     *
     * @param AssessmentEnrollment $assessmentEnrollment
     * @throws AuthorizationException
     * @return void
     */
    public function show(AssessmentEnrollment $assessmentEnrollment)
    {
        $this->authorize('admin.assessment-enrollment.show', $assessmentEnrollment);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AssessmentEnrollment $assessmentEnrollment
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AssessmentEnrollment $assessmentEnrollment)
    {
        $this->authorize('admin.assessment-enrollment.edit', $assessmentEnrollment);


        return view('admin.assessment-enrollment.edit', [
            'assessmentEnrollment' => $assessmentEnrollment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAssessmentEnrollment $request
     * @param AssessmentEnrollment $assessmentEnrollment
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAssessmentEnrollment $request, AssessmentEnrollment $assessmentEnrollment)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AssessmentEnrollment
        $assessmentEnrollment->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/assessment-enrollments'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/assessment-enrollments');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAssessmentEnrollment $request
     * @param AssessmentEnrollment $assessmentEnrollment
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAssessmentEnrollment $request, AssessmentEnrollment $assessmentEnrollment)
    {
        $assessmentEnrollment->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAssessmentEnrollment $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAssessmentEnrollment $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('assessmentEnrollments')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
