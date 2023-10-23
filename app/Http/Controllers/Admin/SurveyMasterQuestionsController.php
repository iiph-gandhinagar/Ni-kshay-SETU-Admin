<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SurveyMasterQuestionsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SurveyMasterQuestion\BulkDestroySurveyMasterQuestion;
use App\Http\Requests\Admin\SurveyMasterQuestion\DestroySurveyMasterQuestion;
use App\Http\Requests\Admin\SurveyMasterQuestion\IndexSurveyMasterQuestion;
use App\Http\Requests\Admin\SurveyMasterQuestion\StoreSurveyMasterQuestion;
use App\Http\Requests\Admin\SurveyMasterQuestion\UpdateSurveyMasterQuestion;
use App\Models\SurveyMaster;
use App\Models\SurveyMasterQuestion;
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

class SurveyMasterQuestionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexSurveyMasterQuestion $request
     * @return array|Factory|View
     */
    public function index(IndexSurveyMasterQuestion $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(SurveyMasterQuestion::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['active', 'id', 'option1', 'option2', 'option3', 'option4', 'order_index', 'question', 'survey_master_id', 'type','created_at'],

            // set columns to searchIn
            ['id', 'option1', 'option2', 'option3', 'option4', 'question', 'type'],
            function ($query) use($request){
                $query->with(['survey_master']);
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

        return view('admin.survey-master-question.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.survey-master-question.create');
        $survey_master = SurveyMaster::get(['id','title']);
        return view('admin.survey-master-question.create',[
            'survey_master' => $survey_master
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSurveyMasterQuestion $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreSurveyMasterQuestion $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['survey_master_id'] = $request['survey_master_id']['id'];
        // Store the SurveyMasterQuestion
        $surveyMasterQuestion = SurveyMasterQuestion::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/survey-master-questions'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/survey-master-questions');
    }

    /**
     * Display the specified resource.
     *
     * @param SurveyMasterQuestion $surveyMasterQuestion
     * @throws AuthorizationException
     * @return void
     */
    public function show(SurveyMasterQuestion $surveyMasterQuestion)
    {
        $this->authorize('admin.survey-master-question.show', $surveyMasterQuestion);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param SurveyMasterQuestion $surveyMasterQuestion
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(SurveyMasterQuestion $surveyMasterQuestion)
    {
        $this->authorize('admin.survey-master-question.edit', $surveyMasterQuestion);
        $survey_master = SurveyMaster::get(['id','title']);
        $surveyMasterQuestion['survey_master_id'] = SurveyMaster::where('id',$surveyMasterQuestion->survey_master_id)->get(['id','title']);
        return view('admin.survey-master-question.edit', [
            'surveyMasterQuestion' => $surveyMasterQuestion,
            'survey_master' => $survey_master
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSurveyMasterQuestion $request
     * @param SurveyMasterQuestion $surveyMasterQuestion
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateSurveyMasterQuestion $request, SurveyMasterQuestion $surveyMasterQuestion)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if(isset($sanitized['survey_master_id']) && isset($sanitized['survey_master_id'][0]) && $sanitized['survey_master_id'][0] != ''){
            $sanitized['survey_master_id'] = $sanitized['survey_master_id'][0]['id'];
        }elseif(isset($sanitized['survey_master_id']) && $sanitized['survey_master_id'] != '' && $sanitized['survey_master_id']['id']){
            $sanitized['survey_master_id'] = $sanitized['survey_master_id']['id'];
        }
        // Update changed values SurveyMasterQuestion
        $surveyMasterQuestion->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/survey-master-questions'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/survey-master-questions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroySurveyMasterQuestion $request
     * @param SurveyMasterQuestion $surveyMasterQuestion
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroySurveyMasterQuestion $request, SurveyMasterQuestion $surveyMasterQuestion)
    {
        $surveyMasterQuestion->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroySurveyMasterQuestion $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroySurveyMasterQuestion $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('surveyMasterQuestions')->whereIn('id', $bulkChunk)
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
        return Excel::download(app(SurveyMasterQuestionsExport::class), 'surveyMasterQuestions.xlsx');
    }
}
