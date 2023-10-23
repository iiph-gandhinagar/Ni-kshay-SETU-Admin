<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Subscriber;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Config;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_register_details()
    {
        $user = [
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
        ];

        $response = $this->post(Config::get('app.GENERAL.app_url') . '/api/store-user-v2', $user);


        // dd(json_decode($response->getContent())->data->api_token);
        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function faker_subscriber()
    {
        $subscriber = factory(Subscriber::class)->create([
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
            'api_token' => 'TERFhuPi6PQo4pYngMzmcXKfRpa1kiZ8h9EJqH1kIO4LZUe2aBNYz3ONUp92',
        ]);
        return $subscriber;
    }
}
