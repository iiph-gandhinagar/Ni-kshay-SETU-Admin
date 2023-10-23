<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Admin\SendNotificationController;
use Log;
use App\Models\Assessment;
use App\Models\AutomaticNotification;
use App\Models\UserDeviceToken;
use App\Models\UserNotification;

class sendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notification;
    protected $user;
    protected $type;
    protected $link;
    protected $notification_id;
    protected $model;


    public function __construct($notification, $user, $type, $link, $notification_id, $model = NULL)
    {
        $this->notification = $notification;
        $this->user = $user;
        $this->type = $type;
        $this->link = $link;
        $this->notification_id = $notification_id;
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $device_id = UserDeviceToken::whereIn('user_id', $this->user)->get('notification_token');
        if ($this->type == "Resource Material") {
            Log::info("Resource material notification ---->");

            $count = SendNotificationController::resourceMaterial($this->notification, $device_id, $this->link);
            Log::info($count);
        } else if ($this->type == "Algorithm") {
            Log::info("algorithm notification ---->");

            $count = SendNotificationController::newModules($this->notification, $device_id, $this->link);
            Log::info($count);
        } else if ($this->type == "Dynamic") {
            Log::info("dynamic algorithm notification ---->");

            $count = SendNotificationController::newModules($this->notification, $device_id, $this->link);
            Log::info($count);
        } else if ($this->type == "Assessment") {
            Log::info("assessment notification ---->");
            $assessment = Assessment::where('id', $this->notification['type_title'])->get(['id', 'assessment_title', 'time_to_complete'])[0];
            $assessment->title = $this->notification['title'];
            $assessment->description = $this->notification['description'];
            $count = SendNotificationController::InitalAssessmentInvitation($assessment, $device_id);
            Log::info($count);
        } else {
            Log::info("general notification ---->");

            // Log::info("inside notification controller ");
            $count = SendNotificationController::sendNotification($this->notification, $device_id);
            Log::info($count);
        }

        Log::info($count);
        if (isset($count) && array_key_exists('error', $count)) {
            $successfulCount = 0;
            $failedCount = 1;
        } else {
            $successfulCount =  $count['successFullCount'];
            $failedCount = $count['failedCount'];
        }
        Log::info($this->model);
        if (isset($this->model) && $this->model) {
            Log::info('inside automatic notification');
            AutomaticNotification::where('id', $this->notification_id)->update([
                'successful_count' => $successfulCount,
                'failed_count' => $failedCount,
                'status' => 'Done'
            ]);
        } else {
            UserNotification::where('id', $this->notification_id)->update([
                'successful_count' => $successfulCount,
                'failed_count' => $failedCount,
                'status' => 'Done'
            ]);
        }
    }
}
