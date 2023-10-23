<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\SendNotificationController;
use App\Models\Assessment;
use App\Models\UserDeviceToken;
use App\Models\AssessmentEnrollment;
use App\Models\AutomaticNotification;
use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;
use Config;

class PlannedAssessment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'planned:assessment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line("Planned Assessment Condition Match Criteria Start!!!");

        // $list_of_assessments = Assessment::where('activated',1)->where('assessment_type','planned')->get();
        $list_of_assessments_1_days = Assessment::where('activated', 1)->where('assessment_type', 'planned')->whereDate('from_date', Carbon::now()->addDays(1))->get();
        // $list_of_assessments_5_days = Assessment::where('activated',1)->where('assessment_type','planned')->whereDate('from_date', Carbon::today()->addDays(5))->get();
        //Carbon::today()->subHour();
        Log::info(Carbon::now()->addDays(1));
        Log::info(Carbon::now()->addHour());
        Log::info(Carbon::now()->addMinute(5));
        foreach ($list_of_assessments_1_days as $planned_ass) {
            // Log::info($planned_ass);
            // Log::info($planned_ass->from_date);
            $subscriber = AssessmentEnrollment::where('assessment_id', $planned_ass->id)->pluck('user_id');
            $notification['title'] = "New Assessment";
            $notification['description'] = "New Assessment: Your Assessment for " . $planned_ass->assessment_title . " will be live in 1 Day, Click here to enroll";
            $notification['type'] = "Future Assessment";
            $notification['subscriber_id'] = implode(',', $subscriber);
            $notification['linking_url'] = Config::get('app.GENERAL.frontend_url') . "/FutureAssessment";
            $notification['created_by'] = 1;
            AutomaticNotification::create($notification);
            $device_id = UserDeviceToken::whereIn('user_id', $subscriber)->get('notification_token');
            $planned_ass['title'] = $notification['title'];
            $planned_ass['description'] = $notification['description'];
            // Log::info($device_id);
            // app('App\Http\Controllers\Admin\SendNotificationController')->InitalAssessmentInvitation($planned_ass,$device_id);
            return SendNotificationController::InitalAssessmentInvitation($planned_ass, $device_id);
        }

        $this->info("Planned Assessment  Condition Match Criteria done!!!");
        return 0;
    }
}
