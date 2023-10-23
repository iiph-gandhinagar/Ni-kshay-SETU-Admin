<?php

namespace Tests\Feature;

use App\Models\Assessment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Http\Response;
use Tests\Feature\RegisterTest;
use Config;

class CertificateTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_all_certificates()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-all-certificates');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    // public function test_get_certificate_by_id()
    // {
    //     $subscriber = (new RegisterTest())->faker_subscriber();
    //     // $assessment = UserAssessment::where('user_id', $subscriber['id'])->limit(1)->get(['assessment_id'])[0];
    //     $assessment = Assessment::where('activated', 1)->limit(1)->get(['id'])[0];
    //     $assessment_details = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $subscriber['api_token'],
    //     ])->postJson(Config::get('app.GENERAL.app_url') . '/api/get-subscriber-assessment-details', [
    //         'assessment_id' => $assessment['id'],
    //     ]);
    //     $response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $subscriber['api_token'],
    //         'lang' => 'en',
    //     ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-certificate-pdf/' . $assessment['id']);

    //     $this->assertEquals(Response::HTTP_OK, $response['code']);
    // }
}