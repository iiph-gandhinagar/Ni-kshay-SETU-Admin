<?php

namespace Tests\Feature;

use App\Models\Otp;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Feature\RegisterTest;
use Illuminate\Http\Response;

class ForgotOtpTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_forgot_otp()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();

        $response = $this->post('http://localhost:8000/api/forgotPassword', [
            'phone_no' => $subscriber['phone_no'],
        ]);
        // Log::info($response->getContent());

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_verifiy_forgot_otp()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $forogotOtp = $this->postJson('http://localhost:8000/api/forgotPassword', [
            'phone_no' => '9898989120',
        ]);
        // dd($forogotOtp['data']);
        $otp = Otp::where('user_id', $subscriber['id'])->where('is_verified', 0)->orderBy('created_at', 'DESC')->get(['otp', 'created_at'])->take(1);
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->postJson('api/verified-forogot-password-otp', [
            'otp' => $otp[0]['otp'],
            'new_password' => 'Asd@1234',
            'confirm_password' => 'Asd@1234',
            'temp_token' => $forogotOtp['data'],
        ]);
        // Log::info($response->getContent());
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }
}
