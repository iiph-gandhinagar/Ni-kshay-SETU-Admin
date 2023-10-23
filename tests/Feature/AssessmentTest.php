<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Feature\RegisterTest;
use Config;

class AssessmentTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_assessment_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-all-assessment');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_past_assessment_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-all-past-assessment');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_future_assessment_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-all-future-assessment');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_assessment_performance_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-assessment-performace');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_store_enrollnment_assessment_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequest();
        $assessment = Assessment::create($newRequest);
        // $assessment = Assessment::where('activated', 1)->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->postJson(Config::get('app.GENERAL.app_url') . '/api/store-assessment-enrollnment', [
            'assessment_id' => $assessment['id'],
            'response' => 'yes',
        ]);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function formRequest()
    {
        $newRequest['time_to_complete'] = 15;
        $newRequest['cadre_id'] = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,70,71,72,73,74,75";
        $newRequest['country_id'] = 0;
        $newRequest['state_id'] = 1;
        $newRequest['assessment_title'] = "Test Assessment";
        $newRequest['initial_invitation'] = 0;
        $newRequest['activated'] = 1;
        $newRequest['district_id'] = "4,9,10,15,17,65,68";
        $newRequest['cadre_type'] = "All";
        $newRequest['created_by'] = 1;
        $newRequest['certificate_type'] = 1;
        $newRequest['created_at'] = now();
        $newRequest['updated_at'] = now();
        return $newRequest;
    }

    public function test_get_assessment_with_assessment_question_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequest();
        $assessment = Assessment::create($newRequest);
        // $assessment = Assessment::where('activated', 1)->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-assessment-with-assessmentquestions/' . $assessment['id']);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_store_assessment_result_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequest();
        $assessment = Assessment::create($newRequest);
        // $assessment = Assessment::where('activated', 1)->limit(1)->get(['id'])[0];
        $dataRequest = $this->formDataRequest($assessment['id']);
        $question = AssessmentQuestion::create($dataRequest);
        // $question = AssessmentQuestion::where('assessment_id', $assessment['id'])->orderBy('order_index', 'desc')->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->postJson(Config::get('app.GENERAL.app_url') . '/api/store-user-assessment-result', [
            'assessment_id' => $assessment['id'],
            "answers" => [
                [
                    "question_id" => $question['id'],
                    "answer" => "option2",
                    "is_submit" => 1,
                ],
            ],
        ]);
        // dd($response);
        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function formDataRequest($id)
    {
        $newRequest['assessment_id'] = $id;
        $newRequest['correct_answer'] = 'option2';
        $newRequest['order_index'] = 1;
        $newRequest['question'] = "No. of Doses in IP for DS-TB patient in daily regimen?";
        $newRequest['option1'] = '56';
        $newRequest['option2'] = '28';
        $newRequest['option3'] = '42';
        $newRequest['option4'] = '24';
        $newRequest['category'] = "PHA";
        $newRequest['created_at'] = now();
        $newRequest['updated_at'] = now();
        return $newRequest;
    }

    public function test_get_subscriber_assessment_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequest();
        $assessment = Assessment::create($newRequest);
        // $assessment = Assessment::where('activated', 1)->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->postJson(Config::get('app.GENERAL.app_url') . '/api/get-subscriber-assessment-details', [
            'assessment_id' => $assessment['id'],
        ]);
        // dd($response);
        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    // public function test_store_complete_assessment_details()
    // {
    //     $subscriber = (new RegisterTest())->faker_subscriber();
    //     $assessment = Assessment::where('activated', 1)->limit(1)->get(['id'])[0];
    //     $response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $subscriber['api_token'],
    //     ])->postJson(
    //         Config::get('app.GENERAL.app_url') . '/api/store-complete-assessment',
    //         [
    //             ['userId' => $subscriber['id']],
    //             ['assessment_id' => $assessment['id']],
    //         ]
    //     );
    //     // dd($response);
    //     $this->assertEquals(Response::HTTP_OK, $response['code']);
    // }

    public function test_get_user_result_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequest();
        $assessment = Assessment::create($newRequest);
        // $assessment = Assessment::where('activated', 1)->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-user-result/' . $assessment['id']);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }
}