<?php

namespace App\Observers;

use App\Models\LbSubModuleUsage;
use App\Models\LbSubscriberRanking;
use Exception;
use Log;

class LbSubModuleUsageObserver
{
    /**
     * Handle the LbSubModuleUsage "created" event.
     *
     * @param  \App\Models\LbSubModuleUsage  $lbSubModuleUsage
     * @return void
     */
    public function created(LbSubModuleUsage $lbSubModuleUsage)
    {
        try {
            if ($lbSubModuleUsage->mins_spent >= $lbSubModuleUsage->total_time && $lbSubModuleUsage->completed_flag != 1) {
                LbSubModuleUsage::where('subscriber_id', $lbSubModuleUsage->subscriber_id)->where('module_id', $lbSubModuleUsage->module_id)->where('sub_module', $lbSubModuleUsage->sub_module)->update(['completed_flag' => 1]);
                $sub_module_usage = LbSubscriberRanking::where('subscriber_id', $lbSubModuleUsage->subscriber_id)->get(['sub_module_usage_count'])[0]->sub_module_usage_count;
                LbSubscriberRanking::where('subscriber_id', $lbSubModuleUsage->subscriber_id)->update(['sub_module_usage_count' => $sub_module_usage + 1]);
            }
        } catch (Exception $e) {
            Log::info("error In creating sub module usage observer--->" . $e);
        }
    }

    /**
     * Handle the LbSubModuleUsage "updated" event.
     *
     * @param  \App\Models\LbSubModuleUsage  $lbSubModuleUsage
     * @return void
     */
    public function updated(LbSubModuleUsage $lbSubModuleUsage)
    {
        try {
            $sub_module_usage = LbSubModuleUsage::where('id', $lbSubModuleUsage->id)->get();
            if ($sub_module_usage[0]['mins_spent'] >= $lbSubModuleUsage->total_time && $sub_module_usage[0]['completed_flag'] != 1) {
                LbSubModuleUsage::where('subscriber_id', $lbSubModuleUsage->subscriber_id)->where('module_id', $lbSubModuleUsage->module_id)->where('sub_module', $lbSubModuleUsage->sub_module)->update(['completed_flag' => 1]);
                $sub_module_usage = LbSubscriberRanking::where('subscriber_id', $lbSubModuleUsage->subscriber_id)->get(['sub_module_usage_count'])[0]->sub_module_usage_count;
                LbSubscriberRanking::where('subscriber_id', $lbSubModuleUsage->subscriber_id)->update(['sub_module_usage_count' => $sub_module_usage + 1]);
            }
        } catch (Exception $e) {
            Log::info("error In updating sub module usage observer--->" . $e);
        }
    }

    // public function saved(LbSubModuleUsage $lbSubModuleUsage)
    // {
    //     Log::info("insdie saved function");
    //     try{
    //         Log::info("sub module usage updated");
    //         Log::info($lbSubModuleUsage);
    //         $sub_module_usage = LbSubModuleUsage::where('id',$lbSubModuleUsage->id)->get();
    //         if($sub_module_usage[0]['mins_spent'] >= $lbSubModuleUsage->total_time){
    //             LbSubModuleUsage::where('subscriber_id',$lbSubModuleUsage->subscriber_id)->where('module_id',$lbSubModuleUsage->module_id)->where('sub_module',$lbSubModuleUsage->sub_module)->update(['completed_flag' =>1]);
    //             $sub_module_usage = LbSubscriberRanking::where('subscriber_id',$lbSubModuleUsage->subscriber_id)->get(['sub_module_usage_count'])[0]->sub_module_usage;
    //             LbSubscriberRanking::where('subscriber_id',)->update(['sub_module_usage_count' => $sub_module_usage + 1]);
    //         }
    //     }catch(Exception $e){
    //         Log::info("error In updating sub module usage observer--->".$e);
    //     }
    // }
    /**
     * Handle the LbSubModuleUsage "deleted" event.
     *
     * @param  \App\Models\LbSubModuleUsage  $lbSubModuleUsage
     * @return void
     */
    public function deleted(LbSubModuleUsage $lbSubModuleUsage)
    {
        //
    }

    /**
     * Handle the LbSubModuleUsage "restored" event.
     *
     * @param  \App\Models\LbSubModuleUsage  $lbSubModuleUsage
     * @return void
     */
    public function restored(LbSubModuleUsage $lbSubModuleUsage)
    {
        //
    }

    /**
     * Handle the LbSubModuleUsage "force deleted" event.
     *
     * @param  \App\Models\LbSubModuleUsage  $lbSubModuleUsage
     * @return void
     */
    public function forceDeleted(LbSubModuleUsage $lbSubModuleUsage)
    {
        //
    }
}
