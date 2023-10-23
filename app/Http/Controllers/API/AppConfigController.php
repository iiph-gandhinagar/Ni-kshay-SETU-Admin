<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\AppManagementFlag;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class AppConfigController extends BaseController
{
    public function getCheckHealthStatus(Request $request)
    {

        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $agent = new Agent();
        $header = $request->header('platform');
        if (isset($header) && $header != "") {
            $plateform = $request->header('platform');
        } else {
            if ($agent->isMobile()) {
                $plateform = 'app';
            } elseif ($agent->isPhone() || $agent->is('iPhone')) {
                $plateform = 'iPhone-app';
            } elseif ($agent->isDesktop()) {
                $plateform = 'web';
            } else {
                $plateform = 'mobile-app';
            }
        }


        $maintenance = AppManagementFlag::where('variable', 'IS_IN_MAINTENANCE')->where('value->en', 'true')->get();
        $android_latest_app_version = AppManagementFlag::where('variable', 'ANDROID_APP_LATEST_VERSION')->get(['id', 'value']);
        $apple_latest_app_version = AppManagementFlag::where('variable', 'IPHONE_APP_LATEST_VERSION')->get(['id', 'value']);
        $android_minimum_app_version = AppManagementFlag::where('variable', 'ANDROID_APP_MINIMUM_ALLOWED_VERSION')->get(['id', 'value']);
        $apple_minimum_app_version = AppManagementFlag::where('variable', 'IPHONE_APP_MINIMUM_ALLOWED_VERSION')->get(['id', 'value']);
        $android_latest_app_version_features = AppManagementFlag::where('variable', 'ANDROID_APP_LATEST_VERSION_FEATURES')->get(['id', 'value']);
        $android_latest_app_version_size = AppManagementFlag::where('variable', 'ANDROID_APP_LATEST_VERSION_SIZE')->get(['id', 'value']);
        $general = AppManagementFlag::where('variable', 'GENERAL')->where('value->' . $lang, '!=', '')->get(['id', 'value']);
        $data = [];

        if ($plateform == 'mobile-app' || $plateform == 'postman' || $plateform == "iPhone-app" || $plateform == 'app') {
            if (count($maintenance) > 0) {
                if ($lang == 'en') {
                    $errorMsg = 'Maintenance Error';
                } else if ($lang == 'hi') {
                    $errorMsg = 'रखरखाव त्रुटि';
                } else if ($lang == 'gu') {
                    $errorMsg = 'જાળવણી ભૂલ';
                } else {
                    $errorMsg = 'देखभाल त्रुटी';
                }
                $data = [
                    'errorCode' => 503,
                    'errorMessage' => $errorMsg,
                    'message' => AppManagementFlag::where('variable', 'MAINTENANCE_MESSAGE')->get(['value'])[0],
                    'alertCategory' => 'PLANNED_MAINTENANCE',
                    'severity' => 'High'
                ];
            } else if (($plateform == 'mobile-app' && $request['app_version'] < $android_minimum_app_version[0]['value']) || ($plateform == "iPhone-app" && $request['app_version'] < $apple_minimum_app_version[0]['value'])) {
                if ($lang == 'en') {
                    $errorMsg = 'Upgrade Required Error';
                } else if ($lang == 'hi') {
                    $errorMsg = 'अपग्रेड आवश्यक त्रुटि';
                } else if ($lang == 'gu') {
                    $errorMsg = 'અપગ્રેડ જરૂરી ભૂલ';
                } else {
                    $errorMsg = 'अपग्रेड आवश्यक त्रुटी';
                }
                $data = [
                    'errorCode' => 426,
                    'errorMessage' => $errorMsg,
                    'message' => AppManagementFlag::where('variable', 'ANDROID_APP_MINIMUM_ALLOWED_VERSION_MESSAGE')->get(['value'])[0],
                    'alertCategory' => 'APP_UPDATE',
                    'severity' => 'High'
                ];
            } else if (($plateform == 'mobile-app' && $request['app_version'] < $android_latest_app_version[0]['value']) || ($plateform == "iPhone-app" && $request['app_version'] < $apple_latest_app_version[0]['value'])) {
                if ($lang == 'en') {
                    $errorMsg = 'App Version Update Required';
                } else if ($lang == 'hi') {
                    $errorMsg = 'अद्यतन ऐप संस्करण त्रुटि';
                } else if ($lang == 'gu') {
                    $errorMsg = 'એપ્લિકેશન સંસ્કરણ અપડેટ કરવામાં ભૂલ';
                } else {
                    $errorMsg = 'अॅप आवृत्ती अपडेट आवश्यक आहे';
                }
                $data = [
                    'errorCode' => 103,
                    'errorMessage' => $errorMsg,
                    'message' => AppManagementFlag::where('variable', 'ANDROID_APP_LATEST_VERSION_MESSAGE')->get(['value'])[0],
                    'Update_size' => $android_latest_app_version_size[0]['value'],
                    'new_feature' => $android_latest_app_version_features[0]['value'],
                    'alertCategory' => 'APP_UPDATE',
                    'severity' => 'Low'
                ];
            } else if (count($general) > 0) {
                if ($lang == 'en') {
                    $errorMsg = 'World Tuberculosis Day';
                } else if ($lang == 'hi') {
                    $errorMsg = 'विश्व टीबी दिवस';
                } else if ($lang == 'gu') {
                    $errorMsg = 'વિશ્વ ટીબી દિવસ';
                } else {
                    $errorMsg = 'जागतिक क्षयरोग दिन';
                }
                $data = [
                    'errorCode' => 202,
                    'errorMessage' => $errorMsg,
                    'message' => ['value' => $general[0]['value']],
                    'alertCategory' => 'GERNERAL',
                    'severity' => 'Low'
                ];
            } else {
                $data = [
                    'errorCode' => 200,
                    'errorMessage' => 'No Error',
                    'message' => ['value' => ''],
                    'alertCategory' => 'UP_TO_DATE',
                    'severity' => 'NA'
                ];
            }
        } else if ($plateform == "web") {
            if (count($maintenance) > 0) {
                if ($lang == 'en') {
                    $errorMsg = 'Maintenance Error';
                } else if ($lang == 'hi') {
                    $errorMsg = 'रखरखाव त्रुटि';
                } else if ($lang == 'gu') {
                    $errorMsg = 'જાળવણી ભૂલ';
                } else {
                    $errorMsg = 'देखभाल त्रुटी';
                }
                $data = [
                    'errorCode' => 503,
                    'errorMessage' => $errorMsg,
                    'message' => AppManagementFlag::where('variable', 'MAINTENANCE_MESSAGE')->get(['value'])[0],
                    'alertCategory' => 'PLANNED_MAINTENANCE',
                    'severity' => 'High'
                ];
            } else if (count($general) > 0) {
                if ($lang == 'en') {
                    $errorMsg = 'World Tuberculosis Day';
                } else if ($lang == 'hi') {
                    $errorMsg = 'विश्व टीबी दिवस';
                } else if ($lang == 'gu') {
                    $errorMsg = 'વિશ્વ ટીબી દિવસ';
                } else {
                    $errorMsg = 'जागतिक क्षयरोग दिन';
                }
                $data = [
                    'errorCode' => 202,
                    'errorMessage' => $errorMsg,
                    'message' => ['value' => $general[0]['value']],
                    'alertCategory' => 'GERNERAL',
                    'severity' => 'Low'
                ];
            } else {
                $data = [
                    'errorCode' => 200,
                    'errorMessage' => 'No Error',
                    'message' => ['value' => ''],
                    'alertCategory' => 'UP_TO_DATE',
                    'severity' => 'NA'
                ];
            }
        }
        $success = true;
        return ['status' => $success, 'data' => $data, 'code' => 200];
    }
}
