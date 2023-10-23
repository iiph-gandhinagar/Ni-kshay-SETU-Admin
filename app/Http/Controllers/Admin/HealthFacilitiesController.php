<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HealthFacility\BulkDestroyHealthFacility;
use App\Http\Requests\Admin\HealthFacility\DestroyHealthFacility;
use App\Http\Requests\Admin\HealthFacility\IndexHealthFacility;
use App\Http\Requests\Admin\HealthFacility\StoreHealthFacility;
use App\Http\Requests\Admin\HealthFacility\UpdateHealthFacility;
use App\Models\HealthFacility;
use App\Models\State;
use App\Models\District;
use App\Models\Block;
use App\Models\Country;
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

class HealthFacilitiesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexHealthFacility $request
     * @return array|Factory|View
     */
    public function index(IndexHealthFacility $request)
    {
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/health-facility-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/health-facility-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/health-facility-search');
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(HealthFacility::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'state_id', 'district_id', 'block_id', 'health_facility_code', 'DMC', 'TRUNAT', 'CBNAAT', 'X_RAY', 'ICTC', 'LPA_Lab', 'CONFIRMATION_CENTER', 'Tobacco_Cessation_clinic', 'ANC_Clinic', 'Nutritional_Rehabilitation_centre', 'De_addiction_centres', 'ART_Centre', 'District_DRTB_Centre', 'NODAL_DRTB_CENTER', 'IRL', 'Pediatric_Care_Facility', 'longitude', 'latitude', 'country_id', 'created_at'],

            // set columns to searchIn
            ['id', 'health_facility_code', 'state.title', 'districts.title', 'blocks.title'],

            function ($query) use ($request) {
                $query->with(['state', 'district', 'block', 'country']);

                $assignedDistrict = '';
                $assignedCountry = '';
                $assignedState = '';
                if (\Auth::user()->role_type == 'country_type' && (\Auth::user()->roles[0]['id'] == 1 || \Auth::user()->roles[0]['id'] == 2)) {
                    // $assignedCountry = \Auth::user()->country;
                    // $assignedState = \Auth::user()->state;
                    // $assignedCadre = \Auth::user()->cadre;
                    // $assignedDistrict = \Auth::user()->district;
                } elseif (\Auth::user()->role_type == 'country_type') {
                    $assignedCountry = \Auth::user()->country;
                } elseif (\Auth::user()->role_type == 'state_type') {
                    $assignedState = \Auth::user()->state;
                } else {
                    $assignedDistrict = \Auth::user()->district;
                }
                if ($assignedCountry != '' && $assignedCountry > 0) {
                    $query->whereIn('health_facilities.country_id', explode(',', $assignedCountry));
                }
                if ($assignedState != '' && $assignedState > 0) {
                    $query->whereIn('health_facilities.state_id', explode(',', $assignedState));
                }
                if ($assignedDistrict != '' && $assignedDistrict > 0) {
                    $query->whereIn('health_facilities.district_id', explode(',', $assignedDistrict));
                }

                // add this line if you want to search by author attributes
                $query->join('state', 'state.id', '=', 'health_facilities.state_id');
                $query->join('districts', 'districts.id', '=', 'health_facilities.district_id');
                $query->join('blocks', 'blocks.id', '=', 'health_facilities.block_id');
                $query->join('country', 'country.id', '=', 'health_facilities.country_id');
            }
        );

        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/health-facility-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/health-facility-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/health-facility-search')];
        }

        return view('admin.health-facility.index', ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/health-facility-search')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.health-facility.create');
        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithHealthFacility();
        $state = $masterData['state'];
        $district = $masterData['district'];
        $taluka = $masterData['block'];
        $health_facility = $masterData['health_facility'];
        $country = Country::get(['id', 'title']);

        return view('admin.health-facility.create', [
            'state' => $state,
            'district' => $district,
            'taluka' => $taluka,
            'country' => $country,
            'health_facility' => $health_facility,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreHealthFacility $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreHealthFacility $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();
        if (isset($request['country_id']) && $request['country_id'] != '') {

            $sanitized['country_id'] = $request['country_id']['id'];
        }
        $sanitized['state_id'] = $request['state_id']['id'];
        $sanitized['district_id'] = $request['district_id']['id'];
        $sanitized['block_id'] = $request['block_id']['id'];
        // Store the HealthFacility
        $healthFacility = HealthFacility::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/health-facilities'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/health-facilities');
    }

    /**
     * Display the specified resource.
     *
     * @param HealthFacility $healthFacility
     * @throws AuthorizationException
     * @return void
     */
    public function show(HealthFacility $healthFacility)
    {
        $this->authorize('admin.health-facility.show', $healthFacility);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param HealthFacility $healthFacility
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(HealthFacility $healthFacility)
    {
        $this->authorize('admin.health-facility.edit', $healthFacility);
        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithHealthFacility();
        $state = $masterData['state'];
        $district = $masterData['district'];
        $taluka = $masterData['block'];
        $health_facility = $masterData['health_facility'];
        $country = Country::get(['id', 'title']);

        if (isset($healthFacility['country_id']) && $healthFacility['country_id'] != "") {
            $healthFacility['country_id'] = $healthFacility['country_id'];
            $healthFacility['country_id'] = Country::where('id', $healthFacility['country_id'])->get(['id', 'title']);
        }

        $healthFacility['state_id'] = State::where('id', $healthFacility->state_id)->get(['id', 'title'])[0];
        $healthFacility['district_id'] = District::where('id', $healthFacility->district_id)->get(['id', 'title', 'state_id'])[0];
        $healthFacility['block_id'] = Block::where('id', $healthFacility->block_id)->get(['id', 'title', 'state_id', 'district_id'])[0];


        return view('admin.health-facility.edit', [
            'healthFacility' => $healthFacility,
            'state' => $state,
            'district' => $district,
            'taluka' => $taluka,
            'country' => $country,
            'health_facility' => $health_facility,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateHealthFacility $request
     * @param HealthFacility $healthFacility
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateHealthFacility $request, HealthFacility $healthFacility)
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
        if (isset($sanitized['state_id']) && $sanitized['state_id'] != "" && count($sanitized['state_id']) > 0) {
            $sanitized['state_id'] = $sanitized['state_id']['id'];
        } else {
            $sanitized['state_id'] = $sanitized['state_id'][0]['id'];
        }

        if (isset($sanitized['district_id']) && $sanitized['district_id'] != "" && count($sanitized['district_id']) > 0) {
            $sanitized['district_id'] = $sanitized['district_id']['id'];
        } else {
            $sanitized['district_id'] = $sanitized['district_id'][0]['id'];
        }

        if (isset($sanitized['block_id']) && $sanitized['block_id'] != ""  && count($sanitized['block_id']) > 0) {
            $sanitized['block_id'] = $sanitized['block_id']['id'];
        } else {
            $sanitized['block_id'] = $sanitized['block_id'][0]['id'];
        }
        // Update changed values HealthFacility
        $healthFacility->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/health-facilities'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/health-facilities');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyHealthFacility $request
     * @param HealthFacility $healthFacility
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyHealthFacility $request, HealthFacility $healthFacility)
    {
        $healthFacility->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyHealthFacility $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyHealthFacility $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('healthFacilities')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
