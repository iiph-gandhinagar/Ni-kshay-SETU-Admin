<?php

namespace Tests\Feature;

use App\Models\TreatmentAlgorithm;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Feature\RegisterTest;
use Config;

class UserActivityTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_subscriber_activity()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->postJson(Config::get('app.GENERAL.app_url') . '/api/store-user-activity', [
            'action' => 'Resource Material Fetched',
        ]);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_store_screening_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequest("treatment");
        TreatmentAlgorithm::create($newRequest);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->postJson(Config::get('app.GENERAL.app_url') . '/api/store-user-screening', [
            'age' => '25',
            'weight' => '63',
            'height' => '250',
            'symptoms_selected' => '1,4',
        ]);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function formRequest()
    {
        $newRequest['node_type'] = "CMS Node(New Page)";
        $newRequest['is_expandable'] = 0;
        $newRequest['has_options'] = 0;
        $newRequest['parent_id'] = 0;
        $newRequest['master_node_id'] = 0;
        $newRequest['index'] = 1;
        $newRequest['state_id'] = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,38";
        $newRequest['cadre_id'] = "1,2,3,4,5,6,7,8";
        $newRequest['description'] = "";
        $newRequest['time_spent'] = 15;
        $newRequest['redirect_node_id'] = 0;
        $newRequest['activated'] = 1;
        $newRequest['send_initial_notification'] = 0;
        $newRequest['created_at'] = now();
        $newRequest['updated_at'] = now();
        $newRequest['title'] = "Extremely Underweight";
        return $newRequest;
    }

    public function test_store_user_enquiry_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->postJson(Config::get('app.GENERAL.app_url') . '/api/store-user-enquiry', [
            'name' => 'Test User',
            'phone' => '9898989120',
            'message' => 'Query Testing',
            'subject' => 'Other',
            'email' => 'testUser@digiflux.io'
        ]);
        // dd($response);
        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }
}
