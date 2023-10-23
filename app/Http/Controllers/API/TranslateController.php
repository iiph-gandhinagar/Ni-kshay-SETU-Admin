<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\AppConfig;
use App\Models\CaseDefinition;
use App\Models\CgcInterventionsAlgorithm;
use App\Models\ChatKeyword;
use App\Models\ChatQuestion;
use App\Models\DiagnosesAlgorithm;
use App\Models\DifferentialCareAlgorithm;
use App\Models\DynamicAlgorithm;
use App\Models\GuidanceOnAdverseDrugReaction;
use App\Models\LatentTbInfection;
use App\Models\Symptom;
use App\Models\TreatmentAlgorithm;
use App\Models\TTrainingTag;
use Stichoza\GoogleTranslate\GoogleTranslate;
use DB;
use Log;
use Exception;

class TranslateController extends BaseController
{

    public function translate()
    {
        // $t_training_tag = ChatKeyword::where('deleted_at',NULL)->limit(10)->get(['id','title']);
        $t_training_tag = json_decode(DB::table('chat_keywords')->where('deleted_at', '=', null)->get(['id', 'title']), true);
        $t_training_tag_questions = [];
        foreach ($t_training_tag as $t_tag) {
            // for($i = 0;$i < count($t_training_tag);$i++){
            // json_decode($t_training_tag[$i], true)
            $title = json_decode($t_tag['title'], true);
            $t_tag['title'] = ['en' => $title['en'], 'gu' => isset($title['gu']) ? $title['gu'] : null, 'hi' => isset($title['hi']) ? $title['hi'] : null, 'mr' => isset($title['mr']) ? $title['mr'] : null, 'ta' => isset($title['ta']) ? $title['ta'] : null, 'pa' => GoogleTranslate::trans($title['en'], 'pa'), 'te' => GoogleTranslate::trans($title['en'], 'te'), 'kn' => GoogleTranslate::trans($title['en'], 'kn')];
            // $t_training_tag_questions[$i]['title']['mr'] = GoogleTranslate::trans($t_training_tag[$i]['title'], 'mr');
            ChatKeyword::where('id', $t_tag['id'])->update(['title' => $t_tag['title']]);
        }
        return true;
    }

    public function translate_training_tag()
    {
        $t_training_tag = json_decode(DB::table('t_training_tag')->where('deleted_at', '=', null)->get(['id', 'response']), true);

        foreach ($t_training_tag as $t_tag) {
            $title = json_decode($t_tag['response'], true);
            if (isset($title['en']) && $title['en'] != '') {
                $t_tag['response'] = ['en' => $title['en'], 'gu' => isset($title['gu']) ? $title['gu'] : null, 'hi' => isset($title['hi']) ? $title['hi'] : null, 'mr' => isset($title['mr']) ? $title['mr'] : null, 'ta' => isset($title['ta']) ? $title['ta'] : null, 'pa' => GoogleTranslate::trans($title['en'], 'pa'), 'te' => GoogleTranslate::trans($title['en'], 'te'), 'kn' => GoogleTranslate::trans($title['en'], 'kn')];
                TTrainingTag::where('id', $t_tag['id'])->update(['response' => $t_tag['response']]);
            }
        }
        return true;
    }

    public function translate_chat_question()
    {
        $chat_question = json_decode(DB::table('chat_questions')->where('deleted_at', '=', null)->get(['id', 'question', 'answer']), true);
        try {
            // DB::beginTransaction();
            foreach ($chat_question as $question) {
                $title = json_decode($question['question'], true);
                $answer = json_decode($question['answer'], true);
                if (isset($title['en']) && $title['en'] != '') {
                    $question['question'] = ['en' => $title['en'], 'hi' => isset($title['hi']) ? $title['hi'] : null, 'gu' => isset($title['gu']) ? $title['gu'] : Null, 'mr' => isset($title['mr']) ? $title['mr'] : null, 'ta' => isset($title['ta']) ? $title['ta'] : null, 'pa' => GoogleTranslate::trans($title['en'], 'pa'), 'te' => GoogleTranslate::trans($title['en'], 'te'), 'kn' => GoogleTranslate::trans($title['en'], 'kn')];
                    ChatQuestion::where('id', $question['id'])->update(['question' => $question['question']]);
                }
                if (isset($answer['en']) && $answer['en'] != '') {
                    $question['answer'] = ['en' => $answer['en'], 'hi' => isset($answer['hi']) ? $answer['hi'] : null, 'gu' => isset($answer['gu']) ? $answer['gu'] : Null, 'mr' => isset($answer['mr']) ? $answer['mr'] : null, 'ta' => isset($answer['ta']) ? $answer['ta'] : null, 'pa' => GoogleTranslate::trans($answer['en'], 'pa'), 'te' => GoogleTranslate::trans($answer['en'], 'te'), 'kn' => GoogleTranslate::trans($answer['en'], 'kn')];
                    ChatQuestion::where('id', $question['id'])->update(['answer' => $question['answer']]);
                }
            }
            // DB::commit();
            return true;
        } catch (Exception $e) {
            Log::error("Error in processing Chat question translate");
            Log::error($e);
            // DB::rollback();
        }
    }

    public function translate_case_definition()
    {
        $case_def_algo = json_decode(DB::table('case_definitions')->where('deleted_at', '=', null)->get(['id', 'title', 'description']), true);
        try {
            //     DB::beginTransaction();
            foreach ($case_def_algo as $case_aglo) {
                $title = json_decode($case_aglo['title'], true);
                $description = json_decode($case_aglo['description'], true);
                if (isset($title['en']) && $title['en'] != '') {
                    $case_aglo['title'] = ['en' => $title['en'], 'hi' => isset($title['hi']) ? $title['hi'] : null, 'gu' => isset($title['gu']) ? $title['gu'] : Null, 'mr' => isset($title['mr']) ? $title['mr'] : null, 'ta' => isset($title['ta']) ? $title['ta'] : null, 'pa' => GoogleTranslate::trans($title['en'], 'pa'), 'te' => GoogleTranslate::trans($title['en'], 'te'), 'kn' => GoogleTranslate::trans($title['en'], 'kn')];
                    CaseDefinition::where('id', $case_aglo['id'])->update(['title' => $case_aglo['title']]);
                }
                if (isset($description['en']) &&  $description['en'] != '') {
                    $case_aglo['description'] = ['en' => $description['en'], 'hi' => isset($description['hi']) ? $description['hi'] : null, 'gu' => isset($description['gu']) ? $description['gu'] : Null, 'mr' => isset($description['mr']) ? $description['mr'] : null, 'ta' => isset($description['ta']) ? $description['ta'] : null, 'pa' => GoogleTranslate::trans($description['en'], 'pa'), 'te' => GoogleTranslate::trans($description['en'], 'te'), 'kn' => GoogleTranslate::trans($description['en'], 'kn')];
                    CaseDefinition::where('id', $case_aglo['id'])->update(['description' => $case_aglo['description']]);
                }
            }
            // DB::commit();
            return true;
        } catch (Exception $e) {
            Log::error("Error in processing Chat question translate");
            Log::error($e);
            // DB::rollback();
        }
    }

    public function translate_cgc_algo()
    {
        $cgc_algo = json_decode(DB::table('cgc_interventions_algorithms')->where('deleted_at', '=', null)->get(['id', 'title', 'description']), true);
        try {
            //     DB::beginTransaction();
            foreach ($cgc_algo as $cgc) {
                $title = json_decode($cgc['title'], true);
                $description = json_decode($cgc['description'], true);
                if (isset($title['en']) && $title['en'] != '') {
                    $cgc_algo['title'] = ['en' => $title['en'], 'hi' => isset($title['hi']) ? $title['hi'] : null, 'gu' => isset($title['gu']) ? $title['gu'] : Null, 'mr' => isset($title['mr']) ? $title['mr'] : null, 'ta' => isset($title['ta']) ? $title['ta'] : null, 'pa' => GoogleTranslate::trans($title['en'], 'pa'), 'te' => GoogleTranslate::trans($title['en'], 'te'), 'kn' => GoogleTranslate::trans($title['en'], 'kn')];
                    CgcInterventionsAlgorithm::where('id', $cgc['id'])->update(['title' => $cgc_algo['title']]);
                }
                if (isset($description['en']) &&  $description['en'] != '') {
                    $cgc_algo['description'] = ['en' => $description['en'], 'hi' => isset($description['hi']) ? $description['hi'] : null, 'gu' => isset($description['gu']) ? $description['gu'] : Null, 'mr' => isset($description['mr']) ? $description['mr'] : null, 'ta' => isset($description['ta']) ? $description['ta'] : null, 'pa' => GoogleTranslate::trans($description['en'], 'pa'), 'te' => GoogleTranslate::trans($description['en'], 'te'), 'kn' => GoogleTranslate::trans($description['en'], 'kn')];
                    CgcInterventionsAlgorithm::where('id', $cgc['id'])->update(['description' => $cgc_algo['description']]);
                }
            }
            // DB::commit();
            return true;
        } catch (Exception $e) {
            Log::error("Error in processing Chat question translate");
            Log::error($e);
            // DB::rollback();
        }
    }

    public function translate_diagnosis_algo()
    {
        $diagnosis_algo = json_decode(DB::table('diagnoses_algorithms')->where('deleted_at', '=', null)->get(['id', 'title', 'description']), true);
        try {
            //     DB::beginTransaction();
            foreach ($diagnosis_algo as $diagnosis) {
                $title = json_decode($diagnosis['title'], true);
                $description = json_decode($diagnosis['description'], true);
                if (isset($title['en']) && $title['en'] != '') {
                    $diagnosis_algo['title'] = ['en' => $title['en'], 'hi' => isset($title['hi']) ? $title['hi'] : null, 'gu' => isset($title['gu']) ? $title['gu'] : Null, 'mr' => isset($title['mr']) ? $title['mr'] : null, 'ta' => isset($title['ta']) ? $title['ta'] : null, 'pa' => GoogleTranslate::trans($title['en'], 'pa'), 'te' => GoogleTranslate::trans($title['en'], 'te'), 'kn' => GoogleTranslate::trans($title['en'], 'kn')];
                    DiagnosesAlgorithm::where('id', $diagnosis['id'])->update(['title' => $diagnosis_algo['title']]);
                }
                if (isset($description['en']) &&  $description['en'] != '') {
                    $diagnosis_algo['description'] = ['en' => $description['en'], 'hi' => isset($description['hi']) ? $description['hi'] : null, 'gu' => isset($description['gu']) ? $description['gu'] : Null, 'mr' => isset($description['mr']) ? $description['mr'] : null, 'ta' => isset($description['ta']) ? $description['ta'] : null, 'pa' => GoogleTranslate::trans($description['en'], 'pa'), 'te' => GoogleTranslate::trans($description['en'], 'te'), 'kn' => GoogleTranslate::trans($description['en'], 'kn')];
                    DiagnosesAlgorithm::where('id', $diagnosis['id'])->update(['description' => $diagnosis_algo['description']]);
                }
            }
            // DB::commit();
            return true;
        } catch (Exception $e) {
            Log::error("Error in processing Chat question translate");
            Log::error($e);
            // DB::rollback();
        }
    }

    public function translate_differential_care_algo()
    {
        $differential_algo = json_decode(DB::table('differential_care_algorithms')->where('deleted_at', '=', null)->get(['id', 'title', 'description']), true);
        try {
            //     DB::beginTransaction();
            foreach ($differential_algo as $diff_algo) {
                $title = json_decode($diff_algo['title'], true);
                $description = json_decode($diff_algo['description'], true);
                if (isset($title['en']) && $title['en'] != '') {
                    $differential_algo['title'] = ['en' => $title['en'], 'hi' => isset($title['hi']) ? $title['hi'] : null, 'gu' => isset($title['gu']) ? $title['gu'] : Null, 'mr' => isset($title['mr']) ? $title['mr'] : null, 'ta' => isset($title['ta']) ? $title['ta'] : null, 'pa' => GoogleTranslate::trans($title['en'], 'pa'), 'te' => GoogleTranslate::trans($title['en'], 'te'), 'kn' => GoogleTranslate::trans($title['en'], 'kn')];
                    DifferentialCareAlgorithm::where('id', $diff_algo['id'])->update(['title' => $differential_algo['title']]);
                }
                if (isset($description['en']) &&  $description['en'] != '') {
                    $differential_algo['description'] = ['en' => $description['en'], 'hi' => isset($description['hi']) ? $description['hi'] : null, 'gu' => isset($description['gu']) ? $description['gu'] : Null, 'mr' => isset($description['mr']) ? $description['mr'] : null, 'ta' => isset($description['ta']) ? $description['ta'] : null, 'pa' => GoogleTranslate::trans($description['en'], 'pa'), 'te' => GoogleTranslate::trans($description['en'], 'te'), 'kn' => GoogleTranslate::trans($description['en'], 'kn')];
                    DifferentialCareAlgorithm::where('id', $diff_algo['id'])->update(['description' => $differential_algo['description']]);
                }
            }
            // DB::commit();
            return true;
        } catch (Exception $e) {
            Log::error("Error in processing Chat question translate");
            Log::error($e);
            // DB::rollback();
        }
    }

    public function translate_guidance_on_adr_algo()
    {
        $guidance_on_adr_algo = json_decode(DB::table('guidance_on_adverse_drug_reactions')->where('deleted_at', '=', null)->get(['id', 'title', 'description']), true);
        try {
            //     DB::beginTransaction();
            foreach ($guidance_on_adr_algo as $guidance_algo) {
                $title = json_decode($guidance_algo['title'], true);
                $description = json_decode($guidance_algo['description'], true);
                if (isset($title['en']) && $title['en'] != '') {
                    $guidance_on_adr_algo['title'] = ['en' => $title['en'], 'hi' => isset($title['hi']) ? $title['hi'] : null, 'gu' => isset($title['gu']) ? $title['gu'] : Null, 'mr' => isset($title['mr']) ? $title['mr'] : null, 'ta' => isset($title['ta']) ? $title['ta'] : null, 'pa' => GoogleTranslate::trans($title['en'], 'pa'), 'te' => GoogleTranslate::trans($title['en'], 'te'), 'kn' => GoogleTranslate::trans($title['en'], 'kn')];
                    GuidanceOnAdverseDrugReaction::where('id', $guidance_algo['id'])->update(['title' => $guidance_on_adr_algo['title']]);
                }
                if (isset($description['en']) &&  $description['en'] != '') {
                    $guidance_on_adr_algo['description'] = ['en' => $description['en'], 'hi' => isset($description['hi']) ? $description['hi'] : null, 'gu' => isset($description['gu']) ? $description['gu'] : Null, 'mr' => isset($description['mr']) ? $description['mr'] : null, 'ta' => isset($description['ta']) ? $description['ta'] : null, 'pa' => GoogleTranslate::trans($description['en'], 'pa'), 'te' => GoogleTranslate::trans($description['en'], 'te'), 'kn' => GoogleTranslate::trans($description['en'], 'kn')];
                    GuidanceOnAdverseDrugReaction::where('id', $guidance_algo['id'])->update(['description' => $guidance_on_adr_algo['description']]);
                }
            }
            // DB::commit();
            return true;
        } catch (Exception $e) {
            Log::error("Error in processing Chat question translate");
            Log::error($e);
            // DB::rollback();
        }
    }

    public function translate_latent_tb_algo()
    {
        $latent_tb_algo = json_decode(DB::table('latent_tb_infections')->where('deleted_at', '=', null)->get(['id', 'title', 'description']), true);
        try {
            //     DB::beginTransaction();
            foreach ($latent_tb_algo as $latent_tb_algo) {
                $title = json_decode($latent_tb_algo['title'], true);
                $description = json_decode($latent_tb_algo['description'], true);
                if (isset($title['en']) && $title['en'] != '') {
                    $latent_tb_algo['title'] = ['en' => $title['en'], 'hi' => isset($title['hi']) ? $title['hi'] : null, 'gu' => isset($title['gu']) ? $title['gu'] : Null, 'mr' => isset($title['mr']) ? $title['mr'] : null, 'ta' => isset($title['ta']) ? $title['ta'] : null, 'pa' => GoogleTranslate::trans($title['en'], 'pa'), 'te' => GoogleTranslate::trans($title['en'], 'te'), 'kn' => GoogleTranslate::trans($title['en'], 'kn')];
                    LatentTbInfection::where('id', $latent_tb_algo['id'])->update(['title' => $latent_tb_algo['title']]);
                }
                if (isset($description['en']) &&  $description['en'] != '') {
                    $latent_tb_algo['description'] = ['en' => $description['en'], 'hi' => isset($description['hi']) ? $description['hi'] : null, 'gu' => isset($description['gu']) ? $description['gu'] : Null, 'mr' => isset($description['mr']) ? $description['mr'] : null, 'ta' => isset($description['ta']) ? $description['ta'] : null, 'pa' => GoogleTranslate::trans($description['en'], 'pa'), 'te' => GoogleTranslate::trans($description['en'], 'te'), 'kn' => GoogleTranslate::trans($description['en'], 'kn')];
                    LatentTbInfection::where('id', $latent_tb_algo['id'])->update(['description' => $latent_tb_algo['description']]);
                }
            }
            // DB::commit();
            return true;
        } catch (Exception $e) {
            Log::error("Error in processing Chat question translate");
            Log::error($e);
            // DB::rollback();
        }
    }

    public function translate_treatment_algo()
    {
        $treatment = json_decode(DB::table('treatment_algorithms')->where('deleted_at', '=', null)->get(['id', 'title', 'description']), true);
        try {
            //     DB::beginTransaction();
            foreach ($treatment as $treatment_algo) {
                $title = json_decode($treatment_algo['title'], true);
                $description = json_decode($treatment_algo['description'], true);
                if (isset($title['en']) && ($title['en'] != '' && $title['en'] != NULL)) {
                    $tamil_title = GoogleTranslate::trans($title['en'], 'pa');
                    $tamil_title_te = GoogleTranslate::trans($title['en'], 'te');
                    $tamil_title_kn = GoogleTranslate::trans($title['en'], 'kn');
                    $treatment['title'] = ['en' => $title['en'], 'hi' => isset($title['hi']) ? $title['hi'] : null, 'gu' => isset($title['gu']) ? $title['gu'] : Null, 'mr' => isset($title['mr']) ? $title['mr'] : null, 'ta' => isset($title['ta']) ? $title['ta'] : null, 'pa' => isset($tamil_title) ? $tamil_title : NULL, 'te' => isset($tamil_title_te) ? $tamil_title_te : NULL, 'kn' => isset($tamil_title_kn) ? $tamil_title_kn : NULL];
                    TreatmentAlgorithm::where('id', $treatment_algo['id'])->update(['title' => $treatment['title']]);
                }

                if (isset($description['en']) &&  ($description['en'] != '' && $description['en'] != NULL)) {
                    $tamil_desc = GoogleTranslate::trans($description['en'], 'pa');
                    $tamil_desc_te = GoogleTranslate::trans($description['en'], 'te');
                    $tamil_desc_kn = GoogleTranslate::trans($description['en'], 'kn');
                    $treatment['description'] = ['en' => $description['en'], 'hi' => isset($description['hi']) ? $description['hi'] : null, 'gu' => isset($description['gu']) ? $description['gu'] : Null, 'mr' => isset($description['mr']) ? $description['mr'] : null, 'ta' => isset($description['ta']) ? $description['ta'] : null, 'pa' => isset($tamil_desc) ? $tamil_desc : NULL, 'te' => isset($tamil_desc_te) ? $tamil_desc_te : NULL, 'kn' => isset($tamil_desc_kn) ? $tamil_desc_kn : NULL];
                    TreatmentAlgorithm::where('id', $treatment_algo['id'])->update(['description' => $treatment['description']]);
                }
            }
            // DB::commit();
            return true;
        } catch (Exception $e) {
            Log::error("Error in processing Chat question translate");
            Log::error($e);
            // DB::rollback();
        }
    }

    public function translate_app_config()
    {
        $app_config = json_decode(DB::table('app_config')->where('deleted_at', '=', null)->get(['id', 'value_json']), true);
        try {
            //     DB::beginTransaction();
            foreach ($app_config as $app_config_data) {
                $title = json_decode($app_config_data['value_json'], true);
                if (isset($title['en']) && ($title['en'] != '' && $title['en'] != NULL)) {
                    $tamil_title = GoogleTranslate::trans($title['en'], 'pa');
                    $tamil_title_te = GoogleTranslate::trans($title['en'], 'te');
                    $tamil_title_kn = GoogleTranslate::trans($title['en'], 'kn');
                    $app_config['title'] = ['en' => $title['en'], 'hi' => isset($title['hi']) ? $title['hi'] : null, 'gu' => isset($title['gu']) ? $title['gu'] : Null, 'mr' => isset($title['mr']) ? $title['mr'] : null, 'ta' => isset($title['ta']) ? $title['ta'] : null, 'pa' => isset($tamil_title) ? $tamil_title : NULL, 'te' => isset($tamil_title_te) ? $tamil_title_te : NULL, 'kn' => isset($tamil_title_kn) ? $tamil_title_kn : NULL];
                    AppConfig::where('id', $app_config_data['id'])->update(['value_json' => $app_config['title']]);
                }
            }
            // DB::commit();
            return true;
        } catch (Exception $e) {
            Log::error("Error in processing Chat question translate");
            Log::error($e);
            // DB::rollback();
        }
    }

    public function translate_screening_tool()
    {
        $screening_tool = json_decode(DB::table('symptoms')->where('deleted_at', '=', null)->get(['id', 'symptoms_title']), true);
        try {
            //     DB::beginTransaction();
            foreach ($screening_tool as $tool) {
                $title = json_decode($tool['symptoms_title'], true);
                if (isset($title['en']) && ($title['en'] != '' && $title['en'] != NULL)) {
                    $tamil_title = GoogleTranslate::trans($title['en'], 'pa');
                    $tamil_title_te = GoogleTranslate::trans($title['en'], 'pa');
                    $tamil_title_kn = GoogleTranslate::trans($title['en'], 'pa');
                    $screening_tool['symptoms_title'] = ['en' => $title['en'], 'hi' => isset($title['hi']) ? $title['hi'] : null, 'gu' => isset($title['gu']) ? $title['gu'] : Null, 'mr' => isset($title['mr']) ? $title['mr'] : null, 'ta' => isset($title['ta']) ? $title['ta'] : null, 'pa' => isset($tamil_title) ? $tamil_title : NULL, 'te' => isset($tamil_title_te) ? $tamil_title_te : NULL, 'kn' => isset($tamil_title_kn) ? $tamil_title_kn : NULL];
                    Symptom::where('id', $tool['id'])->update(['symptoms_title' => $screening_tool['symptoms_title']]);
                }
            }
            // DB::commit();
            return true;
        } catch (Exception $e) {
            Log::error("Error in processing screening tool translate");
            Log::error($e);
            // DB::rollback();
        }
    }
    public function translate_dynamic_algo()
    {
        $dynamic_algo = json_decode(DB::table('dynamic_algorithm')->where('deleted_at', '=', null)->get(['id', 'title', 'description']), true);
        try {
            //     DB::beginTransaction();
            foreach ($dynamic_algo as $dynamic) {
                $title = json_decode($dynamic['title'], true);
                $description = json_decode($dynamic['description'], true);
                if (isset($title['en']) && $title['en'] != '') {
                    $dynamic_algo['title'] = ['en' => $title['en'], 'hi' => isset($title['hi']) ? $title['hi'] : null, 'gu' => isset($title['gu']) ? $title['gu'] : Null, 'mr' => GoogleTranslate::trans($title['en'], 'mr'), 'ta' => GoogleTranslate::trans($title['en'], 'ta'), 'pa' => GoogleTranslate::trans($title['en'], 'pa'), 'te' => GoogleTranslate::trans($title['en'], 'te'), 'kn' => GoogleTranslate::trans($title['en'], 'kn')];
                    DynamicAlgorithm::where('id', $dynamic['id'])->update(['title' => $dynamic_algo['title']]);
                }
                if (isset($description['en']) &&  $description['en'] != '') {
                    $dynamic_algo['description'] = ['en' => $description['en'], 'hi' => isset($description['hi']) ? $description['hi'] : null, 'gu' => isset($description['gu']) ? $description['gu'] : Null, 'mr' => GoogleTranslate::trans($description['en'], 'mr'), 'ta' => GoogleTranslate::trans($description['en'], 'ta'), 'pa' => GoogleTranslate::trans($description['en'], 'pa'), 'te' => GoogleTranslate::trans($description['en'], 'te'), 'kn' => GoogleTranslate::trans($description['en'], 'kn')];
                    DynamicAlgorithm::where('id', $dynamic['id'])->update(['description' => $dynamic_algo['description']]);
                }
            }
            // DB::commit();
            return true;
        } catch (Exception $e) {
            Log::error("Error in processing Chat question translate");
            Log::error($e);
            // DB::rollback();
        }
    }
}
