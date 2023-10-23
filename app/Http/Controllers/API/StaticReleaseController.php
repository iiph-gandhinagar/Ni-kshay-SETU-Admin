<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\StaticRelease;
use App\Models\StaticModule;
use Illuminate\Http\Request;
use App\Helpers\RequestHelpers;
use App\Models\StaticResourceMaterial;

class StaticReleaseController extends BaseController
{

    public function getStaticRelease(Request $request)
    {
        $lang = $request->header('lang');
        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $paginationParams = RequestHelpers::getPaginationParams($request);
        $staticRelease = StaticRelease::where('active', 1)
            ->orderBy('order_index')
            ->paginate($paginationParams['per_page']);
        $success = true;
        return ['status' => $success, 'data' => $staticRelease, 'code' => 200];
    }

    public function staticModules(Request $request)
    {
        $lang = $request->header('lang');
        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $paginationParams = RequestHelpers::getPaginationParams($request);
        $staticModules = StaticModule::with(['media'])
            ->where('active', 1)
            ->orderBy('order_index')
            ->paginate($paginationParams['per_page']);
        $success = true;
        return ['status' => $success, 'data' => $staticModules, 'code' => 200];
    }

    public function getStaticModuleBySlug(Request $request, $slug)
    {
        $lang = $request->header('lang');
        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $staticModules = StaticModule::with(['media'])->where('slug', $slug)->get();
        $success = true;
        return ['status' => $success, 'data' => $staticModules, 'code' => 200];
    }

    public function staticresourceMaterial(Request $request)
    {
        $lang = $request->header('lang');
        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);

        $paginationParams = RequestHelpers::getPaginationParams($request);
        $staticModules = StaticResourceMaterial::with(['media'])
            ->where('active', 1)
            ->orderBy('order_index')
            ->paginate($paginationParams['per_page']);
        $success = true;
        return ['status' => $success, 'data' => $staticModules, 'code' => 200];
    }
}
