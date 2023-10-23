<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SurveyMasterHistoriesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SurveyMasterHistory\BulkDestroySurveyMasterHistory;
use App\Http\Requests\Admin\SurveyMasterHistory\DestroySurveyMasterHistory;
use App\Http\Requests\Admin\SurveyMasterHistory\IndexSurveyMasterHistory;
use App\Http\Requests\Admin\SurveyMasterHistory\StoreSurveyMasterHistory;
use App\Http\Requests\Admin\SurveyMasterHistory\UpdateSurveyMasterHistory;
use App\Models\Subscriber;
use App\Models\SurveyMaster;
use App\Models\SurveyMasterHistory;
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

class SurveyMasterHistoriesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexSurveyMasterHistory $request
     * @return array|Factory|View
     */
    public function index(IndexSurveyMasterHistory $request)
    {
        $survey = SurveyMaster::get(['id','title']);
        $subscriber =Subscriber::get(['id','name']);
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(SurveyMasterHistory::class)->modifyQuery(function ($query) use ($request) {
            if ($request->has('survey_id')) {
                $query->where('survey_master_histories.survey_id', $request->survey_id);
            }
            if ($request->has('subscriber_id')) {
                $query->where('survey_master_histories.user_id', $request->subscriber_id);
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['answer', 'id', 'survey_id', 'survey_question_id', 'user_id','created_at'],

            // set columns to searchIn
            ['answer', 'id'],
            function ($query) use($request){
                $query->with(['user','survey_master','survey_master_question']);
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

        return view('admin.survey-master-history.index', ['data' => $data,'survey' => $survey,'subscriber' => $subscriber]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.survey-master-history.create');

        return view('admin.survey-master-history.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSurveyMasterHistory $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreSurveyMasterHistory $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the SurveyMasterHistory
        $surveyMasterHistory = SurveyMasterHistory::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/survey-master-histories'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/survey-master-histories');
    }

    /**
     * Display the specified resource.
     *
     * @param SurveyMasterHistory $surveyMasterHistory
     * @throws AuthorizationException
     * @return void
     */
    public function show(SurveyMasterHistory $surveyMasterHistory)
    {
        $this->authorize('admin.survey-master-history.show', $surveyMasterHistory);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param SurveyMasterHistory $surveyMasterHistory
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(SurveyMasterHistory $surveyMasterHistory)
    {
        $this->authorize('admin.survey-master-history.edit', $surveyMasterHistory);


        return view('admin.survey-master-history.edit', [
            'surveyMasterHistory' => $surveyMasterHistory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateSurveyMasterHistory $request
     * @param SurveyMasterHistory $surveyMasterHistory
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateSurveyMasterHistory $request, SurveyMasterHistory $surveyMasterHistory)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values SurveyMasterHistory
        $surveyMasterHistory->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/survey-master-histories'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/survey-master-histories');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroySurveyMasterHistory $request
     * @param SurveyMasterHistory $surveyMasterHistory
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroySurveyMasterHistory $request, SurveyMasterHistory $surveyMasterHistory)
    {
        $surveyMasterHistory->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroySurveyMasterHistory $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroySurveyMasterHistory $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('surveyMasterHistories')->whereIn('id', $bulkChunk)
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
        return Excel::download(new SurveyMasterHistoriesExport($request), 'surveyMasterHistories.xlsx');
    }
}
