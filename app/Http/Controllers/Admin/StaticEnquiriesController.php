<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaticEnquiry\BulkDestroyStaticEnquiry;
use App\Http\Requests\Admin\StaticEnquiry\DestroyStaticEnquiry;
use App\Http\Requests\Admin\StaticEnquiry\IndexStaticEnquiry;
use App\Http\Requests\Admin\StaticEnquiry\StoreStaticEnquiry;
use App\Http\Requests\Admin\StaticEnquiry\UpdateStaticEnquiry;
use App\Models\StaticEnquiry;
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

class StaticEnquiriesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexStaticEnquiry $request
     * @return array|Factory|View
     */
    public function index(IndexStaticEnquiry $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/static-enquiry-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/static-enquiry-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/static-enquiry-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(StaticEnquiry::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'subject', 'email', 'message','created_at'],

            // set columns to searchIn
            ['id', 'subject', 'email', 'message']
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/static-enquiry-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/static-enquiry-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/static-enquiry-search')];
        }

        return view('admin.static-enquiry.index', ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/static-enquiry-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.static-enquiry.create');

        return view('admin.static-enquiry.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStaticEnquiry $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreStaticEnquiry $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the StaticEnquiry
        $staticEnquiry = StaticEnquiry::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/static-enquiries'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/static-enquiries');
    }

    /**
     * Display the specified resource.
     *
     * @param StaticEnquiry $staticEnquiry
     * @throws AuthorizationException
     * @return void
     */
    public function show(StaticEnquiry $staticEnquiry)
    {
        $this->authorize('admin.static-enquiry.show', $staticEnquiry);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param StaticEnquiry $staticEnquiry
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(StaticEnquiry $staticEnquiry)
    {
        $this->authorize('admin.static-enquiry.edit', $staticEnquiry);


        return view('admin.static-enquiry.edit', [
            'staticEnquiry' => $staticEnquiry,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStaticEnquiry $request
     * @param StaticEnquiry $staticEnquiry
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateStaticEnquiry $request, StaticEnquiry $staticEnquiry)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values StaticEnquiry
        $staticEnquiry->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/static-enquiries'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/static-enquiries');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyStaticEnquiry $request
     * @param StaticEnquiry $staticEnquiry
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyStaticEnquiry $request, StaticEnquiry $staticEnquiry)
    {
        $staticEnquiry->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyStaticEnquiry $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyStaticEnquiry $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('staticEnquiries')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
