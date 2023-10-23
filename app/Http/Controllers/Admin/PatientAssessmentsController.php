<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PatientAssessmentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PatientAssessment\BulkDestroyPatientAssessment;
use App\Http\Requests\Admin\PatientAssessment\DestroyPatientAssessment;
use App\Http\Requests\Admin\PatientAssessment\IndexPatientAssessment;
use App\Http\Requests\Admin\PatientAssessment\StorePatientAssessment;
use App\Http\Requests\Admin\PatientAssessment\UpdatePatientAssessment;
use App\Models\PatientAssessment;
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

class PatientAssessmentsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexPatientAssessment $request
     * @return array|Factory|View
     */
    public function index(IndexPatientAssessment $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(PatientAssessment::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'nikshay_id', 'patient_name', 'age', 'gender', 'patient_selected_data', 'PULSE_RATE', 'TEMPERATURE', 'BLOOD_PRESSURE', 'RESPIRATORY_RATE', 'OXYGEN_SATURATION', 'TEXT_BMI', 'TEXT_MUAC', 'PEDAL_OEDEMA', 'GENERAL_CONDITION', 'TEXT_ICTERUS', 'TEXT_HEMOGLOBIN', 'COUNT_WBC', 'TEXT_RBS', 'TEXT_HIV', 'TEXT_XRAY', 'TEXT_HEMOPTYSIS'],

            // set columns to searchIn
            ['id', 'nikshay_id', 'patient_name', 'age', 'gender', 'patient_selected_data', 'BLOOD_PRESSURE', 'PEDAL_OEDEMA', 'GENERAL_CONDITION', 'TEXT_ICTERUS', 'TEXT_HIV', 'TEXT_XRAY', 'TEXT_HEMOPTYSIS']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.patient-assessment.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.patient-assessment.create');

        return view('admin.patient-assessment.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePatientAssessment $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StorePatientAssessment $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the PatientAssessment
        $patientAssessment = PatientAssessment::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/patient-assessments'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/patient-assessments');
    }

    /**
     * Display the specified resource.
     *
     * @param PatientAssessment $patientAssessment
     * @throws AuthorizationException
     * @return void
     */
    public function show(PatientAssessment $patientAssessment)
    {
        $this->authorize('admin.patient-assessment.show', $patientAssessment);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param PatientAssessment $patientAssessment
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(PatientAssessment $patientAssessment)
    {
        $this->authorize('admin.patient-assessment.edit', $patientAssessment);


        return view('admin.patient-assessment.edit', [
            'patientAssessment' => $patientAssessment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePatientAssessment $request
     * @param PatientAssessment $patientAssessment
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdatePatientAssessment $request, PatientAssessment $patientAssessment)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values PatientAssessment
        $patientAssessment->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/patient-assessments'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/patient-assessments');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyPatientAssessment $request
     * @param PatientAssessment $patientAssessment
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyPatientAssessment $request, PatientAssessment $patientAssessment)
    {
        $patientAssessment->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyPatientAssessment $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyPatientAssessment $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('patientAssessments')->whereIn('id', $bulkChunk)
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
        $this->authorize('admin.patient-assessment.export');
        return Excel::download(app(PatientAssessmentsExport::class), 'patientAssessments.xlsx');
    }
}
