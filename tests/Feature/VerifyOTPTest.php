<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Feature\RegisterTest;
use App\Models\Otp;
use Config;


class VerifyOTPTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_send_registration_otp()
    {
        // $subscriber = factory(Subscriber::class)->create();
        $subscriber = (new RegisterTest())->faker_subscriber();
        // $subscriber = factory(Subscriber::class)->create([
        //     'name' => 'Test User',
        //     'phone_no' => '9898989120',
        //     'password' => 'Asd@1234',
        //     'cadre_type' => 'National_Level',
        //     'country_id' => 1,
        //     'cadre_id' => 70,
        //     'block_id' => 0,
        //     'state_id' => 0,
        //     'district_id' => 0,
        //     'health_facility_id' => 0,
        //     'api_token' => 'TERFhuPi6PQo4pYngMzmcXKfRpa1kiZ8h9EJqH1kIO4LZUe2aBNYz3ONUp92',
        // ]);
        // $response = $this->actingAs($subscriber)->get('/api/generate-otp');
        // dd($subscriber['api_token']);
        // $response = $this->get('http://localhost:8000/api/generate-otp', [], ['Authorization' => $subscriber['api_token']]);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/generate-otp');
        // dd(json_decode($response->getContent()));
        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_verify_registration_otp()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $otp = factory(Otp::class)->create([
            'phone_no' => '9898989120',
            'user_id' => $subscriber['id'],
            'otp' => '123456',
            'is_verified' => 0,
            'message_body' => 'Hello Test User, Your Otp For Registration in T.B. App is 123456.',
            'is_delivered' => 0,
            'via' => 'SMS',
            'created_at' => now(),
        ]);
        // dd($otp);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->postJson(Config::get('app.GENERAL.app_url') . '/api/verified-otp', ['otp' => $otp['otp']]);
        // dd(json_decode($response->getContent()));
        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }
}
