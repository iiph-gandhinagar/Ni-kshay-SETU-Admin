<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\DifferentialCareAlgorithm;
use App\Models\PatientAssessment;
use App\Models\PatientScoreDetails;
use App\Models\Subscriber;

class DifferentialCareAlgorithmsController extends BaseController
{
    public function getMasterNodes(Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $nodes = DifferentialCareAlgorithm::where('activated', 1)->where('parent_id', 0)->with(['media'])->orderBy('index')->get();
        return $this->sendResponse($nodes, 'List of Master Nodes', 200);
    }

    public function getMasterNodesV2(Request $request)
    {
        $user = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id', 'state_id', 'country_id']);
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        if ($user[0]['state_id'] == 0) { //for india user
            $nodes = DifferentialCareAlgorithm::where('activated', 1)->where('parent_id', 0)->with(['media'])->whereRaw("find_in_set('" . $user[0]['cadre_id'] . "',cadre_id)")
                ->orWhereRaw("find_in_set('" . $user[0]['state_id'] . "',state_id)")->orderBy('index')->get();
        } else {
            $nodes = DifferentialCareAlgorithm::where('activated', 1)->where('parent_id', 0)->with(['media'])->whereRaw("find_in_set('" . $user[0]['cadre_id'] . "',cadre_id)")
                ->whereRaw("find_in_set('" . $user[0]['state_id'] . "',state_id)")->orderBy('index')->get();
        }
        return $this->sendResponse($nodes, 'List of Master Nodes', 200);
    }

    public function getDependentNodes(Request $request, $masterNodeId)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $nodes = DifferentialCareAlgorithm::with(['children', 'media'])->where('activated', 1)->where('parent_id', $masterNodeId)->orderBy('index')->get();
        $title = DifferentialCareAlgorithm::where('activated', 1)->where('id', $masterNodeId)->get(['title', 'description'])[0];
        $nodeDetails = ['title' => $title['title'], 'description' => $title['description'], 'children' => $nodes];

        return $this->sendResponse($nodeDetails, 'List of Dependent Nodes', 200);
    }

    public function storePatientDetails(Request $request)
    {

        foreach ($request['patient_data'] as $item) {
            $newRequest[$item['id']] = $item['value'];
            $dataRequest[$item['id'] . "_SCORE"] = $item['score'];
        }

        $newRequest['nikshay_id'] = $request['patient_details']['nikshay_id'];
        $newRequest['patient_name'] = $request['patient_details']['patient_name'];
        $newRequest['age'] = $request['patient_details']['age'];
        $newRequest['gender'] = $request['patient_details']['gender'];
        $newRequest['patient_selected_data'] = $request->getContent();
        $patient_assessment_details = PatientAssessment::create($newRequest);

        $dataRequest['nikshay_id'] = $request['patient_details']['nikshay_id'];
        $dataRequest['patient_name'] = $request['patient_details']['patient_name'];
        $dataRequest['patient_assessment_id'] = $patient_assessment_details['id'];
        PatientScoreDetails::create($dataRequest);
        return $this->sendResponse("Data Store Successfully", 'Patient Details Store Successfully', 200);
    }
}
