<?php

namespace App\Console\Commands;

use App\Models\UserAssessment;
use Illuminate\Console\Command;

class UserResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assessment:update-score';

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
        $this->line("Calculate marks in Assessment begins!!!");

        $userAssessment = UserAssessment::where('is_calculated', 0)->get(['user_id', 'assessment_id']);
        // Log::info($userAssessment);
        for ($i = 0; $i < count($userAssessment); $i++) {
            $getTimeAndDate = UserAssessment::where('user_id', $userAssessment[$i]['user_id'])->where('assessment_id', $userAssessment[$i]['assessment_id'])->get(['total_time', 'created_at']);
            // Log::info($getTimeAndDate);
            $data =  Date("Y-m-d H:i:s", strtotime($getTimeAndDate[0]['total_time'] . "min", strtotime($getTimeAndDate[0]['created_at'])));
            // Log::info($data);

            if ($data < date("Y-m-d H:i:s")) {
                // Log::info(app('App\Http\Controllers\API\UserAssessmentResultController')->completeAssessment($userAssessment[$i]['user_id'],$userAssessment[$i]['assessment_id']));
                app('App\Http\Controllers\API\UserAssessmentResultController')->completeAssessment($userAssessment[$i]['user_id'], $userAssessment[$i]['assessment_id']);
            } else {
                $i++;
            }
        }

        $this->info("Calculate marks in Assessment successfully done!");
        return 0;
    }
}
