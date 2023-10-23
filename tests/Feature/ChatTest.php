<?php

namespace Tests\Feature;

use App\Models\ChatKeyword;
use App\Models\ChatQuestion;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Feature\RegisterTest;
use Log;
use Config;

class ChatTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_chat_keyword()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-keywords');

        $this->assertEquals(true, $response['success']);
    }

    // public function test_chat_question_by_keyword()
    // {
    //     $subscriber = (new RegisterTest())->faker_subscriber();
    //     $newRequest = $this->formRequest();
    //     $keyowrd = ChatKeyword::create($newRequest);
    //     // $keyowrd = ChatKeyword::orderBy('custom_ordering', 'desc')->limit(1)->get(['id'])[0];
    //     $response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $subscriber['api_token'],
    //         'lang' => 'en',
    //     ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-questions-by-keyword-v3/' . $keyowrd['id']);

    //     $this->assertEquals(true, $response['success']);
    // }

    // public function test_search_by_keyword()
    // {
    //     $subscriber = (new RegisterTest())->faker_subscriber();
    //     $newRequest = $this->formRequest();
    //     $keyowrd = ChatKeyword::create($newRequest);
    //     // $keyowrd = ChatKeyword::orderBy('custom_ordering', 'desc')->limit(1)->get(['title'])[0];
    //     $response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $subscriber['api_token'],
    //         'lang' => 'en',
    //     ])->getJson(Config::get('app.GENERAL.app_url') . '/api/search-by-keyword-v2/' . $keyowrd['title']);
    //     // dd($response);
    //     $this->assertEquals(true, $response['success']);
    // }

    public function formRequest()
    {
        $newRequest['title'] = "Tuberculosis";
        $newRequest['hit'] = 1111;
        $newRequest['modules'] = '2,6';
        $newRequest['sub_modules'] = '';
        $newRequest['resource_material'] = '21,22';
        $newRequest['custom_ordering'] = 1;
        $newRequest['created_at'] = now();
        $newRequest['updated_at'] = now();
        return $newRequest;
    }

    // public function test_get_text_to_speech()
    // {
    //     $subscriber = (new RegisterTest())->faker_subscriber();
    //     $response = $this->withHeaders([
    //         'Accept' => '*/*',
    //         'Content-Type' => 'multipart/form-data',
    //         'Authorization' => 'Bearer ' . $subscriber['api_token'],
    //         'lang' => 'gu',
    //     ])->post(Config::get('app.GENERAL.app_url') . '/api/get-text-to-speech', [
    //         'text' => '<p>&bull;&nbsp;&nbsp; &nbsp;Recheck the dose of drugs&nbsp;<br />\n&bull;&nbsp;&nbsp; &nbsp;Exclude all other causes of symptoms&nbsp;<br />\n&bull;&nbsp;&nbsp; &nbsp;Estimate the severity of the adverse effects&nbsp;<br />\n&bull;&nbsp;&nbsp; &nbsp;Document the adverse effects&nbsp;<br />\n&bull;&nbsp;&nbsp; &nbsp;The offending drugs may need to be stopped (if the severity is more) and to be reintroduced gradually when the symptoms disappear&nbsp;<br />\n&nbsp;</p>'
    //     ]);
    //     // Log::info(json_decode($response->getContent()));
    //     // dd($response);
    //     // dd(json_decode($response->getStatusCode()));
    //     $this->assertEquals(200, $response->getStatusCode());
    // }

    public function test_submit_question_hit_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequestChat();
        $question_id = ChatQuestion::create($newRequest);
        // $question_id = ChatQuestion::orderBy('id', 'desc')->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->postJson(Config::get('app.GENERAL.app_url') . '/api/submit-question-hit', [
            'question_id' => $question_id['id'],
            'session_token' => 'qsa-sweaf-was',
        ]);
        // dd($response);
        $this->assertEquals(true, $response['success']);
    }

    public function test_submit_feedback_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequestChat();
        $question_id = ChatQuestion::create($newRequest);
        // $question_id = ChatQuestion::orderBy('id', 'desc')->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->postJson(Config::get('app.GENERAL.app_url') . '/api/submit-feedback', [
            'activity_id' => 1,
            'question_id' => $question_id['id'],
            'tag_id' => 0,
            'like' => 1,
            'dislike' => 0,
        ]);
        // dd($response);
        $this->assertEquals(true, $response['success']);
    }

    public function formRequestChat()
    {
        $newRequest['question'] = "What is the process for obtaining OTP based Pradhan Mantri TB Mukt Bharat abhiyaan consent from the patient?";
        $newRequest['answer'] = "At the time of enrolment in Ni-kshay Portal, Health staff has to explai";
        $newRequest['hit'] = '2';
        $newRequest['cadre_id'] = '1,2';
        $newRequest['category'] = 'ACSM';
        $newRequest['activated'] = 1;
        $newRequest['like_count'] = 1;
        $newRequest['dislike_count'] = 0;
        $newRequest['created_at'] = now();
        $newRequest['updated_at'] = now();
        return $newRequest;
    }

    // public function test_get_tag_with_master_details()
    // {
    //     $subscriber = (new RegisterTest())->faker_subscriber();
    //     $response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $subscriber['api_token'],
    //         'lang' => 'en',
    //     ])->getJson(Config::get('app.GENERAL.app_url') . '/api/get-tag-with-master-data');

    //     $this->assertEquals(true, $response['success']);
    // }
}