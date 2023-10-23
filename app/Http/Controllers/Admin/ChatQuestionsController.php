<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ChatQuestionsExport;
use App\Exports\MarathiFaqExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChatQuestion\BulkDestroyChatQuestion;
use App\Http\Requests\Admin\ChatQuestion\DestroyChatQuestion;
use App\Http\Requests\Admin\ChatQuestion\IndexChatQuestion;
use App\Http\Requests\Admin\ChatQuestion\StoreChatQuestion;
use App\Http\Requests\Admin\ChatQuestion\UpdateChatQuestion;
use Illuminate\Http\Request;
use App\Models\ChatQuestion;
use App\Models\ChatQuestionKeyword;
use App\Models\ChatKeyword;
use App\Models\Cadre;
use App\Models\TTrainingTag;
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
use Validator;
use Log;

class ChatQuestionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexChatQuestion $request
     * @return array|Factory|View
     */
    public function index(IndexChatQuestion $request)
    {
        $category = ChatQuestion::distinct()->get(['category']);
        $keywords = ChatKeyword::distinct()->get(['id', 'title']);

        // if (!$request->ajax() && $request->session()->has('orderDirection')) {
        //     $request['orderDirection'] = session('orderDirection');
        //     $request['orderBy'] = session('orderBy');
        // }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/chat-question-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/chat-question-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/chat-question-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ChatQuestion::class)->modifyQuery(function ($query) use ($request) {
            // $query->where('activated',1);
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }
            if ($request->has('keyword')) {
                $query->whereHas('question_keywords.keywords', function ($q) use ($request) {
                    $q->where('id', $request->keyword);
                });
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'question', 'answer', 'hit', 'cadre_id', 'category', 'activated', 'like_count', 'dislike_count', 'created_at'],

            // set columns to searchIn
            ['id', 'question', 'cadre_id', 'category', 'chat_keywords.title'],
            function ($query) use ($request) {
                $query->with(['question_keywords.keywords']);

                $query->join('chat_question_keywords', 'chat_question_keywords.question_id', '=', 'chat_questions.id')->groupBy('question_id');
                $query->join('chat_keywords', 'chat_keywords.id', '=', 'chat_question_keywords.keyword_id');
            }
        );
        // $this->setTranslate();
        if ($request->ajax()) {
            // if ($request['orderDirection'] || $request['orderDirection'] == 0) {
            //     session(['orderDirection' => $request['orderDirection']]);
            //     session(['orderBy' => $request['orderBy']]);
            // }
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/chat-question-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/chat-question-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/chat-question-search')];
        }
        return view('admin.chat-question.index', [
            'data' => $data,
            'category' => $category,
            'keyword' => $keywords,
            'search' => session(\Str::slug($request->getPathInfo()) . '/chat-question-search')
        ]);
    }

    public function getQuestionVsTags(Request $request)
    {
        $data = AdminListing::create(ChatQuestion::class)->modifyQuery(
            function ($query) use ($request) {
                if ($request->orderBy == "tag_count") {
                    if ($request->orderDirection == 'desc') {
                        $query->orderBy('tag_count', 'desc');
                    } else if ($request->orderDirection == 'asc') {
                        $query->orderBy('tag_count', 'asc');
                    }
                    /** 
                     * It removes tag_count ordering column
                     * (✘) before unset => order by chat_questions.tag_count which is wrong (bcz tag_count is not in chat_questions table)
                     * (✔) after unset => order by tag_count
                     */
                    unset($request['orderBy']);
                    unset($request['orderDirection']);
                }
                $query->leftJoin("t_training_tag", function ($join) {
                    $join->where(DB::raw("FIND_IN_SET(chat_questions.id, t_training_tag.questions)"), ">", \DB::raw("0"));
                });
                $query->select('chat_questions.*', \DB::raw("COUNT(t_training_tag.id) as tag_count"))->groupBy('chat_questions.id');
            }
        )->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'question', 'answer', 'hit', 'cadre_id', 'category', 'activated', 'like_count', 'dislike_count', 'created_at'],

            // set columns to searchIn
            ['id', 'question', 'cadre_id', 'category'],
        );
        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }
        return view('admin.chat-question.chat-question-vs-tag', [
            'data' => $data,
        ]);
    }

    public function getTagsByQuestion(Request $request, $questionId)
    {
        $questionTitle = ChatQuestion::where('id', $questionId)->pluck('question')[0];
        $data = AdminListing::create(TTrainingTag::class)->modifyQuery(function ($query) use ($request, $questionId) {
            $query->whereRaw("FIND_IN_SET('" . $questionId . "',questions)");
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'tag', 'is_fix_response', 'like_count', 'dislike_count', 'questions', 'modules', 'sub_modules', 'resource_material', 'updated_at', 'created_at'],

            // set columns to searchIn
            ['id', 'tag', 'pattern', 'response', 'questions', 'modules', 'sub_modules', 'resource_material']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view(
            'admin.chat-question.chat-question-tag-list',
            ['data' => $data, 'questionId' => $questionId, 'question_title' => $questionTitle]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.chat-question.create');
        $cadres = Cadre::get(['id', 'title']);
        $keywords = ChatKeyword::get(['id', 'title']);

        return view('admin.chat-question.create', [
            'keywords' => $keywords,
            'cadre' => $cadres
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreChatQuestion $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreChatQuestion $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);

        // Store the ChatQuestion
        $chatQuestion = ChatQuestion::create($sanitized);
        $questionId = $chatQuestion['id'];
        $keywords = $sanitized['keyword_id'];

        //insert keywords
        foreach ($keywords as $value) {
            DB::table('chat_question_keywords')->insert(['keyword_id' => $value, 'question_id' => $questionId]);
        }

        if ($request->ajax()) {
            return ['redirect' => url('admin/chat-questions'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/chat-questions');
    }

    /**
     * Display the specified resource.
     *
     * @param ChatQuestion $chatQuestion
     * @throws AuthorizationException
     * @return void
     */
    public function show(ChatQuestion $chatQuestion)
    {
        $this->authorize('admin.chat-question.show', $chatQuestion);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ChatQuestion $chatQuestion
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(ChatQuestion $chatQuestion)
    {
        $this->authorize('admin.chat-question.edit', $chatQuestion);

        $chatQuestion['keyword_id'] = ChatQuestionKeyword::where('question_id', $chatQuestion['id'])->pluck('keyword_id');

        $keywords = ChatKeyword::get(['id', 'title']);
        $cadres = Cadre::get(['id', 'title']);
        $chatQuestion['cadre_id'] = explode(',', $chatQuestion['cadre_id']);
        $chatQuestion['all_cadres'] = $cadres;
        $chatQuestion['all_keywords'] = $keywords;

        return view('admin.chat-question.edit', [
            'chatQuestion' => $chatQuestion,
            'keywords' => $keywords,
            'cadre' => $cadres,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateChatQuestion $request
     * @param ChatQuestion $chatQuestion
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateChatQuestion $request, ChatQuestion $chatQuestion)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['cadre_id'] = implode(",", $sanitized['cadre_id']);
        // Update changed values ChatQuestion
        $chatQuestion->update($sanitized);
        $questionId = $chatQuestion['id'];
        $keywords = $sanitized['keyword_id'];

        //delete all existing keywords
        ChatQuestionKeyword::where(['question_id' => $questionId])->delete();
        //insert keywords
        foreach ($keywords as $value) {
            DB::table('chat_question_keywords')->insert(['keyword_id' => $value, 'question_id' => $questionId]);
        }

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/chat-questions'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/chat-questions');
    }

    public function addTag(ChatQuestion $chatQuestion)
    {
        $this->authorize('admin.chat-question.addTag', $chatQuestion);

        $rules = [
            'question' => 'max:50',
        ];
        // Validator::make($request->all(), $rules)->validate();
        $validator = Validator::make($chatQuestion->toArray(), $rules);
        if ($validator->fails()) {
            return response()->json($validator, 400);
        } else {
            $uniqueTag =  TTrainingTag::where('tag', 'LIKE', '%Question | ' . $chatQuestion->question . '%')->count();
            if ($uniqueTag > 0) {
                return response()->json('Tag Is Already there.', 409);
            } else {
                $question = $chatQuestion->getTranslations('question');
                $questionHi = isset($question['hi']) ?  $question['hi'] : NULL;
                $questionGu = isset($question['gu']) ?  $question['gu'] : NULL;
                $questionMr = isset($question['mr']) ?  $question['mr'] : NULL;
                $answer = $chatQuestion->getTranslations('answer');

                // $max_id = TTrainingTag::max('id');
                // $tagValues['id'] = $max_id  + 1;
                $tagValues['tag'] = "Question | " . $question['en'];
                $pattern = $question['en'];
                if ($questionHi != NULL) {
                    $pattern .= "|" . $questionHi;
                }
                if ($questionGu != NULL) {
                    $pattern .= "|" . $questionGu;
                }
                if ($questionMr != NULL) {
                    $pattern .= "|" . $questionMr;
                }
                $options = array(
                    'ignore_errors' => true,
                    // other options go here
                );
                $tagValues['pattern'] = $pattern;
                $tagValues['is_fix_response'] = 1;
                $tagValues['response'] = ["en" => isset($answer['en']) ?  \Soundasleep\Html2Text::convert($answer['en'], $options) : "", 'hi' => isset($answer['hi']) ?  \Soundasleep\Html2Text::convert($answer['hi'], $options) : "", 'gu' => isset($answer['gu']) ?  \Soundasleep\Html2Text::convert($answer['gu'], $options) : "", 'mr' => isset($answer['mr']) ?  \Soundasleep\Html2Text::convert($answer['mr'], $options) : "", 'ta' => isset($answer['ta']) ?  \Soundasleep\Html2Text::convert($answer['ta'], $options) : ""];
                $createtag = TTrainingTag::create($tagValues);
                return response()->json('Tag create Successfully', 200);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyChatQuestion $request
     * @param ChatQuestion $chatQuestion
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyChatQuestion $request, ChatQuestion $chatQuestion)
    {
        $chatQuestion->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyChatQuestion $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyChatQuestion $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('chatQuestions')->whereIn('id', $bulkChunk)
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
        $chatQuestion = ChatQuestion::get();

        foreach ($chatQuestion as $key => $chatQuestions) {
            $chatQuestions = ChatQuestion::find($chatQuestions->id);
            $chatQuestions->setTranslation('question_json', 'en', $chatQuestion[$key]->question)->save();
            $chatQuestions->setTranslation('answer_json', 'en', $chatQuestion[$key]->answer)->save();
        }
    }
    /**
     * Export entities
     *
     * @return BinaryFileResponse|null
     */
    public function export(): ?BinaryFileResponse
    {
        return Excel::download(app(ChatQuestionsExport::class), 'chatQuestions.xlsx');
    }

    public function exportMarathi(): ?BinaryFileResponse
    {
        return Excel::download(app(MarathiFaqExport::class), 'chatQuestions-marathi.xlsx');
    }
}
