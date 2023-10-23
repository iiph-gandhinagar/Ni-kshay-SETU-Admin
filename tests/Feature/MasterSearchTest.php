<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Http\Response;
use Tests\Feature\RegisterTest;
use Config;


class MasterSearchTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_master_search()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-master-search', [
            'search_text' => 'pulmonary',
        ]);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_module_master_search()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-module-master-search', [
            'search_text' => 'Available',
        ]);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_sub_module_master_search()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-sub-module-master-search', [
            'search_text' => 'Extra Pulmonary',
        ]);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_resource_material_master_search()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-resource-material-master-search', [
            'search_text' => 'Extra Pulmonary',
        ]);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_chat_question_master_search()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-chat-question-master-search', [
            'search_text' => 'Available',
        ]);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }
}
