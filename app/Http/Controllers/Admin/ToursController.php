<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tour\BulkDestroyTour;
use App\Http\Requests\Admin\Tour\DestroyTour;
use App\Http\Requests\Admin\Tour\IndexTour;
use App\Http\Requests\Admin\Tour\StoreTour;
use App\Http\Requests\Admin\Tour\UpdateTour;
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
use Log;

class ToursController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTour $request
     * @return array|Factory|View
     */
    public function index(IndexTour $request)
    {
        $tour_slides = TourSlide::get(['id', 'title', 'tour_id']);
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Tour::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title', 'active', 'default', 'created_at'],

            // set columns to searchIn
            ['id', 'title']
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

        return view('admin.tour.index', ['data' => $data, 'tour_slides' => $tour_slides]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.tour.create');

        return view('admin.tour.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTour $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTour $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if ($request['default'] == true || $request['default'] == 1) {
            $tour_default_flag = Tour::where('default', 1)->get(['id']);
            if (count($tour_default_flag) > 0) {
                abort(400, "Default already set in another tour");
            } else {
                // Store the Tour
                $tour = Tour::create($sanitized);
            }
        } else {
            // Store the Tour
            $tour = Tour::create($sanitized);
        }


        if ($request->ajax()) {
            return ['redirect' => url('admin/tours'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/tours');
    }

    /**
     * Display the specified resource.
     *
     * @param Tour $tour
     * @throws AuthorizationException
     * @return void
     */
    public function show(Tour $tour)
    {
        $this->authorize('admin.tour.show', $tour);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Tour $tour
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Tour $tour)
    {
        $this->authorize('admin.tour.edit', $tour);


        return view('admin.tour.edit', [
            'tour' => $tour,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTour $request
     * @param Tour $tour
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTour $request, Tour $tour)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        if ($request['default'] == true || $request['default'] == 1) {
            $tour_default_flag = Tour::where('id', '!=', $tour->id)->where('default', 1)->get(['id']);
            if (count($tour_default_flag) > 0) {
                abort(400, "Default already set in another tour");
            } else {
                // Store the Tour
                $tour->update($sanitized);
            }
        } else {
            // Store the Tour
            $tour->update($sanitized);
        }

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/tours'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/tours');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTour $request
     * @param Tour $tour
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTour $request, Tour $tour)
    {
        $tour->delete();
        TourSlide::where('tour_id', $tour->id)->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTour $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTour $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('tours')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
