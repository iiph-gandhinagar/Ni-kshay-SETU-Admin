<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;
use Log;
use Config;

class SmsNotification extends Notification
{
    use Queueable;
    private $notifyUser;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notifyUser)
    {
        $this->notifyUser = $notifyUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        Log::info("is sms flag enabled-->" . Config::get('app.GENERAL.sms_enabled'));
        // Log::info(Config::get('app.GENERAL.sms_enabled') ?  ['database','slack']://,'sms'
        // ['database','slack']);
        return Config::get('app.GENERAL.sms_enabled') ?  [NotifyToAll::class] : //SMSChannel::class //
            ['slack'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function toDatabase($notifiable)
    {
        Log::info('inside database notification');
        return [];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toSlack($notifiable)
    {
        Log::info('inside slack notification');
        // Log::info($notifiable);
        return (new SlackMessage)->content('Hey! Kindly find technical knowledge support app for National TB Program (NTEP). "Nikshay SETU". Search on Google Play Store or visit: www.nikshay-setu.in');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toSms($notifiable)
    {
        Log::info('inside sms notification');
        return
            $this->notifyUser;
    }
}
