<?php

namespace Tests\Feature;

use App\Models\CaseDefinition;
use App\Models\CgcInterventionsAlgorithm;
use App\Models\DiagnosesAlgorithm;
use App\Models\DifferentialCareAlgorithm;
use App\Models\DynamicAlgorithm;
use App\Models\GuidanceOnAdverseDrugReaction;
use App\Models\LatentTbInfection;
use App\Models\TreatmentAlgorithm;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Feature\RegisterTest;
use Config;
use Log;

class AlgorithmTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_diagnoses_master_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        // Log::info(env('APP_URL') . '/api/get-diagnoses-algorithms-master-nodes-v2');
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-diagnoses-algorithms-master-nodes-v2');
        // dd($response['status']);
        // dd(json_decode($response));
        // dd($response);
        // dd(json_decode($response->getContent()));

        $this->assertEquals(true, $response['success']);
    }

    public function test_diagnoses_dependent_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequest('diagnosis');
        $nodes = DiagnosesAlgorithm::create($newRequest);
        // $nodes = DiagnosesAlgorithm::where('activated', 1)->where('parent_id', 0)->orderBy('index')->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-diagnoses-algorithms-dependent-nodes/' . $nodes['id']);

        $this->assertEquals(true, $response['success']);
    }

    public function formRequest($algo)
    {
        $newRequest['node_type'] = "Linking Node";
        $newRequest['is_expandable'] = 0;
        $newRequest['has_options'] = 0;
        $newRequest['parent_id'] = 0;
        $newRequest['master_node_id'] = 0;
        $newRequest['index'] = 1;
        $newRequest['state_id'] = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,38";
        $newRequest['cadre_id'] = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,77,78";
        $newRequest['description'] = "";
        $newRequest['time_spent'] = 15;
        $newRequest['redirect_node_id'] = 0;
        $newRequest['activated'] = 1;
        $newRequest['send_initial_notification'] = 0;
        $newRequest['created_at'] = now();
        $newRequest['updated_at'] = now();
        if ($algo == "diagnosis") {
            $newRequest['title'] = "Presumptive Pulmonary TB";
        } else if ($algo == "treatment") {
            $newRequest['title'] = "TB and Nutrition";
        } else if ($algo == "guidance") {
            $newRequest['title'] = "Reporting of ADRs";
        } else if ($algo == "latent") {
            $newRequest['title'] = "TB Preventive Treatment Completion";
        } else if ($algo == "cgc") {
            $newRequest['title'] = "Post Treatment Follow-up Intervention";
        } else if ($algo == "differential") {
            $newRequest['title'] = "Assessment of patients with active pulmonary TB";
        } else if ($algo == "case_defintion") {
            $newRequest['title'] = "Types Presumptive TB case";
        } else if ($algo == "case_defintion") {
            $newRequest['title'] = "Types Presumptive TB case";
        }
        return $newRequest;
    }

    public function test_treatment_master_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-treatment-algorithms-master-nodes-v2');

        $this->assertEquals(true, $response['success']);
    }

    public function test_treatment_dependent_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequest("treatment");
        $nodes = TreatmentAlgorithm::create($newRequest);
        // $nodes = TreatmentAlgorithm::where('activated', 1)->where('parent_id', 0)->orderBy('index')->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-treatment-algorithms-dependent-nodes/' . $nodes['id']);
        $this->assertEquals(true, $response['success']);
    }

    public function test_guidance_on_adr_master_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-guidance-on-adverse-drug-reactions-master-nodes-v2');

        $this->assertEquals(true, $response['success']);
    }

    public function test_guidance_on_adr_dependent_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequest("guidance");
        $nodes = GuidanceOnAdverseDrugReaction::create($newRequest);
        // $nodes = GuidanceOnAdverseDrugReaction::where('activated', 1)->where('parent_id', 0)->orderBy('index')->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-guidance-on-adverse-drug-reactions-dependent-nodes/' . $nodes['id']);

        $this->assertEquals(true, $response['success']);
    }

    public function test_latent_tb_infection_master_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-latent-tb-infection-master-nodes-v2');

        $this->assertEquals(true, $response['success']);
    }

    public function test_latent_tb_infection_dependent_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequest("latent");
        $nodes = LatentTbInfection::create($newRequest);
        // $nodes = LatentTbInfection::where('activated', 1)->where('parent_id', 0)->orderBy('index')->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-latent-tb-infection-dependent-nodes/' . $nodes['id']);

        $this->assertEquals(true, $response['success']);
    }

    public function test_latent_tb_infection_all_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-latent-tb-infection-all-nodes');

        $this->assertEquals(true, $response['success']);
    }

    public function test_cgc_intervention_algorithm_master_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-cgc-interventions-algorithms-master-nodes-v2');

        $this->assertEquals(true, $response['success']);
    }

    public function test_cgc_intervention_algorithm_dependent_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequest("cgc");
        $nodes = CgcInterventionsAlgorithm::create($newRequest);
        // $nodes = CgcInterventionsAlgorithm::where('activated', 1)->where('parent_id', 0)->orderBy('index')->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-cgc-interventions-algorithms-dependent-nodes/' . $nodes['id']);

        $this->assertEquals(true, $response['success']);
    }

    public function test_differential_care_algorithm_master_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-differential-care-algorithms-master-nodes-v2');

        $this->assertEquals(true, $response['success']);
    }

    public function test_differential_care_algorithm_dependent_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequest("differential");
        $nodes = DifferentialCareAlgorithm::create($newRequest);
        // $nodes = DifferentialCareAlgorithm::where('activated', 1)->where('parent_id', 0)->orderBy('index')->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-differential-care-algorithms-dependent-nodes/' . $nodes['id']);

        $this->assertEquals(true, $response['success']);
    }

    public function test_case_definition_algorithm_master_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-case-definitions-master-nodes-v2');

        $this->assertEquals(true, $response['success']);
    }

    public function test_case_defintion_algorithm_dependent_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequest("case_defintion");
        $nodes = CaseDefinition::create($newRequest);
        // $nodes = CaseDefinition::where('activated', 1)->where('parent_id', 0)->orderBy('index')->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-case-definitions-dependent-nodes/' . $nodes['id']);

        $this->assertEquals(true, $response['success']);
    }

    public function test_dynamic_algorithm_master_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        // $nodes = DynamicAlgorithm::where('activated', 1)->where('parent_id', 0)->orderBy('index')->limit(1)->get(['algo_key'])[0];
        $newRequest = $this->formRequest('diagnosis');
        $newRequest['algo_key'] = 1;
        $nodes = DynamicAlgorithm::create($newRequest);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-dynamic-algorithms-master-nodes-v2/' . $nodes['algo_key']);

        $this->assertEquals(true, $response['success']);
    }

    // public function test_dynamic_algorithm_dependent_node()
    // {
    //     $subscriber = (new RegisterTest())->faker_subscriber();
    //     $nodes = DynamicAlgorithm::where('activated', 1)->where('parent_id', 0)->orderBy('index')->limit(1)->get(['algo_key', 'parent_id'])[0];
    //     $response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $subscriber['api_token'],
    //         'lang' => 'en',
    //     ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-dynamic-algorithms-dependent-nodes/' . $nodes['algo_key'] . '/' . $nodes['parent_id']);

    //     $this->assertEquals(true, $response['success']);
    // }

    public function test_dynamic_algorithm_section_node()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-dynamic-algo-group-by-section');

        $this->assertEquals(true, $response['success']);
    }
}