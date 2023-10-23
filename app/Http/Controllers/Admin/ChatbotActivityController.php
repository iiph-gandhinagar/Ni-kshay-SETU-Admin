<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChatbotActivity\BulkDestroyChatbotActivity;
use App\Http\Requests\Admin\ChatbotActivity\DestroyChatbotActivity;
use App\Http\Requests\Admin\ChatbotActivity\IndexChatbotActivity;
use App\Http\Requests\Admin\ChatbotActivity\StoreChatbotActivity;
use App\Http\Requests\Admin\ChatbotActivity\UpdateChatbotActivity;
use App\Models\ChatbotActivity;
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

class ChatbotActivityController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexChatbotActivity $request
     * @return array|Factory|View
     */
    public function index(IndexChatbotActivity $request)
    {
        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithHealthFacility();
        $subscriber = $masterData['subscriber'];
        $action = ChatbotActivity::distinct()->get(['action']);

        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ChatbotActivity::class)->modifyQuery(function ($query) use ($request) {
            if ($request->has('subscriber_id')) {
                $query->where('user_id', $request->subscriber_id);
            }
            if ($request->has('action')) {
                $query->where('action', $request->action);
            }
            if ($request->has('response') && $request['response'] != "") {
                $query->where('response', $request->response);
            }
            if ($request->orderDirection == "") {
                $query->orderBy('created_at', 'desc');
                $request->orderDirection = "asc";
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'user_id', 'action', 'payload', 'plateform', 'ip_address', 'tag_id', 'question_id', 'like', 'dislike', 'created_at', 'response'],

            // set columns to searchIn
            ['id', 'action', 'payload', 'plateform', 'ip_address', 'cadre.title'],
            function ($q) use ($request) {
                $q->with(['user', 'user.cadre', 'tag']);
                $q->leftJoin('subscribers', 'subscribers.id', '=', 'chatbot_activity.user_id');
                $q->leftJoin('cadre', 'cadre.id', '=', 'subscribers.cadre_id');
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

        return view('admin.chatbot-activity.index', [
            'data' => $data,
            'subscriber' => $subscriber,
            'action' => $action,
            'response' => isset($request->response) ? $request->response : ""

        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.chatbot-activity.create');

        return view('admin.chatbot-activity.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreChatbotActivity $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreChatbotActivity $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the ChatbotActivity
        $chatbotActivity = ChatbotActivity::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/chatbot-activities'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/chatbot-activities');
    }

    /**
     * Display the specified resource.
     *
     * @param ChatbotActivity $chatbotActivity
     * @throws AuthorizationException
     * @return void
     */
    public function show(ChatbotActivity $chatbotActivity)
    {
        $this->authorize('admin.chatbot-activity.show', $chatbotActivity);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ChatbotActivity $chatbotActivity
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(ChatbotActivity $chatbotActivity)
    {
        $this->authorize('admin.chatbot-activity.edit', $chatbotActivity);


        return view('admin.chatbot-activity.edit', [
            'chatbotActivity' => $chatbotActivity,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateChatbotActivity $request
     * @param ChatbotActivity $chatbotActivity
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateChatbotActivity $request, ChatbotActivity $chatbotActivity)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values ChatbotActivity
        $chatbotActivity->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/chatbot-activities'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/chatbot-activities');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyChatbotActivity $request
     * @param ChatbotActivity $chatbotActivity
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyChatbotActivity $request, ChatbotActivity $chatbotActivity)
    {
        $chatbotActivity->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyChatbotActivity $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyChatbotActivity $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('chatbotActivities')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
