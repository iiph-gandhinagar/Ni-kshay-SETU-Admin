<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubscriberActivity\IndexSubscriberActivity;
use App\Models\SubscriberActivity;
use App\Models\Subscriber;
use App\Models\Cadre;
use Brackets\AdminListing\Facades\AdminListing;
use Illuminate\Contracts\View\Factory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Response;
use Log;
use DB;

class SubscriberActivitiesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexSubscriberActivity $request
     * @return array|Factory|View
     */
    public function index(IndexSubscriberActivity $request)
    {
        $masterData = \StateWiseFilterData::getStateWiseFilterDataWithHealthFacility();
        $subscriber = $masterData['subscriber'];
        $state = $masterData['state'];
        // $cadre = Cadre::get(['id','title']);
        $cadre = $masterData['cadres'];
        // $country = Country::get(['id','title']);
        $country = $masterData['country'];
        $action = SubscriberActivity::distinct()->get(['action']);
        $plateform = SubscriberActivity::distinct()->get(['plateform']);

        if (\Auth::user()->roles[0]['id'] == 10) {
            $cadre = Cadre::whereIn('id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get(['id', 'title']);
            $subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->get();
        }

        if (!$request->ajax() && session(\Str::slug($request->getPathInfo()))) {
            $request['page'] = session(\Str::slug($request->getPathInfo()));
        }
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(SubscriberActivity::class)
            ->modifyQuery(function ($query) use ($request) {

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
                    $query->whereHas('user.country', function ($q) use ($assignedCountry) {
                        $q->where('id', explode(',', $assignedCountry));
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
                if ($request->has('action')) {
                    $query->where('action', $request->action);
                }
                if ($request->has('cadre_id')) {
                    $query->whereHas('user', function ($q) use ($request) {
                        $q->where('cadre_id', $request->cadre_id);
                    });
                }
                if ($request->has('country_id')) {
                    $query->whereHas('user', function ($q) use ($request) {
                        $q->where('country_id', $request->country_id);
                    });
                }
                if ($request->has('state_id') && $request->state_id != null) {
                    $query->whereHas('user', function ($q) use ($request) {
                        $q->where('state_id', $request->state_id);
                    });
                }
                if ($request->has('district_id') && $request->district_id != null) {
                    $query->whereHas('user', function ($q) use ($request) {
                        $q->where('district_id', $request->district_id);
                    });
                }
                if ($request->has('block_id') && $request->block_id != null) {
                    $query->whereHas('user', function ($q) use ($request) {
                        $q->where('block_id', $request->block_id);
                    });
                }
                if ($request->has('subscriber_id')) {
                    $query->where('user_id', $request->subscriber_id);
                }
                if ($request->has('plateform')) {
                    $query->where('plateform', $request->plateform);
                }

                if ($request->has('date') && $request->date != "") {
                    $query->whereDate('subscriber_activities.created_at', date('Y-m-d', strtotime($request->date)));
                }
                if ($request->has('from_date') && $request['from_date'] != '') {
                    $query->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->has('to_date') && $request['to_date'] != '') {
                    $query->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($request->to_date)));
                }
            })->processRequestAndGet(
                // pass the request with params
                $request,

                // set columns to query
                ['id', 'user_id', 'action', 'ip_address', 'plateform', 'created_at'],

                // set columns to searchIn
                ['id', 'action', 'ip_address', 'plateform', 'subscribers.name', 'cadre.title', 'state.title', 'country.title'], //

                function ($q) use ($request) {
                    $q->with(['user', 'user.cadre', 'user.state', 'user.country']);

                    $q->join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id');
                    $q->leftJoin('cadre', 'cadre.id', '=', 'subscribers.cadre_id');
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

        return view('admin.subscriber-activity.index', [
            'data' => $data,
            'subscriber' => $subscriber,
            'cadre' => $cadre,
            'state' => $state,
            'plateform' => $plateform,
            'action' => $action,
            'date' => isset($request->date) ? $request->date : "",
            'country' => $country
        ]);
    }

    /**
     * Export entities
     *
     * @return BinaryFileResponse|null
     */
    public function export(Request $request)
    // : ?BinaryFileResponse
    {
        $this->authorize('admin.subscriber-activity.export');
        $newRquest = $request;
        $assignedState = \Auth::user()->state;

        $result = $this->exportCondition($assignedState, $newRquest);


        $output = 'id|Name|Action|Ip Address|Phone No|Country|Cadre Type|Cadre|State|Created At|Updated At'; //
        // $output = "id|Action|Ip Address|Plateform|Created At|Updated At";
        $output .= "\n";
        foreach ($result as $row) { //,$row->action,$row->ip_address,$row->payload
            // $output.= implode('|',array($row->id,$row->action,$row->ip_address,$row->plateform,$row->created_at,$row->updated_at));
            $output .= implode("|", array($row->id, $row->name, $row->action, $row->ip_address, $row->phone_no, (isset($row->country_title) ? $row->country_title : "NA"), $row->cadre_type, (isset($row->cadre_title) ? $row->cadre_title : NULl), (isset($row->state_title) ? $row->state_title : NULL), $row->created_at, $row->updated_at));
            $output .= "\n";
        }
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="subscriber_activity.csv"',
        );

        return Response::make(rtrim($output, "\n"), 200, $headers);
    }

    public function exportCondition($assignedState, $newRquest)
    {
        $result = DB::table('subscriber_activities')
            ->join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
            ->join('cadre', 'cadre.id', '=', 'subscribers.cadre_id')
            ->leftJoin('state', 'state.id', '=', 'subscribers.state_id')
            ->leftJoin('country', 'country.id', '=', 'subscribers.country_id')
            ->where('action', 'Not LIKE', '%user_App_Version%');

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
        if ($assignedState != '' && $assignedState > 0) {
            $result = $result->whereIn('subscribers.state_id', explode(',', $assignedState));
        }
        if ($assignedCountry != '' && $assignedCountry > 0) {
            $result = $result->whereIn('subscribers.country_id', explode(',', $assignedCountry));
        }
        if ($assignedDistrict != '' && $assignedDistrict > 0) {
            $result = $result->whereIn('subscribers.district_id', explode(',', $assignedDistrict));
        }
        if ($assignedCadre != '' && $assignedCadre > 0) {
            $result = $result->whereIn('subscribers.cadre_id', explode(',', $assignedCadre));
        }
        if ($newRquest->has('subscriber_id') && $newRquest['subscriber_id'] != NULL && $newRquest['subscriber_id'] != 'null') {
            $result = $result->where('user_id', $newRquest->subscriber_id);
        }
        if ($newRquest->has('action') && $newRquest['action'] != NULL && $newRquest['action'] != 'null') {
            $result = $result->where('action', $newRquest->action);
        }
        if ($newRquest->has('plateform') && $newRquest['plateform'] != NULL && $newRquest['plateform'] != 'null') {
            $result = $result->where('plateform', $newRquest->plateform);
        }
        if ($newRquest->has('cadre_id') && $newRquest['cadre_id'] != NULL && $newRquest['cadre_id'] != 'null') {
            $result =  $result->where('subscribers.cadre_id', $newRquest->cadre_id);
        }
        if ($newRquest->has('country_id') && $newRquest['country_id'] != NULL && $newRquest['country_id'] != 'null') {
            $result =  $result->where('subscribers.country_id', $newRquest->country_id);
        }
        if ($newRquest->has('state_id') && $newRquest['state_id'] != NULL && $newRquest['state_id'] != 'null') {

            $result =  $result->where('subscribers.state_id', $newRquest->state_id);
        }
        if ($newRquest->has('todayDate') && $newRquest['todayDate'] != NULL && $newRquest['todayDate'] != 'null') {
            // $result =  $result->whereDate('created_at', Carbon::createFromFormat('d/m/Y', $newRquest->todayDate)->format('Y-m-d'));
            $result =  $result->whereDate('subscriber_activities.created_at', date('Y-m-d', strtotime($newRquest->todayDate)));
        }
        $result = $result->get(['subscriber_activities.id', 'subscribers.name as name', 'subscribers.phone_no as phone_no', 'subscribers.cadre_type as cadre_type', 'cadre.title as cadre_title', 'country.title as country_title', 'state.title as state_title', 'action', 'ip_address', 'plateform', 'subscriber_activities.created_at', 'subscriber_activities.updated_at']);
        return $result;
    }
}
