<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Feature\RegisterTest;
use Config;

class UserProfileTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_profile()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();

        // Log::info($subscriber);
        // dd($subscriber);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/generate-otp');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_update_user_profile()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        // Log::info($subscriber);
        // dd($subscriber);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->postJson(Config::get('app.GENERAL.app_url') . '/api/update-user-details-v2', [
            'cadre_type' => 'State_Level',
            'name' => 'Testing User',
            'state_id' => 1,
        ]);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }
}
