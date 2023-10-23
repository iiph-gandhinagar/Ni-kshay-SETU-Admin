<?php

namespace App\Http\Controllers\API;

use App\Models\Enquiry;
use Log;
use Validator;
use Illuminate\Http\Request;
// use Mail;
use App\Http\Controllers\API\BaseController as BaseController;
use Config;



class EnquiryController extends BaseController
{

    public function storeEnquiry(Request $request)
    {

        $newRequest = $request->all();
        $rules = [
            'name' => 'required',
            'phone' => 'required',
            'message' => 'required',
            'subject' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $success = false;
            return ['status' => $success, 'data' => $validator->getMessageBag(), 'code' => 400];
        } else {


            $enquiry_details = Enquiry::create($newRequest);
            $enquiry = Enquiry::where('id', $enquiry_details->id)->with(['user', 'user.cadre', 'user.state', 'user.district', 'user.block', 'user.health_facility'])->get()[0];

            $data = array('name' => $newRequest['name'], 'description' => $newRequest['message'], 'subject' => $newRequest['subject'], 'email' => $newRequest['email'], 'enquiry' => $enquiry);
            $toArray = explode(',', Config::get('mail.MAIL_TO.address'));
            try {
                $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
                $beautymail->send('mail.enquiry-mail', $data, function ($message) use ($toArray) {
                    $message->to($toArray);
                    // $message->cc(Config::get('mail.MAIL_TO.address'), 'Admin');
                    $message->bcc('info@company.io', 'Company');
                    $message->subject('Nikshy SETU Enquiry');
                });
            } catch (\Exception $e) {
                // Log::error('Email Issue');
                Log::error($e);
            }
            $success = true;
            return ['status' => $success, 'data' => 'Thank you for submitting your query!', 'code' => 200];
        }
    }
}
