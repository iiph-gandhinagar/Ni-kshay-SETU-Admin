<?php

namespace Tests\Feature;

use App\Models\CgcIntervention;
use App\Models\ResourceMaterial;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Feature\RegisterTest;
use Config;

class MaterialTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_all_chapter_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-all-chapters');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    // public function test_get_all_chapter_by_id_details()
    // {
    //     $subscriber = (new RegisterTest())->faker_subscriber();
    //     $chapter_id = CgcIntervention::limit(1)->get(['id'])[0];
    //     $response = $this->withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $subscriber['api_token'],
    //     ])->get(Config::get('app.GENERAL.app_url') . '/api/get-chapter-by-id/' . $chapter_id['id']);

    //     $this->assertEquals(Response::HTTP_OK, $response['code']);
    // }

    public function test_get_material_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-material/pdfs');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_root_folder_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-root-folders');

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function test_get_files_by_parent_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $newRequest = $this->formRequest();
        $materials = ResourceMaterial::create($newRequest);
        // $materials = ResourceMaterial::with(['media'])->where('parent_id', 0)->where('type_of_materials', 'folder')->orderBy('index')->limit(1)->get(['id'])[0];
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-files-by-parent/' . $materials['id']);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }

    public function formRequest()
    {
        $newRequest['title'] = "resource";
        $newRequest['type_of_materials'] = "ppt";
        $newRequest['country_id'] = "1";
        $newRequest['state'] = "1";
        $newRequest['cadre'] = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,57,58,59,60,61,62,63,70,71,72,73,74,75,77,78,79,82,83";
        $newRequest['parent_id'] = "1";
        $newRequest['index'] = 1000;
        $newRequest['created_by'] = 1;
        $newRequest['created_at'] = now();
        $newRequest['updated_at'] = now();
        return $newRequest;
    }

    public function test_get_health_facility_details()
    {
        $subscriber = (new RegisterTest())->faker_subscriber();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $subscriber['api_token'],
            'lang' => 'en',
        ])->get(Config::get('app.GENERAL.app_url') . '/api/get-health-facilities', [
            'state_id' => 1,
            'district_id' => 3,
            'block_id' => 10,
            'health_facility' =>  'x_ray,ICTC',
            'sort' => 'asc',
        ]);

        $this->assertEquals(Response::HTTP_OK, $response['code']);
    }
}