<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\TTrainingTag;
use App\Models\TModuleMaster;
use App\Models\TSubModuleMaster;
use App\Models\ResourceMaterial;
use App\Models\ChatQuestion;
use Config;

class TrainingController extends BaseController
{
    public function getAllTags(Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $tagData = TTrainingTag::get(['id', 'tag', 'is_fix_response', 'pattern', 'response->en as response_en', 'response->hi as response_hi', 'response->gu as response_gu', 'questions', 'modules', 'sub_modules', 'resource_material']);

        foreach ($tagData as $item) {
            $item['patterns'] = array_filter(explode('|', $item['pattern']));
            $item['responses_en'] = array_filter(explode('|', $item['response_en']));
            $item['responses_hi'] = array_filter(explode('|', $item['response_hi']));
            $item['responses_gu'] = array_filter(explode('|', $item['response_gu']));
            $item['responses_mr'] = array_filter(explode('|', $item['response_mr']));
            $item['questions'] = array_filter(array_map('intval', explode(',', $item['questions'])));
            $item['modules'] = array_filter(array_map('intval', explode(',', $item['modules'])));
            $item['sub_modules'] = array_filter(array_map('intval', explode(',', $item['sub_modules'])));
            $item['resource_material'] = array_filter(array_map('intval', explode(',', $item['resource_material'])));

            unset($item['pattern']);
            unset($item['response_en']);
            unset($item['response_hi']);
            unset($item['response_gu']);
            unset($item['response_mr']);
        }
        return $this->sendResponse($tagData, 'Tag Details!', 200);
    }

    /** Shifted to other controller */
    public function getTagWithMasterData(Request $request)
    {
        $lang = $request->header('lang');
        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);

        $data = \Http::timeout(Config::get('app.GENERAL.training_url_timeout'))->get(Config::get('app.GENERAL.training_url') . '/predict?query=2');
        $tagDetails = json_decode($data->getBody()->getContents(), true);

        if (isset($tagDetails['questions']) && $tagDetails['questions'] != "") {
            $tagDetails['questions'] = ChatQuestion::whereIn('id', $tagDetails['questions'])->get(['id', 'question', 'answer']);
        }

        if (isset($tagDetails['resource_material']) && $tagDetails['resource_material'] != "") {
            $tagDetails['resource_material'] = ResourceMaterial::with(['media'])->whereIn('id', $tagDetails['resource_material'])->get(['id', 'title']);
        }

        if (isset($tagDetails['modules']) && $tagDetails['modules'] != "") {
            $tagDetails['modules'] = TModuleMaster::whereIn('id', $tagDetails['modules'])->get(['id', 'name']);
        }

        if (isset($tagDetails['sub_modules']) && $tagDetails['sub_modules'] != "") {
            $tagDetails['sub_modules'] = TSubModuleMaster::whereIn('id', $tagDetails['sub_modules'])->get(['id', 'name', 'module_id', 'existing_module_ref']);
        }

        return $this->sendResponse($tagDetails, 'Tag Details!', 200);
    }
}
