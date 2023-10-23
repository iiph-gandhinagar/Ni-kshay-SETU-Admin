<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\District\BulkDestroyDistrict;
use App\Http\Requests\Admin\District\DestroyDistrict;
use App\Http\Requests\Admin\District\IndexDistrict;
use App\Http\Requests\Admin\District\StoreDistrict;
use App\Http\Requests\Admin\District\UpdateDistrict;
use App\Models\Country;
use App\Models\District;
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

class DistrictsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexDistrict $request
     * @return array|Factory|View
     */
    public function index(IndexDistrict $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/district-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/district-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/district-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(District::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'state_id', 'title', 'country_id', 'created_at'],

            // set columns to searchIn
            ['id', 'state.title', 'title', 'country.title'],

            function ($query) use ($request) {
                $assignedState = \Auth::user()->state;
                $assignedDistrict = \Auth::user()->district;

                if ($assignedState != '' && $assignedState > 0) {
                    $query->whereIn('districts.state_id', explode(',', $assignedState));
                }
                if ($assignedDistrict != '' && $assignedDistrict > 0) {
                    $query->whereIn('districts.id', explode(',', $assignedDistrict));
                }

                $query->with(['state', 'country']);

                // add this line if you want to search by author attributes
                $query->join('state', 'state.id', '=', 'districts.state_id');
                $query->leftJoin('country', 'country.id', '=', 'districts.country_id');
            }
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/district-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/district-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/district-search')];
        }

        return view('admin.district.index', ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/district-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.district.create');
        $masterData = \StateWiseFilterData::getStateWiseMasterData();
        $state = $masterData['state'];
        $country = Country::get(['id', 'title']);

        return view('admin.district.create', [
            'state' => $state,
            'country' => $country,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDistrict $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreDistrict $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($request['country_id']) && $request['country_id'] != '') {

            $sanitized['country_id'] = $request['country_id']['id'];
        }
        // Store the District
        $district = District::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/districts'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/districts');
    }

    /**
     * Display the specified resource.
     *
     * @param District $district
     * @throws AuthorizationException
     * @return void
     */
    public function show(District $district)
    {
        $this->authorize('admin.district.show', $district);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param District $district
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(District $district)
    {
        $this->authorize('admin.district.edit', $district);
        $masterData = \StateWiseFilterData::getStateWiseMasterData();
        $state = $masterData['state'];
        $country = Country::get(['id', 'title']);
        if (isset($district['country_id']) && $district['country_id'] != "") {
            $district['country_id'] = $district['country_id'];
            $district['country_id'] = Country::where('id', $district['country_id'])->get(['id', 'title']);
        }


        return view('admin.district.edit', [
            'district' => $district,
            'state' => $state,
            'country' => $country,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDistrict $request
     * @param District $district
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateDistrict $request, District $district)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($request['country_id'])) {
            if ($request['country_id'] == NULL) {
                $sanitized['country_id'] = 0;
            } else {
                $sanitized['country_id'] = $request['country_id'][0]['id'];
            }
        }
        // Update changed values District
        $district->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/districts'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/districts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyDistrict $request
     * @param District $district
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyDistrict $request, District $district)
    {
        $district->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyDistrict $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyDistrict $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('districts')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
