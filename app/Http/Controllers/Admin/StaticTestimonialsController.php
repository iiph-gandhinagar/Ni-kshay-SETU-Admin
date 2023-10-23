<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaticTestimonial\BulkDestroyStaticTestimonial;
use App\Http\Requests\Admin\StaticTestimonial\DestroyStaticTestimonial;
use App\Http\Requests\Admin\StaticTestimonial\IndexStaticTestimonial;
use App\Http\Requests\Admin\StaticTestimonial\StoreStaticTestimonial;
use App\Http\Requests\Admin\StaticTestimonial\UpdateStaticTestimonial;
use App\Models\StaticTestimonial;
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

class StaticTestimonialsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexStaticTestimonial $request
     * @return array|Factory|View
     */
    public function index(IndexStaticTestimonial $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/static-testimonial-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/static-testimonial-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/static-testimonial-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(StaticTestimonial::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['active', 'description', 'id', 'name', 'order_index','created_at'],

            // set columns to searchIn
            ['description', 'id', 'name']
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/static-testimonial-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/static-testimonial-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/static-testimonial-search')];
        }

        return view('admin.static-testimonial.index', ['data' => $data,'search' => session(\Str::slug($request->getPathInfo()) . '/static-testimonial-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.static-testimonial.create');

        return view('admin.static-testimonial.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreStaticTestimonial $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreStaticTestimonial $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the StaticTestimonial
        $staticTestimonial = StaticTestimonial::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/static-testimonials'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/static-testimonials');
    }

    /**
     * Display the specified resource.
     *
     * @param StaticTestimonial $staticTestimonial
     * @throws AuthorizationException
     * @return void
     */
    public function show(StaticTestimonial $staticTestimonial)
    {
        $this->authorize('admin.static-testimonial.show', $staticTestimonial);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param StaticTestimonial $staticTestimonial
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(StaticTestimonial $staticTestimonial)
    {
        $this->authorize('admin.static-testimonial.edit', $staticTestimonial);


        return view('admin.static-testimonial.edit', [
            'staticTestimonial' => $staticTestimonial,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStaticTestimonial $request
     * @param StaticTestimonial $staticTestimonial
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateStaticTestimonial $request, StaticTestimonial $staticTestimonial)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values StaticTestimonial
        $staticTestimonial->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/static-testimonials'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/static-testimonials');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyStaticTestimonial $request
     * @param StaticTestimonial $staticTestimonial
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyStaticTestimonial $request, StaticTestimonial $staticTestimonial)
    {
        $staticTestimonial->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyStaticTestimonial $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyStaticTestimonial $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('staticTestimonials')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
