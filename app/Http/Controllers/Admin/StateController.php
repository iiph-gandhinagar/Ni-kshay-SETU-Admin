<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\State\BulkDestroyState;
use App\Http\Requests\Admin\State\DestroyState;
use App\Http\Requests\Admin\State\IndexState;
use App\Http\Requests\Admin\State\StoreState;
use App\Http\Requests\Admin\State\UpdateState;
use App\Models\Country;
use App\Models\State;
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

class StateController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexState $request
     * @return array|Factory|View
     */
    public function index(IndexState $request)
    {
        if(!$request->ajax() && session(\Str::slug($request->getPathInfo()))){
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/state-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/state-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/state-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(State::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title','country_id','created_at'],

            // set columns to searchIn
            ['id', 'title'],
            function($query) use($request){
                $query->with(['country']);
            }
        );

        if ($request->ajax()) {
            if($request['page'] && $request['page'] > 0){ 
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/state-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/state-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/state-search')];
        }

        return view('admin.state.index', ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/state-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.state.create');
        $country = Country::get(['id','title']);

        return view('admin.state.create',['country' => $country]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreState $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreState $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        if (isset($request['country_id']) && $request['country_id'] != '') {

            $sanitized['country_id'] = $request['country_id']['id'];
        }
        // Store the State
        $state = State::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/states'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/states');
    }

    /**
     * Display the specified resource.
     *
     * @param State $state
     * @throws AuthorizationException
     * @return void
     */
    public function show(State $state)
    {
        $this->authorize('admin.state.show', $state);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param State $state
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(State $state)
    {
        $this->authorize('admin.state.edit', $state);
        $country = Country::get(['id','title']);
        if (isset($state['country_id']) && $state['country_id'] != "") {
            $state['country_id'] = $state['country_id'];
            $state['country_id'] = Country::where('id', $state['country_id'])->get(['id', 'title']);
        }

        return view('admin.state.edit', [
            'state' => $state,
            'country' => $country,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateState $request
     * @param State $state
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateState $request, State $state)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($request['country_id'])) {
            if ($request['country_id'] == NULL) {
                $sanitized['country_id'] = 0;
            }else{
                $sanitized['country_id'] = $request['country_id'][0]['id'];
            }
            
        }

        // Update changed values State
        $state->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/states'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/states');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyState $request
     * @param State $state
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyState $request, State $state)
    {
        $state->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyState $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyState $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('states')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
