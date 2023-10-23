<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Config;

class LoginTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login()
    {
        $subscriber = $this->post(Config::get('app.GENERAL.app_url') . '/api/store-user-v2', [
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

        $response = $this->post(Config::get('app.GENERAL.app_url') . '/api/login', [
            'phone_no' => '9898989120',
            'password' => 'Asd@1234',
        ]);
        // dd(json_decode($response->getContent()));
        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }
}
