<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Log;
use App\Models\UserDeviceToken;
use Config;

class SendNotificationController extends Controller
{
    public static function sendNotification($notificationData, $deviceTokens)
    {
        $messaging = app('firebase.messaging');

        $title = $notificationData['title'];
        $body = $notificationData['description'];
        $condition = "!('anytopicyoudontwanttouse' in topics)";
        // 'TopicA' in topics && ('TopicB' in topics || 'TopicC' in topics)
        $message = CloudMessage::withTarget('condition', $condition)
            ->withNotification(Notification::create($title, $body));
        // ->withData(['type' => 'Achievement']);

        try {
            if (count($deviceTokens) > 0) {
                $successFullCount = 0;
                $failedCount = 0;
                $token_Array = collect($deviceTokens)->pluck('notification_token')->toArray();
                $split_array = array_chunk($token_Array, 500);
                for ($i = 0; $i < count($split_array); $i++) {
                    $sendReport = $messaging->sendMulticast($message, $split_array[$i]);
                    if (isset($sendReport)) {
                        Log::info('Successful sends: ' . $sendReport->successes()->count() . PHP_EOL);
                        if ($sendReport->successes()->count() . PHP_EOL > 0) {
                            // $successFullCount++;
                            $successFullCount = $successFullCount + $sendReport->successes()->count();
                        }
                        Log::info('Failed sends: ' . $sendReport->failures()->count() . PHP_EOL);
                        if ($sendReport->failures()->count() . PHP_EOL > 0) {
                            // $failedCount++;
                            $failedCount = $failedCount + $sendReport->failures()->count();
                        }

                        if ($sendReport->hasFailures()) {
                            // foreach ($sendReport->failures()->getItems() as $key => $failure) {
                            //     Log::info($failure->error()->getMessage() . PHP_EOL);
                            // }
                            // Log::info("Un known Tokens -------->");
                            // Log::info($sendReport->unknownTokens());
                            UserDeviceToken::whereIN('notification_token', $sendReport->unknownTokens())->delete();
                        }
                    }
                }
                return ["successFullCount" => $successFullCount, "failedCount" => $failedCount];
            } else {
                Log::info("no User Registered");
                return ['error' => "No device token found for selected subscriber. "];
            }
        } catch (\Exception $e) {
            Log::info("error" . $e);
            // return $e->getMessage();
            return ['error' => $e->getMessage()];
        }
    }

    public static function leaderBoardBadge($notificationData, $deviceTokens)
    {
        $title = $notificationData['title'];
        $body = $notificationData['description'];
        $condition = "!('anytopicyoudontwanttouse' in topics)";
        // 'TopicA' in topics && ('TopicB' in topics || 'TopicC' in topics)
        $message = CloudMessage::withTarget('condition', $condition)
            ->withNotification(Notification::create($title, $body))
            ->withData(['link' => Config::get('app.GENERAL.frontend_url') . "/Tasks"]);
        $notifiy = static::generalSendNotification($deviceTokens, $message);
        if (isset($notifiy) && isset($notifiy['successFullCount'])) {
            return ["successFullCount" => $notifiy['successFullCount'], "failedCount" => $notifiy['failedCount']];
        } else {
            return ['error' => $notifiy['error']];
        }
    }

    public static function surveyForms($notificationData, $deviceTokens)
    {
        $title = $notificationData['title'];
        $body = $notificationData['description'];
        $condition = "!('anytopicyoudontwanttouse' in topics)";
        // 'TopicA' in topics && ('TopicB' in topics || 'TopicC' in topics)
        $message = CloudMessage::withTarget('condition', $condition)
            ->withNotification(Notification::create($title, $body))
            ->withData(['link' => Config::get('app.GENERAL.frontend_url') . "/SurveyFormList"]);
        $notifiy = static::generalSendNotification($deviceTokens, $message);
        if (isset($notifiy) && isset($notifiy['successFullCount'])) {
            return ["successFullCount" => $notifiy['successFullCount'], "failedCount" => $notifiy['failedCount']];
        } else {
            return ['error' => $notifiy['error']];
        }
    }

    public static function newModules($notificationData, $deviceTokens, $link)
    {
        $title = $notificationData['title'];
        $body = $notificationData['description'];
        $condition = "!('anytopicyoudontwanttouse' in topics)";
        // 'TopicA' in topics && ('TopicB' in topics || 'TopicC' in topics)
        $message = CloudMessage::withTarget('condition', $condition)
            ->withNotification(Notification::create($title, $body))
            ->withData(['link' => $link]);
        $notifiy = static::generalSendNotification($deviceTokens, $message);
        if (isset($notifiy) && isset($notifiy['successFullCount'])) {
            return ["successFullCount" => $notifiy['successFullCount'], "failedCount" => $notifiy['failedCount']];
        } else {
            return ['error' => $notifiy['error']];
        }
    }

    public static function resourceMaterial($notificationData, $deviceTokens, $link)
    {
        $title = $notificationData['title'];
        $body = $notificationData['description'];
        $condition = "!('anytopicyoudontwanttouse' in topics)";
        // 'TopicA' in topics && ('TopicB' in topics || 'TopicC' in topics)
        $message = CloudMessage::withTarget('condition', $condition)
            ->withNotification(Notification::create($title, $body))
            ->withData(['link' => $link]);
        $notifiy = static::generalSendNotification($deviceTokens, $message);
        if (isset($notifiy) && isset($notifiy['successFullCount'])) {
            return ["successFullCount" => $notifiy['successFullCount'], "failedCount" => $notifiy['failedCount']];
        } else {
            return ['error' => $notifiy['error']];
        }
    }

    public static function deepLinkingNotification($notificationData, $deviceTokens)
    {
        $messaging = app('firebase.messaging');
        $title = $notificationData['title'];
        $body = $notificationData['description'];
        $condition = "!('anytopicyoudontwanttouse' in topics)";
        // 'TopicA' in topics && ('TopicB' in topics || 'TopicC' in topics)
        $message = CloudMessage::withTarget('condition', $condition)
            ->withNotification(Notification::create($title, $body))
            ->withData(['type' => 'Achievement', 'old_level' => $notificationData['old_level'], 'current_level' => $notificationData['current_level'], 'old_badge' => $notificationData['old_badge'], 'current_badge' => $notificationData['current_badge']]);

        try {
            if (count($deviceTokens) > 0) {
                $successFullCount = 0;
                $failedCount = 0;
                $token_Array = collect($deviceTokens)->pluck('notification_token')->toArray();
                $split_array = array_chunk($token_Array, 500);
                for ($i = 0; $i < count($split_array); $i++) {
                    $sendReport = $messaging->sendMulticast($message, $split_array[$i]);
                    if (isset($sendReport)) {
                        Log::info('Successful sends: ' . $sendReport->successes()->count() . PHP_EOL);
                        if ($sendReport->successes()->count() . PHP_EOL > 0) {
                            // $successFullCount++;
                            $successFullCount = $successFullCount + $sendReport->successes()->count();
                        }
                        Log::info('Failed sends: ' . $sendReport->failures()->count() . PHP_EOL);
                        if ($sendReport->failures()->count() . PHP_EOL > 0) {
                            // $failedCount++;
                            $failedCount = $failedCount + $sendReport->failures()->count();
                        }

                        if ($sendReport->hasFailures()) {
                            // foreach ($sendReport->failures()->getItems() as $key => $failure) {
                            //     Log::info($failure->error()->getMessage() . PHP_EOL);
                            // }
                            // Log::info("Un known Tokens -------->");
                            // Log::info($sendReport->unknownTokens());
                            UserDeviceToken::whereIN('notification_token', $sendReport->unknownTokens())->delete();
                        }
                    }
                }
                return ["successFullCount" => $successFullCount, "failedCount" => $failedCount];
            } else {
                Log::info("no User Registered");
                return ['error' => "No device token found for selected subscriber. "];
            }
        } catch (\Exception $e) {
            Log::info("error" . $e);
            // return $e->getMessage();
            return ['error' => $e->getMessage()];
        }
    }

    public static function InitalAssessmentInvitation($notificationData, $deviceTokens)
    {

        $title = $notificationData['title'];
        $body = $notificationData['description'];
        $condition = "!('anytopicyoudontwanttouse' in topics)";
        // 'TopicA' in topics && ('TopicB' in topics || 'TopicC' in topics)
        $message = CloudMessage::withTarget('condition', $condition)
            ->withNotification(Notification::create($title, $body))
            ->withData(['assessment_title' => $notificationData->assessment_title, 'time_to_complete' => $notificationData->time_to_complete, "link" => Config::get('app.GENERAL.frontend_url') . "/FutureAssessment"]);
        $notifiy = static::generalSendNotification($deviceTokens, $message);
        if (isset($notifiy) && isset($notifiy['successFullCount'])) {
            return ["successFullCount" => $notifiy['successFullCount'], "failedCount" => $notifiy['failedCount']];
        } else {
            return ['error' => $notifiy['error']];
        }
    }

    public static function generalSendNotification($deviceTokens, $message)
    {
        $messaging = app('firebase.messaging');
        try {
            if (count($deviceTokens) > 0) {
                $successFullCount = 0;
                $failedCount = 0;
                $token_Array = collect($deviceTokens)->pluck('notification_token')->toArray();
                $split_array = array_chunk($token_Array, 500);
                for ($i = 0; $i < count($split_array); $i++) {
                    $sendReport = $messaging->sendMulticast($message, $split_array[$i]);
                    if (isset($sendReport)) {
                        Log::info('Successful sends: ' . $sendReport->successes()->count() . PHP_EOL);
                        if ($sendReport->successes()->count() . PHP_EOL > 0) {
                            // $successFullCount++;
                            $successFullCount = $successFullCount + $sendReport->successes()->count();
                        }
                        Log::info('Failed sends: ' . $sendReport->failures()->count() . PHP_EOL);
                        if ($sendReport->failures()->count() . PHP_EOL > 0) {
                            // $failedCount++;
                            $failedCount = $failedCount + $sendReport->failures()->count();
                        }

                        if ($sendReport->hasFailures()) {
                            // foreach ($sendReport->failures()->getItems() as $key => $failure) {
                            //     Log::info($failure->error()->getMessage() . PHP_EOL);
                            // }
                            // Log::info("Un known Tokens -------->");
                            // Log::info($sendReport->unknownTokens());
                            UserDeviceToken::whereIN('notification_token', $sendReport->unknownTokens())->delete();
                        }
                    }
                }
                return ["successFullCount" => $successFullCount, "failedCount" => $failedCount];
            } else {
                Log::info("no User Registered");
                return ['error' => "No device token found for selected subscriber. "];
            }
        } catch (\Exception $e) {
            Log::info("error" . $e);
            // return $e->getMessage();
            return ['error' => $e->getMessage()];
        }
    }
}