<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssessmentCertificate\BulkDestroyAssessmentCertificate;
use App\Http\Requests\Admin\AssessmentCertificate\DestroyAssessmentCertificate;
use App\Http\Requests\Admin\AssessmentCertificate\IndexAssessmentCertificate;
use App\Http\Requests\Admin\AssessmentCertificate\StoreAssessmentCertificate;
use App\Http\Requests\Admin\AssessmentCertificate\UpdateAssessmentCertificate;
use App\Models\AssessmentCertificate;
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

class AssessmentCertificatesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAssessmentCertificate $request
     * @return array|Factory|View
     */
    public function index(IndexAssessmentCertificate $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AssessmentCertificate::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title', 'top', 'left','created_by'],

            // set columns to searchIn
            ['id', 'title'],
            function ($query) use ($request) {
                $query->with(['user']);
            }
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.assessment-certificate.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.assessment-certificate.create');

        return view('admin.assessment-certificate.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAssessmentCertificate $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAssessmentCertificate $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['created_by'] = \Auth::user()->id;
        // Store the AssessmentCertificate
        $assessmentCertificate = AssessmentCertificate::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/assessment-certificates'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/assessment-certificates');
    }

    /**
     * Display the specified resource.
     *
     * @param AssessmentCertificate $assessmentCertificate
     * @throws AuthorizationException
     * @return void
     */
    public function show(AssessmentCertificate $assessmentCertificate)
    {
        $this->authorize('admin.assessment-certificate.show', $assessmentCertificate);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AssessmentCertificate $assessmentCertificate
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AssessmentCertificate $assessmentCertificate)
    {
        $this->authorize('admin.assessment-certificate.edit', $assessmentCertificate);


        return view('admin.assessment-certificate.edit', [
            'assessmentCertificate' => $assessmentCertificate,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAssessmentCertificate $request
     * @param AssessmentCertificate $assessmentCertificate
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAssessmentCertificate $request, AssessmentCertificate $assessmentCertificate)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AssessmentCertificate
        $assessmentCertificate->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/assessment-certificates'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/assessment-certificates');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAssessmentCertificate $request
     * @param AssessmentCertificate $assessmentCertificate
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAssessmentCertificate $request, AssessmentCertificate $assessmentCertificate)
    {
        $assessmentCertificate->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAssessmentCertificate $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAssessmentCertificate $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('assessmentCertificates')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
