<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\GuidanceOnAdverseDrugReaction;
use App\Models\Subscriber;

class GuidanceOnAdverseDrugReactionsController extends BaseController
{
    public function getMasterNodes(Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $nodes = GuidanceOnAdverseDrugReaction::where('activated', 1)->where('parent_id', 0)->with(['media'])->orderBy('index')->get();
        return $this->sendResponse($nodes, 'List of Master Nodes', 200);
    }

    public function getMasterNodesV2(Request $request)
    {
        $user = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id', 'state_id', 'country_id']);
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        if ($user[0]['state_id'] == 0) { //for india user
            $nodes = GuidanceOnAdverseDrugReaction::where('activated', 1)->where('parent_id', 0)->with(['media'])->whereRaw("find_in_set('" . $user[0]['cadre_id'] . "',cadre_id)")
                ->orWhereRaw("find_in_set('" . $user[0]['state_id'] . "',state_id)")->orderBy('index')->get();
        } else {
            $nodes = GuidanceOnAdverseDrugReaction::where('activated', 1)->where('parent_id', 0)->with(['media'])->whereRaw("find_in_set('" . $user[0]['cadre_id'] . "',cadre_id)")
                ->whereRaw("find_in_set('" . $user[0]['state_id'] . "',state_id)")->orderBy('index')->get();
        }
        return $this->sendResponse($nodes, 'List of Master Nodes', 200);
    }

    public function getDependentNodes(Request $request, $masterNodeId)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);

        $nodes = GuidanceOnAdverseDrugReaction::with(['children', 'media'])->where('activated', 1)->where('parent_id', $masterNodeId)->orderBy('index')->get();
        $title = GuidanceOnAdverseDrugReaction::where('activated', 1)->where('id', $masterNodeId)->get(['title', 'description'])[0];
        $nodeDetails = ['title' => $title['title'], 'description' => $title['description'], 'children' => $nodes];

        return $this->sendResponse($nodeDetails, 'List of Dependent Nodes', 200);
    }
}
