<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\DynamicAlgorithm;
use App\Models\DynamicAlgoMaster;
use App\Models\Subscriber;
 
class DynamicAlgorithmsController extends BaseController
{
    public function getMasterNodes(Request $request, $key)
    {
        $lang = $request->header('lang');
        if($lang == NULL){
            $lang = 'en';
        }
        app()->setLocale($lang);
        $nodes = DynamicAlgorithm::where('activated',1)->where('algo_key',$key)->where('parent_id',0)
                                ->with(['media'])->orderBy('index')->get();
        return $this->sendResponse($nodes,'List of Master Nodes', 200);
    }

    public function getMasterNodesV2(Request $request, $key)
    {
        $user = Subscriber::where('api_token', $request->bearerToken())->get(['id','cadre_id','state_id','country_id']);
        $lang = $request->header('lang');
        if($lang == NULL){
            $lang = 'en';
        }
        app()->setLocale($lang);
        if($user[0]['state_id'] == 0){//for india user
            $nodes = DynamicAlgorithm::where('activated',1)->where('algo_key',$key)->where('parent_id',0)
                                    ->with(['media'])->whereRaw("find_in_set('".$user[0]['cadre_id']."',cadre_id)")
                                    ->orWhereRaw("find_in_set('".$user[0]['state_id']."',state_id)")->orderBy('index')->get();
        }else{
            $nodes = DynamicAlgorithm::where('activated',1)->where('algo_key',$key)->where('parent_id',0)
                                    ->with(['media'])->whereRaw("find_in_set('".$user[0]['cadre_id']."',cadre_id)")
                                    ->whereRaw("find_in_set('".$user[0]['state_id']."',state_id)")->orderBy('index')->get();
        }
        return $this->sendResponse($nodes,'List of Master Nodes', 200);
    }

    public function getDependentNodes(Request $request,$key,$masterNodeId)
    {
        $lang = $request->header('lang');
        if($lang == NULL){
            $lang = 'en';
        }
        app()->setLocale($lang);
        $nodes = DynamicAlgorithm::where('activated',1)->with(['children','media'])
                                ->where('algo_key',$key)
                                ->where('parent_id',$masterNodeId)
                                ->orderBy('index')->get();
        $title = DynamicAlgorithm::where('activated',1)
                                ->where('algo_key',$key)
                                ->where('id',$masterNodeId)
                                ->get(['title','description'])[0];
        $nodeDetails = ['title' => $title['title'],'description' => $title['description'], 'children' => $nodes];

        return $this->sendResponse($nodeDetails,'List of Dependent Nodes', 200);
    }

    public function getDynamicAlgoMasterGroupBySection()
    {
        $dynamicAlgo = DynamicAlgoMaster::with(['media'])->where('active',1)->get();
        // $dynamicAlgo = $dynamicAlgo->toArray();
        // $dynamicAlgoList['dynamicAlgoForPMT'] = array_filter($dynamicAlgo,function($item){ return $item['section'] == 'Patient Management Tool';});
        // $dynamicAlgoList['dynamicAlgoForNtep'] = array_filter($dynamicAlgo,function($item){ return $item['section'] == 'NTEP Interventions';});
        // $dynamicAlgoList['dynamicAlgoForCaseFindings'] = array_filter($dynamicAlgo,function($item){ return $item['section'] == 'Learn About Case Findings';});
        return $this->sendResponse($dynamicAlgo,'List of Dynamic Algo Master', 200);
    }
}
