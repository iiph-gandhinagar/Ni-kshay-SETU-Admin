<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\State;
use App\Models\Block;
use App\Models\District;
use App\Models\Cadre;
use App\Models\HealthFacility;
use App\Models\Country;

class StateDistrictBlockController extends BaseController
{
    public function getAllCountry()
    {
        $country = Country::get();
        $success = true;
        return ['status' => $success, 'data' => $country, 'code' => 200];
    }

    public function getAllStates()
    {
        $states = State::orderByRaw("FIELD(title , 'INDIA') DESC")->orderby('title', 'asc')->get();
        $success = true;
        return ['status' => $success, 'data' => $states, 'code' => 200];
    }

    public function getAllDistrict($state_id)
    {
        $districts = District::where('state_id', $state_id)->get();
        $success = true;
        return ['success' => $success, 'data' => $districts, 'code' => 200];
    }

    public function getAllBlock($district_id)
    {
        $blocks = Block::where('district_id', $district_id)->get();
        $success = true;
        return ['success' => $success, 'data' => $blocks, 'code' => 200];
    }

    public function getAllcadre($type)
    {
        $cadre = Cadre::where('cadre_type', $type)
            ->orderByRaw("FIELD(title , 'Consultant - Development Partner','Technical Officer (TB)','Specialist (TB)','National Consultant (TB)','Joint Director (TB)','Additional Deputy Director General (ADDG) -TB','Deputy Director General (DDG) - TB') DESC")
            ->orderby('title', 'asc')
            ->get();
        $success = true;
        return ['success' => $success, 'data' => $cadre, 'code' => 200];
    }

    public function getAllCadreType()
    {
        $cadreType = Cadre::distinct()->orderByRaw("FIELD(cadre_type , 'National_Level') DESC")->get('cadre_type');
        $success = true;
        return ['success' => $success, 'data' => $cadreType, 'code' => 200];
    }
    public function getAllHealth($block_id)
    {
        $health = HealthFacility::where('block_id', $block_id)->get();
        $success = true;
        return ['success' => $success, 'data' => $health, 'code' => 200];
    }

    public function getHealthFacility()
    {
        $health = HealthFacility::get();
        $success = true;
        return ['success' => $success, 'data' => $health, 'code' => 200];
    }

    public function getFilterData(Request $request)
    {
        $orderId = "ASC";
        if (isset($request['sort']) && $request['sort'] == 'DESC' || $request['sort'] == 'desc') {
            $orderId = "DESC";
        }
        $filterData = HealthFacility::where('state_id', '>', 0)
            ->with(['state', 'district', 'block']);

        if (isset($request['health_facility']) && $request['health_facility'] != '') {
            $facilitiesColumnsArray = explode(',', $request['health_facility']);
            $filterData = $filterData->where(function ($query) use ($facilitiesColumnsArray) {
                foreach ($facilitiesColumnsArray as $key => $facilityCol) {
                    $filterData = $query->orWhere($facilityCol, 1);
                }
            });
        }

        if (isset($request['search_term']) && $request['search_term'] != '') {
            $filterData = $filterData->where('health_facility_code', 'like', '%' . $request['search_term'] . '%');
        }

        if (isset($request['state_id']) && $request['state_id'] > 0) {
            $filterData = $filterData->where('state_id', $request['state_id']);
        }

        if (isset($request['district_id']) && $request['district_id'] > 0) {
            $filterData = $filterData->where('district_id', $request['district_id']);
        }

        if (isset($request['block_id']) && $request['block_id'] > 0) {
            $filterData = $filterData->where('block_id', $request['block_id']);
        }
        $filterData = $filterData
            ->orderBy('health_facility_code', $orderId)
            ->paginate();

        $success = true;
        return ['success' => $success, 'data' =>  $filterData, 'code' => 200]; //FilterResource::collection()
    }
}
