<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\ModuleMappingToName;
use App\Models\Subscriber;
use App\Models\SubscriberActivity;
use App\Models\UserAssessment;
use Illuminate\Http\Request;
use Log;
use DB;

class TvDashboardController extends BaseController
{
    public function getDashboardDetails()
    {
        // $subscriber_enrolled_today = Subscriber::whereDate('created_at', '=', date('Y-m-d'))->count();
        $total_completed_assessment = UserAssessment::count();
        $total_visitors = SubscriberActivity::count();
        $total_subscribers = Subscriber::count();
        $chatbot_keyword_hits = DB::select("SELECT DISTINCT(ck.title),ck.hit FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s 
                            on s.id=ckh.subscriber_id 
                            group by ckh.keyword_id
                            order by ck.hit DESC
                            limit 5");
        $cadre_wise_subscribers = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
            on cd.ID = s.cadre_id
            GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");

        $top_5_modles = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
            on s.id = sa.user_id WHERE sa.action LIKE 'module_%'
            GROUP BY action ORDER BY count(*) DESC  LIMIT 5");

        $actions = collect([]);
        foreach ($top_5_modles as $items) {
            $mapping_name = ModuleMappingToName::where('module_name', $items->action)->get(['mapping_name']);
            if (isset($mapping_name) && count($mapping_name) > 0) {
                $actions->push(['action' => $mapping_name[0]['mapping_name'], 'TotalCount' => $items->TotalCount]);
            } else {
                continue;
            }
        }

        $cumulative_subscribers_summary = DB::select("select SQL_NO_CACHE YEAR(ua.created_at) as year, concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at) as month, COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
            on s.id=ua.user_id GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
            ORDER BY YEAR(ua.created_at) desc,MONTH(ua.created_at) desc limit 6");

        $state_wise_subscribers = DB::select("SELECT DISTINCT(state_id),St.title as title,count(*) as TotalCount FROM subscribers s left Join state St 
                on St.ID = s.state_id WHERE St.deleted_at IS NULL and s.state_id != 37 and s.state_id  not in(37,0)
                GROUP BY state_id ORDER BY count(*) DESC");

        $success = true;
        $data = collect([]);
        // $data['subscriber_enrolled_today'] = $subscriber_enrolled_today;
        $data['total_completed_assessment'] = $total_completed_assessment;
        $data['total_visitors'] = $total_visitors;
        $data['total_subscribers'] = $total_subscribers;
        $data['chatbot_keyword_hits'] = $chatbot_keyword_hits;
        $data['cadre_wise_subscribers'] = $cadre_wise_subscribers;
        $data['top_5_modles'] = $actions->toArray();
        $data['cumulative_subscribers_summary'] = $cumulative_subscribers_summary;
        $data['state_wise_subscribers'] = $state_wise_subscribers;
        return ['status' => $success, 'data' => [$data], 'code' => 200];
    }

    public function staticDashboardData(Request $request)
    {
        $dashboardData = $this->getDashboardDetails();
        $static_dashboard_data['total_completed_assessment'] = $dashboardData['data'][0]['total_completed_assessment'];
        $static_dashboard_data['total_visitors'] = $dashboardData['data'][0]['total_visitors'];
        $static_dashboard_data['total_subscribers'] = $dashboardData['data'][0]['total_subscribers'];
        $static_dashboard_data['state_wise_subscribers'] = $dashboardData['data'][0]['state_wise_subscribers'];
        $static_dashboard_data['cadre_wise_subscribers'] =  DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
            on cd.ID = s.cadre_id
            where cd.title not like '%other%'
            GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");

        $top_modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
            on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments'
            GROUP BY action ORDER BY count(*) DESC  LIMIT 10");

        $actions = collect([]);
        foreach ($top_modules as $items) {
            $mapping_name = ModuleMappingToName::where('module_name', $items->action)->get(['mapping_name']);
            if (isset($mapping_name) && count($mapping_name) > 0) {
                $actions->push(['action' => $mapping_name[0]['mapping_name'], 'TotalCount' => $items->TotalCount]);
            } else {
                continue;
            }
        }

        $level_wise_subscribers = DB::select("SELECT count(s.id) as totalCount,c.cadre_type FROM `subscribers` s join cadre c on c.id = s.cadre_id group by c.cadre_type");
        $static_dashboard_data['level_wise_subscribers'] = $level_wise_subscribers;

        $static_dashboard_data['module_usage'] = $actions->toArray();

        $success = true;
        return ['status' => $success, 'data' => $static_dashboard_data, 'code' => 200];
    }
}
