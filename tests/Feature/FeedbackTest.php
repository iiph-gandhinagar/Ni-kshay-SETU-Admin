<?php

namespace Tests\Feature;

use App\Models\SurveyMaster;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Feature\RegisterTest;
use Config;

class FeedbackTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_feedback_details()
    {
        $subscriber = $this->postJson(Config::get('app.GENERAL.app_url') . '/api/store-user-v2', [
            'name' => 'Test User',
            'phone_no' => '9898989120',
            'password' => 'Asd@1234',
            'cadre_type' => 'National_Level',
            'country_id' => 1,
            'cadre_id' => 70,
            'block_id' => 0,
            'state_id' => 0,
            'district_id' => 0,
            'health_facility_id' => 0,
            'is_verified' => 1,
        ]);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['data']['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-feedback-details', [
            'feedback_question_skip' => 0,
        ]);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_store_feedback_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        // dd($subscriber['data']['api_token']);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->postJson(
            Config::get('app.GENERAL.app_url') . '/api/store-feedback-details',
            [
                "payload" => [
                    "ratings" => [
                        [
                            "id" => 4,
                            "rating" => 4,
                            "skip" => 0
                        ],
                    ],

                    "review" => "User Friendly App UI"
                ],
            ]
        );

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_survey_forms_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-survey-forms');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_survey_forms_id_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        // $survey = SurveyMaster::orderBy('order_index', 'desc')->limit(1)->get(['id'])[0];
        $newRequest = $this->formRequest();
        $survey = SurveyMaster::create($newRequest);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-survey-by-id/' . $survey['id']);
        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function formRequest()
    {
        $newRequest['title'] = "Test";
        $newRequest['country_id'] = "1";
        $newRequest['cadre_id'] = "1,2,3";
        $newRequest['state_id'] = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,38";
        $newRequest['district_id'] = "1,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18";
        $newRequest['cadre_type'] = "All";
        $newRequest['order_index'] = 1;
        $newRequest['send_initial_notification'] = 0;
        $newRequest['active'] = 1;
        $newRequest['created_at'] = now();
        $newRequest['updated_at'] = now();
        return $newRequest;
    }

    public function test_store_survey_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        // $survey = SurveyMaster::orderBy('order_index', 'desc')->limit(1)->get(['id'])[0];
        $newRequest = $this->formRequest();
        $survey = SurveyMaster::create($newRequest);
        // $survey_question = SurveyMasterQuestion::where('survey_master_id', $survey['id'])->limit(1)->get(['id'])[0];
        //need to insert survy and survey question details to check apis response
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->postJson(Config::get('app.GENERAL.app_url') . '/api/store-survey-details', [
            [
                "survey_id" => $survey['id'],
                "survey_question_id" => 1,
                "answer" => "option1"
            ],
            [
                "survey_id" => $survey['id'],
                "survey_question_id" => 2,
                "answer" => "option2"
            ],
        ]);
        // dd($response);
        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }
}
