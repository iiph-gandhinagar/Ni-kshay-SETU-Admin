<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserAssessmentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserAssessment\BulkDestroyUserAssessment;
use App\Http\Requests\Admin\UserAssessment\DestroyUserAssessment;
use App\Http\Requests\Admin\UserAssessment\IndexUserAssessment;
use App\Http\Requests\Admin\UserAssessment\StoreUserAssessment;
use App\Http\Requests\Admin\UserAssessment\UpdateUserAssessment;
use App\Models\UserAssessment;
use App\Models\Assessment;
use App\Models\Subscriber;
use App\Models\Cadre;
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
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Log;

class UserAssessmentsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexUserAssessment $request
     * @return array|Factory|View
     */
    public function index(IndexUserAssessment $request)
    {
        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithHealthFacility();
        $state = $masterData['state'];
        $district = $masterData['district'];
        $block = $masterData['block'];
        $assessment = $masterData['assessment'];
        $subscriber = $masterData['subscriber'];
        $health_facility = $masterData['health_facility'];
        $country = $masterData['country'];
        $cadre = $masterData['cadres'];
        // Log::info("Auth user ----->");
        // Log::info(auth()->user()->state);
        if (\Auth::user()->roles[0]['id'] == 10) {
            $cadre = Cadre::whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get(['id', 'title']);

            $assessment = Assessment::where('created_by', \Auth::user()->id)->get();
            $subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get();
        }

        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(UserAssessment::class)
            ->modifyQuery(function ($query) use ($request) {
                $assignedDistrict = '';
                $assignedCountry = '';
                $assignedState = '';
                $assignedcadre = '';
                if (\Auth::user()->role_type == 'country_type' && (auth()->user()->roles[0]['id'] == 1 || \Auth::user()->roles[0]['id'] == 2)) {
                    // $assignedCountry = \Auth::user()->country;
                    // $assignedState = \Auth::user()->state;
                    // $assignedCadre = \Auth::user()->cadre;
                    // $assignedDistrict = \Auth::user()->district;
                } elseif (\Auth::user()->role_type == 'country_type') {
                    $assignedCountry = \Auth::user()->country;
                    $assignedcadre = \Auth::user()->cadre;
                } elseif (\Auth::user()->role_type == 'state_type') {
                    $assignedState = \Auth::user()->state;
                    $assignedcadre = \Auth::user()->cadre;
                } else {
                    $assignedDistrict = \Auth::user()->district;
                    $assignedcadre = \Auth::user()->cadre;
                }
                $assignedRole = \Auth::user()->roles[0]['id'];

                if ($assignedCountry != '' && $assignedCountry > 0) {
                    $query->whereHas('user.country', function ($q) use ($assignedCountry) {
                        $q->whereIn('id', explode(',', $assignedCountry));
                    });
                }
                if ($assignedState != '' && $assignedState > 0) {
                    $query->whereHas('user.state', function ($q) use ($assignedState) {
                        $q->whereIn('id', explode(',', $assignedState));
                    });
                }
                if ($assignedDistrict != '' && $assignedDistrict > 0) {
                    $query->whereHas('user.district', function ($q) use ($assignedDistrict) {
                        $q->whereIn('id', explode(',', $assignedDistrict));
                    });
                }
                if ($assignedcadre != '' && $assignedcadre > 0) {
                    $query->whereHas('user.cadre', function ($q) use ($assignedcadre) {
                        $q->whereIn('id', explode(',', $assignedcadre));
                    });
                }
                if ($assignedRole != '' && $assignedRole == 10) {
                    $query->whereHas('user.cadre', function ($q) use ($assignedState) {
                        $q->whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17]);
                    });
                }
                if ($request->has('assessment_id')) {
                    $query->where('assessment_id', $request->assessment_id);
                }
                if ($request->has('subscriber_id')) {
                    $query->where('user_id', $request->subscriber_id);
                }
                if ($request->has('cadre_id')) {
                    $query->whereHas('user.cadre', function ($q) use ($request) {
                        $q->where('id', $request->cadre_id);
                    });
                }
                if ($request->has('country_id')) {
                    $query->whereHas('user.country', function ($q) use ($request) {
                        $q->where('id', $request->country_id);
                    });
                }
                if ($request->has('date') && $request->date != "") {
                    // $query->whereDate('user_assessments.created_at', Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'));
                    $query->whereDate('user_assessments.created_at', date('Y-m-d', strtotime($request->date)));
                }
                if ($request->has('state_id') && $request->state_id != '') {
                    $query->whereHas('user.state', function ($q) use ($request) {
                        $q->where('id', $request->state_id);
                    });
                }
                if ($request->has('district_id') && $request->district_id != '') {

                    $query->whereHas('user.district', function ($q) use ($request) {
                        $q->where('id', $request->district_id);
                    });
                }
                if ($request->has('block_id') && $request->block_id != '') {

                    $query->whereHas('user.block', function ($q) use ($request) {
                        $q->where('id', $request->block_id);
                    });
                }
                if ($request->has('health_facility_id')) {

                    $query->whereHas('user.health_facility', function ($q) use ($request) {
                        $q->where('id', $request->health_facility_id);
                    });
                }

                if ($request->has('from_date') && $request['from_date'] != '') {
                    $query->whereDate('user_assessments.created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->has('to_date') && $request['to_date'] != '') {
                    $query->whereDate('user_assessments.created_at', '<=', date('Y-m-d', strtotime($request->to_date)));
                }
            })
            ->processRequestAndGet(
                // pass the request with params
                $request,

                // set columns to query
                ['id', 'assessment_id', 'user_id', 'total_marks', 'obtained_marks', 'attempted', 'right_answers', 'wrong_answers', 'skipped', 'created_at'],

                // set columns to searchIn
                ['id', 'assessments.assessment_title', 'subscribers.name', 'cadre.title', 'state.title', 'blocks.title', 'districts.title', 'state.title', 'health_facilities.health_facility_code', 'country.title'], //, 

                function ($query) use ($request) {
                    $query->with(['assessment_with_trashed', 'user', 'user.cadre', 'user.country', 'user.state', 'user.district', 'user.block', 'user.health_facility']);

                    //add this line if you want to search by author attributes
                    $query->join('assessments', 'assessments.id', '=', 'user_assessments.assessment_id');
                    $query->join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id');
                    $query->leftJoin('state', 'state.id', '=', 'subscribers.state_id');
                    $query->leftJoin('country', 'country.id', '=', 'subscribers.country_id');
                    $query->join('cadre', 'cadre.id', '=', 'subscribers.cadre_id');
                    $query->leftJoin('blocks', 'blocks.id', '=', 'subscribers.block_id');
                    $query->leftJoin('districts', 'districts.id', '=', 'subscribers.district_id');
                    $query->leftJoin('health_facilities', 'health_facilities.id', '=', 'subscribers.health_facility_id');
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

        return view('admin.user-assessment.index', [
            'data' => $data,
            'assessment' => $assessment,
            'subscriber' => $subscriber,
            'cadre' => $cadre,
            'state' => $state,
            'district' => $district,
            'block' => $block,
            'health_facility' => $health_facility,
            'from_date' => isset($request->from_date) ? $request->from_date : "",
            'to_date' => isset($request->to_date) ? $request->to_date : "",
            'state_id' => isset($request->state_id) ? $request->state_id : "",
            'district_id' => isset($request->district_id) ? $request->district_id : "",
            'block_id' => isset($request->block_id) ? $request->block_id : "",
            'date' => isset($request->date) ? $request->date : "",
            'country' => $country,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.user-assessment.create');

        return view('admin.user-assessment.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserAssessment $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreUserAssessment $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the UserAssessment
        $userAssessment = UserAssessment::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/user-assessments'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/user-assessments');
    }

    /**
     * Display the specified resource.
     *
     * @param UserAssessment $userAssessment
     * @throws AuthorizationException
     * @return void
     */
    public function show(UserAssessment $userAssessment)
    {
        $this->authorize('admin.user-assessment.show', $userAssessment);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param UserAssessment $userAssessment
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(UserAssessment $userAssessment)
    {
        $this->authorize('admin.user-assessment.edit', $userAssessment);


        return view('admin.user-assessment.edit', [
            'userAssessment' => $userAssessment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserAssessment $request
     * @param UserAssessment $userAssessment
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateUserAssessment $request, UserAssessment $userAssessment)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values UserAssessment
        $userAssessment->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/user-assessments'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/user-assessments');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyUserAssessment $request
     * @param UserAssessment $userAssessment
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyUserAssessment $request, UserAssessment $userAssessment)
    {
        $userAssessment->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyUserAssessment $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyUserAssessment $request): Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    DB::table('userAssessments')->whereIn('id', $bulkChunk)
                        ->update([
                            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }

    /**
     * Export entities
     *
     * @return BinaryFileResponse|null
     */
    public function export(Request $request): ?BinaryFileResponse
    {
        $this->authorize('admin.user-assessment.export');
        return Excel::download(new UserAssessmentsExport($request), 'userAssessments.xlsx');
    }
}
