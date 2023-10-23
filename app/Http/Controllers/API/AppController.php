<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\AppConfig;
use App\Models\MasterCm;
use App\Models\LbLevel;
use Illuminate\Http\Request;
use Cache;
use Config;

class AppController extends BaseController
{

    public function getConfiguration(Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }

        app()->setLocale($lang);

        if (Cache::has('db_config')) {
            $dbConfig = Cache::get('db_config');
        } else {

            $dbConfig = AppConfig::all();
            Cache::put('db_config', $dbConfig, Config::get('app.GENERAL.app_config_cache_time_out'));
        }

        $appConfigsForClient = new \stdClass();
        $dbConfig->map(function ($config) use ($lang, $appConfigsForClient) {
            // $config->setlocale($lang);
            $appConfigsForClient->{$config->key} = $config->value_json;
        });


        $lang_array = Config::get('translatable.locales');
        $array = [];
        for ($i = 0; $i < count($lang_array); $i++) {
            if ($lang_array[$i] == 'en') {
                $array[$i] = ['title' => "English", 'sub_title' => 'English', 'code' => $lang_array[$i], 'img_url' => "https://api.nikshay-setu.in/uploads/1674454407LangEng.png"];
            }
            if ($lang_array[$i] == 'hi') {
                $array[$i] = ['title' => "हिन्दी", 'sub_title' => 'Hindi', 'code' => $lang_array[$i], 'img_url' => "https://api.nikshay-setu.in/uploads/1674454349LangHindi.png"];
            }
            if ($lang_array[$i] == 'gu') {
                $array[$i] = ['title' => "ગુજરાતી", 'sub_title' => 'Gujarati', 'code' => $lang_array[$i], 'img_url' => "https://api.nikshay-setu.in/uploads/1674454382LangGujarati.png"];
            }
            if ($lang_array[$i] == 'mr') {
                $array[$i] = ['title' => "मराठी", 'sub_title' => 'Marathi', 'code' => $lang_array[$i], 'img_url' => "https://api.nikshay-setu.in/uploads/1674454275LangMarathi.png"];
            }
            if ($lang_array[$i] == 'ta') {
                $array[$i] = ['title' => "தமிழ்", 'sub_title' => 'Tamil', 'code' => $lang_array[$i], 'img_url' => "https://api.nikshay-setu.in/uploads/1674551981LangTamil.png"];
            }
            if ($lang_array[$i] == 'pa') {
                $array[$i] = ['title' => "ਪੰਜਾਬੀ", 'sub_title' => 'Punjabi', 'code' => $lang_array[$i], 'img_url' => "https://api.nikshay-setu.in/uploads/1674551981LangTamil.png"];
            }
            if ($lang_array[$i] == 'te') {
                $array[$i] = ['title' => "తెలుగు", 'sub_title' => 'Telugu', 'code' => $lang_array[$i], 'img_url' => "https://api.nikshay-setu.in/uploads/1674551981LangTamil.png"];
            }
            if ($lang_array[$i] == 'kn') {
                $array[$i] = ['title' => "ಕನ್ನಡ", 'sub_title' => 'Kannada', 'code' => $lang_array[$i], 'img_url' => "https://api.nikshay-setu.in/uploads/1674551981LangTamil.png"];
            }
        }

        $appConfig = [
            'health_facility_mapping' => trans('admin.appConfig'),
            'app_translations' => $appConfigsForClient,
            'master_cms' =>  MasterCm::get(),
            'languages' => $array,
            'Leader_Board_Information' => LbLevel::get(),
        ];



        $success = true;
        return ['status' => $success, 'data' => $appConfig, 'code' => 200];
    }
}
