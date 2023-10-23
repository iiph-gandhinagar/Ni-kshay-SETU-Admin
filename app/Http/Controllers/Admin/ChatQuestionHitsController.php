<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ChatQuestionHitsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChatQuestionHit\BulkDestroyChatQuestionHit;
use App\Http\Requests\Admin\ChatQuestionHit\DestroyChatQuestionHit;
use App\Http\Requests\Admin\ChatQuestionHit\IndexChatQuestionHit;
use App\Http\Requests\Admin\ChatQuestionHit\StoreChatQuestionHit;
use App\Http\Requests\Admin\ChatQuestionHit\UpdateChatQuestionHit;
use App\Models\ChatQuestionHit;
use App\Models\ChatQuestion;
use App\Models\Subscriber;
use Brackets\AdminListing\Facades\AdminListing;
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
use Illuminate\Http\Request;

class ChatQuestionHitsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexChatQuestionHit $request
     * @return array|Factory|View
     */
    public function index(IndexChatQuestionHit $request)
    {
        $assignedState = \Auth::user()->state;
        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithSubscriber();
        $subscriber = $masterData['subscriber'];
        if (\Auth::user()->roles[0]['id'] == 10) {
            $subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get();
        }
        $category = ChatQuestion::distinct('category')->get(['category']);

        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ChatQuestionHit::class)->modifyQuery(function ($query) use ($request) {
            $assignedRole = \Auth::user()->roles[0]['id'];
            $assignedDistrict = '';
            $assignedCountry = '';
            $assignedState = '';
            $assignedCadre = '';
            if (\Auth::user()->role_type == 'country_type' && (\Auth::user()->roles[0]['id'] == 1 || \Auth::user()->roles[0]['id'] == 2)) {
                // $assignedCountry = \Auth::user()->country;
                // $assignedState = \Auth::user()->state;
                // $assignedCadre = \Auth::user()->cadre;
                // $assignedDistrict = \Auth::user()->district;
            } elseif (\Auth::user()->role_type == 'country_type') {
                $assignedCountry = \Auth::user()->country;
            } elseif (\Auth::user()->role_type == 'state_type') {
                $assignedState = \Auth::user()->state;
            } else {
                $assignedDistrict = \Auth::user()->district;
            }
            $assignedCadre = \Auth::user()->cadre;
            $assignedRole = \Auth::user()->roles[0]['id'];
            if ($assignedCountry != '' && $assignedCountry > 0) {
                $query->whereHas('user', function ($q) use ($assignedCountry) {
                    $q->whereIn('country_id', explode(',', $assignedCountry));
                });
            }
            if ($assignedState != '' && $assignedState > 0) {
                $query->whereHas('user.state', function ($q) use ($assignedState) {
                    $q->whereIn('id', explode(',', $assignedState));
                });
            }
            if ($assignedDistrict != '' && $assignedDistrict > 0) {
                $query->whereHas('user.district', function ($q) use ($assignedDistrict) {
                    $q->whereIn('id', explode(',', $assignedDistrict));
                });
            }
            if ($assignedCadre != '' && $assignedCadre > 0) {
                $query->whereHas('user.cadre', function ($q) use ($assignedCadre) {
                    $q->whereIn('id', explode(',', $assignedCadre));
                });
            }

            if ($assignedRole != '' && $assignedRole == 10) {
                $query->whereHas('user.cadre', function ($q) use ($assignedState) {
                    $q->whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17]);
                });
            }

            if ($request->has('subscriber_id')) {
                $query->where('subscriber_id', $request->subscriber_id);
            }

            if ($request->has('category')) {
                $query->whereHas('questions', function ($q) use ($request) {
                    $q->where('category', $request->category);
                });
            }
            if ($request->orderDirection == "" && $request->orderDirection == 'asc') {
                $query->orderBy('created_at', 'desc');
                $request->orderDirection = "asc";
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'question_id', 'subscriber_id', 'created_at'],

            // set columns to searchIn
            ['id', 'chat_questions.question', 'subscribers.name', 'chat_questions.category'], //

            function ($q) use ($request) {
                $q->with(['user', 'questions_with_trashed']);

                $q->join('chat_questions', 'chat_questions.id', '=', 'chat_question_hits.question_id');
                $q->join('subscribers', 'subscribers.id', '=', 'chat_question_hits.subscriber_id');
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

        return view('admin.chat-question-hit.index', ['data' => $data, 'subscriber' => $subscriber, 'category' => $category]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.chat-question-hit.create');

        return view('admin.chat-question-hit.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreChatQuestionHit $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreChatQuestionHit $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the ChatQuestionHit
        $chatQuestionHit = ChatQuestionHit::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/chat-question-hits'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/chat-question-hits');
    }

    /**
     * Display the specified resource.
     *
     * @param ChatQuestionHit $chatQuestionHit
     * @throws AuthorizationException
     * @return void
     */
    public function show(ChatQuestionHit $chatQuestionHit)
    {
        $this->authorize('admin.chat-question-hit.show', $chatQuestionHit);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ChatQuestionHit $chatQuestionHit
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(ChatQuestionHit $chatQuestionHit)
    {
        $this->authorize('admin.chat-question-hit.edit', $chatQuestionHit);


        return view('admin.chat-question-hit.edit', [
            'chatQuestionHit' => $chatQuestionHit,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateChatQuestionHit $request
     * @param ChatQuestionHit $chatQuestionHit
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateChatQuestionHit $request, ChatQuestionHit $chatQuestionHit)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values ChatQuestionHit
        $chatQuestionHit->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/chat-question-hits'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/chat-question-hits');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyChatQuestionHit $request
     * @param ChatQuestionHit $chatQuestionHit
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyChatQuestionHit $request, ChatQuestionHit $chatQuestionHit)
    {
        $chatQuestionHit->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyChatQuestionHit $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyChatQuestionHit $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    ChatQuestionHit::whereIn('id', $bulkChunk)->delete();

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
        $this->authorize('admin.chat-question-hit.export');
        return Excel::download(new ChatQuestionHitsExport($request), 'chatQuestionHits.xlsx');
    }
}
