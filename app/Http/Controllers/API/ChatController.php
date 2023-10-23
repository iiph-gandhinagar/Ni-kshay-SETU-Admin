<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\ChatKeyword;
use App\Models\ChatKeywordHit;
use App\Models\ChatQuestion;
use App\Models\ChatQuestionHit;
use App\Models\ChatQuestionKeyword;
use App\Models\Subscriber;
use App\Models\ChatbotActivity;
use App\Models\TTrainingTag;
use App\Models\TModuleMaster;
use App\Models\TSubModuleMaster;
use App\Models\ResourceMaterial;
use Validator;
use Jenssegers\Agent\Agent;
use DB;
use Log;
use Config;

class ChatController extends BaseController
{
    public $keywordLimit;
    public $questionLimit;

    public function __construct()
    {
        $this->keywordLimit = \Config::get('constant.limit.KEYWORD_LIMIT');
        $this->questionLimit = \Config::get('constant.limit.QUESTION_LIMIT');
    }

    public function getTopKeywords(Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $searchedKeywords = [];
        if ($request['session_token'] && $request['session_token'] != '') {
            $searchedKeywords = ChatKeywordHit::where('session_token', $request['session_token'])->pluck('keyword_id');
        }
        $keywordData = ChatKeyword::whereNotIn('id', $searchedKeywords)->orderBy('custom_ordering', 'asc')->paginate($this->keywordLimit);
        // $keywordData->setPath(config('app.url')); 
        return $this->sendResponse($keywordData, 'List of Keywords!', 200);
    }

    public function getQuestionsByKeyword(Request $request, $keyword)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $searchedQuestions = [];
        if ($request['session_token'] && $request['session_token'] != '') {
            $searchedQuestions = ChatQuestionHit::where('session_token', $request['session_token'])->pluck('question_id');
        }
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id'])[0];
        $questionData = ChatQuestion::where('activated', 1)->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
            ->whereNotIn('id', $searchedQuestions)->orderBy('hit', 'desc')->paginate($this->questionLimit);
        //function for store keyword hit
        /* ->whereHas('keywords', function ($q) use ($keyword) {
            $q->where('keyword_id', $keyword);
        }) */
        $this->storeKeywordHit($request, $keyword);

        //function for store chatbot activity
        $request['action'] = 'keyword-click';
        $request['payload'] = ChatKeyword::where('id', $keyword)->pluck('title')[0];
        $this->storeChatBotActivity($request);

        return $this->sendResponse($questionData, 'List of Questions!', 200);
    }

    public function getQuestionsByKeywordV2(Request $request, $keyword)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $searchedQuestions = [];
        $tagDetails = [];
        $chatKeywordDetails = ChatKeyword::where('id', $keyword)->get()[0];
        if ($request['session_token'] && $request['session_token'] != '') {
            $searchedQuestions = ChatQuestionHit::where('session_token', $request['session_token'])->pluck('question_id');
        }
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id'])[0];
        $tagDetails['questions'] = ChatQuestion::where('activated', 1)->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
            ->whereNotIn('id', $searchedQuestions)->orderBy('hit', 'desc')->get(['id', 'question', 'answer']);
        /* ->whereHas('keywords', function ($q) use ($keyword) {
            $q->where('keyword_id', $keyword);
        }) */

        if (isset($chatKeywordDetails['resource_material']) && $chatKeywordDetails['resource_material'] != "" && $chatKeywordDetails['resource_material'] != null) {
            $resourceMaterialIds = explode(',', $chatKeywordDetails['resource_material']);
            $tagDetails['resource_material'] = ResourceMaterial::with(['media'])->whereIn('id', $resourceMaterialIds)
                ->get(['id', 'title', 'type_of_materials', 'icon_type']);
        }

        if (isset($chatKeywordDetails['modules']) && $chatKeywordDetails['modules'] != "" && $chatKeywordDetails['modules'] != null) {
            $moduleIds = explode(',', $chatKeywordDetails['modules']);
            $tagDetails['modules'] = TModuleMaster::whereIn('id', $moduleIds)->get(['id', 'name']);
        }

        if (isset($chatKeywordDetails['sub_modules']) && $chatKeywordDetails['sub_modules'] != "" && $chatKeywordDetails['sub_modules'] != null) {
            $subModuleIds = explode(',', $chatKeywordDetails['sub_modules']);
            $tagDetails['sub_modules'] = TSubModuleMaster::whereIn('id', $subModuleIds)->get(['id', 'name', 'module_id', 'existing_module_ref']);
        }
        //function for store keyword hit
        $this->storeKeywordHit($request, $keyword);

        //function for store chatbot activity
        $request['action'] = 'keyword-click';
        $request['payload'] = ChatKeyword::where('id', $keyword)->pluck('title')[0];
        $this->storeChatBotActivity($request);

        return $this->sendResponse($tagDetails, 'List of Questions!', 200);
    }

    public function getQuestionsByKeywordV3(Request $request, $keyword)
    {
        $keywordInput = ChatKeyword::where('id', $keyword)->get('title')[0];
        $data = \Http::timeout(Config::get('app.GENERAL.training_url_timeout'))->get(Config::get('app.GENERAL.training_url') . '/mlpredict?query=' . $keywordInput['title']);
        $queryInEng =  $keywordInput['title'];
        $tagDetails = json_decode($data->getBody()->getContents(), true);
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }

        app()->setLocale($lang);
        $searchedQuestions = [];
        // $chatKeywordDetails = ChatKeyword::where('id',$keyword)->get()[0];
        if ($request['session_token'] && $request['session_token'] != '') {
            $searchedQuestions = ChatQuestionHit::where('session_token', $request['session_token'])->pluck('question_id');
        }
        $subscriber = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id', 'country_id'])[0];
        if (isset($tagDetails['questions']) && $tagDetails['questions'] != "") {
            $tagDetails['questions'] = ChatQuestion::where('activated', 1)
                ->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
                ->whereIn('id', $tagDetails['questions'])
                ->whereNotIn('id', $searchedQuestions)
                ->orderBy('hit', 'desc')
                ->get(['id', 'question', 'answer']);
        }
        if (isset($tagDetails['resource_material']) && $tagDetails['resource_material'] != "" && $tagDetails['resource_material'] != null) {
            // $resourceMaterialIds = explode(',',$tagDetails['resource_material']);
            if ($subscriber['country_id'] != 0) {
                $tagDetails['resource_material'] = ResourceMaterial::with(['media'])
                    ->whereIn('id', $tagDetails['resource_material'])
                    ->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre)")
                    ->whereRaw("find_in_set('" . $subscriber['country_id'] . "',country_id)")
                    ->where('type_of_materials', '!=', 'folder')
                    ->get(['id', 'title', 'type_of_materials', 'icon_type']);
            } else {
                $tagDetails['resource_material'] = ResourceMaterial::with(['media'])
                    ->whereIn('id', $tagDetails['resource_material'])
                    ->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre)")
                    ->where('state', 'LIKE', '%' . $subscriber['state_id'] . '%')
                    ->where('type_of_materials', '!=', 'folder')
                    ->get(['id', 'title', 'type_of_materials', 'icon_type']);
            }
        }

        if (isset($tagDetails['modules']) && $tagDetails['modules'] != "" && $tagDetails['modules'] != null) {
            // $moduleIds = explode(',',$tagDetails['modules']);
            $tagDetails['modules'] = TModuleMaster::whereIn('id', $tagDetails['modules'])->get(['id', 'name']);
        }

        if (isset($tagDetails['sub_modules']) && $tagDetails['sub_modules'] != "" && $tagDetails['sub_modules'] != null) {
            // $subModuleIds = explode(',',$tagDetails['sub_modules']);
            $tagDetails['sub_modules'] = TSubModuleMaster::whereIn('id', $tagDetails['sub_modules'])->get(['id', 'name', 'module_id', 'existing_module_ref']);
        }

        $request['response'] = 1;
        if ($tagDetails == "") {
            $request['response'] = 0;
        }

        if ($tagDetails != '' && isset($tagDetails['id'])) {
            $request['tag_id'] = $tagDetails['id'];
        }

        /* Additional Integration with iDefeat */
        // try {
        //     $ntepResponse = Http::timeout(Config::get('app.GENERAL.NTEP_timeout'))->get(Config::get('app.GENERAL.NTEP_base_url') . $queryInEng);
        //     $tagDetails['external_idefeat'] = $ntepResponse->json();
        // } catch (\Exception $e) {
        // }

        //function for store keyword hit
        $this->storeKeywordHit($request, $keyword);
        // if(isset($tagDetails['id'])){
        //     $fetch_external_material = TTrainingTag::where('id', $tagDetails['id'])->get(['fetch_external_material'])[0];
        // }


        //function for store chatbot activity
        $request['action'] = 'keyword-click';
        $request['payload'] = ChatKeyword::where('id', $keyword)->pluck('title')[0];
        $insertedId = $this->storeChatBotActivity($request);
        $tagDetails['activity_id'] = $insertedId;
        unset($tagDetails['responses_en']);
        unset($tagDetails['responses_hi']);
        unset($tagDetails['responses_gu']);
        if (isset($tagDetails['resource_material']) && count($tagDetails['resource_material']) == 0) {
            unset($tagDetails['resource_material']);
        }


        // if (!(isset($request['NTEP'])) && $request['NTEP'] != 1 ) { /* || isset($fetch_external_material) && $fetch_external_material->fetch_external_material == 0 */
        //     if (isset($tagDetails['external_idefeat']) && count($tagDetails['external_idefeat']) == 0) {
        //         unset($tagDetails['external_idefeat']);
        //     }
        // }

        // if (isset($tagDetails['external_idefeat']) && count($tagDetails['external_idefeat']) == 0) {
        // unset($tagDetails['external_idefeat']);
        // }

        // unset($tagDetails['external_idefeat']);
        if (isset($tagDetails) && ((isset($tagDetails['modules']) && count($tagDetails['modules']) > 0) || (isset($tagDetails['patterns']) && count($tagDetails['patterns']) > 0) || (isset($tagDetails['questions']) && count($tagDetails['questions']) > 0) || (isset($tagDetails['resource_material']) && count($tagDetails['resource_material']) > 0))) {

            return $this->sendResponse($tagDetails, 'List of Questions!', 200);
        } else {
            return $this->sendResponse(null, 'Error!', 400);
        }
    }
    public function serachQuestionsByKeyword($keyword, Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
            app()->setLocale($lang);
            $keyword =  strtoupper($keyword);
            $searchedQuestions = [];
            if ($request['session_token'] && $request['session_token'] != '') {
                $searchedQuestions = ChatQuestionHit::where('session_token', $request['session_token'])->pluck('question_id');
            }
            $subscriber = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id'])[0];

            $keywordIDs = ChatKeyword::where(DB::raw('UPPER(title)'), 'LIKE', '%' . $keyword . '%')->orWhere('title->' . $lang, 'SOUNDS LIKE', "%{$keyword}%")->orderBy('custom_ordering', 'asc')->pluck('id');

            $questionIDs = ChatQuestionKeyword::whereIn('keyword_id', $keywordIDs)->whereNotIn('question_id', $searchedQuestions)->pluck('question_id');
            if (count($questionIDs) == 0) { //no questions found by keywords
                $questionData = ChatQuestion::where('activated', 1)->where(DB::raw('UPPER(question)'), 'LIKE', '%' . $keyword . '%')
                    ->orWhere('question->' . $lang, 'SOUNDS LIKE', "%{$keyword}%")
                    ->whereNotIn('id', $searchedQuestions)
                    ->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
                    ->orderBy('hit', 'desc')->paginate($this->questionLimit);
            } else if (count($questionIDs) < $this->questionLimit) { //less questions found from hits than limt
                $questionData = ChatQuestion::where('activated', 1)->where(function ($query) use ($questionIDs, $keyword, $lang) {
                    $query->whereIn('id', $questionIDs);
                    $query->orWhere(DB::raw('UPPER(question)'), 'LIKE', '%' . $keyword . '%');
                    $query->orWhere('question->' . $lang, 'SOUNDS LIKE', "%{$keyword}%");
                })->whereNotIn('id', $searchedQuestions)
                    ->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
                    ->orderBy('hit', 'desc')->paginate($this->questionLimit);
            } else { //got enough questions by keyword
                $questionData = ChatQuestion::where('activated', 1)->whereRaw("find_in_set('" . $subscriber['cadre_id'] . "',cadre_id)")
                    ->whereIn('id', $questionIDs)->orderBy('hit', 'desc')
                    ->paginate($this->questionLimit);
            }

            //function for store chatbot activity
            $request['action'] = 'text-search';
            $request['payload'] = $keyword;
            $this->storeChatBotActivity($request);

            return $this->sendResponse($questionData, 'List of Questions!', 200);
        }
    }

    public function serachQuestionsByKeywordV2(Request $request, $keywordInput = '')
    {
        $lang = $request->header('lang');
        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);

        if (isset($keywordInput) && $keywordInput != '') {
            $keyword = $keywordInput;
        } else {
            $keyword = $request['query'];
        }
        $subscriberDetails = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id', 'country_id'])[0];

        $data = \Http::timeout(Config::get('app.GENERAL.training_url_timeout'))->get(Config::get('app.GENERAL.training_url') . '/mlpredict?query=' . $keyword);
        $tagDetails = json_decode($data->getBody()->getContents(), true);

        if (isset($tagDetails['questions']) && $tagDetails['questions'] != "") {

            $tagDetails['questions'] = ChatQuestion::whereIn('id', $tagDetails['questions'])
                ->whereRaw("find_in_set('" . $subscriberDetails['cadre_id'] . "',cadre_id)")
                ->orderBy('hit', 'desc')
                ->get(['id', 'question', 'answer']);
        }

        if (isset($tagDetails['resource_material']) && $tagDetails['resource_material'] != "") {
            if ($subscriberDetails['country_id'] != 0) {
                $tagDetails['resource_material'] = ResourceMaterial::with(['media'])
                    ->whereIn('id', $tagDetails['resource_material'])
                    ->whereRaw("find_in_set('" . $subscriberDetails['cadre_id'] . "',cadre)")
                    ->whereRaw("find_in_set('" . $subscriberDetails['country_id'] . "',country_id)")
                    ->where('type_of_materials', '!=', 'folder')
                    ->get(['id', 'title', 'type_of_materials', 'icon_type']);
            } else {

                $tagDetails['resource_material'] = ResourceMaterial::with(['media'])
                    ->whereIn('id', $tagDetails['resource_material'])
                    ->whereRaw("find_in_set('" . $subscriberDetails['cadre_id'] . "',cadre)")
                    ->where('state', 'LIKE', '%' . $subscriberDetails['state_id'] . '%')
                    ->where('type_of_materials', '!=', 'folder')
                    ->get(['id', 'title', 'type_of_materials', 'icon_type']);
            }
        }

        if (isset($tagDetails['modules']) && $tagDetails['modules'] != "") {
            $tagDetails['modules'] = TModuleMaster::whereIn('id', $tagDetails['modules'])->get(['id', 'name']);
        }

        if (isset($tagDetails['sub_modules']) && $tagDetails['sub_modules'] != "") {
            $tagDetails['sub_modules'] = TSubModuleMaster::whereIn('id', $tagDetails['sub_modules'])->get(['id', 'name', 'module_id', 'existing_module_ref']);
        }
        $request['response'] = 1;
        if ($tagDetails == "") {
            $request['response'] = 0;
        }

        if ($tagDetails != '' && isset($tagDetails['id'])) {
            $request['tag_id'] = $tagDetails['id'];
            // $fetch_external_material = TTrainingTag::where('id', $tagDetails['id'])->get(['fetch_external_material'])[0];
        }

        /* Additional Integration with iDefeat */
        // try {
        //     $ntepResponse = Http::timeout(Config::get('app.GENERAL.NTEP_timeout'))->get(Config::get('app.GENERAL.NTEP_base_url') . $keyword);
        //     $tagDetails['external_idefeat'] = $ntepResponse->json();
        // } catch (\Exception $e) {
        // }

        if (isset($tagDetails['is_fix_response']) && $tagDetails['is_fix_response'] == 1) {
            if (isset($tagDetails['responses_' . $lang]) && count($tagDetails['responses_' . $lang]) > 0) {
                $randomNumber = rand(0, count($tagDetails['responses_' . $lang]) - 1);
                $tagDetails['responses'] = $tagDetails['responses_' . $lang][$randomNumber];
            } else {
                $randomNumber = rand(0, count($tagDetails['responses_en']) - 1);
                $tagDetails['responses'] = $tagDetails['responses_en'][$randomNumber];
            }
        }

        //function for store chatbot activity
        $request['action'] = 'text-search';
        $request['payload'] = $keyword;

        $insertedId = $this->storeChatBotActivity($request);
        $tagDetails['activity_id'] = $insertedId;
        unset($tagDetails['responses_en']);
        unset($tagDetails['responses_hi']);
        unset($tagDetails['responses_gu']);

        if (isset($tagDetails['resource_material']) && count($tagDetails['resource_material']) == 0) {
            unset($tagDetails['resource_material']);
        }

        // if (!(isset($request['NTEP'])) && $request['NTEP'] != 1 ) { /* || isset($fetch_external_material) && $fetch_external_material->fetch_external_material == 0 */
        //     unset($tagDetails['external_idefeat']);
        // }
        // if (isset($tagDetails['external_idefeat']) && count($tagDetails['external_idefeat']) == 0) {
        // unset($tagDetails['external_idefeat']);
        // }
        // unset($tagDetails['external_idefeat']);

        return $this->sendResponse($tagDetails, 'List of Details!', 200);
    }

    public function getTextToSpeech(Request $request)
    {
        $lang = $request->header('lang');
        $text = $request->text;
        if ($lang == NULL) {
            $lang = 'en';
        }
        // $text = strip_tags($text);
        $options = array(
            'ignore_errors' => true,
            // other options go here
        );
        $text = \Soundasleep\Html2Text::convert($text, $options);
        $remove_special_char = stripcslashes($text);
        $speach_text = str_replace('&', ' and ', $remove_special_char);
        app()->setLocale($lang);
        $data = \Http::timeout(Config::get('app.GENERAL.training_url_timeout'))->get(Config::get('app.GENERAL.training_url') . '/tts2?text=' . $speach_text . '&lang=' . $lang);
        return response($data->getBody())->withHeaders($data->getHeaders());
    }

    public function storeKeywordHit(Request $request, $keyword)
    {
        $modifiedRequest = $request->all();
        $modifiedRequest['subscriber_id'] = Subscriber::where('api_token', $request->bearerToken())->pluck('id')[0];
        $modifiedRequest['keyword_id'] = $keyword;
        $rules = [
            'keyword_id' => 'required',
            'subscriber_id' => 'required',
            'session_token' => 'required'
        ];
        $validator = Validator::make($modifiedRequest, $rules);
        if ($validator->fails()) {
            return $this->sendError('Error', $validator->getMessageBag(), 400);
        } else {
            ChatKeywordHit::create($modifiedRequest);
            ChatKeyword::where('id', $modifiedRequest['keyword_id'])->increment('hit', 1);
        }

        return $this->sendResponse([], 'Keyword hits updated!', 200);
    }

    public function storeQuestionHit(Request $request)
    {
        $modifiedRequest = $request->all();
        $modifiedRequest['subscriber_id'] = Subscriber::where('api_token', $request->bearerToken())->pluck('id')[0];
        $rules = [
            'question_id' => 'required',
            'subscriber_id' => 'required',
            'session_token' => 'required',
        ];
        $validator = Validator::make($modifiedRequest, $rules);
        if ($validator->fails()) {
            return $this->sendError('Error', $validator->getMessageBag(), 400);
        } else {
            ChatQuestionHit::create($modifiedRequest);
            ChatQuestion::where('activated', 1)->where('id', $modifiedRequest['question_id'])->increment('hit', 1);
        }

        $lang = $request->header('lang');
        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);

        //function for store chatbot activity
        $request['action'] = 'question-click';
        $request['payload'] = ChatQuestion::where('id', $modifiedRequest['question_id'])->pluck('question')[0];
        $insertedId = $this->storeChatBotActivity($request);
        return $this->sendResponse(['activity_id' => $insertedId], 'Question hits updated!', 200);
    }

    public function submitFeedback(Request $request)
    {
        $modifiedRequest = $request->all();
        $rules = [
            'activity_id' => 'required',
            'question_id' => 'required_without:tag_id',
            'tag_id' => 'required_without:question_id',
            'like' => 'required_without:dislike',
            'dislike' => 'required_without:like',
        ];
        $validator = Validator::make($modifiedRequest, $rules);
        if ($validator->fails()) {
            return $this->sendError('Error', $validator->getMessageBag(), 400);
        } else {
            if ($modifiedRequest['question_id'] > 0) {
                if ($modifiedRequest['like'] > 0) {
                    ChatbotActivity::where('id', $modifiedRequest['activity_id'])->update(['question_id' => $modifiedRequest['question_id'], 'like' => $modifiedRequest['like']]);
                    ChatQuestion::where('id', $modifiedRequest['question_id'])->increment('like_count', 1);
                } else {
                    ChatbotActivity::where('id', $modifiedRequest['activity_id'])->update(['question_id' => $modifiedRequest['question_id'], 'dislike' => $modifiedRequest['dislike']]);
                    ChatQuestion::where('id', $modifiedRequest['question_id'])->increment('dislike_count', 1);
                }
            } else if ($modifiedRequest['tag_id'] > 0) {
                if ($modifiedRequest['like'] > 0) {
                    ChatbotActivity::where('id', $modifiedRequest['activity_id'])->update(['tag_id' => $modifiedRequest['tag_id'], 'like' => $modifiedRequest['like']]);
                    TTrainingTag::where('id', $modifiedRequest['tag_id'])->increment('like_count', 1);
                } else {
                    ChatbotActivity::where('id', $modifiedRequest['activity_id'])->update(['tag_id' => $modifiedRequest['tag_id'], 'dislike' => $modifiedRequest['dislike']]);
                    TTrainingTag::where('id', $modifiedRequest['tag_id'])->increment('dislike_count', 1);
                }
            }
            return $this->sendResponse([], 'Feedback submitted successfully!', 200);
        }
    }

    public function storeChatBotActivity(Request $request)
    {
        $modifiedRequest = $request->all();
        $modifiedRequest['ip_address'] = $this->getUserIp();
        $modifiedRequest['user_id'] = Subscriber::where('api_token', $request->bearerToken())->pluck('id')[0];
        $agent = new Agent();
        $header = $request->header('platform');
        if (isset($header) && $header != "") {
            $modifiedRequest['plateform']  = $request->header('platform');
        } else {
            if ($agent->isMobile()) {
                $modifiedRequest['plateform'] = 'app';
            } elseif ($agent->isDesktop()) {
                $modifiedRequest['plateform'] = 'web';
            } elseif ($agent->is('iPhone')) {
                $modifiedRequest['plateform'] = 'iPhone-app';
            } else {
                $modifiedRequest['plateform'] = 'mobile-app';
            }
        }
        $insertedId = ChatbotActivity::create($modifiedRequest)->id;
        return $insertedId;
    }

    public function getUserIp()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return request()->ip(); // it will return server ip when no client ip found
    }
}
