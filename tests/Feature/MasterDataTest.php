<?php

namespace Tests\Feature;

use App\Models\StaticBlog;
use App\Models\StaticModule;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\Feature\RegisterTest;
use Tests\TestCase;
use Config;

class MasterDataTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_states_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-all-state');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_Country_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-all-country');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_district_by_state_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-district-by-state/' . rand(1, 37));

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_block_by_district_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-block-by-district/' . rand(1, 802));

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_cadre_by_type_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-all-cadre/Block_Level');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_health_by_block_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-health-by-block/' . rand(1, 6361));

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_symptoms_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-all-symptoms');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_tour_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-all-tour');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_tour_slides_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-all-tour-slides');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_tv_dashboard_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-all-dashboard-data');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_blogs_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-all-blogs');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_blogs_by_slug_details()
    {
        $newRequst = $this->formRequest();
        $blog = StaticBlog::create($newRequst);
        // $blog = StaticBlog::where('active', 1)->orderby('order_index')->limit(1)->get(['slug'])[0];
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-blogs-details/' . $blog['slug']);
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_similar_blogs_by_slug_details()
    {
        $newRequst = $this->formRequest();
        $blog = StaticBlog::create($newRequst);
        // $blog = StaticBlog::where('active', 1)->orderby('order_index', 'desc')->limit(1)->get(['slug'])[0];
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-similar-blogs-details/' . $blog['slug']);
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_faq_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-all-faq');
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_web_app_config_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-all-app-config');
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_static_dashboard_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-dashboard-details');
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_static_home_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-home-data');
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_static_release_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-static-release');
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_static_modules_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-static-modules');
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_static_modules_by_slug_details()
    {
        $newRequst = $this->formRequest();
        $modules = StaticBlog::create($newRequst);
        // $modules = StaticModule::where('active', 1)->orderBy('order_index', 'desc')->limit(1)->get(['slug'])[0];
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-static-modules-by-slug/' . $modules['slug']);
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function formRequest()
    {
        $newRequst['title'] = "Pradhan Mantri TB Mukt Bharat Abhiyaan";
        $newRequst['slug'] = "pradhan-mantri-tb-mukt-bharat-abhiyaan";
        $newRequst['short_description'] = "The Ministry of asasas and Family Welfarrgets by 2025.";
        $newRequst['description'] = "<ul><li>The Ministry of Health and Family Welfare (MoHFW).</li></ul>";
        $newRequst['author'] = "Team Ni-kshay SETU";
        $newRequst['source'] = "https://tbcindia.gov.in/index1.php?lang=1&level=1&sublinkid=5629&lid=3670";
        $newRequst['order_index'] = 1;
        $newRequst['active'] = 1;
        $newRequst['keywords'] = "TB mukt";
        $newRequst['created_At'] = now();
        $newRequst['updated_at'] = now();
        return $newRequst;
    }

    public function test_get_static_resource_material_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-static-resource-material');
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_level_wise_badge_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-level-wise-badges-information');
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_flash_news_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-all-flash-news');
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_similar_apps_details()
    {
        $response = $this->get(Config::get('app.GENERAL.app_url') . '/api/get-all-similar-apps');
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_module_usage_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-module-usage');
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_recently_added_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-recently-added');
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_all_automatic_notification_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-all-automatic-notification');
        // dd($response);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }
}