<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\DynamicAlgoMaster;
use App\Models\FlashNews;
use App\Models\FlashSimilarApp;
use App\Models\ResourceMaterial;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;
use DB;

class FlashNewsController extends BaseController
{
    public function getAllFlashNews()
    {
        $flash_news = FlashNews::with(['media'])->where('active', 1)->orderBy('order_index', 'desc')->get();
        $success = true;
        return ['status' => $success, 'data' => $flash_news, 'code' => 200];
    }

    public function getSimilarApps()
    {
        $similar_apps = FlashSimilarApp::with(['media'])->where('active', 1)->orderBy('order_index')->get();
        $success = true;
        return ['status' => $success, 'data' => $similar_apps, 'code' => 200];
    }

    public function getAllHomeData(Request $request)
    {
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'state_id', 'cadre_id', 'country_id'])[0];
        $homeData = [];
        $most_usefull_module = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
            on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.user_id = $subscriber->id
            GROUP BY action ORDER BY count(*) DESC  LIMIT 4");
        $recent_used = collect([]);
        foreach ($most_usefull_module as $module) {
            if ($module->action == "module_case_defintion") {
                $recent_used->push(['title' => "Case Definition", "type" => "Case Definition", "icon" => "learning.svg", "link" => "AlgorithmList", "activityType" => "module_case_defintion"]);
            }
            if ($module->action == "module_screening_tool") {
                $recent_used->push(['title' => "Screening Tool", "type" => "Screening", "icon" => "checking-tool.svg", "link" => "Screening", "activityType" => "module_screening_tool"]);
            }
            if ($module->action == "module_differentiated_care_tb_patient") {
                $recent_used->push(['title' => "Differentiated Care Of TB Patients", "type" => "Differentiated Care Of TB Patients", "icon" => "algorithm.svg", "link" => "AlgorithmList", "activityType" => "module_differentiated_care_tb_patient"]);
            }
            if ($module->action == "module_past_assessments") {
                $recent_used->push(['title' => "Past Assessment", "type" => "Past Assessment", "icon" => "PastAss", "link" => "PastAssessments", "activityType" => "module_past_assessments"]);
            }
            if ($module->action == "module_current_assessments") {
                $recent_used->push(['title' => "Current Assessment", "type" => "Assessment", "icon" => "Ass", "link" => "CurrentAssessments", "activityType" => "module_current_assessments"]);
            }
            if ($module->action == "module_latent_tb") {
                $recent_used->push(['title' => "TB Preventive Treatment", "type" => "Latent TB Infection", "icon" => "latent-tb.svg", "link" => "AlgorithmList", "activityType" => "module_latent_tb"]);
            }
            if ($module->action == "module_guidance_on_adr") {
                $recent_used->push(['title' => "Guidance on ADR", "type" => "Guidance on ADR", "icon" => "adr.svg", "type" => "Guidance on ADR", "activityType" => "module_guidance_on_adr"]);
            }
            if ($module->action == "module_Referral-Health Facility") {
                $recent_used->push(['title' => "Referral-Health Facility", "type" => "referral-health", "icon" => "hospital", "link" => "ReferralHealthFacility", "activityType" => "module_Referral-Health Facility"]);
            }
            if ($module->action == "module_treatment_care_cascade") {
                $recent_used->push(['title' => "Treatment Care Cascade", "type" => "Treatment Algorithm", "icon" => "treatment.svg", "link" => "AlgorithmList", "activityType" => "module_treatment_care_cascade"]);
            }
            if ($module->action == "module_cgc") {
                $recent_used->push(['title' => "NTEP Intervention", "type" => "CGC", "icon" => "algorithm.svg", "link" => "Algorithms", "activityType" => "CGC", "sectionKey" => "NTEP",]);
            }
            if ($module->action == "module_diagnostic_care_cascade") {
                $recent_used->push(['title' => "Diagnostic Care Cascade", "type" => "Diagnosis Algorithm", "icon" => "algorithm.svg", "link" => "AlgorithmList", "activityType" => "module_diagnostic_care_cascade"]);
            }
            if ($module->action == "module_Resource_Materials_videos") {
                $resource_ntep = ResourceMaterial::where('title', 'like', "%Videos%")->get(['id', 'title']);
                $module->push(['title' => $resource_ntep[0]['title'], "type" => "video", "icon" => "video", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_video", 'id' => $resource_ntep[0]['id']]);
            }
            if ($module->action == "module_Resource_Materials_document") {
                $resource_ntep = ResourceMaterial::where('title', 'like', "%Documents%")->get(['id', 'title']);
                $module->push(['title' => $resource_ntep[0]['title'], "type" => "document", "icon" => "document", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_document", 'id' => $resource_ntep[0]['id']]);
            }
            if ($module->action == "module_Resource_Materials_ppt") {
                $resource_ntep = ResourceMaterial::where('title', 'like', "%Presentations%")->get(['id', 'title']);
                $module->push(['title' => $resource_ntep[0]['title'], "type" => "ppt", "icon" => "ppt", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_ppt", 'id' => $resource_ntep[0]['id']]);
            }
            if ($module->action == "module_Resource_Materials_pdf_office_orders") {
                $resource_ntep = ResourceMaterial::where('title', 'like', "%Office Orders%")->get(['id', 'title']);
                $module->push(['title' => $resource_ntep[0]['title'], "type" => "pdf", "icon" => "pdf", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_pdf", 'id' => $resource_ntep[0]['id']]);
            }
            if ($module->action == "module_Resource_Materials_image") {
                $resource_ntep = ResourceMaterial::where('title', 'like', "%Others%")->get(['id', 'title']);
                $module->push(['title' => $resource_ntep[0]['title'], "type" => "image", "icon" => "image", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_image", 'id' => $resource_ntep[0]['id']]);
            }
            if ($module->action == "module_Resource_Materials_NTEP Guidelines") {
                $resource_ntep = ResourceMaterial::where('title', 'like', "%NTEP Guidelines%")->get(['id', 'title']);
                $recent_used->push(['title' => $resource_ntep[0]['title'], "type" => "NTEP", "icon" => "NTEP", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_NTEP", 'id' => $resource_ntep[0]['id']]);
            }
        }
        if (isset($recent_used) && count($recent_used) == 0) {
            $recent_used->push(['title' => "Case Definition", "type" => "Case Definition", "icon" => "learning.svg", "link" => "AlgorithmList", "activityType" => "module_case_defintion"]);
            $recent_used->push(['title' => "Guidance on ADR", "type" => "Guidance on ADR", "icon" => "adr.svg", "type" => "Guidance on ADR", "activityType" => "module_guidance_on_adr"]);
            $recent_used->push(['title' => "Diagnostic Care Cascade", "type" => "Diagnosis Algorithm", "icon" => "algorithm.svg", "link" => "AlgorithmList", "activityType" => "module_diagnostic_care_cascade"]);
            $recent_used->push(['title' => "Treatment Care Cascade", "type" => "Treatment Algorithm", "icon" => "treatment.svg", "link" => "AlgorithmList", "activityType" => "module_treatment_care_cascade"]);
        }
        $flash_news = FlashNews::with(['media'])->where('active', 1)->orderBy('order_index', 'desc')->get();
        $similar_apps = FlashSimilarApp::with(['media'])->where('active', 1)->orderBy('order_index', 'desc')->get();

        if ($subscriber['state_id'] == 0) { //for india user
            $recetly_added_material = ResourceMaterial::with(['media'])->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre)")
                ->orWhereRaw("find_in_set('" . $subscriber['state_id'] . "',state)")->orderBy('created_at', 'desc')->limit(10)->get();
        } else {
            $recetly_added_material = ResourceMaterial::with(['media'])->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre)")
                ->whereRaw("find_in_set('" . $subscriber['state_id'] . "',state)")->orderBy('created_at', 'desc')->limit(10)->get();
        }

        $homeData['most_usefull'] = $recent_used;
        $homeData['flash_news'] = $flash_news;
        $homeData['similar_apps'] = $similar_apps;
        $homeData['recently_added'] = $recetly_added_material;

        $success = true;
        return ['status' => $success, 'data' => $homeData, 'code' => 200];
    }

    public function getModuleUsage(Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }

        app()->setLocale($lang);
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'state_id', 'cadre_id', 'country_id'])[0];
        $homeData = [];
        $most_usefull_module = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
            on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.user_id = $subscriber->id
            GROUP BY action ORDER BY count(*) DESC  LIMIT 4");
        $recent_used = collect([]);
        // getTranslationsForValues(["Case" "asd"],"en");
        // getTranslationForValue("asd","en");
        if (isset($recent_used) && count($recent_used) <= 3) {
            $recent_used = collect([]);
            $recent_used->push(['title' => $this->getTranslationForValue("Case Definition", $lang), "type" => "Case Definition", "icon" => "learning.svg", "link" => "AlgorithmList", "activityType" => "module_case_defintion"]);
            $recent_used->push(['title' => $this->getTranslationForValue("Guidance on ADR", $lang), "type" => "Guidance on ADR", "icon" => "adr.svg", "link" => "AlgorithmList", "type" => "Guidance on ADR", "activityType" => "module_guidance_on_adr"]);
            $recent_used->push(['title' => $this->getTranslationForValue("Diagnostic Care Cascade", $lang), "type" => "Diagnosis Algorithm", "icon" => "algorithm.svg", "link" => "AlgorithmList", "activityType" => "module_diagnostic_care_cascade"]);
            $recent_used->push(['title' => $this->getTranslationForValue("Treatment Care Cascade", $lang), "type" => "Treatment Algorithm", "icon" => "treatment.svg", "link" => "AlgorithmList", "activityType" => "module_treatment_care_cascade"]);
        } else {
            foreach ($most_usefull_module as $module) {
                if ($module->action == "module_case_defintion") {
                    $recent_used->push(['title' => $this->getTranslationForValue("Case Definition", $lang), "type" => "Case Definition", "icon" => "learning.svg", "link" => "AlgorithmList", "activityType" => "module_case_defintion"]);
                }
                if ($module->action == "module_screening_tool") {
                    $recent_used->push(['title' => $this->getTranslationForValue("Screening Tool", $lang), "type" => "Screening", "icon" => "checking-tool.svg", "link" => "Screening", "activityType" => "module_screening_tool"]);
                }
                if ($module->action == "module_differentiated_care_tb_patient") {
                    $recent_used->push(['title' => $this->getTranslationForValue("Differentiated Care Of TB Patients", $lang), "type" => "Differentiated Care Of TB Patients", "icon" => "algorithm.svg", "link" => "AlgorithmList", "activityType" => "module_differentiated_care_tb_patient"]);
                }
                if ($module->action == "module_past_assessments") {
                    $recent_used->push(['title' => $this->getTranslationForValue("Past Assessment", $lang), "type" => "Past Assessment", "icon" => "PastAss", "link" => "PastAssessments", "activityType" => "module_past_assessments"]);
                }
                if ($module->action == "module_current_assessments") {
                    $recent_used->push(['title' => $this->getTranslationForValue("Current Assessment", $lang), "type" => "Assessment", "icon" => "Ass", "link" => "CurrentAssessments", "activityType" => "module_current_assessments"]);
                }
                if ($module->action == "module_latent_tb") {
                    $recent_used->push(['title' => $this->getTranslationForValue("TB Preventive Treatment", $lang), "type" => "Latent TB Infection", "icon" => "latent-tb.svg", "link" => "AlgorithmList", "activityType" => "module_latent_tb"]);
                }
                if ($module->action == "module_guidance_on_adr") {
                    $recent_used->push(['title' => $this->getTranslationForValue("Guidance on ADR", $lang), "type" => "Guidance on ADR", "icon" => "adr.svg", "type" => "Guidance on ADR", "activityType" => "module_guidance_on_adr"]);
                }
                if ($module->action == "module_Referral-Health Facility") {
                    $recent_used->push(['title' => $this->getTranslationForValue("Referral-Health Facility", $lang), "type" => "referral-health", "icon" => "hospital", "link" => "ReferralHealthFacility", "activityType" => "module_Referral-Health Facility"]);
                }
                if ($module->action == "module_treatment_care_cascade") {
                    $recent_used->push(['title' => $this->getTranslationForValue("Treatment Care Cascade", $lang), "type" => "Treatment Algorithm", "icon" => "treatment.svg", "link" => "AlgorithmList", "activityType" => "module_treatment_care_cascade"]);
                }
                if ($module->action == "module_cgc") {
                    $recent_used->push(['title' => $this->getTranslationForValue("NTEP Intervention", $lang), "type" => "CGC", "icon" => "algorithm.svg", "link" => "Algorithms", "activityType" => "CGC", "sectionKey" => "NTEP",]);
                }
                if ($module->action == "module_diagnostic_care_cascade") {
                    $recent_used->push(['title' => $this->getTranslationForValue("Diagnostic Care Cascade", $lang), "type" => "Diagnosis Algorithm", "icon" => "algorithm.svg", "link" => "AlgorithmList", "activityType" => "module_diagnostic_care_cascade"]);
                }
                if ($module->action == "module_Resource_Materials_videos") {
                    $resource_ntep = ResourceMaterial::where('title', 'like', "%Videos%")->get(['id', 'title']);
                    $module->push(['title' => $this->getTranslationForValue($resource_ntep[0]['title'], $lang), "type" => "video", "icon" => "video", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_video", 'id' => $resource_ntep[0]['id']]);
                }
                if ($module->action == "module_Resource_Materials_document") {
                    $resource_ntep = ResourceMaterial::where('title', 'like', "%Documents%")->get(['id', 'title']);
                    $module->push(['title' => $this->getTranslationForValue($resource_ntep[0]['title'], $lang), "type" => "document", "icon" => "document", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_document", 'id' => $resource_ntep[0]['id']]);
                }
                if ($module->action == "module_Resource_Materials_ppt") {
                    $resource_ntep = ResourceMaterial::where('title', 'like', "%Presentations%")->get(['id', 'title']);
                    $module->push(['title' => $this->getTranslationForValue($resource_ntep[0]['title'], $lang), "type" => "ppt", "icon" => "ppt", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_ppt", 'id' => $resource_ntep[0]['id']]);
                }
                if ($module->action == "module_Resource_Materials_pdf_office_orders") {
                    $resource_ntep = ResourceMaterial::where('title', 'like', "%Office Orders%")->get(['id', 'title']);
                    $module->push(['title' => $this->getTranslationForValue($resource_ntep[0]['title'], $lang), "type" => "pdf", "icon" => "pdf", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_pdf", 'id' => $resource_ntep[0]['id']]);
                }
                if ($module->action == "module_Resource_Materials_image") {
                    $resource_ntep = ResourceMaterial::where('title', 'like', "%Others%")->get(['id', 'title']);
                    $module->push(['title' => $this->getTranslationForValue($resource_ntep[0]['title'], $lang), "type" => "image", "icon" => "image", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_image", 'id' => $resource_ntep[0]['id']]);
                }
                if ($module->action == "module_Resource_Materials_NTEP Guidelines") {
                    $resource_ntep = ResourceMaterial::where('title', 'like', "%NTEP Guidelines%")->get(['id', 'title']);
                    $recent_used->push(['title' => $this->getTranslationForValue($resource_ntep[0]['title'], $lang), "type" => "NTEP", "icon" => "NTEP", "link" => "ResourceMaterials", "activityType" => "module_Resource_Materials_NTEP", 'id' => $resource_ntep[0]['id']]);
                }
            }
        }




        $homeData['most_usefull'] = $recent_used;
        $success = true;
        return ['status' => $success, 'data' => $homeData, 'code' => 200];
    }

    public function getTranslationForValue($algo, $lang_key)
    {
        return GoogleTranslate::trans($algo, $lang_key);
    }

    public function getRecentlyAdded(Request $request)
    {
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'state_id', 'cadre_id', 'country_id'])[0];
        $recetly_added_material = collect([]);
        $dynamic_algorithms = DynamicAlgoMaster::with(['media'])->where('active', 1)->orderBy('created_at', 'desc')->limit(5)->get();
        if ($subscriber['state_id'] == 0) { //for india user
            $recetly_added_material = ResourceMaterial::with(['media'])->where('type_of_materials', '!=', 'folder')->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre)")
                // ->orWhereRaw("find_in_set('" . $subscriber['state_id'] . "',state)")->orderBy('created_at', 'desc')
                ->whereRaw("find_in_set('" . $subscriber['country_id'] . "',country_id)")->orderBy('created_at', 'desc')->limit(5)->get();
        } else {
            $recetly_added_material = ResourceMaterial::with(['media'])->where('type_of_materials', '!=', 'folder')->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre)")
                ->whereRaw("find_in_set('" . $subscriber['state_id'] . "',state)")->orderBy('created_at', 'desc')->limit(5)->get();
        }
        // array_push($homeData,$dynamic_algorithms);
        $combine_details = array_merge($recetly_added_material->toArray(), $dynamic_algorithms->toArray());
        $recetly_added_material = array_slice($combine_details, 0, 5);
        // $recetly_added_material->push($dynamic_algorithms);

        $success = true;
        return ['status' => $success, 'data' => $recetly_added_material, 'code' => 200];
    }
}
