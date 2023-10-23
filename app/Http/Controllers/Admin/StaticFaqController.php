<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaticFaq\BulkDestroyStaticFaq;
use App\Http\Requests\Admin\StaticFaq\DestroyStaticFaq;
use App\Http\Requests\Admin\StaticFaq\IndexStaticFaq;
use App\Http\Requests\Admin\StaticFaq\StoreStaticFaq;
use App\Http\Requests\Admin\StaticFaq\UpdateStaticFaq;
use App\Models\StaticFaq;
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

class StaticFaqController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexStaticFaq $request
     * @return array|Factory|View
     */
    public function index(IndexStaticFaq $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/static-faq-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/static-faq-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/static-faq-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(StaticFaq::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['active', 'description', 'id', 'order_index', 'question','created_at'],

            // set columns to searchIn
            ['description', 'id', 'question']
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/static-faq-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/static-faq-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/static-faq-search')];
        }

        return view('admin.static-faq.index', ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/static-faq-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.static-faq.create');

        return view('admin.static-faq.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStaticFaq $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreStaticFaq $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the StaticFaq
        $staticFaq = StaticFaq::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/static-faqs'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/static-faqs');
    }

    /**
     * Display the specified resource.
     *
     * @param StaticFaq $staticFaq
     * @throws AuthorizationException
     * @return void
     */
    public function show(StaticFaq $staticFaq)
    {
        $this->authorize('admin.static-faq.show', $staticFaq);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param StaticFaq $staticFaq
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(StaticFaq $staticFaq)
    {
        $this->authorize('admin.static-faq.edit', $staticFaq);


        return view('admin.static-faq.edit', [
            'staticFaq' => $staticFaq,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStaticFaq $request
     * @param StaticFaq $staticFaq
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateStaticFaq $request, StaticFaq $staticFaq)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values StaticFaq
        $staticFaq->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/static-faqs'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/static-faqs');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyStaticFaq $request
     * @param StaticFaq $staticFaq
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyStaticFaq $request, StaticFaq $staticFaq)
    {
        $staticFaq->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyStaticFaq $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyStaticFaq $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('staticFaqs')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
