<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ChatKeywordHitsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChatKeywordHit\BulkDestroyChatKeywordHit;
use App\Http\Requests\Admin\ChatKeywordHit\DestroyChatKeywordHit;
use App\Http\Requests\Admin\ChatKeywordHit\IndexChatKeywordHit;
use App\Http\Requests\Admin\ChatKeywordHit\StoreChatKeywordHit;
use App\Http\Requests\Admin\ChatKeywordHit\UpdateChatKeywordHit;
use App\Models\ChatKeywordHit;
use App\Models\Subscriber;
use App\Models\Cadre;
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


class ChatKeywordHitsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexChatKeywordHit $request
     * @return array|Factory|View
     */
    public function index(IndexChatKeywordHit $request)
    {
        // $cadre = Cadre::get(['id', 'title']);
        // $country = Country::get(['id','title']);

        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithHealthFacility();
        $subscriber = $masterData['subscriber'];
        $state = $masterData['state'];
        $block = $masterData['block'];
        $district = $masterData['district'];
        $cadre = $masterData['cadres'];
        $country = $masterData['country'];
        $health_facility = $masterData['health_facility'];

        if (\Auth::user()->roles[0]['id'] == 10) {
            $cadre = Cadre::whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get(['id', 'title']);
            $subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get();
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(ChatKeywordHit::class)->modifyQuery(function ($query) use ($request) {
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
                $assignedCadre = \Auth::user()->cadre;
            } elseif (\Auth::user()->role_type == 'state_type') {
                $assignedState = \Auth::user()->state;
                $assignedCadre = \Auth::user()->cadre;
            } else {
                $assignedDistrict = \Auth::user()->district;
                $assignedCadre = \Auth::user()->cadre;
            }
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
                $query->whereHas('user.cadre', function ($q) {
                    $q->whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17]);
                });
            }
            if ($request->has('subscriber_id')) {
                $query->where('subscriber_id', $request->subscriber_id);
            }
            if ($request->has('cadre_id')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('cadre_id', $request->cadre_id);
                });
            }
            if ($request->has('country_id')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('country_id', $request->country_id);
                });
            }
            if ($request->has('state_id')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('state_id', $request->state_id);
                });
            }
            if ($request->has('district_id')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('district_id', $request->district_id);
                });
            }
            if ($request->has('block_id')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('block_id', $request->block_id);
                });
            }
            if ($request->has('health_facility_id')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('health_facility_id', $request->health_facility_id);
                });
            }
            if ($request->orderDirection == "") {
                $query->orderBy('created_at', 'desc');
                $request->orderDirection = "asc";
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'keyword_id', 'subscriber_id', 'created_at'],

            // set columns to searchIn
            ['id', 'chat_keywords.title', 'subscribers.name', 'cadre.title', 'state.title', 'districts.title', 'blocks.title', 'health_facilities.health_facility_code', 'country.title'], //
            function ($q) use ($request) {
                $q->with(['user', 'keyword', 'user.cadre', 'user.state', 'user.district', 'user.block', 'user.health_facility', 'user.country']);

                $q->join('chat_keywords', 'chat_keywords.id', '=', 'chat_keyword_hits.keyword_id');
                $q->join('subscribers', 'subscribers.id', '=', 'chat_keyword_hits.subscriber_id');
                $q->join('cadre', 'cadre.id', '=', 'subscribers.cadre_id');
                $q->leftJoin('state', 'state.id', '=', 'subscribers.state_id');
                $q->leftjoin('districts', 'districts.id', '=', 'subscribers.district_id');
                $q->leftjoin('country', 'country.id', '=', 'subscribers.country_id');
                $q->leftjoin('blocks', 'blocks.id', '=', 'subscribers.block_id');
                $q->leftJoin('health_facilities', 'health_facilities.id', '=', 'subscribers.health_facility_id');
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

        return view('admin.chat-keyword-hit.index', [
            'data' => $data,
            'subscriber' => $subscriber,
            'cadre' => $cadre,
            'state' => $state,
            'district' => $district,
            'block' => $block,
            'health_facility' => $health_facility,
            'country' => $country
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
        $this->authorize('admin.chat-keyword-hit.create');

        return view('admin.chat-keyword-hit.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreChatKeywordHit $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreChatKeywordHit $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the ChatKeywordHit
        $chatKeywordHit = ChatKeywordHit::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/chat-keyword-hits'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/chat-keyword-hits');
    }

    /**
     * Display the specified resource.
     *
     * @param ChatKeywordHit $chatKeywordHit
     * @throws AuthorizationException
     * @return void
     */
    public function show(ChatKeywordHit $chatKeywordHit)
    {
        $this->authorize('admin.chat-keyword-hit.show', $chatKeywordHit);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ChatKeywordHit $chatKeywordHit
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(ChatKeywordHit $chatKeywordHit)
    {
        $this->authorize('admin.chat-keyword-hit.edit', $chatKeywordHit);


        return view('admin.chat-keyword-hit.edit', [
            'chatKeywordHit' => $chatKeywordHit,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateChatKeywordHit $request
     * @param ChatKeywordHit $chatKeywordHit
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateChatKeywordHit $request, ChatKeywordHit $chatKeywordHit)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values ChatKeywordHit
        $chatKeywordHit->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/chat-keyword-hits'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/chat-keyword-hits');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyChatKeywordHit $request
     * @param ChatKeywordHit $chatKeywordHit
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyChatKeywordHit $request, ChatKeywordHit $chatKeywordHit)
    {
        $chatKeywordHit->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyChatKeywordHit $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyChatKeywordHit $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    ChatKeywordHit::whereIn('id', $bulkChunk)->delete();

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
        $this->authorize('admin.chat-keyword-hit.export');
        return Excel::download(new ChatKeywordHitsExport($request), 'chatKeywordHits.xlsx');
    }
}
