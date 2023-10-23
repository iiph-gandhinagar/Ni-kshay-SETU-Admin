<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;
use Log;
use Config;

class OtpNotification extends Notification
{
    use Queueable;
    private $otp;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        Log::info(Config::get('app.GENERAL.sms_enabled'));
        Log::info(Config::get('app.GENERAL.sms_enabled') ?  ['database', 'slack', 'sms'] :
            ['database', 'slack']);
        return Config::get('app.GENERAL.sms_enabled') ?  ['database', 'slack', SMSChannel::class] :
            ['database']; //'slack'
    }

    // /**
    //  * Get the mail representation of the notification.
    //  *
    //  * @param  mixed  $notifiable
    //  * @return \Illuminate\Notifications\Messages\MailMessage
    //  */
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

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


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        Log::info('inside database notification');
        return [
            'otp' => $this->otp,
            'body' => 'Hello ' . $notifiable['name'] . ', Your Otp For Registration in T.B. App is ' . $this->otp . '.',
        ];
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
        return (new SlackMessage)->content('Hello ' . $notifiable['name'] . ', Your Otp For Registration in T.B. App is ' . $this->otp . '.');
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
        return [
            'otp' => $this->otp,
            'phone_no' => $notifiable['phone_no'],
            // 'body' => 'Hi '.$notifiable['name'].', Welcome for joining our efforts to fight against Tuberculosis. Your OTP is '.$this->otp.' for registration in Nikshay SETU powered by Digiflux.',
            'body' => 'Thank you for signing up with Ni-kshay Setu. ' . $this->otp . ' is your OTP for registration. OTP is valid for 8 minutes. Do not share this OTP with anyone. - IIPHG'
        ];
    }
}
