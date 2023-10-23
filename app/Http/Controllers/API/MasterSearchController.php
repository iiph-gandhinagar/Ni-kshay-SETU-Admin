<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\CaseDefinition;
use App\Models\CgcInterventionsAlgorithm;
use App\Models\ChatQuestion;
use App\Models\DiagnosesAlgorithm;
use App\Models\DifferentialCareAlgorithm;
use App\Models\DynamicAlgoMaster;
use App\Models\DynamicAlgorithm;
use App\Models\GuidanceOnAdverseDrugReaction;
use App\Models\LatentTbInfection;
use App\Models\ResourceMaterial;
use App\Models\Subscriber;
use App\Models\TreatmentAlgorithm;
use Illuminate\Http\Request;
use Log;
use DB;

class MasterSearchController extends BaseController
{
    public function getMasterSearchResult(Request $request)
    {

        /* Module Master Search Data Get */
        $module = $this->getModuleSearch($request);

        /* Resource Material Master Search Data Get */
        $search_resource_material = $this->getResourceMaterialSearch($request);
        /* Chat Question Master Search Data Get */
        $search_chat_question = $this->getChatQuestionSearch($request);

        /* Sub Modules Master Search Data */
        $sub_modules = $this->getSubModuleSearch($request);

        $master_search = [];
        $master_search['modules'] = isset($module) && count($module) > 0 ? $module['data']['modules'] : [];

        $master_search['sub_modules'] = isset($sub_modules) && count($sub_modules) > 0 ? $sub_modules['data']['sub_modules'] : [];

        $master_search['chat_question'] = isset($search_chat_question) && count($search_chat_question) > 0 ?  $search_chat_question['data']['chat_question'] : [];

        $master_search['resource_material'] = isset($search_resource_material) && count($search_resource_material) > 0 ? $search_resource_material['data']['resource_material'] : [];

        $success = true;
        return ['status' => $success, 'data' => $master_search, 'code' => 200];
    }

    public function getModuleSearch(Request $request)
    {
        $modules = ["Screening Tool", "Case Definition", "Diagnostic Care Cascade", "Guidance on ADR", "Treatment Care Cascade", "TB Preventive Treatment", "Differentiated Care Of TB Patients", "NTEP Intervention", "Current Assessment", "Past Assessment", "Survey", "Rating", "Certificate", "Referral-Health Facility"];
        $dynamic_algo = DynamicAlgoMaster::with(['media'])->where('active', 1)->get(['id', 'name'])->toArray();
        $resource_material = ResourceMaterial::where('parent_id', 0)->get(['id', 'title'])->toArray();
        $search_text = $request['search_text'];
        // $modules = array_merge($modules,array_pluck($dynamic_algo,'title'));

        $search_modules = array_values(array_filter($modules, function ($el) use ($search_text) {
            return (stripos($el, $search_text) !== false);
        }));

        $search_resource_material = array_values(array_filter($resource_material, function ($el) use ($search_text) {
            return (stripos($el['title'], $search_text) !== false);
        }));

        $search_dynamic_algo = array_values(array_filter($dynamic_algo, function ($el) use ($search_text) {
            return (stripos($el['name'], $search_text) !== false);
        }));

        $module = collect([]);
        if (isset($search_modules) && count($search_modules) > 0) {
            // $title = DiagnosesAlgorithm::where('id',$search_diagnoses_algorithm[0]->parent_id)->get(['id','title']);
            foreach ($search_modules as $modules) {
                if ($modules == "Screening Tool") {
                    $module->push(['title' => $modules, "type" => "Screening", "icon" => "checking-tool.svg", "link" => "Screening", "activityType" => "module_screening_tool"]);
                }
                if ($modules == "Case Definition") {
                    $module->push(['title' => $modules, "type" => "Case Definition", "icon" => "learning.svg", "link" => "AlgorithmList", "activityType" => "module_case_defintion"]);
                }
                if ($modules == "Diagnostic Care Cascade") {
                    $module->push(['title' => $modules, "type" => "Diagnosis Algorithm", "icon" => "algorithm.svg", "link" => "AlgorithmList", "activityType" => "module_diagnostic_care_cascade"]);
                }
                if ($modules == "Guidance on ADR") {
                    $module->push(['title' => $modules, "type" => "Guidance on ADR", "icon" => "adr.svg", "type" => "Guidance on ADR", "activityType" => "module_guidance_on_adr"]);
                }
                if ($modules == "Treatment Care Cascade") {
                    $module->push(['title' => $modules, "type" => "Treatment Algorithm", "icon" => "treatment.svg", "link" => "AlgorithmList", "activityType" => "module_treatment_care_cascade"]);
                }
                if ($modules == "TB Preventive Treatment") {
                    $module->push(['title' => $modules, "type" => "Latent TB Infection", "icon" => "latent-tb.svg", "link" => "AlgorithmList", "activityType" => "module_latent_tb"]);
                }
                if ($modules == "Differentiated Care Of TB Patients") {
                    $module->push(['title' => $modules, "type" => "Differentiated Care Of TB Patients", "icon" => "algorithm.svg", "link" => "AlgorithmList", "activityType" => "module_differentiated_care_tb_patient"]);
                }
                if ($modules == "NTEP Intervention") {
                    $module->push(['title' => $modules, "type" => "CGC", "icon" => "algorithm.svg", "link" => "Algorithms", "activityType" => "CGC", "sectionKey" => "NTEP",]);
                }
                if ($modules == "Current Assessment") {
                    $module->push(['title' => $modules, "type" => "Assessment", "icon" => "Ass", "link" => "CurrentAssessments", "activityType" => "module_current_assessments"]);
                }
                if ($modules == "Past Assessment") {
                    $module->push(['title' => $modules, "type" => "Past Assessment", "icon" => "PastAss", "link" => "PastAssessments", "activityType" => "module_past_assessments"]);
                }
                if ($modules == "Survey") {
                    $module->push(['title' => $modules, "type" => "survey", "icon" => "survey", "link" => "survey", "activityType" => "module_SURVEY_FORM"]);
                }
                if ($modules == "Rating") {
                    $module->push(['title' => $modules, "type" => "rating", "icon" => "rating", "link" => "rating", "activityType" => "module_RATING"]);
                }
                if ($modules == "Certificate") {
                    $module->push(['title' => $modules, "type" => "certificate", "icon" => "certi", "link" => "certificate", "activityType" => "module_CERTIFICATES"]);
                }
                if ($modules == "Referral-Health Facility") {
                    $module->push(['title' => $modules, "type" => "referral-health", "icon" => "hospital", "link" => "ReferralHealthFacility", "activityType" => "module_Referral-Health Facility"]);
                }
            }
        }
        if (isset($search_resource_material) && count($search_resource_material) > 0) {
            foreach ($search_resource_material as $material) {
                if ($material['title'] == "NTEP Guidelines") {
                    $module->push(['title' => $material['title'], "type" => "NTEP", "icon" => "NTEP", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_NTEP", 'id' => $material['id']]);
                }
                if ($material['title'] == "Office Orders") {
                    $module->push(['title' => $material['title'], "type" => "pdf", "icon" => "pdf", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_pdf", 'id' => $material['id']]);
                }
                if ($material['title'] == "Videos") {
                    $module->push(['title' => $material['title'], "type" => "video", "icon" => "video", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_video", 'id' => $material['id']]);
                }
                if ($material['title'] == "Presentations") {
                    $module->push(['title' => $material['title'], "type" => "ppt", "icon" => "ppt", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_ppt", 'id' => $material['id']]);
                }
                if ($material['title'] == "Documents") {
                    $module->push(['title' => $material['title'], "type" => "document", "icon" => "document", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_document", 'id' => $material['id']]);
                }
                if ($material['title'] == "Others") {
                    $module->push(['title' => $material['title'], "type" => "image", "icon" => "image", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_image", 'id' => $material['id']]);
                }
                if ($material['title'] == "translation title") {
                    $module->push(['title' => $material['title'], "type" => "folder", "icon" => "folder", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_folder", 'id' => $material['id']]);
                }
            }
        }
        if (isset($search_dynamic_algo) && count($search_dynamic_algo) > 0) {
            foreach ($search_dynamic_algo as $dynamic) {
                $module->push(['title' => $dynamic['name'], "type" => "Dynamic", "icon" => "algorithm.svg", 'media' => $dynamic['media'], "link" => "AlgorithmList", 'id' => $dynamic['id']]);
            }
        }
        $master_search['modules'] = isset($module) && count($module) > 0 ? $module : [];
        $success = true;
        return ['status' => $success, 'data' => $master_search, 'code' => 200];
    }

    public function getSubModuleSearch(Request $request)
    {
        $user = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id', 'state_id', 'country_id']);
        $search_text = $request['search_text'];

        $search_dynamic_algo = DynamicAlgorithm::with(['media', 'parent', 'parent.media'])->where('activated', 1)->where(DB::raw('lower(title)'), 'like', '%' . strtolower($search_text) . '%')->orWhere(DB::raw('lower(description)'), 'like', '%' . strtolower($search_text) . '%')->get(['id', 'title', 'parent_id', 'description', 'node_type', 'master_node_id', 'state_id', 'cadre_id']);

        $search_case_definition = CaseDefinition::with(['media', 'parent', 'parent.media'])->where('activated', 1)->where(DB::raw('lower(title)'), 'like', '%' . strtolower($search_text) . '%')->orWhere(DB::raw('lower(description)'), 'like', '%' . strtolower($search_text) . '%')->get(['id', 'title', 'parent_id', 'description', 'node_type', 'master_node_id', 'state_id', 'cadre_id']);

        $search_diagnoses_algorithm = DiagnosesAlgorithm::with(['media', 'parent', 'parent.media'])->where('activated', 1)->where(DB::raw('lower(title)'), 'like', '%' . strtolower($search_text) . '%')->orWhere(DB::raw('lower(description)'), 'like', '%' . strtolower($search_text) . '%')->get(['id', 'title', 'parent_id', 'description', 'node_type', 'master_node_id', 'state_id', 'cadre_id']);

        $search_guidance_on_adr_algorithm = GuidanceOnAdverseDrugReaction::with(['media', 'parent', 'parent.media'])->where('activated', 1)->where(DB::raw('lower(title)'), 'like', '%' . strtolower($search_text) . '%')->orWhere(DB::raw('lower(description)'), 'like', '%' . strtolower($search_text) . '%')->get(['id', 'title', 'parent_id', 'description', 'node_type', 'master_node_id', 'state_id', 'cadre_id']);

        $search_treatment_algorithm = TreatmentAlgorithm::with(['media', 'parent', 'parent.media'])->where('activated', 1)->where(DB::raw('lower(title)'), 'like', '%' . strtolower($search_text) . '%')->orWhere(DB::raw('lower(description)'), 'like', '%' . strtolower($search_text) . '%')->get(['id', 'title', 'parent_id', 'description', 'node_type', 'master_node_id', 'state_id', 'cadre_id']);

        $search_latent_tb_algorithm = LatentTbInfection::with(['media', 'parent', 'parent.media'])->where('activated', 1)->where(DB::raw('lower(title)'), 'like', '%' . strtolower($search_text) . '%')->orWhere(DB::raw('lower(description)'), 'like', '%' . strtolower($search_text) . '%')->get(['id', 'title', 'parent_id', 'description', 'node_type', 'master_node_id', 'state_id', 'cadre_id']);

        $search_differential_algorithm = DifferentialCareAlgorithm::with(['media', 'parent', 'parent.media'])->where('activated', 1)->where(DB::raw('lower(title)'), 'like', '%' . strtolower($search_text) . '%')->orWhere(DB::raw('lower(description)'), 'like', '%' . strtolower($search_text) . '%')->get(['id', 'title', 'parent_id', 'description', 'node_type', 'master_node_id', 'state_id', 'cadre_id']);

        $search_cgc_algorithm = CgcInterventionsAlgorithm::with(['media', 'parent', 'parent.media'])->where('activated', 1)->where(DB::raw('lower(title)'), 'like', '%' . strtolower($search_text) . '%')->orWhere(DB::raw('lower(description)'), 'like', '%' . strtolower($search_text) . '%')->get(['id', 'title', 'parent_id', 'description', 'node_type', 'master_node_id', 'state_id', 'cadre_id']);

        $sub_modules = collect();

        if (isset($search_dynamic_algo) && count($search_dynamic_algo) > 0) {
            foreach ($search_dynamic_algo as $dynamic_algo) {

                if (isset($dynamic_algo->parent) && $dynamic_algo->parent != null) {
                    if ($user[0]['state_id'] == 0) { //for india user

                        if (in_array($user[0]['cadre_id'], explode(',', $dynamic_algo->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($dynamic_algo->parent) && $dynamic_algo->parent != null ? $dynamic_algo->parent->id : $dynamic_algo->id, 'title' => isset($dynamic_algo->parent) && $dynamic_algo->parent != null ? $dynamic_algo->parent->title : $dynamic_algo->title, 'module' => 'Dynamic', 'media' => isset($dynamic_algo->parent) &&  $dynamic_algo->parent != null ? $dynamic_algo->parent->getFirstMediaPath('node_icon') : $dynamic_algo->getFirstMediaPath('node_icon'), 'description' => $dynamic_algo->description, 'node_type' => $dynamic_algo->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $dynamic_algo->parent->state_id)) && in_array($user[0]['cadre_id'], explode(',', $dynamic_algo->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($dynamic_algo->parent) && $dynamic_algo->parent != null ? $dynamic_algo->parent->id : $dynamic_algo->id, 'title' => isset($dynamic_algo->parent) && $dynamic_algo->parent != null ? $dynamic_algo->parent->title : $dynamic_algo->title, 'module' => 'Dynamic', 'media' => isset($dynamic_algo->parent) &&  $dynamic_algo->parent != null ? $dynamic_algo->parent->getFirstMediaPath('node_icon') : $dynamic_algo->getFirstMediaPath('node_icon'), 'description' => $dynamic_algo->description, 'node_type' => $dynamic_algo->node_type]);
                        }
                    }
                } else {
                    if ($user[0]['state_id'] == 0) { //for india user
                        if (in_array($user[0]['cadre_id'], explode(',', $dynamic_algo->cadre_id))) {
                            $sub_modules->push(['id' => isset($dynamic_algo->parent) && $dynamic_algo->parent != null ? $dynamic_algo->parent->id : $dynamic_algo->id, 'title' => isset($dynamic_algo->parent) && $dynamic_algo->parent != null ? $dynamic_algo->parent->title : $dynamic_algo->title, 'module' => 'Dynamic', 'media' => isset($dynamic_algo->parent) &&  $dynamic_algo->parent != null ? $dynamic_algo->parent->getFirstMediaPath('node_icon') : $dynamic_algo->getFirstMediaPath('node_icon'), 'description' => $dynamic_algo->description, 'node_type' => $dynamic_algo->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $dynamic_algo->state_id)) && in_array($user[0]['cadre_id'], explode(',', $dynamic_algo->cadre_id))) {
                            $sub_modules->push(['id' => isset($dynamic_algo->parent) && $dynamic_algo->parent != null ? $dynamic_algo->parent->id : $dynamic_algo->id, 'title' => isset($dynamic_algo->parent) && $dynamic_algo->parent != null ? $dynamic_algo->parent->title : $dynamic_algo->title, 'module' => 'Dynamic', 'media' => isset($dynamic_algo->parent) &&  $dynamic_algo->parent != null ? $dynamic_algo->parent->getFirstMediaPath('node_icon') : $dynamic_algo->getFirstMediaPath('node_icon'), 'description' => $dynamic_algo->description, 'node_type' => $dynamic_algo->node_type]);
                        }
                    }
                }
            }
        }
        if (isset($search_case_definition) && count($search_case_definition) > 0) {
            foreach ($search_case_definition as $case_algo) {
                $case_definition = CaseDefinition::with(['media'])->where('id', $case_algo->id)->get()[0];
                if (isset($case_algo->parent) && $case_algo->parent != null) {

                    if ($user[0]['state_id'] == 0) { //for india user

                        if (in_array($user[0]['cadre_id'], explode(',', $case_algo->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($case_algo->parent) && $case_algo->parent != null ? $case_algo->parent->id : $case_algo->id, 'title' => isset($case_algo->parent) && $case_algo->parent != null ? $case_algo->parent->title : $case_algo->title, 'module' => 'Case Definition', 'media' => isset($case_algo->parent) &&  $case_algo->parent != null ? $case_algo->parent->getFirstMediaPath('node_icon') : $case_algo->getFirstMediaPath('node_icon'), 'description' => $case_algo->description, 'node_type' => $case_algo->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $case_algo->parent->state_id)) && in_array($user[0]['cadre_id'], explode(',', $case_algo->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($case_algo->parent) && $case_algo->parent != null ? $case_algo->parent->id : $case_algo->id, 'title' => isset($case_algo->parent) && $case_algo->parent != null ? $case_algo->parent->title : $case_algo->title, 'module' => 'Case Definition', 'media' => isset($case_algo->parent) &&  $case_algo->parent != null ? $case_algo->parent->getFirstMediaPath('node_icon') : $case_algo->getFirstMediaPath('node_icon'), 'description' => $case_algo->description, 'node_type' => $case_algo->node_type]);
                        }
                    }
                } else {
                    if ($user[0]['state_id'] == 0) { //for india user
                        if (in_array($user[0]['cadre_id'], explode(',', $case_algo->cadre_id))) {
                            $sub_modules->push(['id' => isset($case_algo->parent) && $case_algo->parent != null ? $case_algo->parent->id : $case_algo->id, 'title' => isset($case_algo->parent) && $case_algo->parent != null ? $case_algo->parent->title : $case_algo->title, 'module' => 'Case Definition', 'media' => isset($case_algo->parent) &&  $case_algo->parent != null ? $case_algo->parent->getFirstMediaPath('node_icon') : $case_algo->getFirstMediaPath('node_icon'), 'description' => $case_algo->description, 'node_type' => $case_algo->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $case_algo->state_id)) && in_array($user[0]['cadre_id'], explode(',', $case_algo->cadre_id))) {
                            $sub_modules->push(['id' => isset($case_algo->parent) && $case_algo->parent != null ? $case_algo->parent->id : $case_algo->id, 'title' => isset($case_algo->parent) && $case_algo->parent != null ? $case_algo->parent->title : $case_algo->title, 'module' => 'Case Definition', 'media' => isset($case_algo->parent) &&  $case_algo->parent != null ? $case_algo->parent->getFirstMediaPath('node_icon') : $case_algo->getFirstMediaPath('node_icon'), 'description' => $case_algo->description, 'node_type' => $case_algo->node_type]);
                        }
                    }
                }
            }
        }
        if (isset($search_diagnoses_algorithm) && count($search_diagnoses_algorithm) > 0) {
            foreach ($search_diagnoses_algorithm as $diagnoses_algo) {
                if (isset($diagnoses_algo->parent) && $diagnoses_algo->parent != null) {
                    if ($user[0]['state_id'] == 0) { //for india user

                        if (in_array($user[0]['cadre_id'], explode(',', $diagnoses_algo->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($diagnoses_algo->parent) && $diagnoses_algo->parent != null ? $diagnoses_algo->parent->id : $diagnoses_algo->id, 'title' => isset($diagnoses_algo->parent) && $diagnoses_algo->parent != null ? $diagnoses_algo->parent->title : $diagnoses_algo->title, 'module' => 'Diagnosis Algorithm', 'media' => isset($diagnoses_algo->parent) &&  $diagnoses_algo->parent != null ? $diagnoses_algo->parent->getFirstMediaPath('node_icon') : $diagnoses_algo->getFirstMediaPath('node_icon'), 'description' => $diagnoses_algo->description, 'node_type' => $diagnoses_algo->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $diagnoses_algo->parent->state_id)) && in_array($user[0]['cadre_id'], explode(',', $diagnoses_algo->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($diagnoses_algo->parent) && $diagnoses_algo->parent != null ? $diagnoses_algo->parent->id : $diagnoses_algo->id, 'title' => isset($diagnoses_algo->parent) && $diagnoses_algo->parent != null ? $diagnoses_algo->parent->title : $diagnoses_algo->title, 'module' => 'Diagnosis Algorithm', 'media' => isset($diagnoses_algo->parent) &&  $diagnoses_algo->parent != null ? $diagnoses_algo->parent->getFirstMediaPath('node_icon') : $diagnoses_algo->getFirstMediaPath('node_icon'), 'description' => $diagnoses_algo->description, 'node_type' => $diagnoses_algo->node_type]);
                        }
                    }
                } else {
                    if ($user[0]['state_id'] == 0) { //for india user
                        if (in_array($user[0]['cadre_id'], explode(',', $diagnoses_algo->cadre_id))) {
                            $sub_modules->push(['id' => isset($diagnoses_algo->parent) && $diagnoses_algo->parent != null ? $diagnoses_algo->parent->id : $diagnoses_algo->id, 'title' => isset($diagnoses_algo->parent) && $diagnoses_algo->parent != null ? $diagnoses_algo->parent->title : $diagnoses_algo->title, 'module' => 'Diagnosis Algorithm', 'media' => isset($diagnoses_algo->parent) &&  $diagnoses_algo->parent != null ? $diagnoses_algo->parent->getFirstMediaPath('node_icon') : $diagnoses_algo->getFirstMediaPath('node_icon'), 'description' => $diagnoses_algo->description, 'node_type' => $diagnoses_algo->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $diagnoses_algo->state_id)) && in_array($user[0]['cadre_id'], explode(',', $diagnoses_algo->cadre_id))) {
                            $sub_modules->push(['id' => isset($diagnoses_algo->parent) && $diagnoses_algo->parent != null ? $diagnoses_algo->parent->id : $diagnoses_algo->id, 'title' => isset($diagnoses_algo->parent) && $diagnoses_algo->parent != null ? $diagnoses_algo->parent->title : $diagnoses_algo->title, 'module' => 'Diagnosis Algorithm', 'media' => isset($diagnoses_algo->parent) &&  $diagnoses_algo->parent != null ? $diagnoses_algo->parent->getFirstMediaPath('node_icon') : $diagnoses_algo->getFirstMediaPath('node_icon'), 'description' => $diagnoses_algo->description, 'node_type' => $diagnoses_algo->node_type]);
                        }
                    }
                }
            }
        }
        if (isset($search_guidance_on_adr_algorithm) && count($search_guidance_on_adr_algorithm) > 0) {
            foreach ($search_guidance_on_adr_algorithm as  $guidance_algo) {
                if (isset($guidance_algo->parent) && $guidance_algo->parent != null) {
                    if ($user[0]['state_id'] == 0) { //for india user

                        if (in_array($user[0]['cadre_id'], explode(',', $guidance_algo->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($guidance_algo->parent) && $guidance_algo->parent != null ? $guidance_algo->parent->id : $guidance_algo->id, 'title' => isset($guidance_algo->parent) && $guidance_algo->parent != null ? $guidance_algo->parent->title : $guidance_algo->title, 'module' => 'Guidance on ADR', 'media' => isset($guidance_algo->parent) &&  $guidance_algo->parent != null ? $guidance_algo->parent->getFirstMediaPath('node_icon') : $guidance_algo->getFirstMediaPath('node_icon'), 'description' => $guidance_algo->description, 'node_type' => $guidance_algo->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $guidance_algo->parent->state_id)) && in_array($user[0]['cadre_id'], explode(',', $guidance_algo->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($guidance_algo->parent) && $guidance_algo->parent != null ? $guidance_algo->parent->id : $guidance_algo->id, 'title' => isset($guidance_algo->parent) && $guidance_algo->parent != null ? $guidance_algo->parent->title : $guidance_algo->title, 'module' => 'Guidance on ADR', 'media' => isset($guidance_algo->parent) &&  $guidance_algo->parent != null ? $guidance_algo->parent->getFirstMediaPath('node_icon') : $guidance_algo->getFirstMediaPath('node_icon'), 'description' => $guidance_algo->description, 'node_type' => $guidance_algo->node_type]);
                        }
                    }
                } else {
                    if ($user[0]['state_id'] == 0) { //for india user
                        if (in_array($user[0]['cadre_id'], explode(',', $guidance_algo->cadre_id))) {
                            $sub_modules->push(['id' => isset($guidance_algo->parent) && $guidance_algo->parent != null ? $guidance_algo->parent->id : $guidance_algo->id, 'title' => isset($guidance_algo->parent) && $guidance_algo->parent != null ? $guidance_algo->parent->title : $guidance_algo->title, 'module' => 'Guidance on ADR', 'media' => isset($guidance_algo->parent) &&  $guidance_algo->parent != null ? $guidance_algo->parent->getFirstMediaPath('node_icon') : $guidance_algo->getFirstMediaPath('node_icon'), 'description' => $guidance_algo->description, 'node_type' => $guidance_algo->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $guidance_algo->state_id)) && in_array($user[0]['cadre_id'], explode(',', $guidance_algo->cadre_id))) {
                            $sub_modules->push(['id' => isset($guidance_algo->parent) && $guidance_algo->parent != null ? $guidance_algo->parent->id : $guidance_algo->id, 'title' => isset($guidance_algo->parent) && $guidance_algo->parent != null ? $guidance_algo->parent->title : $guidance_algo->title, 'module' => 'Guidance on ADR', 'media' => isset($guidance_algo->parent) &&  $guidance_algo->parent != null ? $guidance_algo->parent->getFirstMediaPath('node_icon') : $guidance_algo->getFirstMediaPath('node_icon'), 'description' => $guidance_algo->description, 'node_type' => $guidance_algo->node_type]);
                        }
                    }
                }
            }
        }
        if (isset($search_treatment_algorithm) && count($search_treatment_algorithm) > 0) {
            foreach ($search_treatment_algorithm as $tratment_algo) {

                if (isset($tratment_algo->parent) && $tratment_algo->parent != null) {
                    if ($user[0]['state_id'] == 0) { //for india user

                        if (in_array($user[0]['cadre_id'], explode(',', $tratment_algo->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($tratment_algo->parent) && $tratment_algo->parent != null ? $tratment_algo->parent->id : $tratment_algo->id, 'title' => isset($tratment_algo->parent) && $tratment_algo->parent != null ? $tratment_algo->parent->title : $tratment_algo->title, 'module' => 'Treatment Algorithm', 'media' =>  isset($tratment_algo->parent) &&  $tratment_algo->parent != null ? $tratment_algo->parent->getFirstMediaPath('node_icon') : $tratment_algo->getFirstMediaPath('node_icon'), 'description' => $tratment_algo->description, 'node_type' => $tratment_algo->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $tratment_algo->parent->state_id)) && in_array($user[0]['cadre_id'], explode(',', $tratment_algo->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($tratment_algo->parent) && $tratment_algo->parent != null ? $tratment_algo->parent->id : $tratment_algo->id, 'title' => isset($tratment_algo->parent) && $tratment_algo->parent != null ? $tratment_algo->parent->title : $tratment_algo->title, 'module' => 'Treatment Algorithm', 'media' =>  isset($tratment_algo->parent) &&  $tratment_algo->parent != null ? $tratment_algo->parent->getFirstMediaPath('node_icon') : $tratment_algo->getFirstMediaPath('node_icon'), 'description' => $tratment_algo->description, 'node_type' => $tratment_algo->node_type]);
                        }
                    }
                } else {
                    if ($user[0]['state_id'] == 0) { //for india user
                        if (in_array($user[0]['cadre_id'], explode(',', $tratment_algo->cadre_id))) {
                            $sub_modules->push(['id' => isset($tratment_algo->parent) && $tratment_algo->parent != null ? $tratment_algo->parent->id : $tratment_algo->id, 'title' => isset($tratment_algo->parent) && $tratment_algo->parent != null ? $tratment_algo->parent->title : $tratment_algo->title, 'module' => 'Treatment Algorithm', 'media' =>  isset($tratment_algo->parent) &&  $tratment_algo->parent != null ? $tratment_algo->parent->getFirstMediaPath('node_icon') : $tratment_algo->getFirstMediaPath('node_icon'), 'description' => $tratment_algo->description, 'node_type' => $tratment_algo->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $tratment_algo->state_id)) && in_array($user[0]['cadre_id'], explode(',', $tratment_algo->cadre_id))) {
                            $sub_modules->push(['id' => isset($tratment_algo->parent) && $tratment_algo->parent != null ? $tratment_algo->parent->id : $tratment_algo->id, 'title' => isset($tratment_algo->parent) && $tratment_algo->parent != null ? $tratment_algo->parent->title : $tratment_algo->title, 'module' => 'Treatment Algorithm', 'media' =>  isset($tratment_algo->parent) &&  $tratment_algo->parent != null ? $tratment_algo->parent->getFirstMediaPath('node_icon') : $tratment_algo->getFirstMediaPath('node_icon'), 'description' => $tratment_algo->description, 'node_type' => $tratment_algo->node_type]);
                        }
                    }
                }
            }
        }
        if (isset($search_latent_tb_algorithm) && count($search_latent_tb_algorithm) > 0) {
            foreach ($search_latent_tb_algorithm as $latent_algo) {

                if (isset($latent_algo->parent) && $latent_algo->parent != null) {
                    if ($user[0]['state_id'] == 0) { //for india user

                        if (in_array($user[0]['cadre_id'], explode(',', $latent_algo->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($latent_algo->parent) && $latent_algo->parent != null ? $latent_algo->parent->id : $latent_algo->id, 'title' => isset($latent_algo->parent) && $latent_algo->parent != null ? $latent_algo->parent->title : $latent_algo->title, 'module' => 'Latent TB Infection', 'media' => isset($latent_algo->parent) &&  $latent_algo->parent != null ? $latent_algo->parent->getFirstMediaPath('node_icon') : $latent_algo->getFirstMediaPath('node_icon'), 'description' => $latent_algo->description, 'node_type' => $latent_algo->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $latent_algo->parent->state_id)) && in_array($user[0]['cadre_id'], explode(',', $latent_algo->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($latent_algo->parent) && $latent_algo->parent != null ? $latent_algo->parent->id : $latent_algo->id, 'title' => isset($latent_algo->parent) && $latent_algo->parent != null ? $latent_algo->parent->title : $latent_algo->title, 'module' => 'Latent TB Infection', 'media' => isset($latent_algo->parent) &&  $latent_algo->parent != null ? $latent_algo->parent->getFirstMediaPath('node_icon') : $latent_algo->getFirstMediaPath('node_icon'), 'description' => $latent_algo->description, 'node_type' => $latent_algo->node_type]);
                        }
                    }
                } else {
                    if ($user[0]['state_id'] == 0) { //for india user
                        if (in_array($user[0]['cadre_id'], explode(',', $latent_algo->cadre_id))) {
                            $sub_modules->push(['id' => isset($latent_algo->parent) && $latent_algo->parent != null ? $latent_algo->parent->id : $latent_algo->id, 'title' => isset($latent_algo->parent) && $latent_algo->parent != null ? $latent_algo->parent->title : $latent_algo->title, 'module' => 'Latent TB Infection', 'media' => isset($latent_algo->parent) &&  $latent_algo->parent != null ? $latent_algo->parent->getFirstMediaPath('node_icon') : $latent_algo->getFirstMediaPath('node_icon'), 'description' => $latent_algo->description, 'node_type' => $latent_algo->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $latent_algo->state_id)) && in_array($user[0]['cadre_id'], explode(',', $latent_algo->cadre_id))) {
                            $sub_modules->push(['id' => isset($latent_algo->parent) && $latent_algo->parent != null ? $latent_algo->parent->id : $latent_algo->id, 'title' => isset($latent_algo->parent) && $latent_algo->parent != null ? $latent_algo->parent->title : $latent_algo->title, 'module' => 'Latent TB Infection', 'media' => isset($latent_algo->parent) &&  $latent_algo->parent != null ? $latent_algo->parent->getFirstMediaPath('node_icon') : $latent_algo->getFirstMediaPath('node_icon'), 'description' => $latent_algo->description, 'node_type' => $latent_algo->node_type]);
                        }
                    }
                }
            }
        }
        if (isset($search_differential_algorithm) && count($search_differential_algorithm) > 0) {
            foreach ($search_differential_algorithm as $diff_algo) {

                if (isset($diff_algo->parent) && $diff_algo->parent != null) {
                    if ($user[0]['state_id'] == 0) { //for india user

                        if (in_array($user[0]['cadre_id'], explode(',', $diff_algo->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($diff_algo->parent) && $diff_algo->parent != null ? $diff_algo->parent->id : $diff_algo->id, 'title' => isset($diff_algo->parent) && $diff_algo->parent != null ? $diff_algo->parent->title : $diff_algo->title, 'module' => 'Differentiated Care Of TB Patients', 'media' => isset($diff_algo->parent) &&  $diff_algo->parent != null ? $diff_algo->parent->getFirstMediaPath('node_icon') : $diff_algo->getFirstMediaPath('node_icon'), 'description' => $diff_algo->description, 'node_type' => $diff_algo->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $diff_algo->parent->state_id)) && in_array($user[0]['cadre_id'], explode(',', $diff_algo->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($diff_algo->parent) && $diff_algo->parent != null ? $diff_algo->parent->id : $diff_algo->id, 'title' => isset($diff_algo->parent) && $diff_algo->parent != null ? $diff_algo->parent->title : $diff_algo->title, 'module' => 'Differentiated Care Of TB Patients', 'media' => isset($diff_algo->parent) &&  $diff_algo->parent != null ? $diff_algo->parent->getFirstMediaPath('node_icon') : $diff_algo->getFirstMediaPath('node_icon'), 'description' => $diff_algo->description, 'node_type' => $diff_algo->node_type]);
                        }
                    }
                } else {
                    if ($user[0]['state_id'] == 0) { //for india user
                        if (in_array($user[0]['cadre_id'], explode(',', $diff_algo->cadre_id))) {
                            $sub_modules->push(['id' => isset($diff_algo->parent) && $diff_algo->parent != null ? $diff_algo->parent->id : $diff_algo->id, 'title' => isset($diff_algo->parent) && $diff_algo->parent != null ? $diff_algo->parent->title : $diff_algo->title, 'module' => 'Differentiated Care Of TB Patients', 'media' => isset($diff_algo->parent) &&  $diff_algo->parent != null ? $diff_algo->parent->getFirstMediaPath('node_icon') : $diff_algo->getFirstMediaPath('node_icon'), 'description' => $diff_algo->description, 'node_type' => $diff_algo->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $diff_algo->state_id)) && in_array($user[0]['cadre_id'], explode(',', $diff_algo->cadre_id))) {
                            $sub_modules->push(['id' => isset($diff_algo->parent) && $diff_algo->parent != null ? $diff_algo->parent->id : $diff_algo->id, 'title' => isset($diff_algo->parent) && $diff_algo->parent != null ? $diff_algo->parent->title : $diff_algo->title, 'module' => 'Differentiated Care Of TB Patients', 'media' => isset($diff_algo->parent) &&  $diff_algo->parent != null ? $diff_algo->parent->getFirstMediaPath('node_icon') : $diff_algo->getFirstMediaPath('node_icon'), 'description' => $diff_algo->description, 'node_type' => $diff_algo->node_type]);
                        }
                    }
                }
            }
        }
        if (isset($search_cgc_algorithm) && count($search_cgc_algorithm) > 0) {
            foreach ($search_cgc_algorithm as $ntep) {

                if (isset($ntep->parent) && $ntep->parent != null) {
                    if ($user[0]['state_id'] == 0) { //for india user

                        if (in_array($user[0]['cadre_id'], explode(',', $ntep->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($ntep->parent) && $ntep->parent != null ? $ntep->parent->id : $ntep->id, 'title' => isset($ntep->parent) && $ntep->parent != null ? $ntep->parent->title : $ntep->title, 'module' => 'CGC', 'media' => isset($ntep->parent) &&  $ntep->parent != null ? $ntep->parent->getFirstMediaPath('node_icon') : $ntep->getFirstMediaPath('node_icon'), 'description' => $ntep->description, 'node_type' => $ntep->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $ntep->parent->state_id)) && in_array($user[0]['cadre_id'], explode(',', $ntep->parent->cadre_id))) {
                            $sub_modules->push(['id' => isset($ntep->parent) && $ntep->parent != null ? $ntep->parent->id : $ntep->id, 'title' => isset($ntep->parent) && $ntep->parent != null ? $ntep->parent->title : $ntep->title, 'module' => 'CGC', 'media' => isset($ntep->parent) &&  $ntep->parent != null ? $ntep->parent->getFirstMediaPath('node_icon') : $ntep->getFirstMediaPath('node_icon'), 'description' => $ntep->description, 'node_type' => $ntep->node_type]);
                        }
                    }
                } else {
                    if ($user[0]['state_id'] == 0) { //for india user
                        if (in_array($user[0]['cadre_id'], explode(',', $ntep->cadre_id))) {
                            $sub_modules->push(['id' => isset($ntep->parent) && $ntep->parent != null ? $ntep->parent->id : $ntep->id, 'title' => isset($ntep->parent) && $ntep->parent != null ? $ntep->parent->title : $ntep->title, 'module' => 'CGC', 'media' => isset($ntep->parent) &&  $ntep->parent != null ? $ntep->parent->getFirstMediaPath('node_icon') : $ntep->getFirstMediaPath('node_icon'), 'description' => $ntep->description, 'node_type' => $ntep->node_type]);
                        }
                    } else {
                        if (in_array($user[0]['state_id'], explode(',', $ntep->state_id)) && in_array($user[0]['cadre_id'], explode(',', $ntep->cadre_id))) {
                            $sub_modules->push(['id' => isset($ntep->parent) && $ntep->parent != null ? $ntep->parent->id : $ntep->id, 'title' => isset($ntep->parent) && $ntep->parent != null ? $ntep->parent->title : $ntep->title, 'module' => 'CGC', 'media' => isset($ntep->parent) &&  $ntep->parent != null ? $ntep->parent->getFirstMediaPath('node_icon') : $ntep->getFirstMediaPath('node_icon'), 'description' => $ntep->description, 'node_type' => $ntep->node_type]);
                        }
                    }
                }
            }
        }
        $master_search['sub_modules'] = isset($sub_modules) && count($sub_modules) > 0 ? $sub_modules->unique('title')->values() : [];
        $success = true;
        return ['status' => $success, 'data' => $master_search, 'code' => 200];
    }

    public function getResourceMaterialSearch(Request $request)
    {
        $data = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id', 'state_id', 'country_id']);
        $search_text = $request['search_text'];
        $search_resource_material = ResourceMaterial::with(['media'])->whereRaw("find_in_set('" . $data[0]['cadre_id'] . "',cadre)")
            ->whereRaw("find_in_set('" . $data[0]['country_id'] . "',country_id)")->where(DB::raw('lower(title)'), 'like', '%' . strtolower($search_text) . '%')->get(['id', 'title', 'type_of_materials', 'parent_id', 'icon_type']);

        $master_search['resource_material'] = isset($search_resource_material) && count($search_resource_material) > 0 ? $search_resource_material : [];

        $success = true;
        return ['status' => $success, 'data' => $master_search, 'code' => 200];
    }

    public function getChatQuestionSearch(Request $request)
    {
        $data = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id', 'state_id', 'country_id']);
        $search_text = $request['search_text'];
        $search_chat_question = ChatQuestion::whereRaw("find_in_set('" . $data[0]['cadre_id'] . "',cadre_id)")
            ->where(DB::raw('lower(question)'), 'like', '%' . strtolower($search_text) . '%')->orWhere(DB::raw('lower(answer)'), 'like', '%' . strtolower($search_text) . '%')->get(['id', 'question', 'answer']);
        $master_search['chat_question'] = isset($search_chat_question) && count($search_chat_question) > 0 ?  $search_chat_question : [];
        $success = true;
        return ['status' => $success, 'data' => $master_search, 'code' => 200];
    }
}
