<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Feature\RegisterTest;
use Config;

class LeaderboardTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function test_leaderboard_details()
    // {
    //     $subscriber = (new RegisterTest())->faker_subscriber();
    //     $response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $subscriber['api_token'],
    //         'lang' => 'en',
    //     ])->get(Config::get('app.GENERAL.app_url') . '/api/get-leaderboard-details');

    //     $this->assertEquals(Response::HTTP_OK, $response['code']);
    // }

    public function test_leaderboard_task_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-leaderboard-task-list');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    // public function test_leaderboard_achivements_details()
    // {
    //     $subscriber = (new RegisterTest())->faker_subscriber();
    //     $response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $subscriber['api_token'],
    //         'lang' => 'en',
    //     ])->get(Config::get('app.GENERAL.app_url') . '/api/get-leaderboard-achivements');

    //     $this->assertEquals(Response::HTTP_OK, $response['code']);
    // }

    // public function test_store_sub_module_usage_details()
    // {
    //     $subscriber = $this->postJson(Config::get('app.GENERAL.app_url') . '/api/store-user-v2', [
    //         'name' => 'Test User',
    //         'phone_no' => '9898989120',
    //         'password' => 'Asd@1234',
    //         'cadre_type' => 'National_Level',
    //         'country_id' => 1,
    //         'cadre_id' => 70,
    //         'block_id' => 0,
    //         'state_id' => 0,
    //         'district_id' => 0,
    //         'health_facility_id' => 0,
    //         'is_verified' => 1,
    //     ]);
    //     // dd($subscriber['data']['api_token']);
    //     $response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $subscriber['data']['api_token'],
    //         'lang' => 'en',
    //     ])->postJson(
    //         Config::get('app.GENERAL.app_url') . '/api/store-sub-module-usage',
    //         [
    //             [
    //                 "id" => 1,
    //                 "module"  =>  "Guidance on ADR",
    //                 "sub_module_id" => 1,
    //                 "time" => 5,
    //                 "activity_type" =>  "submodule_usage"
    //             ],
    //             [
    //                 "id" => 2,
    //                 "module"  =>  "Guidance on ADR",
    //                 "sub_module_id" => 2,
    //                 "time" => 5,
    //                 "activity_type" =>  "submodule_usage"
    //             ],
    //             [
    //                 "id" => 3,
    //                 "module"  =>  "Case Definition",
    //                 "sub_module_id" => 3,
    //                 "time" => 5,
    //                 "activity_type" =>  "submodule_usage"
    //             ],
    //             [
    //                 "id" => 4,
    //                 "module"  =>  "Diagnosis Algorithm",
    //                 "sub_module_id" => 1,
    //                 "time" => 300,
    //                 "activity_type" =>  "submodule_usage"
    //             ],
    //             [
    //                 "id" => 5,
    //                 "module"  =>  "Treatment Algorithm",
    //                 "sub_module_id" => 2,
    //                 "time" => 300,
    //                 "activity_type" =>  "submodule_usage"
    //             ],
    //             [
    //                 "id" => 6,
    //                 "module"  =>  "Latent TB Infection",
    //                 "sub_module_id" => 1,
    //                 "time" => 300,
    //                 "activity_type" =>  "submodule_usage"
    //             ],
    //             [
    //                 "id" => 7,
    //                 "module"  =>  "Differentiated Care Of TB Patients",
    //                 "sub_module_id" => 1,
    //                 "time" => 300,
    //                 "activity_type" =>  "submodule_usage"
    //             ],
    //             [
    //                 "id" => 8,
    //                 "module"  =>  "NTEP Intervention",
    //                 "sub_module_id" => 1,
    //                 "time" => 300,
    //                 "activity_type" =>  "submodule_usage"
    //             ],
    //         ],
    //     );

    //     $this->assertEquals(Response::HTTP_OK, $response['code']);
    // }
}