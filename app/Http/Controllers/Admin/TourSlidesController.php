<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TourSlide\BulkDestroyTourSlide;
use App\Http\Requests\Admin\TourSlide\DestroyTourSlide;
use App\Http\Requests\Admin\TourSlide\IndexTourSlide;
use App\Http\Requests\Admin\TourSlide\StoreTourSlide;
use App\Http\Requests\Admin\TourSlide\UpdateTourSlide;
use App\Models\Tour;
use App\Models\TourSlide;
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

class TourSlidesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTourSlide $request
     * @return array|Factory|View
     */
    public function index(IndexTourSlide $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(TourSlide::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'tour_id', 'title', 'description', 'type', 'created_at'],

            // set columns to searchIn
            ['id', 'title', 'description', 'type'],
            function ($query) use ($request) {
                $query->with(['tour']);
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

        return view('admin.tour-slide.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.tour-slide.create');
        $tour = Tour::get(['id', 'title']);
        return view('admin.tour-slide.create', [
            'tour' => $tour,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTourSlide $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTourSlide $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        $sanitized['tour_id'] = $request['tour_id']['id'];
        // Store the TourSlide
        $tourSlide = TourSlide::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/tour-slides'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/tour-slides');
    }

    /**
     * Display the specified resource.
     *
     * @param TourSlide $tourSlide
     * @throws AuthorizationException
     * @return void
     */
    public function show(TourSlide $tourSlide)
    {
        $this->authorize('admin.tour-slide.show', $tourSlide);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TourSlide $tourSlide
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(TourSlide $tourSlide)
    {
        $this->authorize('admin.tour-slide.edit', $tourSlide);
        $tour = Tour::get(['id', 'title']);
        $tourSlide['tour_id'] = Tour::where('id', $tourSlide->id)->get(['id', 'title']);

        return view('admin.tour-slide.edit', [
            'tourSlide' => $tourSlide,
            'tour' => $tour,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTourSlide $request
     * @param TourSlide $tourSlide
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTourSlide $request, TourSlide $tourSlide)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($sanitized['tour_id']) && isset($sanitized['tour_id'][0]) && $sanitized['tour_id'][0] != '') {
            $sanitized['tour_id'] = $sanitized['tour_id'][0]['id'];
        } elseif (isset($sanitized['tour_id']) && $sanitized['tour_id'] != '' && $sanitized['tour_id']['id']) {
            $sanitized['tour_id'] = $sanitized['tour_id']['id'];
        }
        // Update changed values TourSlide
        $tourSlide->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/tour-slides'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/tour-slides');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTourSlide $request
     * @param TourSlide $tourSlide
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTourSlide $request, TourSlide $tourSlide)
    {
        $tourSlide->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTourSlide $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTourSlide $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('tourSlides')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
