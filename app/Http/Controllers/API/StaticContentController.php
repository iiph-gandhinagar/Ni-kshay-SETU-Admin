<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\KeyFeature;
use App\Models\StaticAppConfig;
use App\Models\StaticEnquiry;
use App\Models\StaticFaq;
use App\Models\StaticTestimonial;
use App\Models\StaticWhatWeDo;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Log;
use Config;
use Validator;
use App\Rules\GoogleRecaptchaResponseValidator;

class StaticContentController extends BaseController
{

    public function getFAQs(Request $request){
        $lang = $request->header('lang');
        if($lang == NULL){
            $lang = 'en';
        }
        app()->setLocale($lang);
        $Faq = StaticFaq::where('active',1)->orderBy('order_index')->get();
        $success = true;
        return ['status'=> $success,'data' => $Faq,'code' => 200];
    }

    public function getStaticAppConfig(Request $request){
        $lang = $request->header('lang');
        if($lang == NULL){
            $lang = 'en';
        }
        app()->setLocale($lang);

        $staticAppConfig = StaticAppConfig::all();
        $appConfigsForClient = new \stdClass();
        $staticAppConfig->map(function ($config) use ($lang, $appConfigsForClient) {
            // $config->setlocale($lang);
            $appConfigsForClient->{$config->key} = $config->value_json;
        });
        $success = true;
        return ['status'=> $success,'data' => $appConfigsForClient,'code' => 200];
    }

    public function getHomeData(Request $request){
        $lang = $request->header('lang');
        if($lang == NULL){
            $lang = 'en';
        }
        app()->setLocale($lang);
        $homeData['key_Feature'] = KeyFeature::with(['media'])->where('active',1)->orderBy('order_index')->get();
        $homeData['total_subscribers'] = Subscriber::count();
        $homeData['static_testimonials'] = StaticTestimonial::with(['media'])->where('active',1)->orderBy('order_index')->get();
        $homeData['what_we_do'] = StaticWhatWeDo::with(['media'])->where('active',1)->orderBy('order_index')->get();
        $success = true;
        return ['status'=> $success,'data' => $homeData,'code' => 200];
    }

    public function storeStaticEnquiry(Request $request){
        $newRequest = $request->all();
        $rules =[
            'email' => 'required',
            'message' => 'required',
            'subject' => 'required',
            'googleResponse' => new GoogleRecaptchaResponseValidator($request['googleResponse']),
            'googleResponse' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $success = false;
            return ['status' => $success,'data' => $validator->getMessageBag(),'code' => 400];
        }
        else{
            StaticEnquiry::create($newRequest);

            $data = array('description' => $newRequest['message'],'subject' => $newRequest['subject'],'email' => $newRequest['email']);
            $toArray = explode(',',Config::get('mail.MAIL_TO.address'));
            try {
                $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
                $beautymail->send('mail.static-enquiry-mail', $data, function($message) use ($toArray)
                {
                    $message->to($toArray);
                    // $message->cc(Config::get('mail.MAIL_TO.address'), 'Nikshy Setu Admin');
                    $message->bcc('info@company.io', 'Comapny');
                    $message->subject('Nikshy SETU Open Enquiry');
                });
            } catch (\Exception $e) {
                Log::error('Email Issue');
            }
            $success = true;
            return ['status' => $success,'data' => 'Thank you for submitting your query!','code' => 200];
        }

    }
}