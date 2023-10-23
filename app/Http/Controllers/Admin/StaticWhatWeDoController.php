<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaticWhatWeDo\BulkDestroyStaticWhatWeDo;
use App\Http\Requests\Admin\StaticWhatWeDo\DestroyStaticWhatWeDo;
use App\Http\Requests\Admin\StaticWhatWeDo\IndexStaticWhatWeDo;
use App\Http\Requests\Admin\StaticWhatWeDo\StoreStaticWhatWeDo;
use App\Http\Requests\Admin\StaticWhatWeDo\UpdateStaticWhatWeDo;
use App\Models\StaticWhatWeDo;
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

class StaticWhatWeDoController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexStaticWhatWeDo $request
     * @return array|Factory|View
     */
    public function index(IndexStaticWhatWeDo $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/static-what-we-do-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/static-what-we-do-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/static-what-we-do-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(StaticWhatWeDo::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['active', 'id', 'location', 'order_index', 'title','created_at'],

            // set columns to searchIn
            ['id', 'location', 'title']
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/static-what-we-do-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/static-what-we-do-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/static-what-we-do-search')];
        }

        return view('admin.static-what-we-do.index', ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/static-what-we-do-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.static-what-we-do.create');

        return view('admin.static-what-we-do.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStaticWhatWeDo $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreStaticWhatWeDo $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the StaticWhatWeDo
        $staticWhatWeDo = StaticWhatWeDo::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/static-what-we-dos'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/static-what-we-dos');
    }

    /**
     * Display the specified resource.
     *
     * @param StaticWhatWeDo $staticWhatWeDo
     * @throws AuthorizationException
     * @return void
     */
    public function show(StaticWhatWeDo $staticWhatWeDo)
    {
        $this->authorize('admin.static-what-we-do.show', $staticWhatWeDo);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param StaticWhatWeDo $staticWhatWeDo
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(StaticWhatWeDo $staticWhatWeDo)
    {
        $this->authorize('admin.static-what-we-do.edit', $staticWhatWeDo);


        return view('admin.static-what-we-do.edit', [
            'staticWhatWeDo' => $staticWhatWeDo,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStaticWhatWeDo $request
     * @param StaticWhatWeDo $staticWhatWeDo
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateStaticWhatWeDo $request, StaticWhatWeDo $staticWhatWeDo)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values StaticWhatWeDo
        $staticWhatWeDo->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/static-what-we-dos'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/static-what-we-dos');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyStaticWhatWeDo $request
     * @param StaticWhatWeDo $staticWhatWeDo
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyStaticWhatWeDo $request, StaticWhatWeDo $staticWhatWeDo)
    {
        $staticWhatWeDo->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyStaticWhatWeDo $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyStaticWhatWeDo $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('staticWhatWeDos')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
