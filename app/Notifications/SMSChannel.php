<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Config;

class SMSChannel
{

  public function send($notifiable, Notification $notification)
  {
    $data = $notification->toSms($notifiable);
    // url/send/?apikey=apikey&sender=sender details&numbers=91
    $response = Http::get(Config::get('app.GENERAL.SMS_channel_url') . '/send/?apikey=' . Config::get('app.GENERAL.sms_api_key') . '&sender=' . Config::get('app.GENERAL.otp_sms_header') . '&numbers=91' . $data['phone_no'] . '&message=' . $data['body']);


    return $notifiable->routeNotificationFor('sms');
  }
}
