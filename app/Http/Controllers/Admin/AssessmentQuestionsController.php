<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssessmentQuestion\BulkDestroyAssessmentQuestion;
use App\Http\Requests\Admin\AssessmentQuestion\DestroyAssessmentQuestion;
use App\Http\Requests\Admin\AssessmentQuestion\IndexAssessmentQuestion;
use App\Http\Requests\Admin\AssessmentQuestion\StoreAssessmentQuestion;
use App\Http\Requests\Admin\AssessmentQuestion\UpdateAssessmentQuestion;
use App\Models\AssessmentQuestion;
use App\Models\Assessment;
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
use Log;

class AssessmentQuestionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexAssessmentQuestion $request
     * @return array|Factory|View
     */
    public function index(IndexAssessmentQuestion $request)
    {
        // $masterData = \StateWiseFilterData::getStateWiseMasterData();
        // $assessment = $masterData['assessment'];
        $assessment = Assessment::get(['id', 'assessment_title']);
        $category = AssessmentQuestion::distinct()->get(['category']);
        if (!$request->ajax() && $request->session()->has('assessment')) {
            $request['assessment'] = session('assessment');
        }

        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }

        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/assessment-question-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/assessment-question-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/assessment-question-search');
        }

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(AssessmentQuestion::class)->modifyQuery(function ($query) use ($request, $assessment) {
            if (\Auth::user()->roles[0]['id'] == 10) {
                $query->where('created_by', \Auth::user()->id);
            }
            if (isset($request['assessment']) && $request['assessment'] > 0) {
                $query->where('assessment_id', $request['assessment']);
            } else {
                $query->whereIn('assessment_id', $assessment->pluck('id'));
            }
            if (isset($request['category'])) {
                $query->where('category', 'LIKE', $request['category']);
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'assessment_id', 'question', 'option1', 'option2', 'option3', 'option4', 'correct_answer', 'order_index', 'category', 'created_at'],

            // set columns to searchIn
            ['id', 'question', 'option1', 'option2', 'option3', 'option4', 'correct_answer', 'assessments.assessment_title', 'category'],
            function ($query) use ($request) {
                $query->with(['assessment_with_trashed', 'assessment_with_trashed.user']);

                // add this line if you want to search by author attributes
                $query->join('assessments', 'assessments.id', '=', 'assessment_questions.assessment_id');
            }
        );
        // $this->setTranslate();
        if ($request->ajax()) {
            if ($request['assessment'] || $request['assessment'] == 0) {
                session(['assessment' => $request['assessment']]);
            }

            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }

            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/assessment-question-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/assessment-question-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'session' => session('assessment'), 'search' => session(\Str::slug($request->getPathInfo()) . '/assessment-question-search')];
        }

        return view('admin.assessment-question.index', ['data' => $data, 'assessment' => $assessment, 'session' => session('assessment'), 'search' => session(\Str::slug($request->getPathInfo()) . '/assessment-question-search'), 'category' => $category]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.assessment-question.create');

        if (isset(\Auth::user()->state) && \Auth::user()->state != '') {
            $assessment = Assessment::where('created_by', \Auth::user()->id)->get(['id', 'assessment_title']);
        } else {
            $assessment = Assessment::get(['id', 'assessment_title']);
        }

        return view('admin.assessment-question.create', [
            'assessment' => $assessment,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAssessmentQuestion $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreAssessmentQuestion $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the AssessmentQuestion
        $assessmentQuestion = AssessmentQuestion::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/assessment-questions'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/assessment-questions');
    }

    /**
     * Display the specified resource.
     *
     * @param AssessmentQuestion $assessmentQuestion
     * @throws AuthorizationException
     * @return void
     */
    public function show(AssessmentQuestion $assessmentQuestion)
    {
        $this->authorize('admin.assessment-question.show', $assessmentQuestion);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AssessmentQuestion $assessmentQuestion
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(AssessmentQuestion $assessmentQuestion)
    {
        if (\Auth::user()->roles[0]['id'] == 3 || \Auth::user()->roles[0]['id'] == 10) {
            $assessmentQuestion->assessment->load('user');
            if ($assessmentQuestion->assessment->load('user')->user->roles[0]->id != 3 && $assessmentQuestion->assessment->load('user')->user->roles[0]->id != 10) {
                abort(403);
            } else {
                $this->authorize('admin.assessment-question.edit', $assessmentQuestion);
            }
        } else {
            $this->authorize('admin.assessment-question.edit', $assessmentQuestion);
        }

        if (isset(\Auth::user()->state) && \Auth::user()->state != '') {
            $assessment = Assessment::where('created_by', \Auth::user()->id)->get(['id', 'assessment_title']);
        } else {
            $assessment = Assessment::get(['id', 'assessment_title']);
        }

        return view('admin.assessment-question.edit', [
            'assessment' => $assessment,
            'assessmentQuestion' => $assessmentQuestion,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAssessmentQuestion $request
     * @param AssessmentQuestion $assessmentQuestion
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateAssessmentQuestion $request, AssessmentQuestion $assessmentQuestion)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values AssessmentQuestion
        $assessmentQuestion->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/assessment-questions'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/assessment-questions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyAssessmentQuestion $request
     * @param AssessmentQuestion $assessmentQuestion
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyAssessmentQuestion $request, AssessmentQuestion $assessmentQuestion)
    {
        $assessmentQuestion->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyAssessmentQuestion $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyAssessmentQuestion $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('assessmentQuestions')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    public function setTranslate()
    {
        $assessmentQuestion = AssessmentQuestion::get();

        foreach ($assessmentQuestion as $key => $assessmentQuestions) {
            $assessmentQuestions = AssessmentQuestion::find($assessmentQuestions->id);
            $assessmentQuestions->setTranslation('question_value_json', 'en', $assessmentQuestion[$key]->question)->save();
            $assessmentQuestions->setTranslation('option1_value_json', 'en', $assessmentQuestion[$key]->option1)->save();
            $assessmentQuestions->setTranslation('option2_value_json', 'en', $assessmentQuestion[$key]->option2)->save();
            $assessmentQuestions->setTranslation('option3_value_json', 'en', $assessmentQuestion[$key]->option3)->save();
            $assessmentQuestions->setTranslation('option4_value_json', 'en', $assessmentQuestion[$key]->option4)->save();
        }
    }
}
