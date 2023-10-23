<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Subscriber\IndexSubscriber;
use App\Http\Requests\Admin\Subscriber\UpdateSubscriber;
use App\Models\Subscriber;
use App\Models\Cadre;
use App\Models\State;
use App\Models\District;
use App\Models\Block;
use App\Models\Country;
use App\Models\UserAppVersion;
use App\Models\HealthFacility;
use Brackets\AdminListing\Facades\AdminListing;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Log;
use Response;
use DB;

class SubscribersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexSubscriber $request
     * @return array|Factory|View
     */
    public function index(IndexSubscriber $request)
    {

        $app_version = UserAppVersion::distinct()->get(['app_version']);


        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithHealthFacility();
        $state = $masterData['state'];
        $district = $masterData['district'];
        $block = $masterData['block'];
        $health_facility = $masterData['health_facility'];
        $cadre = $masterData['cadres'];
        if (\Auth::user()->roles[0]['id'] == 10) {
            $cadre = Cadre::whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get(['id', 'title']);
        }
        $orWhereCondition = "";
        if (gettype($request->district_id) == "array") {
            if ($request->district_id && count($request->district_id) > 0 && !in_array(0, ($request->district_id))) {
                $orWhereCondition .= '(';
                for ($i = 0; $i < count($request->district_id); $i++) {
                    $districtId = $request->district_id[$i];
                    $orWhereCondition .= "find_in_set('" . $districtId . "',subscribers.district_id) OR ";
                }
            }
        }


        // $orWhereCondition = "find_in_set('12',cadre_id) OR find_in_set('14',cadre_id) OR ";
        $orWhereCondition = substr_replace($orWhereCondition, "", -3);
        $orWhereCondition .= ')';
        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }

        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()) . '/subscriber-search')) {
            $request['search'] = session(\Str::slug($request->getPathInfo()) . '/subscriber-search');
            $search = session(\Str::slug($request->getPathInfo()) . '/subscriber-search');
        }

        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Subscriber::class)->modifyQuery(function ($query) use ($request, $orWhereCondition) {
            $assignedDistrict = '';
            $assignedCountry = '';
            $assignedState = '';
            $assignedCadre = '';
            if (\Auth::user()->role_type == 'country_type' && (\Auth::user()->roles[0]['id'] == 1 || \Auth::user()->roles[0]['id'] == 2)) {
                // $assignedCountry = \Auth::user()->country;
                // $assignedState = \Auth::user()->state;
                // $assignedCadre = \Auth::user()->cadre;
                // $assignedDistrict = \Auth::user()->district;
            } elseif (\Auth::user()->role_type == 'country_type') {
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
                $query->whereIn('subscribers.country_id', explode(',', $assignedCountry));
            }
            if ($assignedState != '' && $assignedState > 0) {
                $query->whereIn('subscribers.state_id', explode(',', $assignedState));
            }
            if ($assignedCadre != '' && $assignedCadre > 0) {
                $query->whereIn('subscribers.cadre_id', explode(',', $assignedCadre));
            }
            if ($assignedDistrict != '' && $assignedDistrict > 0) {
                $query->whereIn('subscribers.district_id', explode(',', $assignedDistrict));
            }
            if ($assignedRole != '' && $assignedRole == 10) {
                $query->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17]);
            }
            if ($request->has('cadre_id')) {
                $query->where('cadre_id', $request->cadre_id);
            }
            if ($request->has('state_id') && $request->state_id != '') {
                $query->where('subscribers.state_id', $request->state_id);
            }
            if (gettype($request->district_id) == "array") {
                if ($request->has('district_id') && $request->district_id != '' && !in_array(0, ($request->district_id))) {
                    // $query->where('subscribers.district_id', $request->district_id);
                    $query->whereRaw($orWhereCondition);
                }
            }
            if ($request->has('block_id') && $request->block_id != '') {
                $query->where('subscribers.block_id', $request->block_id);
            }
            if ($request->has('health_facility_id')) {
                $query->where('subscribers.health_facility_id', $request->health_facility_id);
            }
            if ($request->has('from_date') && $request['from_date'] != '') {
                $query->whereDate('subscribers.created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
            }
            if ($request->has('to_date') && $request['to_date'] != '') {
                $query->whereDate('subscribers.created_at', '<=', date('Y-m-d', strtotime($request->to_date)));
            }
            if ($request->has('user_app_version')) {
                $query->whereHas('user_app_version', function ($q) use ($request) {
                    $q->where('app_version', $request->user_app_version);
                });
            }
        })->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'api_token', 'name', 'phone_no', 'cadre_type', 'is_verified', 'cadre_id', 'country_id', 'block_id', 'district_id', 'state_id', 'health_facility_id', 'subscribers.created_at', 'forgot_otp_time'],

            // set columns to searchIn
            ['id', 'api_token', 'name', 'phone_no', 'cadre_type', 'cadre.title', 'blocks.title', 'districts.title', 'state.title', 'health_facilities.health_facility_code', 'country.title', 'forgot_otp_time'], //
            function ($query) use ($request) {
                $query->with(['health_facility', 'state', 'district', 'block', 'cadre', 'user_app_version', 'country', 'lb_subscriber_rankings', 'lb_subscriber_rankings.lb_level', 'lb_subscriber_rankings.lb_badge']); //->withCount(['subscriber_activities']) //,'lb_subscriber_rankings','lb_subscriber_rankings.lb_level','lb_subscriber_rankings.lb_badge'

                //add this line if you want to search by author attributes
                $query->leftJoin('cadre', 'cadre.id', '=', 'subscribers.cadre_id');
                $query->leftJoin('blocks', 'blocks.id', '=', 'subscribers.block_id');
                $query->leftJoin('districts', 'districts.id', '=', 'subscribers.district_id');
                $query->leftJoin('country', 'country.id', '=', 'subscribers.country_id');
                $query->leftjoin('state', 'state.id', '=', 'subscribers.state_id');
                $query->leftJoin('health_facilities', 'health_facilities.id', '=', 'subscribers.health_facility_id');
                // $query->join('subscriber_activities','subscriber_activities.user_id','=','subscribers.id')->groupBy('user_id')->count();
            }
        );
        if ($request->ajax()) {
            if ($request['page'] && $request['page'] > 0) {
                session([\Str::slug($request->getPathInfo()) => $request['page']]);
            }
            if ($request['search'] && $request['search'] != '') {
                session([\Str::slug($request->getPathInfo()) . '/subscriber-search' => $request['search']]);
            } else {
                session([\Str::slug($request->getPathInfo()) . '/subscriber-search' => '']);
            }
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data, 'search' => session(\Str::slug($request->getPathInfo()) . '/subscriber-search')];
        }

        return view('admin.subscriber.index', [
            'data' => $data,
            'cadre' => $cadre,
            'state' => $state,
            'district' => $district,
            'block' => $block,
            'health_facility' => $health_facility,
            'from_date' => isset($request->from_date) ? $request->from_date : "",
            'to_date' => isset($request->to_date) ? $request->to_date : "",
            'state_id' => isset($request->state_id) ? $request->state_id : "",
            'district_id' => isset($request->district_id) && gettype($request->district_id) == "array" && !in_array(0, ($request->district_id)) ? $request->district_id : [],
            'block_id' => isset($request->block_id) ? $request->block_id : "",
            'app_version' => $app_version,
            'search' => session(\Str::slug($request->getPathInfo()) . '/subscriber-search')
        ]);
    }

    public function edit(Subscriber $subscriber)
    {
        $this->authorize('admin.subscriber.edit', $subscriber);
        $state = State::get(['id', 'title']);
        $district = District::get(['id', 'title', 'state_id']);
        $block = Block::get(['id', 'title', 'district_id']);
        $cadre = Cadre::get(['id', 'title', 'cadre_type']);
        $health_facility = HealthFacility::get(['id', 'health_facility_code', 'block_id']);
        $country = Country::get(['id', 'title']);

        if (\Auth::user()->roles[0]['id'] == 10) {
            $cadre = Cadre::whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get(['id', 'title']);
        }
        if (isset($subscriber['state_id']) && $subscriber['state_id'] != "" && $subscriber['state_id'] != 0) {
            $subscriber['state_id'] = State::where('id', $subscriber['state_id'])->first(['id', 'title']);
        } else {
            $subscriber['state_id'] = [];
        }

        if (isset($subscriber['cadre_id']) && $subscriber['cadre_id'] != "" && $subscriber['cadre_id'] != 0) {
            $subscriber['cadre_id'] = Cadre::where('id', $subscriber['cadre_id'])->first(['id', 'title', 'cadre_type']);
        } else {
            $subscriber['cadre_id'] = [];
        }

        if (isset($subscriber['country_id']) && $subscriber['country_id'] != "" && $subscriber['country_id'] != 0) {
            $subscriber['country_id'] = Country::where('id', $subscriber['country_id'])->first(['id', 'title']);
        } else {
            $subscriber['country_id'] = [];
        }

        if (isset($subscriber['district_id']) && $subscriber['district_id'] != "" && $subscriber['district_id'] != 0) {
            $subscriber['district_id'] = District::where('id', $subscriber['district_id'])->first();
        } else {
            $subscriber['district_id'] = [];
        }

        if (isset($subscriber['block_id']) && $subscriber['block_id'] != "" && $subscriber['block_id'] != 0) {
            $subscriber['block_id'] = Block::where('id', $subscriber['block_id'])->first();
        } else {
            $subscriber['block_id'] = [];
        }

        if (isset($subscriber['health_facility_id']) && $subscriber['health_facility_id'] != "" && $subscriber['health_facility_id'] != 0) {
            $subscriber['health_facility_id'] = HealthFacility::where('id', $subscriber['health_facility_id'])->first();
        } else {
            $subscriber['health_facility_id'] = [];
        }

        return view('admin.subscriber.edit', [
            'subscriber' => $subscriber,
            'block' => $block,
            'state' => $state,
            'district' => $district,
            'cadre' => $cadre,
            'health_facility' => $health_facility,
            'country' => $country,
        ]);
    }

    public function sendForogtOtp(Subscriber $subscriber)
    {
        Log::info($subscriber->id . "->" . $subscriber->name);
        app('App\Http\Controllers\API\NotificationController')->sendForgotOtp($subscriber);
        Subscriber::where('id', $subscriber->id)->update(['forgot_otp_time' => Carbon::now()->format('Y-m-d H:i')]);
    }

    public function update(UpdateSubscriber $request, Subscriber $subscriber)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        if (isset($sanitized['country_id']) && $sanitized['country_id'] != "" && count($sanitized['country_id']) > 0) {
            $sanitized['country_id'] = $sanitized['country_id']['id'];
        } else {
            $sanitized['country_id'] = 0;
        }

        if (isset($sanitized['state_id']) && $sanitized['state_id'] != "" && count($sanitized['state_id']) > 0) {
            $sanitized['state_id'] = $sanitized['state_id']['id'];
        } else {
            $sanitized['state_id'] = 0;
        }

        if (isset($sanitized['district_id']) && $sanitized['district_id'] != "" && count($sanitized['district_id']) > 0) {
            $sanitized['district_id'] = $sanitized['district_id']['id'];
        } else {
            $sanitized['district_id'] = 0;
        }

        if (isset($sanitized['cadre_id']) && $sanitized['cadre_id'] != ""  && count($sanitized['cadre_id']) > 0) {
            $sanitized['cadre_id'] = $sanitized['cadre_id']['id'];
        } else {
            $sanitized['cadre_id'] = 0;
        }

        if (isset($sanitized['block_id']) && $sanitized['block_id'] != ""  && count($sanitized['block_id']) > 0) {
            $sanitized['block_id'] = $sanitized['block_id']['id'];
        } else {
            $sanitized['block_id'] = 0;
        }

        if (isset($sanitized['health_facility_id']) && $sanitized['health_facility_id'] != ""  && count($sanitized['health_facility_id']) > 0) {
            $sanitized['health_facility_id'] = $sanitized['health_facility_id']['id'];
        } else {
            $sanitized['health_facility_id'] = 0;
        }

        // Update changed values Block
        $subscriber->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/subscribers'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
            ];
        }

        return redirect('admin/subscribers');
    }

    /**
     * Export entities
     *
     * @return BinaryFileResponse|null
     */
    public function export(Request $request)
    //: ?BinaryFileResponse
    {
        $this->authorize('admin.subscriber.export');
        // return Excel::download(new SubscribersExport($request), 'subscribers.csv');
        $newRquest = $request;
        $assignedState = \Auth::user()->state;

        $result = $this->exportCondition($assignedState, $newRquest);


        $output = 'id|Name|Phone No|Cadre Type|Country|Cadre|State|District|Block|Health Facility|Level|Badge|Minute Spent|Sub Module Usage Count|App Opened Count|Chatbot Usage|Resource Material Usage|Total Task|App Performance Percentage|App Version|is Verified|Created At|Updated At';
        $output .= "\n";
        foreach ($result as $row) {
            $output .=  implode("|", array(
                $row->id,
                $row->name,
                $row->phone_no,
                $row->cadre_type,
                (isset($row->country_title) ? $row->country_title : "NA"),
                $row->cadre_title  ? $row->cadre_title : '',
                $row->state_title ? $row->state_title : '',
                $row->district_title ? $row->district_title : '',
                $row->block_title ? $row->block_title : "",
                $row->health_title ? $row->health_title : "",
                $row->level_id < 6 ? $row->level : "Expert",
                $row->badge_id < 16 ? $row->badge : "Gold",
                $row->mins_spent_count ? ($row->mins_spent_count) / 60 : '',
                $row->sub_module_usage_count ? $row->sub_module_usage_count : '',
                $row->App_opended_count ? $row->App_opended_count : '',
                $row->chatbot_usage_count ? $row->chatbot_usage_count : '',
                $row->resource_material_accessed_count ? $row->resource_material_accessed_count : '',
                $row->total_task_count ? $row->total_task_count : '',
                $row->total_task_count ? ($row->total_task_count * 100) / 64 . "%" : '',
                $row->app_version ? $row->app_version : '',
                $row->is_verified ? $row->is_verified : '',
                $row->created_at, $row->updated_at
            ));
            $output .= "\n";
        }
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="subscriber_activity.csv"',
        );

        return Response::make(rtrim($output, "\n"), 200, $headers);
    }

    public function getDistrict($type)
    {
        $data = District::where('state_id', $type)->get(['id', 'title']);
        return response()->json($data);
    }

    public function getBlock($state_id, $district_id)
    {
        $data = Block::where('state_id', $state_id)->where('district_id', $district_id)->get(['id', 'title']);
        return response()->json($data);
    }

    public function getHealthFacility($state_id, $district_id, $block_id)
    {

        $data = HealthFacility::where('state_id', $state_id)->where('district_id', $district_id)->where('block_id', $block_id)->get(['id', 'health_facility_code']);
        return response()->json($data);
    }

    public function exportCondition($assignedState, $newRquest)
    {
        $assignedDistrict = '';
        $assignedCountry = '';
        $assignedState = '';
        $assignedCadre = '';


        $result = DB::table('subscribers')
            ->leftJoin('cadre', 'cadre.id', '=', 'subscribers.cadre_id')
            ->leftJoin('user_app_version', 'user_app_version.user_id', '=', 'subscribers.id')
            ->leftJoin('state', 'state.id', '=', 'subscribers.state_id')
            ->leftJoin('country', 'country.id', '=', 'subscribers.country_id')
            ->leftJoin('blocks', 'blocks.id', '=', 'subscribers.block_id')
            ->leftJoin('districts', 'districts.id', '=', 'subscribers.district_id')
            ->leftJoin('health_facilities', 'health_facilities.id', '=', 'subscribers.health_facility_id')
            ->leftJoin('lb_subscriber_rankings', 'lb_subscriber_rankings.subscriber_id', '=', 'subscribers.id')
            ->leftJoin('lb_levels', 'lb_levels.id', '=', 'lb_subscriber_rankings.level_id')
            ->leftJoin('lb_badges', 'lb_badges.id', '=', 'lb_subscriber_rankings.badge_id');

        if (\Auth::user()->role_type == 'country_type' && (\Auth::user()->roles[0]['id'] == 1 || \Auth::user()->roles[0]['id'] == 2)) {
            // $assignedCountry = \Auth::user()->country;
            // $assignedState = \Auth::user()->state;
            // $assignedCadre = \Auth::user()->cadre;
            // $assignedDistrict = \Auth::user()->district;
        } elseif (\Auth::user()->role_type == 'country_type') {
            $assignedCountry = \Auth::user()->country;
            $assignedCadre = \Auth::user()->cadre;
        } elseif (\Auth::user()->role_type == 'state_type') {
            $assignedState = \Auth::user()->state;
            $assignedCadre = \Auth::user()->cadre;
        } else {
            $assignedDistrict = \Auth::user()->district;
            $assignedCadre = \Auth::user()->cadre;
        }

        if ($assignedState != '' && $assignedState > 0) {
            $result->whereIn('subscribers.state_id', explode(',', $assignedState));
        }
        if ($assignedCountry != '' && $assignedCountry > 0) {
            $result = $result->whereIn('subscribers.country_id', explode(',', $assignedCountry));
        }
        if ($assignedCadre != '' && $assignedCadre > 0) {
            $result = $result->whereIn('subscribers.cadre_id', explode(',', $assignedCadre));
        }
        if ($assignedDistrict != '' && $assignedDistrict > 0) {
            $result = $result->whereIn('subscribers.district_id', explode(',', $assignedDistrict));
        }
        if ($newRquest->has('cadre_id') && $newRquest['cadre_id'] != 'null' && $newRquest['cadre_id'] != NULL) {
            $result =  $result->where('subscribers.cadre_id', $newRquest->cadre_id);
        }
        if ($newRquest->has('state_id') && $newRquest['state_id'] != NULL && $newRquest['state_id'] != 'null') {
            $result = $result->where('subscribers.state_id', $newRquest->state_id);
        }
        if ($newRquest->has('district_id') && $newRquest['district_id'] != NULL && $newRquest['district_id'] != 'null') {
            $result = $result->where('subscribers.district_id', $newRquest->district_id);
        }
        if ($newRquest->has('block_id') && $newRquest['block_id'] != NULL && $newRquest['block_id'] != 'null') {
            $result = $result->where('subscribers.block_id', $newRquest->block_id);
        }
        if ($newRquest->has('health_facility_id') && $newRquest['health_facility_id'] != NULL && $newRquest['health_facility_id'] != 'null') {
            $result = $result->where('subscribers.health_facility_id', $newRquest->health_facility_id);
        }
        if ($newRquest->has('app_version') && $newRquest['app_version'] != NULL && $newRquest['app_version'] != 'null') {
            $result = $result->where('user_app_version.app_version', $newRquest->app_version);
        }
        if ($newRquest->has('from_date') && $newRquest['from_date'] != NULL && $newRquest['from_date'] != 'null') {
            $result =  $result->whereDate('subscribers.created_at', '>=', date('Y-m-d', strtotime($newRquest->from_date)));
        }
        if ($newRquest->has('to_date') && $newRquest['to_date'] != NULL && $newRquest['to_date'] != 'null') {
            $result = $result->whereDate('subscribers.created_at', '<=', date('Y-m-d', strtotime($newRquest->to_date)));
        }
        $result = $result->get(['subscribers.id', 'lb_subscriber_rankings.*', 'lb_levels.level->en as level', 'lb_badges.badge->en as badge', 'user_app_version.app_version', 'subscribers.name as name', 'subscribers.phone_no as phone_no', 'subscribers.cadre_type as cadre_type', 'cadre.title as cadre_title', 'country.title as country_title', 'state.title as state_title', 'districts.title as district_title', 'blocks.title as block_title', 'health_facilities.health_facility_code as health_title', 'subscribers.created_at', 'subscribers.updated_at', 'subscribers.is_verified']);
        return $result;
    }
}
