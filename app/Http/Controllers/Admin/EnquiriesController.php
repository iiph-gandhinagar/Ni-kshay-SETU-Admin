<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EnquiriesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Enquiry\IndexEnquiry;
use App\Models\Block;
use App\Models\Cadre;
use App\Models\Country;
use App\Models\District;
use App\Models\Enquiry;
use App\Models\HealthFacility;
use App\Models\State;
// use AWS\CRT\HTTP\Request;
use Brackets\AdminListing\Facades\AdminListing;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class EnquiriesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexEnquiry $request
     * @return array|Factory|View
     */
    public function index(IndexEnquiry $request)
    {
        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithHealthFacility();
        $state = $masterData['state'];
        $block = $masterData['block'];
        $district = $masterData['district'];
        $cadre = $masterData['cadres'];
        $country = $masterData['country'];
        $health_facility = $masterData['health_facility'];

        if (\Auth::user()->roles[0]['id'] == 10) {
            $cadre = Cadre::whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get(['id', 'title']);
        }
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Enquiry::class)->modifyQuery(function ($query) use ($request) {
            $assignedDistrict = '';
            $assignedCountry = '';
            $assignedState = '';
            $assignedCadre = '';
            if (\Auth::user()->role_type == 'country_type' && (\Auth::user()->roles[0]['id'] == 1 || \Auth::user()->roles[0]['id'] == 2)) {
                // $assignedCountry = \Auth::user()->country;
                // $assignedState = \Auth::user()->state;
                // $assignedCadre = \Auth::user()->cadre;
                // $assignedDistrict = \Auth::user()->district;
            } else if (\Auth::user()->role_type == 'country_type') {
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
                    $q->where('country_id', explode(',', $assignedCountry));
                });
            }
            if ($assignedState != '' && $assignedState > 0) {
                $query->whereHas('user.state', function ($q) use ($assignedState) {
                    $q->where('id', $assignedState);
                });
            } else if ($request->has('state')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->whereIn('state_id', explode(',', $request->state));
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
                $query->whereHas('user.cadre', function ($q) use ($assignedState) {
                    $q->whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17]);
                });
            }

            if ($request->has('date') && $request->date != "") {
                // $query->whereDate('enquiries.created_at', Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'));
                $query->whereDate('enquiries.created_at', date('Y-m-d', strtotime($request->date)));
            }

            if ($request->has('country_id')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('country_id', $request->country_id);
                });
            }

            if ($request->has('cadre_id')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('cadre_id', $request->cadre_id);
                });
            }

            if ($request->has('district')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('district_id', $request->district);
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
        })
            ->processRequestAndGet(
                // pass the request with params
                $request,

                // set columns to query
                ['id', 'name', 'email', 'phone', 'subject', 'message', 'enquiries.created_at'],

                // set columns to searchIn
                ['id', 'name', 'email', 'phone', 'subject', 'message', 'state.title', 'cadre.title', 'country.title'], //,'state.title','cadre.title'
                function ($q) use ($request) {
                    $q->with(['user', 'user.cadre', 'user.state', 'user.district', 'user.block', 'user.health_facility', 'user.country']);

                    $q->join('subscribers', 'subscribers.phone_no', '=', 'enquiries.phone');
                    $q->join('cadre', 'cadre.id', '=', 'subscribers.cadre_id');
                    $q->leftJoin('state', 'state.id', '=', 'subscribers.state_id');
                    $q->leftJoin('country', 'country.id', '=', 'subscribers.country_id');
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

        return view('admin.enquiry.index', [
            'data' => $data,
            'date' => isset($request->date) ? $request->date : "",
            'country' => $country,
            'state' => $state,
            'cadre' => $cadre,
            'district' => $district,
            'block' => $block,
            'health_facility' => $health_facility
        ]);
    }

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @throws AuthorizationException
    //  * @return Factory|View
    //  */
    // public function create()
    // {
    //     $this->authorize('admin.enquiry.create');

    //     return view('admin.enquiry.create');
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param StoreEnquiry $request
    //  * @return array|RedirectResponse|Redirector
    //  */
    // public function store(StoreEnquiry $request)
    // {
    //     // Sanitize input
    //     $sanitized = $request->getSanitized();

    //     // Store the Enquiry
    //     $enquiry = Enquiry::create($sanitized);

    //     if ($request->ajax()) {
    //         return ['redirect' => url('admin/enquiries'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
    //     }

    //     return redirect('admin/enquiries');
    // }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param Enquiry $enquiry
    //  * @throws AuthorizationException
    //  * @return void
    //  */
    // public function show(Enquiry $enquiry)
    // {
    //     $this->authorize('admin.enquiry.show', $enquiry);

    //     // TODO your code goes here
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param Enquiry $enquiry
    //  * @throws AuthorizationException
    //  * @return Factory|View
    //  */
    // public function edit(Enquiry $enquiry)
    // {
    //     $this->authorize('admin.enquiry.edit', $enquiry);


    //     return view('admin.enquiry.edit', [
    //         'enquiry' => $enquiry,
    //     ]);
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param UpdateEnquiry $request
    //  * @param Enquiry $enquiry
    //  * @return array|RedirectResponse|Redirector
    //  */
    // public function update(UpdateEnquiry $request, Enquiry $enquiry)
    // {
    //     // Sanitize input
    //     $sanitized = $request->getSanitized();

    //     // Update changed values Enquiry
    //     $enquiry->update($sanitized);

    //     if ($request->ajax()) {
    //         return [
    //             'redirect' => url('admin/enquiries'),
    //             'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
    //         ];
    //     }

    //     return redirect('admin/enquiries');
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param DestroyEnquiry $request
    //  * @param Enquiry $enquiry
    //  * @throws Exception
    //  * @return ResponseFactory|RedirectResponse|Response
    //  */
    // public function destroy(DestroyEnquiry $request, Enquiry $enquiry)
    // {
    //     $enquiry->delete();

    //     if ($request->ajax()) {
    //         return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    //     }

    //     return redirect()->back();
    // }

    // /**
    //  * Remove the specified resources from storage.
    //  *
    //  * @param BulkDestroyEnquiry $request
    //  * @throws Exception
    //  * @return Response|bool
    //  */
    // public function bulkDestroy(BulkDestroyEnquiry $request) : Response
    // {
    //     DB::transaction(static function () use ($request) {
    //         collect($request->data['ids'])
    //             ->chunk(1000)
    //             ->each(static function ($bulkChunk) {
    //                 DB::table('enquiries')->whereIn('id', $bulkChunk)
    //                     ->update([
    //                         'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
    //                 ]);

    //                 // TODO your code goes here
    //             });
    //     });

    //     return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    // }

    /**
     * Export entities
     *
     * @return BinaryFileResponse|null
     */
    public function export(Request $request): ?BinaryFileResponse
    {
        $this->authorize('admin.chat-question-hit.export');
        return Excel::download(new EnquiriesExport($request), 'enquiries.xlsx');
    }
}
