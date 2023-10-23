<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\CaseDefinition;
use App\Models\CgcInterventionsAlgorithm;
use Illuminate\Http\Request;
use App\Models\DiagnosesAlgorithm;
use App\Models\DifferentialCareAlgorithm;
use App\Models\DynamicAlgorithm;
use App\Models\GuidanceOnAdverseDrugReaction;
use App\Models\LatentTbInfection;
use App\Models\Subscriber;
use App\Models\TreatmentAlgorithm;
use Log;

class DiagnosesAlgorithmsController extends BaseController
{
    public function getMasterNodes(Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $nodes = DiagnosesAlgorithm::where('activated', 1)->where('parent_id', 0)->with(['media'])->orderBy('index')->get();
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
            $nodes = DiagnosesAlgorithm::where('activated', 1)->where('parent_id', 0)->with(['media'])->orWhereRaw("find_in_set('" . $user[0]['state_id'] . "',state_id)")->whereRaw("find_in_set('" . $user[0]['cadre_id'] . "',cadre_id)")
                ->orderBy('index')->get();
        } else {
            $nodes = DiagnosesAlgorithm::where('activated', 1)->where('parent_id', 0)->with(['media'])->whereRaw("find_in_set('" . $user[0]['state_id'] . "',state_id)")->whereRaw("find_in_set('" . $user[0]['cadre_id'] . "',cadre_id)")
                ->orderBy('index')->get();
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
        $nodes = DiagnosesAlgorithm::where('activated', 1)->with(['children', 'media'])->where('parent_id', $masterNodeId)->orderBy('index')->get();
        $title = DiagnosesAlgorithm::where('activated', 1)->where('id', $masterNodeId)->get(['title', 'description'])[0];
        $nodeDetails = ['title' => $title['title'], 'description' => $title['description'], 'children' => $nodes];

        return $this->sendResponse($nodeDetails, 'List of Dependent Nodes', 200);
    }

    public function setMasterNodeId(Request $request)
    {
        $diag_algo = DiagnosesAlgorithm::with(['children'])->where('parent_id', 0)->get(['id']);
        $guidance_algo = GuidanceOnAdverseDrugReaction::with(['children'])->where('parent_id', 0)->get(['id']);
        $treatment_algo = TreatmentAlgorithm::with(['children'])->where('parent_id', 0)->get(['id']);
        $case_defintion_algo = CaseDefinition::with(['children'])->where('parent_id', 0)->get(['id']);
        $latent_tb_algo = LatentTbInfection::with(['children'])->where('parent_id', 0)->get(['id']);
        $cgc_algo = CgcInterventionsAlgorithm::with(['children'])->where('parent_id', 0)->get(['id']);
        $diff_care_algo = DifferentialCareAlgorithm::with(['children'])->where('parent_id', 0)->get(['id']);
        $dynamic_algo = DynamicAlgorithm::with(['children'])->where('parent_id', 0)->get(['id']);
        foreach ($diag_algo as $data) {
            $main_id = $data->id;

            foreach ($data->children as $child) {
                DiagnosesAlgorithm::where('id', $child->id)->update(['master_node_id' => $main_id]);
                $this->updateFlag($child, $main_id, "DiagnosesAlgorithm");
            }
        }
        foreach ($guidance_algo as $data) {
            $main_id = $data->id;

            foreach ($data->children as $child) {
                GuidanceOnAdverseDrugReaction::where('id', $child->id)->update(['master_node_id' => $main_id]);
                $this->updateFlag($child, $main_id, "GuidanceOnAdverseDrugReaction");
            }
        }
        foreach ($treatment_algo as $data) {
            $main_id = $data->id;

            foreach ($data->children as $child) {
                TreatmentAlgorithm::where('id', $child->id)->update(['master_node_id' => $main_id]);
                $this->updateFlag($child, $main_id, "TreatmentAlgorithm");
            }
        }
        foreach ($case_defintion_algo as $data) {
            $main_id = $data->id;

            foreach ($data->children as $child) {
                CaseDefinition::where('id', $child->id)->update(['master_node_id' => $main_id]);
                $this->updateFlag($child, $main_id, "CaseDefinition");
            }
        }
        foreach ($latent_tb_algo as $data) {
            $main_id = $data->id;

            foreach ($data->children as $child) {
                LatentTbInfection::where('id', $child->id)->update(['master_node_id' => $main_id]);
                $this->updateFlag($child, $main_id, "LatentTbInfection");
            }
        }
        foreach ($cgc_algo as $data) {
            $main_id = $data->id;

            foreach ($data->children as $child) {
                CgcInterventionsAlgorithm::where('id', $child->id)->update(['master_node_id' => $main_id]);
                $this->updateFlag($child, $main_id, "CgcInterventionsAlgorithm");
            }
        }
        foreach ($diff_care_algo as $data) {
            $main_id = $data->id;

            foreach ($data->children as $child) {
                DifferentialCareAlgorithm::where('id', $child->id)->update(['master_node_id' => $main_id]);
                $this->updateFlag($child, $main_id, "DifferentialCareAlgorithm");
            }
        }
        foreach ($dynamic_algo as $data) {
            $main_id = $data->id;

            foreach ($data->children as $child) {
                DynamicAlgorithm::where('id', $child->id)->update(['master_node_id' => $main_id]);
                $this->updateFlag($child, $main_id, "DynamicAlgorithm");
            }
        }
    }

    public function updateFlag($request, $main_id, $algo)
    {
        if (isset($request->children) && count($request->children) > 0) {
            foreach ($request->children as $child) {
                if ($algo == "DiagnosesAlgorithm") {
                    DiagnosesAlgorithm::where('id', $child->id)->update(['master_node_id' => $main_id]);
                    $this->updateFlag($child, $main_id, "DiagnosesAlgorithm");
                }
                if ($algo == "GuidanceOnAdverseDrugReaction") {
                    GuidanceOnAdverseDrugReaction::where('id', $child->id)->update(['master_node_id' => $main_id]);
                    $this->updateFlag($child, $main_id, "GuidanceOnAdverseDrugReaction");
                }
                if ($algo == "TreatmentAlgorithm") {
                    TreatmentAlgorithm::where('id', $child->id)->update(['master_node_id' => $main_id]);
                    $this->updateFlag($child, $main_id, "TreatmentAlgorithm");
                }
                if ($algo == "CaseDefinition") {
                    CaseDefinition::where('id', $child->id)->update(['master_node_id' => $main_id]);
                    $this->updateFlag($child, $main_id, "CaseDefinition");
                }
                if ($algo == "LatentTbInfection") {
                    LatentTbInfection::where('id', $child->id)->update(['master_node_id' => $main_id]);
                    $this->updateFlag($child, $main_id, "LatentTbInfection");
                }
                if ($algo == "CgcInterventionsAlgorithm") {
                    CgcInterventionsAlgorithm::where('id', $child->id)->update(['master_node_id' => $main_id]);
                    $this->updateFlag($child, $main_id, "CgcInterventionsAlgorithm");
                }
                if ($algo == "DifferentialCareAlgorithm") {
                    DifferentialCareAlgorithm::where('id', $child->id)->update(['master_node_id' => $main_id]);
                    $this->updateFlag($child, $main_id, "DifferentialCareAlgorithm");
                }
                if ($algo == "DynamicAlgorithm") {
                    DynamicAlgorithm::where('id', $child->id)->update(['master_node_id' => $main_id]);
                    $this->updateFlag($child, $main_id, "DynamicAlgorithm");
                }
            }
        } else {

            if ($algo == "DiagnosesAlgorithm") {
                DiagnosesAlgorithm::where('id', $request->id)->update(['master_node_id' => $main_id]);
            }
            if ($algo == "GuidanceOnAdverseDrugReaction") {
                GuidanceOnAdverseDrugReaction::where('id', $request->id)->update(['master_node_id' => $main_id]);
            }
            if ($algo == "TreatmentAlgorithm") {
                TreatmentAlgorithm::where('id', $request->id)->update(['master_node_id' => $main_id]);
            }
            if ($algo == "CaseDefinition") {
                CaseDefinition::where('id', $request->id)->update(['master_node_id' => $main_id]);
            }
            if ($algo == "LatentTbInfection") {
                LatentTbInfection::where('id', $request->id)->update(['master_node_id' => $main_id]);
            }
            if ($algo == "CgcInterventionsAlgorithm") {
                CgcInterventionsAlgorithm::where('id', $request->id)->update(['master_node_id' => $main_id]);
            }
            if ($algo == "DifferentialCareAlgorithm") {
                DifferentialCareAlgorithm::where('id', $request->id)->update(['master_node_id' => $main_id]);
            }
            if ($algo == "DynamicAlgorithm") {
                DynamicAlgorithm::where('id', $request->id)->update(['master_node_id' => $main_id]);
            }
        }
    }
}
