<?php

namespace App\Http\Controllers\API;

use App\Models\ModuleMappingToName;
use App\Models\Subscriber;
use App\Models\Enquiry;
use App\Models\SubscriberActivity;
use App\Models\UserAssessment;
use App\Models\State;
use App\Models\Block;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Log;
use Carbon\Carbon;
use App\Models\LbSubscriberRanking;
use App\Http\Controllers\API\BaseController as BaseController;
use DB;

class GraphController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexEnquiry $request
     * @return array|Factory|View
     */
    public function index(Request $request)
    {
        $user_state = \Auth::user()->state;
        // if ($user_state == null && $request->state_id != 0) {
        //     $user_state = $request->state_id;
        // }
        if ($user_state != null) {
            $state = State::where('id', $user_state)->orderby('title')->get(['id', 'title']);
            $block = Block::where('state_id', $user_state)->orderby('title')->get(['state_id', 'district_id', 'title', 'id']);
            $district = District::where('state_id', $user_state)->orderby('title')->get(['state_id', 'title', 'id']);
        } else {
            $state = State::orderby('title')->get(['id', 'title']);
            $block = Block::orderby('title')->get(['state_id', 'district_id', 'title', 'id']);
            $district = District::orderby('title')->get(['state_id', 'title', 'id']);
        }


        return [
            'state_id' => $user_state,
            'date' => $request->date,
            'state' => $state,
            'block' => $block,
            'district' => $district
        ];
    }

    public function getDistrictData(Request $request)
    {
        $district = District::where('state_id', $request->state_id)->get(['id', 'title']);
        return $district;
    }

    public function getBlockData(Request $request)
    {
        $block = Block::where('state_id', $request->state_id)->where('district_id', $request->district_id)->get(['id', 'title']);
        return $block;
    }

    public function getUserName(Request $request)
    {
        $user_name = \Auth::user()->first_name;
        return ['user_name' => $user_name];
    }

    public function getDashboardDataWithFilters(Request $request)
    {
        if ($request->date != null) {
            $values = explode(" ", $request->date);
            $stateWiseSubscriber = DB::select("SELECT DISTINCT(state_id),St.title as title,count(*) as TotalCount FROM subscribers s left Join state St 
                on St.ID = s.state_id WHERE St.deleted_at IS NULL and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' and s.state_id  not in(37,0)
                GROUP BY state_id ORDER BY count(*) DESC");

            foreach ($stateWiseSubscriber as $sub) {
                $sub->todays_subscriber = Subscriber::whereDate('created_at', '=', date('Y-m-d'))->count();
                $sub->percentage = round(($sub->TotalCount * 100) / Subscriber::count(), 2);
            }

            $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
            on cd.ID = s.cadre_id where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 10");

            /*
                SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                on cd.ID = s.cadre_id where abs(DATEDIFF(s.created_at,now())) between 0 and 30
                GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 10
            */

            $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
            on s.id=ua.user_id where DATE_FORMAT(ua.created_at,'%Y-%m-%d') BETWEEN '$values[0]' and '$values[2]'
            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            /* select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
            on s.id=ua.user_id where abs(DATEDIFF(ua.created_at,now())) between 0 and 30
            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at) */

            $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
            on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            GROUP BY action ORDER BY count(*) DESC  LIMIT 10");

            /*SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
                on s.id = sa.user_id 
                WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and abs(DATEDIFF(sa.created_at,now())) between 0 and 30 
                GROUP BY action ORDER BY count(*) DESC  LIMIT 10
            */

            $actions = collect([]);
            foreach ($top10Modules as $items) {
                $mapping_name = ModuleMappingToName::where('module_name', $items->action)->get(['mapping_name']);
                if (isset($mapping_name) && count($mapping_name) > 0) {
                    $actions->push(['action' => $mapping_name[0]['mapping_name'], 'TotalCount' => $items->TotalCount]);
                } else {
                    continue;
                }
            }

            $questionHitCount = DB::select("SELECT DISTINCT(ch.question),ch.hit FROM `chat_questions` ch 
            join chat_question_hits cqh on cqh.question_id = ch.id  
            join subscribers s on cqh.subscriber_id = s.id
            where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            ORDER BY ch.hit DESC 
            LIMIT 10");

            /* SELECT DISTINCT(ch.question),ch.hit FROM `chat_questions` ch 
            join chat_question_hits cqh on cqh.question_id = ch.id  
            join subscribers s on cqh.subscriber_id = s.id
           	where abs(DATEDIFF(ch.created_at,now())) between 0 and 30
            ORDER BY ch.hit DESC 
            LIMIT 10 */

            $keywordHitCount = DB::select("SELECT DISTINCT(ck.title),ck.hit FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s 
                            on s.id=ckh.subscriber_id 
                            where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                            group by ckh.keyword_id
                            order by ck.hit DESC
                            limit 10");

            /* SELECT DISTINCT(ck.title),ck.hit FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s 
                            on s.id=ckh.subscriber_id 
                            where abs(DATEDIFF(ckh.created_at,now())) between 0 and 30
                            group by ckh.keyword_id
                            order by ck.hit DESC
                            limit 10 */

            $subscriberCount = Subscriber::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->count();
            $assessmetnCount = UserAssessment::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->count();
            $state_level_subscriber = Subscriber::whereDate('updated_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('updated_at', '<=', date('Y-m-d', strtotime($values[2])))->where('country_id', 1)->count();
        } else {
            $stateWiseSubscriber = DB::select("SELECT DISTINCT(state_id),St.title as title,count(*) as TotalCount FROM subscribers s left Join state St 
                on St.ID = s.state_id WHERE St.deleted_at IS NULL and s.state_id != 37 and s.state_id  not in(37,0)
                GROUP BY state_id ORDER BY count(*) DESC");
            $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
            on cd.ID = s.cadre_id 
            GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 10");

            $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
            on s.id=ua.user_id
            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
            on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments'
            GROUP BY action ORDER BY count(*) DESC  LIMIT 10");

            $actions = collect([]);
            foreach ($top10Modules as $items) {
                $mapping_name = ModuleMappingToName::where('module_name', $items->action)->get(['mapping_name']);
                if (isset($mapping_name) && count($mapping_name) > 0) {
                    $actions->push(['action' => $mapping_name[0]['mapping_name'], 'TotalCount' => $items->TotalCount]);
                } else {
                    continue;
                }
            }

            $questionHitCount = DB::select("SELECT DISTINCT(ch.question),ch.hit FROM `chat_questions` ch 
                join chat_question_hits cqh on cqh.question_id = ch.id  
                join subscribers s on cqh.subscriber_id = s.id
                ORDER BY ch.hit DESC 
                LIMIT 10");

            $keywordHitCount = DB::select("SELECT DISTINCT(ck.title),ck.hit FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id 
                group by ckh.keyword_id
                order by ck.hit DESC
                limit 10");

            $subscriberCount = Subscriber::count();
            $assessmetnCount = UserAssessment::count();
            $state_level_subscriber = Subscriber::where('country_id', 1)->count();
        }
        $subscriberAppVisitCount = SubscriberActivity::whereDate('created_at', '=', Carbon::now())->distinct('user_id')->count();
        $enrollSubscriber = Subscriber::whereDate('created_at', '=', date('Y-m-d'))->count();
        $todayCompletedAssessmentCount = UserAssessment::where('is_calculated', 1)->whereDate('created_at', '=', Carbon::now())->count();
        $enquiryCount = Enquiry::whereDate('created_at', '=', date('Y-m-d'))->count();

        $subscriber_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscribers ua
            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

        $subscriber_activity_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscriber_activities ua
        GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
        ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

        $assessment_graph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
            on s.id=ua.user_id
            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

        $chatbot_usage_count = DB::select('SELECT count(*) FROM `subscriber_activities` sa
                        WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
        $resource_material_usage_count = DB::select('SELECT count(*) FROM `subscriber_activities` sa
                WHERE action like "%Open_Resource_Materials%"');

        $total_time_spent = round((LbSubscriberRanking::sum('mins_spent_count')) / 60, 2);

        /* SELECT cadre_type,count(*) as count_data,lb.level_id FROM `subscribers` s
            JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id
            group by `cadre_type`,lb.level_id 
        */

        /* SELECT cadre_type,count(*) as count_data,lb.level_id FROM `subscribers` s
            JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id
            where abs(DATEDIFF(lb.created_at,now())) between 0 and 30 
            group by `cadre_type`,lb.level_id
        */

        /* SELECT fq.feedback_question,ratings,count(*) FROM `user_feedback_history` fh
            join user_feedback_questions fq on fq.id = fh.feedback_id
            where fq.feedback_question like "%User Interface%" or fq.feedback_question like "%Module Content%" or fq.feedback_question like "%Chatbot%"
            group by fq.feedback_question,ratings 
        */
        ///Month wise user activities -------------------------------------------------------------------------------------------------------------------------------------------
        /* 
            select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
            join subscribers s on s.id = sa.user_id
            where action = "user_home_page_visit" and sa.created_at >= DATE_ADD(NOW(),INTERVAL -3 MONTH)
            GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
            having count(*) >= 3 and count(*) < 5
        */

        /* select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
            join subscribers s on s.id = sa.user_id
            where action = "user_home_page_visit" and sa.created_at >= DATE_ADD(NOW(),INTERVAL -3 MONTH)
            GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
            having count(*) >= 5 and count(*) < 7 
        */

        /* select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
            join subscribers s on s.id = sa.user_id
            where action = "user_home_page_visit" and sa.created_at >= DATE_ADD(NOW(),INTERVAL -3 MONTH)
            GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
            having count(*) >= 7 and count(*) < 9
        */

        /* select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
            join subscribers s on s.id = sa.user_id
            where action = "user_home_page_visit" and sa.created_at >= DATE_ADD(NOW(),INTERVAL -3 MONTH)
            GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
            having count(*) >= 10
        */
        ////End Month wise user Activity ----------------------------------------------------------------------------------------------------------------------------------------


        //// last 4 Week user activity ------------------------------------------------------------------------------------------------------------------------------------------
        /* select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
            join subscribers s on s.id = sa.user_id
            where action = "user_home_page_visit" and sa.created_at >= DATE_ADD(NOW(),INTERVAL -4 Week)
            GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
            having count(*) >= 3 and count(*) < 5 
        */

        /* select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
            join subscribers s on s.id = sa.user_id
            where action = "user_home_page_visit" and sa.created_at >= DATE_ADD(NOW(),INTERVAL -4 Week)
            GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
            having count(*) >= 5 and count(*) < 7
        */

        /* select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
            join subscribers s on s.id = sa.user_id
            where action = "user_home_page_visit" and sa.created_at >= DATE_ADD(NOW(),INTERVAL -4 Week)
            GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
            having count(*) >= 7 and count(*) < 9
        */

        /* select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
            join subscribers s on s.id = sa.user_id
            where action = "user_home_page_visit" and sa.created_at >= DATE_ADD(NOW(),INTERVAL -4 Week)
            GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
            having count(*) >= 10
        */
        return [
            "stateWiseSubscriber" => $stateWiseSubscriber, 'cadreWiseSubscriber' => $cadreWiseSubscriber, 'asessmentGraph' => $assessmentGraph, 'top10Modules' => $actions->toArray(),
            'questionHit' => $questionHitCount, 'keywordHit' => $keywordHitCount, 'subscriber' => $subscriberCount, 'subscriberEnrollToday' => $enrollSubscriber, 'enquiry' => $enquiryCount, 'subscriberAppVistCount' => $subscriberAppVisitCount,
            'assessment' => $assessmetnCount, 'completeAssessmentCount' => $todayCompletedAssessmentCount, 'state_level_subscriber' => $state_level_subscriber
        ];
    }

    public function countListGraph(Request $request)
    { //commented code in mails
        $total_assessment_completed = 0;
        $total_subscriber = 0;
        $total_subscriber_activity = 0;
        if ($request['state_id'] != 0 && $request['district_id'] == 0 && $request['block_id'] == 0) {
            if ($request['type'] == "today") {
                $date = date('Y-m-d');
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $total_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request['state_id'])->whereDate('created_at', '=', Carbon::now())->count();

                    $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('subscribers.state_id', $request->state_id)
                        ->where('is_calculated', 1)->whereDate('user_assessments.created_at', '=', Carbon::now())->count();

                    $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('subscribers.state_id', $request->state_id)
                        ->whereDate('subscriber_activities.created_at', '=', Carbon::now())->distinct('user_id')->count();

                    $subscriber_growth = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.created_at like '%$date%' and ua.state_id =$request->state_id
                                    GROUP BY date_format(ua.created_at,'%H %p')
                                    ORDER BY date_format(ua.created_at,'%H %p')");

                    $subscriber_activity_growth = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                    join subscribers s on s.id= ua.user_id
                                                    where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.created_at like '%$date%' and s.state_id =$request->state_id
                                                    GROUP BY date_format(ua.created_at,'%H %p')
                                                    ORDER BY date_format(ua.created_at,'%H %p')");

                    $assessment_graph = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.created_at like '%$date%' and s.state_id =$request->state_id
                                        GROUP BY date_format(ua.created_at,'%H %p')
                                        ORDER BY date_format(ua.created_at,'%H %p')");
                } else {
                    $total_subscriber = Subscriber::where('state_id', $request['state_id'])->whereDate('created_at', '=', Carbon::now())->count();

                    $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->where('subscribers.state_id', $request->state_id)
                        ->where('is_calculated', 1)->whereDate('user_assessments.created_at', '=', Carbon::now())->count();

                    $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->where('subscribers.state_id', $request->state_id)
                        ->whereDate('subscriber_activities.created_at', '=', Carbon::now())->distinct('user_id')->count();

                    $subscriber_growth = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.created_at like '%$date%' and ua.state_id =$request->state_id
                                    GROUP BY date_format(ua.created_at,'%H %p')
                                    ORDER BY date_format(ua.created_at,'%H %p')");

                    $subscriber_activity_growth = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                    join subscribers s on s.id= ua.user_id
                                                    where ua.created_at like '%$date%' and s.state_id =$request->state_id
                                                    GROUP BY date_format(ua.created_at,'%H %p')
                                                    ORDER BY date_format(ua.created_at,'%H %p')");

                    $assessment_graph = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where ua.created_at like '%$date%' and s.state_id =$request->state_id
                                        GROUP BY date_format(ua.created_at,'%H %p')
                                        ORDER BY date_format(ua.created_at,'%H %p')");
                }
            } else {
                if ($request['date'] == '') {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $total_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request['state_id'])->count();

                        $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('subscribers.state_id', $request->state_id)->count();

                        $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('subscribers.state_id', $request->state_id)->count();
                        $subscriber_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.state_id =" . $request['state_id'] . "
                                    GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                    ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

                        $subscriber_activity_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                        join subscribers s on s.id= ua.user_id
                                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id =" . $request['state_id'] . "
                                                        GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                        ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

                        $assessment_graph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where  s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id =" . $request['state_id'] . "
                                        GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                        ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    } else {
                        $total_subscriber = Subscriber::where('state_id', $request['state_id'])->count();

                        $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->where('subscribers.state_id', $request->state_id)->count();

                        $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->where('subscribers.state_id', $request->state_id)->count();
                        $subscriber_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.state_id =" . $request['state_id'] . "
                                    GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                    ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

                        $subscriber_activity_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                        join subscribers s on s.id= ua.user_id
                                                        where s.state_id =" . $request['state_id'] . "
                                                        GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                        ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

                        $assessment_graph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where s.state_id =" . $request['state_id'] . "
                                        GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                        ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    }
                } else {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $total_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request['state_id'])
                            ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();

                        $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                            ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                            ->where('subscribers.state_id', $request->state_id)
                            ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();

                        $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                            ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                            ->where('subscribers.state_id', $request->state_id)
                            ->whereDate('user_assessments.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('user_assessments.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();
                        $subscriber_growth = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                    GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                    ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");

                        $subscriber_activity_growth = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                        join subscribers s on s.id= ua.user_id
                                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                                        GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                                        ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");

                        $assessment_graph = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                        GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                        ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");
                    } else {
                        $total_subscriber = Subscriber::where('state_id', $request['state_id'])
                            ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();

                        $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                            ->where('subscribers.state_id', $request->state_id)
                            ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();

                        $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                            ->where('subscribers.state_id', $request->state_id)
                            ->whereDate('user_assessments.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('user_assessments.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();
                        $subscriber_growth = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                    GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                    ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");

                        $subscriber_activity_growth = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                        join subscribers s on s.id= ua.user_id
                                                        where s.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                                        GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                                        ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");

                        $assessment_graph = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where s.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                        GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                        ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");
                    }
                }
            }
        } elseif ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] == 0) {
            if ($request['type'] == "today") {
                $date = date('Y-m-d');
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $total_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request['state_id'])->where('district_id', $request['district_id'])->whereDate('created_at', '=', Carbon::now())->count();

                    $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('is_calculated', 1)->whereDate('user_assessments.created_at', '=', Carbon::now())->count();

                    $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->whereDate('subscriber_activities.created_at', '=', Carbon::now())->distinct('user_id')->count();

                    $subscriber_growth = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.created_at like '%$date%' and ua.state_id =$request->state_id and ua.district_id = $request->district_id
                                    GROUP BY date_format(ua.created_at,'%H %p')
                                    ORDER BY date_format(ua.created_at,'%H %p')");

                    $subscriber_activity_growth = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                    join subscribers s on s.id= ua.user_id
                                                    where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.created_at like '%$date%' and s.state_id =$request->state_id and s.district_id = $request->district_id
                                                    GROUP BY date_format(ua.created_at,'%H %p')
                                                    ORDER BY date_format(ua.created_at,'%H %p')");

                    $assessment_graph = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.created_at like '%$date%' and s.state_id =$request->state_id and s.district_id = $request->district_id
                                        GROUP BY date_format(ua.created_at,'%H %p')
                                        ORDER BY date_format(ua.created_at,'%H %p')");
                } else {
                    $total_subscriber = Subscriber::where('state_id', $request['state_id'])->where('district_id', $request['district_id'])->whereDate('created_at', '=', Carbon::now())->count();

                    $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('is_calculated', 1)->whereDate('user_assessments.created_at', '=', Carbon::now())->count();

                    $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->whereDate('subscriber_activities.created_at', '=', Carbon::now())->distinct('user_id')->count();

                    $subscriber_growth = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.created_at like '%$date%' and ua.state_id =$request->state_id and ua.district_id = $request->district_id
                                    GROUP BY date_format(ua.created_at,'%H %p')
                                    ORDER BY date_format(ua.created_at,'%H %p')");

                    $subscriber_activity_growth = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                    join subscribers s on s.id= ua.user_id
                                                    where ua.created_at like '%$date%' and s.state_id =$request->state_id and s.district_id = $request->district_id
                                                    GROUP BY date_format(ua.created_at,'%H %p')
                                                    ORDER BY date_format(ua.created_at,'%H %p')");

                    $assessment_graph = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where ua.created_at like '%$date%' and s.state_id =$request->state_id and s.district_id = $request->district_id
                                        GROUP BY date_format(ua.created_at,'%H %p')
                                        ORDER BY date_format(ua.created_at,'%H %p')");
                }
            } else {
                if ($request['date'] == '') {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $total_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request['state_id'])->where('district_id', $request['district_id'])->count();

                        $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('subscribers.state_id', $request->state_id)->where('subscribers.district_id', $request->district_id)->count();

                        $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('subscribers.state_id', $request->state_id)->where('subscribers.district_id', $request->district_id)->count();
                        $subscriber_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.state_id =" . $request['state_id'] . " and ua.district_id = $request->district_id
                                    GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                    ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

                        $subscriber_activity_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                        join subscribers s on s.id= ua.user_id
                                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id =" . $request['state_id'] . " and s.district_id = $request->district_id
                                                        GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                        ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

                        $assessment_graph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id =" . $request['state_id'] . " and s.district_id = $request->district_id
                                        GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                        ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    } else {
                        $total_subscriber = Subscriber::where('state_id', $request['state_id'])->where('district_id', $request['district_id'])->count();

                        $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->where('subscribers.state_id', $request->state_id)->where('subscribers.district_id', $request->district_id)->count();

                        $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->where('subscribers.state_id', $request->state_id)->where('subscribers.district_id', $request->district_id)->count();
                        $subscriber_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.state_id =" . $request['state_id'] . " and ua.district_id = $request->district_id
                                    GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                    ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

                        $subscriber_activity_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                        join subscribers s on s.id= ua.user_id
                                                        where s.state_id =" . $request['state_id'] . " and s.district_id = $request->district_id
                                                        GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                        ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

                        $assessment_graph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where s.state_id =" . $request['state_id'] . " and s.district_id = $request->district_id
                                        GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                        ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    }
                } else {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $total_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request['state_id'])
                            ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();

                        $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                            ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                            ->where('subscribers.state_id', $request->state_id)
                            ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();

                        $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                            ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                            ->where('subscribers.state_id', $request->state_id)
                            ->whereDate('user_assessments.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('user_assessments.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();
                        $subscriber_growth = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' and ua.district_id = $request->district_id
                                    GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                    ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");

                        $subscriber_activity_growth = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                        join subscribers s on s.id= ua.user_id
                                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' and s.district_id = $request->district_id
                                                        GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                                        ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");

                        $assessment_graph = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' and s.district_id = $request->district_id
                                        GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                        ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");
                    } else {
                        $total_subscriber = Subscriber::where('state_id', $request['state_id'])
                            ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();

                        $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                            ->where('subscribers.state_id', $request->state_id)
                            ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();

                        $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                            ->where('subscribers.state_id', $request->state_id)
                            ->whereDate('user_assessments.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('user_assessments.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();
                        $subscriber_growth = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' and ua.district_id = $request->district_id
                                    GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                    ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");

                        $subscriber_activity_growth = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                        join subscribers s on s.id= ua.user_id
                                                        where s.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' and s.district_id = $request->district_id
                                                        GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                                        ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");

                        $assessment_graph = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where s.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' and s.district_id = $request->district_id
                                        GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                        ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");
                    }
                }
            }
        } elseif ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] != 0) {
            if ($request['type'] == "today") {
                $date = date('Y-m-d');
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $total_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request['state_id'])->where('district_id', $request['district_id'])->where('block_id', $request['block_id'])->whereDate('created_at', '=', Carbon::now())->count();

                    $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->where('is_calculated', 1)->whereDate('user_assessments.created_at', '=', Carbon::now())->count();

                    $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->whereDate('subscriber_activities.created_at', '=', Carbon::now())->distinct('user_id')->count();

                    $subscriber_growth = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.created_at like '%$date%' and ua.state_id =$request->state_id and ua.district_id = $request->district_id and ua.block_id = $request->block_id
                                    GROUP BY date_format(ua.created_at,'%H %p')
                                    ORDER BY date_format(ua.created_at,'%H %p')");

                    $subscriber_activity_growth = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                    join subscribers s on s.id= ua.user_id
                                                    where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.created_at like '%$date%' and s.state_id =$request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                                                    GROUP BY date_format(ua.created_at,'%H %p')
                                                    ORDER BY date_format(ua.created_at,'%H %p')");

                    $assessment_graph = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.created_at like '%$date%' and s.state_id =$request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                                        GROUP BY date_format(ua.created_at,'%H %p')
                                        ORDER BY date_format(ua.created_at,'%H %p')");
                } else {
                    $total_subscriber = Subscriber::where('state_id', $request['state_id'])->where('district_id', $request['district_id'])->where('block_id', $request['block_id'])->whereDate('created_at', '=', Carbon::now())->count();

                    $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->where('is_calculated', 1)->whereDate('user_assessments.created_at', '=', Carbon::now())->count();

                    $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->whereDate('subscriber_activities.created_at', '=', Carbon::now())->distinct('user_id')->count();

                    $subscriber_growth = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.created_at like '%$date%' and ua.state_id =$request->state_id and ua.district_id = $request->district_id and ua.block_id = $request->block_id
                                    GROUP BY date_format(ua.created_at,'%H %p')
                                    ORDER BY date_format(ua.created_at,'%H %p')");

                    $subscriber_activity_growth = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                    join subscribers s on s.id= ua.user_id
                                                    where ua.created_at like '%$date%' and s.state_id =$request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                                                    GROUP BY date_format(ua.created_at,'%H %p')
                                                    ORDER BY date_format(ua.created_at,'%H %p')");

                    $assessment_graph = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where ua.created_at like '%$date%' and s.state_id =$request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                                        GROUP BY date_format(ua.created_at,'%H %p')
                                        ORDER BY date_format(ua.created_at,'%H %p')");
                }
            } else {
                if ($request['date'] == '') {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $total_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request['state_id'])
                            ->where('district_id', $request['district_id'])
                            ->where('block_id', $request['block_id'])->count();

                        $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                            ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                            ->where('subscribers.state_id', $request->state_id)
                            ->where('subscribers.district_id', $request->district_id)
                            ->where('subscribers.block_id', $request->block_id)
                            ->count();

                        $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                            ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                            ->where('subscribers.state_id', $request->state_id)
                            ->where('subscribers.district_id', $request->district_id)
                            ->where('subscribers.block_id', $request->block_id)
                            ->count();
                        $subscriber_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.state_id =" . $request['state_id'] . " and ua.district_id = $request->district_id and ua.block_id = $request->block_id
                                    GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                    ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

                        $subscriber_activity_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                        join subscribers s on s.id= ua.user_id
                                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id =" . $request['state_id'] . " and s.district_id = $request->district_id and s.block_id = $request->block_id
                                                        GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                        ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

                        $assessment_graph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id =" . $request['state_id'] . " and s.district_id = $request->district_id and s.block_id = $request->block_id
                                        GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                        ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    } else {
                        $total_subscriber = Subscriber::where('state_id', $request['state_id'])
                            ->where('district_id', $request['district_id'])
                            ->where('block_id', $request['block_id'])->count();

                        $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                            ->where('subscribers.state_id', $request->state_id)
                            ->where('subscribers.district_id', $request->district_id)
                            ->where('subscribers.block_id', $request->block_id)
                            ->count();

                        $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                            ->where('subscribers.state_id', $request->state_id)
                            ->where('subscribers.district_id', $request->district_id)
                            ->where('subscribers.block_id', $request->block_id)
                            ->count();
                        $subscriber_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.state_id =" . $request['state_id'] . " and ua.district_id = $request->district_id and ua.block_id = $request->block_id
                                    GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                    ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

                        $subscriber_activity_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                        join subscribers s on s.id= ua.user_id
                                                        where s.state_id =" . $request['state_id'] . " and s.district_id = $request->district_id and s.block_id = $request->block_id
                                                        GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                        ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

                        $assessment_graph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where s.state_id =" . $request['state_id'] . " and s.district_id = $request->district_id and s.block_id = $request->block_id
                                        GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                        ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    }
                } else {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $total_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request['state_id'])
                            ->where('district_id', $request['district_id'])
                            ->where('block_id', $request['block_id'])
                            ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();

                        $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                            ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                            ->where('subscribers.state_id', $request->state_id)
                            ->where('subscribers.district_id', $request->district_id)
                            ->where('subscribers.block_id', $request->block_id)
                            ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();

                        $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                            ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                            ->where('subscribers.state_id', $request->state_id)
                            ->where('subscribers.district_id', $request->district_id)
                            ->where('subscribers.block_id', $request->block_id)
                            ->whereDate('user_assessments.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('user_assessments.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();
                        $subscriber_growth = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and ua.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' and ua.district_id = $request->district_id and ua.block_id = $request->block_id
                                    GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                    ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");

                        $subscriber_activity_growth = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                        join subscribers s on s.id= ua.user_id
                                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' and s.district_id = $request->district_id and s.block_id = $request->block_id
                                                        GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                                        ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");

                        $assessment_graph = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' and s.district_id = $request->district_id and s.block_id = $request->block_id
                                        GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                        ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");
                    } else {
                        $total_subscriber = Subscriber::where('state_id', $request['state_id'])
                            ->where('district_id', $request['district_id'])
                            ->where('block_id', $request['block_id'])
                            ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();

                        $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                            ->where('subscribers.state_id', $request->state_id)
                            ->where('subscribers.district_id', $request->district_id)
                            ->where('subscribers.block_id', $request->block_id)
                            ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();

                        $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                            ->where('subscribers.state_id', $request->state_id)
                            ->where('subscribers.district_id', $request->district_id)
                            ->where('subscribers.block_id', $request->block_id)
                            ->whereDate('user_assessments.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                            ->whereDate('user_assessments.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                            ->count();
                        $subscriber_growth = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM subscribers ua
                                    where ua.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' and ua.district_id = $request->district_id and ua.block_id = $request->block_id
                                    GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                    ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");

                        $subscriber_activity_growth = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                        join subscribers s on s.id= ua.user_id
                                                        where s.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' and s.district_id = $request->district_id and s.block_id = $request->block_id
                                                        GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                                        ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");

                        $assessment_graph = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM user_assessments ua 
                                        join subscribers s on s.id=ua.user_id
                                        where s.state_id =" . $request['state_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' and s.district_id = $request->district_id and s.block_id = $request->block_id
                                        GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                        ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");
                    }
                }
            }
        } else {
            if ($request['type'] == "today") {
                $date = date('Y-m-d');
                $total_subscriber = Subscriber::whereDate('created_at', '=', Carbon::now())->count();

                $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                    ->where('is_calculated', 1)->whereDate('user_assessments.created_at', '=', Carbon::now())->count();

                $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                    ->whereDate('subscriber_activities.created_at', '=', Carbon::now())->distinct('user_id')->count();

                $subscriber_growth = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM subscribers ua
                                where ua.created_at like '%$date%'
                                GROUP BY date_format(ua.created_at,'%H %p')
                                ORDER BY date_format(ua.created_at,'%H %p')");

                $subscriber_activity_growth = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                join subscribers s on s.id= ua.user_id
                                                where ua.created_at like '%$date%' 
                                                GROUP BY date_format(ua.created_at,'%H %p')
                                                ORDER BY date_format(ua.created_at,'%H %p')");

                $assessment_graph = DB::select("select date_format(ua.created_at,'%H %p') as date, COUNT(*) as subscriber_count FROM user_assessments ua 
                                    join subscribers s on s.id=ua.user_id
                                    where ua.created_at like '%$date%'
                                    GROUP BY date_format(ua.created_at,'%H %p')
                                    ORDER BY date_format(ua.created_at,'%H %p')");
            } else {
                if ($request['date'] == '') {
                    $total_subscriber = Subscriber::count();

                    $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->count();

                    $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->count();
                    $subscriber_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscribers ua
                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

                    $subscriber_activity_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                    join subscribers s on s.id= ua.user_id
                                                    GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                    ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

                    $assessment_graph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua 
                                    join subscribers s on s.id=ua.user_id
                                    GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                    ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                } else {
                    $values = explode(" ", $request->date);
                    $total_subscriber = Subscriber::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->count();

                    $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->count();

                    $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                        ->whereDate('user_assessments.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('user_assessments.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->count();
                    $subscriber_growth = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM subscribers ua
								where (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");

                    $subscriber_activity_growth = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM subscriber_activities ua
                                                    join subscribers s on s.id= ua.user_id
                                                    where (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                                    GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                                    ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");

                    $assessment_graph = DB::select("select (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) as date, COUNT(*) as subscriber_count FROM user_assessments ua 
                                    join subscribers s on s.id=ua.user_id
                                    where (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                    GROUP BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))
                                    ORDER BY (DATE_FORMAT(ua.created_at,'%Y-%m-%d'))");
                }
            }
        }
        return ['status' => "true", 'data' => [
            'subscriber_growth' => $subscriber_growth, 'subscriber_activity_growth' => $subscriber_activity_growth, 'assessment_graph' => $assessment_graph,
            'total_subscriber' => $total_subscriber, 'total_subscriber_activity' => $total_subscriber_activity, 'total_assessment_completed' => $total_assessment_completed
        ], 'code' => 200];
    }

    public function countListData(Request $request)
    { //commented code in mails
        $subscriber_growth = [];
        $subscriber_activity_growth = [];
        $assessment_graph = [];
        $total_assessment_completed = 0;
        $total_subscriber = 0;
        $total_subscriber_activity = 0;
        if ($request['state_id'] == 0 && $request['district_id'] == 0 && $request['block_id'] == 0) {

            $subscriber_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscribers ua
                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $subscriber_activity_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscriber_activities ua
                                            join subscribers s on s.id= ua.user_id
                                            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $assessment_graph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua 
                                    join subscribers s on s.id=ua.user_id
                                    GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                    ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            if ($request['type'] == "today") {

                $total_subscriber = Subscriber::whereDate('created_at', '=', Carbon::now())->count();

                $total_assessment_completed = UserAssessment::where('is_calculated', 1)->whereDate('created_at', '=', Carbon::now())->count();

                $total_subscriber_activity = SubscriberActivity::whereDate('created_at', '=', Carbon::now())->distinct('user_id')->count();
            } else {
                if ($request['date'] == '') {
                    $total_subscriber = Subscriber::count();

                    $total_subscriber_activity = SubscriberActivity::count();

                    $total_assessment_completed = UserAssessment::count();
                } else {
                    $values = explode(" ", $request->date);
                    $total_subscriber = Subscriber::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->count();

                    $total_subscriber_activity = SubscriberActivity::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->count();

                    $total_assessment_completed = UserAssessment::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->count();
                }
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] == 0 && $request['block_id'] == 0) {
            $subscriber_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscribers ua
                                where ua.state_id =" . $request['state_id'] . "
                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $subscriber_activity_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscriber_activities ua
                                            join subscribers s on s.id= ua.user_id
                                            where s.state_id =" . $request['state_id'] . "
                                            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $assessment_graph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua 
                                    join subscribers s on s.id=ua.user_id
                                    where s.state_id =" . $request['state_id'] . "
                                    GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                    ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            if ($request['type'] == "today") {

                $total_subscriber = Subscriber::where('state_id', $request['state_id'])->whereDate('created_at', '=', Carbon::now())->count();

                $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->where('subscribers.state_id', $request->state_id)
                    ->where('is_calculated', 1)->whereDate('user_assessments.created_at', '=', Carbon::now())->count();

                $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->where('subscribers.state_id', $request->state_id)
                    ->whereDate('subscriber_activities.created_at', '=', Carbon::now())->distinct('user_id')->count();
            } else {
                if ($request['date'] == '') {
                    $total_subscriber = Subscriber::where('state_id', $request['state_id'])->count();

                    $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->where('subscribers.state_id', $request->state_id)->count();

                    $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->where('subscribers.state_id', $request->state_id)->count();
                } else {
                    $values = explode(" ", $request->date);
                    $total_subscriber = Subscriber::where('state_id', $request['state_id'])
                        ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->count();

                    $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->count();

                    $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->whereDate('user_assessments.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('user_assessments.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->count();
                }
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] == 0) {
            $subscriber_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscribers ua
                                where ua.state_id =" . $request['state_id'] . " and ua.district_id =" . $request['district_id'] . "
                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $subscriber_activity_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscriber_activities ua
                                            join subscribers s on s.id= ua.user_id
                                            where s.state_id =" . $request['state_id'] . " and s.district_id =" . $request['district_id'] . "
                                            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $assessment_graph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua 
                                    join subscribers s on s.id=ua.user_id
                                    where s.state_id =" . $request['state_id'] . " and s.district_id = " . $request['district_id'] . "
                                    GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                    ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            if ($request['type'] == "today") {

                $total_subscriber = Subscriber::where('state_id', $request['state_id'])->where('district_id', $request['district_id'])->whereDate('created_at', '=', Carbon::now())->count();

                $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('is_calculated', 1)->whereDate('user_assessments.created_at', '=', Carbon::now())->count();

                $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->whereDate('subscriber_activities.created_at', '=', Carbon::now())->distinct('user_id')->count();
            } else {
                if ($request['date'] == '') {
                    $total_subscriber = Subscriber::where('state_id', $request['state_id'])->where('district_id', $request['district_id'])->count();

                    $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->where('subscribers.state_id', $request->state_id)->where('subscribers.district_id', $request->district_id)->count();

                    $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->where('subscribers.state_id', $request->state_id)->where('subscribers.district_id', $request->district_id)->count();
                } else {
                    $values = explode(" ", $request->date);
                    $total_subscriber = Subscriber::where('state_id', $request['state_id'])
                        ->where('district_id', $request['district_id'])
                        ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->count();

                    $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->count();

                    $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->whereDate('user_assessments.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('user_assessments.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->count();
                }
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] != 0 && $request['date'] == '') {
            $subscriber_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscribers ua
                                where ua.state_id =" . $request['state_id'] . " and ua.district_id =" . $request['district_id'] . " and ua.block_id = " . $request['block_id'] . "
                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $subscriber_activity_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscriber_activities ua
                                            join subscribers s on s.id= ua.user_id
                                            where s.state_id =" . $request['state_id'] . " and s.district_id =" . $request['district_id'] . " and s.block_id=" . $request['block_id'] . "
                                            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $assessment_graph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua 
                                    join subscribers s on s.id=ua.user_id
                                    where s.state_id =" . $request['state_id'] . " and s.district_id = " . $request['district_id'] . " and s.block_id=" . $request['block_id'] . " 
                                    GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                    ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            if ($request['type'] == "today") {

                $total_subscriber = Subscriber::where('state_id', $request['state_id'])->where('district_id', $request['district_id'])->where('block_id', $request['block_id'])->whereDate('created_at', '=', Carbon::now())->count();

                $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('subscribers.block_id', $request->block_id)
                    ->where('is_calculated', 1)->whereDate('user_assessments.created_at', '=', Carbon::now())->count();

                $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('subscribers.block_id', $request->block_id)
                    ->whereDate('subscriber_activities.created_at', '=', Carbon::now())->distinct('user_id')->count();
            } else {
                if ($request['date'] == '') {
                    $total_subscriber = Subscriber::where('state_id', $request['state_id'])
                        ->where('district_id', $request['district_id'])
                        ->where('block_id', $request['block_id'])->count();

                    $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->count();

                    $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->count();
                } else {
                    $values = explode(" ", $request->date);
                    $total_subscriber = Subscriber::where('state_id', $request['state_id'])
                        ->where('district_id', $request['district_id'])
                        ->where('block_id', $request['block_id'])
                        ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->count();

                    $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->count();

                    $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->whereDate('user_assessments.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('user_assessments.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->count();
                }
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] != 0 && $request['date'] != "") {
            $values = explode(" ", $request->date);
            $subscriber_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscribers ua
                                where ua.state_id =" . $request['state_id'] . " and ua.district_id =" . $request['district_id'] . " and ua.block_id = " . $request['block_id'] . " and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $subscriber_activity_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscriber_activities ua
                                            join subscribers s on s.id= ua.user_id
                                            where s.state_id =" . $request['state_id'] . " and s.district_id =" . $request['district_id'] . " and s.block_id=" . $request['block_id'] . " and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $assessment_graph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua 
                                    join subscribers s on s.id=ua.user_id
                                    where s.state_id =" . $request['state_id'] . " and s.district_id = " . $request['district_id'] . " and s.block_id=" . $request['block_id'] . " and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                    GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                    ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            if ($request['type'] == "today") {

                $total_subscriber = Subscriber::where('state_id', $request['state_id'])
                    ->where('district_id', $request['district_id'])
                    ->where('block_id', $request['block_id'])
                    ->whereDate('created_at', '=', Carbon::now())->count();

                $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('subscribers.block_id', $request->block_id)
                    ->where('is_calculated', 1)->whereDate('created_at', '=', Carbon::now())->count();

                $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('subscribers.block_id', $request->block_id)
                    ->whereDate('created_at', '=', Carbon::now())->distinct('user_id')->count();
            } else {

                $total_subscriber = Subscriber::where('state_id', $request['state_id'])
                    ->where('district_id', $request['district_id'])
                    ->where('block_id', $request['block_id'])
                    ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->count();

                $total_subscriber_activity = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                    ->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('subscribers.block_id', $request->block_id)
                    ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->count();

                $total_assessment_completed = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                    ->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('subscribers.block_id', $request->block_id)
                    ->whereDate('user_assessments.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('user_assessments.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->count();
            }
        } else if ($request['state_id'] == 0 && $request['district_id'] == 0 && $request['block_id'] == 0 && $request['date'] != "") {
            $values = explode(" ", $request->date);
            $subscriber_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscribers ua
                                where (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $subscriber_activity_growth = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM subscriber_activities ua
                                            join subscribers s on s.id= ua.user_id
                                            where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $assessment_graph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua 
                                    join subscribers s on s.id=ua.user_id
                                    where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                    GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                    ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            if ($request['type'] == "today") {

                $total_subscriber = Subscriber::whereDate('created_at', '=', Carbon::now())->count();

                $total_assessment_completed = UserAssessment::where('is_calculated', 1)->whereDate('created_at', '=', Carbon::now())->count();

                $total_subscriber_activity = SubscriberActivity::whereDate('created_at', '=', Carbon::now())->distinct('user_id')->count();
            } else {

                $total_subscriber = Subscriber::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->count();

                $total_subscriber_activity = SubscriberActivity::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->count();

                $total_assessment_completed = UserAssessment::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->count();
            }
        }

        return ['status' => "true", 'data' => [
            'subscriber_growth' => $subscriber_growth, 'subscriber_activity_growth' => $subscriber_activity_growth, 'assessment_graph' => $assessment_graph,
            'total_subscriber' => $total_subscriber, 'total_subscriber_activity' => $total_subscriber_activity, 'total_assessment_completed' => $total_assessment_completed
        ], 'code' => 200];
    }

    public function usageCountData(Request $request)
    { //commented code in mails
        $chatbot_usage_count = 0;
        $resource_material_usage_count = 0;
        $screening_tool = 0;
        $total_time_spent = 0;
        if ($request['state_id'] == 0 && $request['district_id'] == 0 && $request['block_id'] == 0) {
            if ($request['date'] == '') {
                $chatbot_usage_count = SubscriberActivity::where('action', 'LIKE', "%Search By Keyword Fetched%")->orWhere('action', 'LIKE', "%Chat Questions By Keyword Fetched%")->count();
                // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                $resource_material_usage_count = SubscriberActivity::where('action', 'LIKE', '%Open_Resource_Materials%')->count();
                $screening_tool = SubscriberActivity::where('action', 'LIKE', '%module_screening_tool%')->count();
                // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                $total_time_spent = round((LbSubscriberRanking::sum('mins_spent_count')) / 60, 2);
            } else {
                $values = explode(" ", $request->date);
                $chatbot_usage_count = SubscriberActivity::where('action', 'LIKE', "%Search By Keyword Fetched%")
                    ->orWhere('action', 'LIKE', "%Chat Questions By Keyword Fetched%")
                    ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->count();
                // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                $resource_material_usage_count = SubscriberActivity::where('action', 'LIKE', '%Open_Resource_Materials%')
                    ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->count();
                $screening_tool = SubscriberActivity::where('action', 'LIKE', '%module_screening_tool%')->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->count();
                // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                $total_time_spent = round((LbSubscriberRanking::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->sum('mins_spent_count')) / 60, 2);
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] == 0 && $request['block_id'] == 0) {
            if ($request['date'] == '') {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $chatbot_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('action', 'like', "%Search By Keyword Fetched%")
                        ->orWhere('action', 'like', "%Chat Questions By Keyword Fetched%")
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                    $resource_material_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('action', 'LIKE', '%Open_Resource_Materials%')
                        ->count();

                    $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('action', 'LIKE', '%module_screening_tool%')
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                    $total_time_spent = round((LbSubscriberRanking::join('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('subscribers.state_id', $request->state_id)->sum('mins_spent_count')) / 60, 2);
                } else {
                    $chatbot_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('action', 'like', "%Search By Keyword Fetched%")
                        ->orWhere('action', 'like', "%Chat Questions By Keyword Fetched%")
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                    $resource_material_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('action', 'LIKE', '%Open_Resource_Materials%')
                        ->count();

                    $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('action', 'LIKE', '%module_screening_tool%')
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                    $total_time_spent = round((LbSubscriberRanking::join('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
                        ->where('subscribers.state_id', $request->state_id)->sum('mins_spent_count')) / 60, 2);
                }
            } else {
                $values = explode(" ", $request->date);
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $chatbot_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('action', 'like', "%Search By Keyword Fetched%")
                        ->orWhere('action', 'like', "%Chat Questions By Keyword Fetched%")
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                    $resource_material_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('action', 'LIKE', '%Open_Resource_Materials%')
                        ->count();

                    $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('action', 'LIKE', '%module_screening_tool%')
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                    $total_time_spent = round((LbSubscriberRanking::join('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->whereDate('lb_subscriber_rankings.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('lb_subscriber_rankings.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)->sum('mins_spent_count')) / 60, 2);
                } else {
                    $chatbot_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('action', 'like', "%Search By Keyword Fetched%")
                        ->orWhere('action', 'like', "%Chat Questions By Keyword Fetched%")
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                    $resource_material_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('action', 'LIKE', '%Open_Resource_Materials%')
                        ->count();

                    $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('action', 'LIKE', '%module_screening_tool%')
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                    $total_time_spent = round((LbSubscriberRanking::join('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
                        ->whereDate('lb_subscriber_rankings.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('lb_subscriber_rankings.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)->sum('mins_spent_count')) / 60, 2);
                }
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] == 0) {
            if ($request['date'] == '') {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $chatbot_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('action', 'like', "%Search By Keyword Fetched%")
                        ->orWhere('action', 'like', "%Chat Questions By Keyword Fetched%")
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                    $resource_material_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('action', 'LIKE', '%Open_Resource_Materials%')
                        ->count();

                    $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('action', 'LIKE', '%module_screening_tool%')
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                    $total_time_spent = round((LbSubscriberRanking::join('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
                        ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)->where('subscribers.district_id', $request->district_id)->sum('mins_spent_count')) / 60, 2);
                } else {
                    $chatbot_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('action', 'like', "%Search By Keyword Fetched%")
                        ->orWhere('action', 'like', "%Chat Questions By Keyword Fetched%")
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                    $resource_material_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('action', 'LIKE', '%Open_Resource_Materials%')
                        ->count();

                    $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('action', 'LIKE', '%module_screening_tool%')
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                    $total_time_spent = round((LbSubscriberRanking::join('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
                        ->where('subscribers.state_id', $request->state_id)->where('subscribers.district_id', $request->district_id)->sum('mins_spent_count')) / 60, 2);
                }
            } else {
                $values = explode(" ", $request->date);
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $chatbot_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.state_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('action', 'like', "%Search By Keyword Fetched%")
                        ->orWhere('action', 'like', "%Chat Questions By Keyword Fetched%")
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                    $resource_material_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.state_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('action', 'LIKE', '%Open_Resource_Materials%')
                        ->count();

                    $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.state_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('action', 'LIKE', '%module_screening_tool%')
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                    $total_time_spent = round((LbSubscriberRanking::join('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
                        ->whereIn('subscribers.state_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->whereDate('lb_subscriber_rankings.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('lb_subscriber_rankings.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)->where('subscribers.district_id', $request->district_id)->sum('mins_spent_count')) / 60, 2);
                } else {
                    $chatbot_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('action', 'like', "%Search By Keyword Fetched%")
                        ->orWhere('action', 'like', "%Chat Questions By Keyword Fetched%")
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                    $resource_material_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('action', 'LIKE', '%Open_Resource_Materials%')
                        ->count();

                    $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('action', 'LIKE', '%module_screening_tool%')
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                    $total_time_spent = round((LbSubscriberRanking::join('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
                        ->whereDate('lb_subscriber_rankings.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('lb_subscriber_rankings.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)->where('subscribers.district_id', $request->district_id)->sum('mins_spent_count')) / 60, 2);
                }
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] != 0 && $request['date'] == '') {
            if ($request['date'] == '') {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $chatbot_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.state_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->where('action', 'like', "%Search By Keyword Fetched%")
                        ->orWhere('action', 'like', "%Chat Questions By Keyword Fetched%")
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                    $resource_material_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.state_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->where('action', 'LIKE', '%Open_Resource_Materials%')
                        ->count();
                    $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.state_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->where('action', 'LIKE', '%module_screening_tool%')
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                    $total_time_spent = round((LbSubscriberRanking::join('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
                        ->whereIn('subscribers.state_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->sum('mins_spent_count')) / 60, 2);
                } else {
                    $chatbot_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->where('action', 'like', "%Search By Keyword Fetched%")
                        ->orWhere('action', 'like', "%Chat Questions By Keyword Fetched%")
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                    $resource_material_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->where('action', 'LIKE', '%Open_Resource_Materials%')
                        ->count();
                    $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->where('action', 'LIKE', '%module_screening_tool%')
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                    $total_time_spent = round((LbSubscriberRanking::join('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->sum('mins_spent_count')) / 60, 2);
                }
            } else {
                $values = explode(" ", $request->date);
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $chatbot_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.state_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->where('action', 'like', "%Search By Keyword Fetched%")
                        ->orWhere('action', 'like', "%Chat Questions By Keyword Fetched%")
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                    $resource_material_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.state_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->where('action', 'LIKE', '%Open_Resource_Materials%')
                        ->count();
                    $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereIn('subscribers.state_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->where('action', 'LIKE', '%module_screening_tool%')
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                    $total_time_spent = round((LbSubscriberRanking::join('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
                        ->whereIn('subscribers.state_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->sum('mins_spent_count')) / 60, 2);
                } else {
                    $chatbot_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->where('action', 'like', "%Search By Keyword Fetched%")
                        ->orWhere('action', 'like', "%Chat Questions By Keyword Fetched%")
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                    $resource_material_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->where('action', 'LIKE', '%Open_Resource_Materials%')
                        ->count();
                    $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->where('action', 'LIKE', '%module_screening_tool%')
                        ->count();
                    // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                    $total_time_spent = round((LbSubscriberRanking::join('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
                        ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                        ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('subscribers.state_id', $request->state_id)
                        ->where('subscribers.district_id', $request->district_id)
                        ->where('subscribers.block_id', $request->block_id)
                        ->sum('mins_spent_count')) / 60, 2);
                }
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] != 0 && $request['date'] != "") {
            $values = explode(" ", $request->date);
            if (\Auth::user()->roles[0]['id'] == 10) {
                $chatbot_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                    ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                    ->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('subscribers.block_id', $request->block_id)
                    ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->where('action', 'like', "%Search By Keyword Fetched%")
                    ->orWhere('action', 'like', "%Chat Questions By Keyword Fetched%")
                    ->count();
                // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                $resource_material_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                    ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                    ->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('subscribers.block_id', $request->block_id)
                    ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->where('action', 'LIKE', '%Open_Resource_Materials%')
                    ->count();
                $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                    ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                    ->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('subscribers.block_id', $request->block_id)
                    ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->where('action', 'LIKE', '%module_screening_tool%')
                    ->count();
                // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                $total_time_spent = round((LbSubscriberRanking::join('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
                    ->whereIn('subscribers.cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])
                    ->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('subscribers.block_id', $request->block_id)
                    ->whereDate('subscribers.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('subscribers.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->sum('mins_spent_count')) / 60, 2);
            } else {
                $chatbot_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                    ->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('subscribers.block_id', $request->block_id)
                    ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->where('action', 'like', "%Search By Keyword Fetched%")
                    ->orWhere('action', 'like', "%Chat Questions By Keyword Fetched%")
                    ->count();
                // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
                $resource_material_usage_count = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                    ->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('subscribers.block_id', $request->block_id)
                    ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->where('action', 'LIKE', '%Open_Resource_Materials%')
                    ->count();
                $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                    ->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('subscribers.block_id', $request->block_id)
                    ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->where('action', 'LIKE', '%module_screening_tool%')
                    ->count();
                // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
                $total_time_spent = round((LbSubscriberRanking::join('subscribers', 'subscribers.id', '=', 'lb_subscriber_rankings.subscriber_id')
                    ->where('subscribers.state_id', $request->state_id)
                    ->where('subscribers.district_id', $request->district_id)
                    ->where('subscribers.block_id', $request->block_id)
                    ->whereDate('subscribers.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                    ->whereDate('subscribers.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                    ->sum('mins_spent_count')) / 60, 2);
            }
        } else if ($request['state_id'] == 0 && $request['district_id'] == 0 && $request['block_id'] == 0 && $request['date'] != "") {
            $values = explode(" ", $request->date);

            $chatbot_usage_count = SubscriberActivity::whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                ->where('action', 'like', "%Search By Keyword Fetched%")
                ->where('action', 'like', "%Chat Questions By Keyword Fetched%")
                ->count();
            // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Search By Keyword Fetched%" or action like "%Chat Questions By Keyword Fetched%" ');
            $resource_material_usage_count = SubscriberActivity::whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                ->where('action', 'LIKE', '%Open_Resource_Materials%')
                ->count();
            $screening_tool = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
                ->whereDate('subscriber_activities.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                ->whereDate('subscriber_activities.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                ->where('action', 'LIKE', '%module_screening_tool%')
                ->count();
            // DB::select('SELECT count(*) FROM `subscriber_activities` sa WHERE action like "%Open_Resource_Materials%"');
            $total_time_spent = round((LbSubscriberRanking::whereDate('subscribers.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                ->whereDate('subscribers.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                ->sum('mins_spent_count')) / 60, 2);
        }
        return ['status' => True, 'data' => ['chatbot_usage_count' => $chatbot_usage_count, 'resource_material_usage_count' => $resource_material_usage_count, 'total_time_spent' => $total_time_spent, 'screening_tool' => $screening_tool], 'code' => 200];
    }

    public function subscriberCount(Request $request)
    { //commented code in mails
        $stateWiseSubscriber = [];
        $state_level_subscriber = 0;
        if ($request['state_id'] == 0 && $request['district_id'] == 0 && $request['block_id'] == 0) {
            if ($request['date'] == '') {
                $stateWiseSubscriber = DB::select("SELECT DISTINCT(state_id),St.title as title,count(*) as TotalCount FROM subscribers s left Join state St 
                    on St.ID = s.state_id WHERE St.deleted_at IS NULL and s.state_id  not in(37,0)
                    GROUP BY state_id ORDER BY count(*) DESC");
                $state_level_subscriber = Subscriber::where('country_id', 1)->count();
            } else {
                $values = explode(" ", $request->date);
                $stateWiseSubscriber = DB::select("SELECT DISTINCT(state_id),St.title as title,count(*) as TotalCount FROM subscribers s left Join state St 
                    on St.ID = s.state_id WHERE St.deleted_at IS NULL and s.state_id  not in(37,0)  and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                    GROUP BY state_id ORDER BY count(*) DESC");
                $state_level_subscriber = Subscriber::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->where('country_id', 1)->count();
            }

            foreach ($stateWiseSubscriber as $today_subscriber) {
                $today_subscriber->todays_subscriber = Subscriber::where('state_id', $today_subscriber->state_id)->whereDate('created_at', '=', date('Y-m-d'))->count();
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] == 0 && $request['block_id'] == 0) {
            if ($request['date'] == '') {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $stateWiseSubscriber = DB::select("SELECT district_id,d.title as title,count(*) as TotalCount FROM subscribers s Join districts d 
                        on d.ID = s.district_id WHERE s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id
                        GROUP BY district_id ORDER BY count(*) DESC");
                    $state_level_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request->state_id)->where('district_id', 0)->count();
                } else {
                    $stateWiseSubscriber = DB::select("SELECT district_id,d.title as title,count(*) as TotalCount FROM subscribers s Join districts d 
                        on d.ID = s.district_id WHERE s.state_id = $request->state_id
                        GROUP BY district_id ORDER BY count(*) DESC");
                    $state_level_subscriber = Subscriber::where('state_id', $request->state_id)->where('district_id', 0)->count();
                }
            } else {
                $values = explode(" ", $request->date);
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $stateWiseSubscriber = DB::select("SELECT district_id,d.title as title,count(*) as TotalCount FROM subscribers s Join districts d 
                        on d.ID = s.district_id WHERE s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        GROUP BY district_id ORDER BY count(*) DESC");
                    $state_level_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request->state_id)->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->where('district_id', 0)->count();
                } else {
                    $stateWiseSubscriber = DB::select("SELECT district_id,d.title as title,count(*) as TotalCount FROM subscribers s Join districts d 
                        on d.ID = s.district_id WHERE s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        GROUP BY district_id ORDER BY count(*) DESC");
                    $state_level_subscriber = Subscriber::where('state_id', $request->state_id)->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->where('district_id', 0)->count();
                }
            }
            foreach ($stateWiseSubscriber as $today_subscriber) {
                $today_subscriber->todays_subscriber = Subscriber::where('state_id', $request->state_id)->where('district_id', $today_subscriber->district_id)->whereDate('created_at', '=', date('Y-m-d'))->count();
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] == 0) {
            if ($request['date'] == '') {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $stateWiseSubscriber = DB::select("SELECT block_id,b.title as title,count(*) as TotalCount FROM subscribers s Join blocks b 
                        on b.ID = s.block_id WHERE s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and b.state_id = $request->state_id and b.district_id = $request->district_id
                        GROUP BY block_id ORDER BY count(*) DESC");
                    $state_level_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request->state_id)->where('district_id', $request->district_id)->where('block_id', 0)->count();
                } else {
                    $stateWiseSubscriber = DB::select("SELECT block_id,b.title as title,count(*) as TotalCount FROM subscribers s Join blocks b 
                        on b.ID = s.block_id WHERE b.state_id = $request->state_id and b.district_id = $request->district_id
                        GROUP BY block_id ORDER BY count(*) DESC");
                    $state_level_subscriber = Subscriber::where('state_id', $request->state_id)->where('district_id', $request->district_id)->where('block_id', 0)->count();
                }
            } else {
                $values = explode(" ", $request->date);
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $stateWiseSubscriber = DB::select("SELECT block_id,b.title as title,count(*) as TotalCount FROM subscribers s Join blocks b 
                    on b.ID = s.block_id WHERE s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and b.state_id = $request->state_id and b.district_id = $request->district_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                    GROUP BY block_id ORDER BY count(*) DESC");
                    $state_level_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request->state_id)
                        ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('district_id', $request->district_id)->where('block_id', 0)->count();
                } else {
                    $stateWiseSubscriber = DB::select("SELECT block_id,b.title as title,count(*) as TotalCount FROM subscribers s Join blocks b 
                    on b.ID = s.block_id WHERE b.state_id = $request->state_id and b.district_id = $request->district_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                    GROUP BY block_id ORDER BY count(*) DESC");
                    $state_level_subscriber = Subscriber::where('state_id', $request->state_id)
                        ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                        ->where('district_id', $request->district_id)->where('block_id', 0)->count();
                }
            }
            foreach ($stateWiseSubscriber as $today_subscriber) {
                $today_subscriber->todays_subscriber = Subscriber::where('state_id', $request->state_id)->where('district_id', $request->district_id)->where('block_id', $today_subscriber->block_id)->whereDate('created_at', '=', date('Y-m-d'))->count();
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] != 0 && $request['date'] == '') {
            if ($request['date'] == '') {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $stateWiseSubscriber = DB::select("SELECT health_facility_id,h.health_facility_code as title,count(*) as TotalCount FROM subscribers s Join health_facilities h 
                    on h.ID = s.health_facility_id 
                    WHERE s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and h.state_id = $request->state_id and h.district_id = $request->district_id and h.block_id=$request->block_id
                    GROUP BY health_facility_id ORDER BY count(*) DESC");
                    $state_level_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request->state_id)->where('district_id', $request->district_id)->where('block_id', $request->block_id)->where('health_facility_id', 0)->count();
                } else {
                    $stateWiseSubscriber = DB::select("SELECT health_facility_id,h.health_facility_code as title,count(*) as TotalCount FROM subscribers s Join health_facilities h 
                    on h.ID = s.health_facility_id 
                    WHERE h.state_id = $request->state_id and h.district_id = $request->district_id and h.block_id=$request->block_id
                    GROUP BY health_facility_id ORDER BY count(*) DESC");
                    $state_level_subscriber = Subscriber::where('state_id', $request->state_id)->where('district_id', $request->district_id)->where('block_id', $request->block_id)->where('health_facility_id', 0)->count();
                }
            } else {
                $values = explode(" ", $request->date);
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $stateWiseSubscriber = DB::select("SELECT health_facility_id,h.health_facility_code as title,count(*) as TotalCount FROM subscribers s Join health_facilities h 
                    on h.ID = s.health_facility_id WHERE s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and h.state_id = $request->state_id and h.district_id = $request->district_id and h.block_id=$request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' 
                    GROUP BY health_facility_id ORDER BY count(*) DESC");
                    $state_level_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request->state_id)->where('district_id', $request->district_id)->where('block_id', $request->block_id)->where('health_facility_id', 0)->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->count();
                } else {
                    $stateWiseSubscriber = DB::select("SELECT health_facility_id,h.health_facility_code as title,count(*) as TotalCount FROM subscribers s Join health_facilities h 
                    on h.ID = s.health_facility_id WHERE h.state_id = $request->state_id and h.district_id = $request->district_id and h.block_id=$request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' 
                    GROUP BY health_facility_id ORDER BY count(*) DESC");
                    $state_level_subscriber = Subscriber::where('state_id', $request->state_id)->where('district_id', $request->district_id)->where('block_id', $request->block_id)->where('health_facility_id', 0)->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->count();
                }
            }
            foreach ($stateWiseSubscriber as $today_subscriber) {
                $today_subscriber->todays_subscriber = Subscriber::where('state_id', $request->state_id)->where('district_id', $request->district_id)->where('block_id', $request->block_id)->where('health_facility_id', $today_subscriber->health_facility_id)->whereDate('created_at', '=', date('Y-m-d'))->count();
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] != 0 && $request['date'] != "") {

            $values = explode(" ", $request->date);
            if (\Auth::user()->roles[0]['id'] == 10) {
                $stateWiseSubscriber = DB::select("SELECT health_facility_id,h.health_facility_code as title,count(*) as TotalCount FROM subscribers s Join health_facilities h 
                    on h.ID = s.health_facility_id WHERE s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and h.state_id = $request->state_id and h.district_id = $request->district_id and h.block_id=$request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' 
                    GROUP BY health_facility_id ORDER BY count(*) DESC");
                $state_level_subscriber = Subscriber::whereIn('cadre_id', [107, 106, 105, 104, 103, 102, 101, 100, 99, 98, 97, 96, 95, 94, 93, 92, 91, 90, 89, 88, 57, 17])->where('state_id', $request->state_id)->where('district_id', $request->district_id)->where('block_id', $request->block_id)->where('health_facility_id', 0)->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->count();
            } else {
                $stateWiseSubscriber = DB::select("SELECT health_facility_id,h.health_facility_code as title,count(*) as TotalCount FROM subscribers s Join health_facilities h 
                    on h.ID = s.health_facility_id WHERE h.state_id = $request->state_id and h.district_id = $request->district_id and h.block_id=$request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' 
                    GROUP BY health_facility_id ORDER BY count(*) DESC");
                $state_level_subscriber = Subscriber::where('state_id', $request->state_id)->where('district_id', $request->district_id)->where('block_id', $request->block_id)->where('health_facility_id', 0)->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->count();
            }

            foreach ($stateWiseSubscriber as $today_subscriber) {
                $today_subscriber->todays_subscriber = Subscriber::where('state_id', $request->state_id)->where('district_id', $request->district_id)->where('block_id', $request->block_id)->where('health_facility_id', $today_subscriber->health_facility_id)->whereDate('created_at', '=', date('Y-m-d'))->count();
            }
        } else if ($request['state_id'] == 0 && $request['district_id'] == 0 && $request['block_id'] == 0 && $request['date'] != "") {

            $values = explode(" ", $request->date);
            $stateWiseSubscriber = DB::select("SELECT health_facility_id,h.health_facility_code as title,count(*) as TotalCount FROM subscribers s 
                                WHERE (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' 
                                GROUP BY health_facility_id ORDER BY count(*) DESC");
            $state_level_subscriber = Subscriber::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->count();
            foreach ($stateWiseSubscriber as $today_subscriber) {
                $today_subscriber->todays_subscriber = Subscriber::where('state_id', $today_subscriber->state_id)->whereDate('created_at', '=', date('Y-m-d'))->count();
            }
        }
        foreach ($stateWiseSubscriber as $sub) {
            // $sub->todays_subscriber = Subscriber::whereDate('created_at', '=', date('Y-m-d'))->count();
            $sub->percentage = round(($sub->TotalCount * 100) / Subscriber::count(), 2);
        }
        return ['status' => True, 'data' => ["state_wise_subscriber" => $stateWiseSubscriber, 'state_level_subscriber' => $state_level_subscriber], 'code' => 200];
    }

    public function cadreWiseSubscriber(Request $request)
    { //commented code in mails
        if ($request['state_id'] != 0 && $request['district_id'] == 0 && $request['block_id'] == 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                    } else {
                        $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                    } else {
                        $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.state_id = $request->state_id
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(s.created_at,now())) between 0 and 30 and s.state_id = $request->state_id
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                } else {
                    $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where abs(DATEDIFF(s.created_at,now())) between 0 and 30 and s.state_id = $request->state_id
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                }
            }
        } elseif ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] == 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                    } else {
                        $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.state_id = $request->state_id and s.district_id = $request->district_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id 
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                    } else {
                        $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.state_id = $request->state_id and s.district_id = $request->district_id 
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(s.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id 
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                } else {
                    $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where abs(DATEDIFF(s.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id 
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                }
            }
        } elseif ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] != 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id =$request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                    } else {
                        $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id =$request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id =$request->block_id
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                    } else {
                        $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id =$request->block_id
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(s.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id =$request->block_id
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                } else {
                    $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where abs(DATEDIFF(s.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id =$request->block_id
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                }
            }
        } else {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                } else {
                    $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
                }
            } else {
                $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                        on cd.ID = s.cadre_id where abs(DATEDIFF(s.created_at,now())) between 0 and 30
                        GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 5");
            }
        }


        return ['status' => True, 'data' => $cadreWiseSubscriber, 'code' => 200];
    }

    public function moduleUsage(Request $request)
    { //commented code in mails
        if ($request['state_id'] != 0 && $request['district_id'] == 0 && $request['block_id'] == 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $top10Modules =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.action LIKE 'module_%'  and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                    } else {
                        $top10Modules =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%'  and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $top10Modules =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                    } else {
                        $top10Modules =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
                    on s.id = sa.user_id 
                    WHERE s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and abs(DATEDIFF(sa.created_at,now())) between 0 and 30 and s.state_id = $request->state_id
                    GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                } else {
                    $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
                    on s.id = sa.user_id 
                    WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and abs(DATEDIFF(sa.created_at,now())) between 0 and 30 and s.state_id = $request->state_id
                    GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                }
            }
        } elseif ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] == 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $top10Modules =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id and s.district_id = $request->district_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                    } else {
                        $top10Modules =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id and s.district_id = $request->district_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $top10Modules =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id and s.district_id = $request->district_id GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                    } else {
                        $top10Modules =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id and s.district_id = $request->district_id GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
                    on s.id = sa.user_id 
                    WHERE s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and abs(DATEDIFF(sa.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id
                    GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                } else {
                    $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
                    on s.id = sa.user_id 
                    WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and abs(DATEDIFF(sa.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id
                    GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                }
            }
        } elseif ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] != 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $top10Modules =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE  s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'  GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                    } else {
                        $top10Modules =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'  GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $top10Modules =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE  s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                    } else {
                        $top10Modules =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
                    on s.id = sa.user_id 
                    WHERE s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and abs(DATEDIFF(sa.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                    GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                } else {
                    $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
                    on s.id = sa.user_id 
                    WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and abs(DATEDIFF(sa.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                    GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                }
            }
        } else {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    $top10Modules =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                } else {
                    // $top10Modules =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' GROUP BY action ORDER BY count(*) DESC");
                    $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
                        on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments'
                        GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
                }
            } else {
                $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
                    on s.id = sa.user_id 
                    WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and abs(DATEDIFF(sa.created_at,now())) between 0 and 30
                    GROUP BY action ORDER BY count(*) DESC  LIMIT 10");
            }
        }

        $actions = collect([]);
        foreach ($top10Modules as $items) {
            $mapping_name = ModuleMappingToName::where('module_name', $items->action)->get(['mapping_name']);
            if (isset($mapping_name) && count($mapping_name) > 0) {
                $actions->push(['action' => $mapping_name[0]['mapping_name'], 'TotalCount' => $items->TotalCount]);
            } else {
                continue;
            }
        }

        return ['status' => True, 'data' => $actions->toArray(), 'code' => 200];
    }

    public function leaderboardLevels(Request $request)
    { //commented code in mails

        /* SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
            JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id
            join lb_levels l on l.id = lb.level_id
            group by `cadre_type`,lb.level_id
        */

        /* SELECT cadre_type,count(*) as count_data,lb.level_id FROM `subscribers` s
            JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id
            join lb_levels l on l.id = lb.level_id
            where abs(DATEDIFF(lb.created_at,now())) between 0 and 30 
            group by `cadre_type`,lb.level_id
        */
        $leaderBoardLevel = [];
        if ($request['state_id'] == 0 && $request['district_id'] == 0 && $request['block_id'] == 0) {
            if ($request->type == "overall") {
                if ($request['date'] == '') {
                    $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id
                        join lb_levels l on l.id = lb.level_id
                        group by `cadre_type`,lb.level_id ");
                } else {
                    $values = explode(" ", $request->date);
                    $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id
                        join lb_levels l on l.id = lb.level_id
                        where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        group by `cadre_type`,lb.level_id ");
                }
            } else {
                $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id
                        join lb_levels l on l.id = lb.level_id
                        where abs(DATEDIFF(lb.created_at,now())) between 0 and 30 
                        group by `cadre_type`,lb.level_id");
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] == 0 && $request['block_id'] == 0) {
            if ($request->type == "overall") {
                if ($request['date'] == '') {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id
                        group by `cadre_type`,lb.level_id ");
                    } else {
                        $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where s.state_id = $request->state_id
                        group by `cadre_type`,lb.level_id ");
                    }
                } else {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        group by `cadre_type`,lb.level_id ");
                    } else {
                        $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        group by `cadre_type`,lb.level_id ");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(lb.created_at,now())) between 0 and 30 and s.state_id = $request->state_id
                        group by `cadre_type`,lb.level_id");
                } else {
                    $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where abs(DATEDIFF(lb.created_at,now())) between 0 and 30 and s.state_id = $request->state_id
                        group by `cadre_type`,lb.level_id");
                }
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] == 0) {
            if ($request->type == "overall") {
                if ($request['date'] == '') {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id
                        group by `cadre_type`,lb.level_id ");
                    } else {
                        $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where s.state_id = $request->state_id and s.district_id = $request->district_id
                        group by `cadre_type`,lb.level_id ");
                    }
                } else {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        group by `cadre_type`,lb.level_id ");
                    } else {
                        $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where s.state_id = $request->state_id and s.district_id = $request->district_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        group by `cadre_type`,lb.level_id ");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id
                        join lb_levels l on l.id = lb.level_id
                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(lb.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id
                        group by `cadre_type`,lb.level_id");
                } else {
                    $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id
                        join lb_levels l on l.id = lb.level_id
                        where abs(DATEDIFF(lb.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id
                        group by `cadre_type`,lb.level_id");
                }
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] != 0 && $request['date'] == '') {
            if ($request->type == "overall") {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id
                        join lb_levels l on l.id = lb.level_id
                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                        group by `cadre_type`,lb.level_id ");
                } else {
                    $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id
                        join lb_levels l on l.id = lb.level_id
                        where s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                        group by `cadre_type`,lb.level_id ");
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id
                        join lb_levels l on l.id = lb.level_id
                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(lb.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                        group by `cadre_type`,lb.level_id");
                } else {
                    $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id
                        join lb_levels l on l.id = lb.level_id
                        where abs(DATEDIFF(lb.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                        group by `cadre_type`,lb.level_id");
                }
            }
        } else if ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] != 0 && $request['date'] != "") {

            if ($request->type == "overall") {
                $values = explode(" ", $request->date);
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and  s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        group by `cadre_type`,lb.level_id ");
                } else {
                    $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        group by `cadre_type`,lb.level_id ");
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(lb.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                        group by `cadre_type`,lb.level_id");
                } else {
                    $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(lb.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                        group by `cadre_type`,lb.level_id");
                }
            }
        } else if ($request['state_id'] == 0 && $request['district_id'] == 0 && $request['block_id'] == 0 && $request['date'] != "") {
            if ($request->type == "overall") {
                $values = explode(" ", $request->date);
                $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id 
                        join lb_levels l on l.id = lb.level_id
                        where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        group by `cadre_type`,lb.level_id ");
            } else {
                $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s
                        JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id
                        join lb_levels l on l.id = lb.level_id
                        where abs(DATEDIFF(lb.created_at,now())) between 0 and 30
                        group by `cadre_type`,lb.level_id");
            }
        }
        return ['status' => True, 'data' => $leaderBoardLevel, 'code' => 200];
    }

    public function chatQuestionHits(Request $request)
    { //commented code in mails

        /*  SELECT  CQ.question, COUNT(CQH.QUESTION_ID) 
                FROM chat_question_hits CQH 
                INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id
                inner join subscribers s on s.id= CQH.subscriber_id
                where abs(DATEDIFF(CQH.created_at,now())) between 0 and 30
                GROUP BY CQ.question
                ORDER BY COUNT(CQ.hit) DESC
                LIMIT 10
        */
        /* SELECT DISTINCT(ch.question),ch.hit FROM `chat_questions` ch 
            join chat_question_hits cqh on cqh.question_id = ch.id  
            join subscribers s on cqh.subscriber_id = s.id
           	where abs(DATEDIFF(ch.created_at,now())) between 0 and 30
            ORDER BY ch.hit DESC 
            LIMIT 10 */
        if ($request['state_id'] != 0 && $request['district_id'] == 0 && $request['block_id'] == 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                    } else {
                        $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                    } else {
                        $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.state_id = $request->state_id GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(CQH.created_at,now())) between 0 and 30 and s.state_id = $request->state_id GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                } else {
                    $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where abs(DATEDIFF(CQH.created_at,now())) between 0 and 30 and s.state_id = $request->state_id GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                }
            }
        } elseif ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] == 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                    } else {
                        $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.state_id = $request->state_id and s.district_id = $request->district_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                    } else {
                        $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.state_id = $request->state_id and s.district_id = $request->district_id GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(CQH.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                } else {
                    $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where abs(DATEDIFF(CQH.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                }
            }
        } elseif ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] != 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id  and s.district_id = $request->district_id and s.block_id = $request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                    } else {
                        $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.state_id = $request->state_id  and s.district_id = $request->district_id and s.block_id = $request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                    } else {
                        $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where  s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(CQH.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                } else {
                    $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where abs(DATEDIFF(CQH.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                }
            }
        } else {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                } else {
                    $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
                }
            } else {
                $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where abs(DATEDIFF(CQH.created_at,now())) between 0 and 30 GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
            }
        }
        return ['status' => true, 'data' => $questionHitCount, 'code' => 200];
    }

    public function chatkeywordHits(Request $request)
    { //commented code in mails
        /* SELECT ck.title,count(ckh.keyword_id) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s 
                                    on s.id=ckh.subscriber_id 
                                    where  abs(DATEDIFF(ckh.created_at,now())) between 0 and 30 and s.state_id =21
                                    group by ck.title
                                    order by count(ck.hit) desc
                                    limit 10;
                                    
                                    
                                    
        SELECT ck.title,count(ckh.keyword_id) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s 
                                    on s.id=ckh.subscriber_id 
                                    where  s.state_id =21
                                    group by ck.title
                                    order by count(ck.hit) desc
                                    limit 10 */
        if ($request['state_id'] != 0 && $request['district_id'] == 0 && $request['block_id'] == 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by ckh.keyword_id order by  count(*) DESC limit 10");
                    } else {
                        $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id where s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by ckh.keyword_id order by  count(*) DESC limit 10");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s  on s.id=ckh.subscriber_id  where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id group by ckh.keyword_id order by  count(*) DESC limit 10");
                    } else {
                        $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s  on s.id=ckh.subscriber_id  where s.state_id = $request->state_id group by ckh.keyword_id order by  count(*) DESC limit 10");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s 
                                        on s.id=ckh.subscriber_id 
                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(ckh.created_at,now())) between 0 and 30  and s.state_id = $request->state_id
                                        group by ckh.keyword_id
                                        order by  count(*) DESC
                                        limit 10");
                } else {
                    $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s 
                                        on s.id=ckh.subscriber_id 
                                        where abs(DATEDIFF(ckh.created_at,now())) between 0 and 30  and s.state_id = $request->state_id
                                        group by ckh.keyword_id
                                        order by  count(*) DESC
                                        limit 10");
                }
            }
        } elseif ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] == 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and  s.state_id = $request->state_id and s.district_id = $request->district_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by ckh.keyword_id order by  count(*) DESC limit 10");
                    } else {
                        $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id where s.state_id = $request->state_id and s.district_id = $request->district_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by ckh.keyword_id order by  count(*) DESC limit 10");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s  on s.id=ckh.subscriber_id  where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id group by ckh.keyword_id order by  count(*) DESC limit 10");
                    } else {
                        $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s  on s.id=ckh.subscriber_id  where s.state_id = $request->state_id and s.district_id = $request->district_id group by ckh.keyword_id order by  count(*) DESC limit 10");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s 
                                        on s.id=ckh.subscriber_id 
                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(ckh.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id
                                        group by ckh.keyword_id
                                        order by  count(*) DESC
                                        limit 10");
                } else {
                    $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s 
                                        on s.id=ckh.subscriber_id 
                                        where abs(DATEDIFF(ckh.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id
                                        group by ckh.keyword_id
                                        order by  count(*) DESC
                                        limit 10");
                }
            }
        } elseif ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] != 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by ckh.keyword_id order by  count(*) DESC limit 10");
                    } else {
                        $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id where s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by ckh.keyword_id order by  count(*) DESC limit 10");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s  on s.id=ckh.subscriber_id  where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id group by ckh.keyword_id order by  count(*) DESC limit 10");
                    } else {
                        $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s  on s.id=ckh.subscriber_id  where s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id group by ckh.keyword_id order by  count(*) DESC limit 10");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s 
                                        on s.id=ckh.subscriber_id 
                                        where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(ckh.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                                        group by ckh.keyword_id
                                        order by  count(*) DESC
                                        limit 10");
                } else {
                    $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s 
                                        on s.id=ckh.subscriber_id 
                                        where abs(DATEDIFF(ckh.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                                        group by ckh.keyword_id
                                        order by  count(*) DESC
                                        limit 10");
                }
            }
        } else {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request->date);
                    $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by ckh.keyword_id order by  count(*) DESC limit 10");
                } else {
                    $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id group by ckh.keyword_id order by  count(*) DESC limit 10");
                }
            } else {
                $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s 
                                        on s.id=ckh.subscriber_id 
                                        where abs(DATEDIFF(ckh.created_at,now())) between 0 and 30
                                        group by ckh.keyword_id
                                        order by  count(*) DESC
                                        limit 10");
            }
        }
        return ['status' => True, 'data' => $keywordHit, 'code' => 200];
    }

    public function userFeedback(Request $request)
    { //commented code in mails
        /* SELECT fq.feedback_question, ROUND(avg(ratings), 2) as avg
                FROM `user_feedback_history` fh
                join user_feedback_questions fq on fq.id = fh.feedback_id
                join subscribers s on s.id = fh.subscriber_id
                where fq.feedback_question like '%User Interface%' or fq.feedback_question like '%Module Content%' or fq.feedback_question like '%Chatbot%' and skip = 0
                group by feedback_id
                ORDER by feedback_id
        */

        if (\Auth::user()->roles[0]['id'] == 10) {

            $userFeedback = DB::select("SELECT fq.feedback_question, ROUND(avg(ratings), 2)  as avg
                   FROM `user_feedback_history` fh
                    join user_feedback_questions fq on fq.id = fh.feedback_id
                    join subscribers s on s.id = fh.subscriber_id
                    where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and skip = 0 and (feedback_id =1 or feedback_id=2 or feedback_id=3)
                    group by feedback_id
                    ORDER by feedback_id");
        } else {
            $userFeedback = DB::select("SELECT fq.feedback_question, ROUND(avg(ratings), 2)  as avg
                   FROM `user_feedback_history` fh
                    join user_feedback_questions fq on fq.id = fh.feedback_id
                    join subscribers s on s.id = fh.subscriber_id
                    where skip = 0 and (feedback_id =1 or feedback_id=2 or feedback_id=3)
                    group by feedback_id
                    ORDER by feedback_id");
        }


        return ['status' => True, 'data' => $userFeedback, 'code' => 200];
    }

    public function assessmentSubmission(Request $request)
    { //commented code in mails

        if ($request['state_id'] != 0 && $request['district_id'] == 0 && $request['block_id'] == 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request['date']);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                                on s.id=ua.user_id
                                                where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    } else {
                        $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                                on s.id=ua.user_id
                                                where s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                                on s.id=ua.user_id
                                                where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id
                                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    } else {
                        $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                                on s.id=ua.user_id
                                                where s.state_id = $request->state_id
                                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $assessmentGraph = DB::select("select FROM_DAYS( TO_DAYS(ua.created_at) - MOD(TO_DAYS(ua.created_at) -2,7)) AS week, COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                            on s.id=ua.user_id
                                            where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(ua.created_at,now())) between 0 and 30 and s.state_id = $request->state_id
                                            GROUP BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) 
                                            ORDER BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) 
                                            limit 4");
                } else {
                    $assessmentGraph = DB::select("select FROM_DAYS( TO_DAYS(ua.created_at) - MOD(TO_DAYS(ua.created_at) -2,7)) AS week, COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                            on s.id=ua.user_id
                                            where abs(DATEDIFF(ua.created_at,now())) between 0 and 30 and s.state_id = $request->state_id
                                            GROUP BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) 
                                            ORDER BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) 
                                            limit 4");
                }
            }
        } elseif ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] == 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request['date']);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                                on s.id=ua.user_id
                                                where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    } else {
                        $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                                on s.id=ua.user_id
                                                where s.state_id = $request->state_id and s.district_id = $request->district_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                                on s.id=ua.user_id
                                                where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id
                                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    } else {
                        $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                                on s.id=ua.user_id
                                                where s.state_id = $request->state_id and s.district_id = $request->district_id
                                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $assessmentGraph = DB::select("select FROM_DAYS( TO_DAYS(ua.created_at) - MOD(TO_DAYS(ua.created_at) -2,7)) AS week, COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                            on s.id=ua.user_id
                                            where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(ua.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id
                                            GROUP BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) 
                                            ORDER BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) 
                                            limit 4");
                } else {
                    $assessmentGraph = DB::select("select FROM_DAYS( TO_DAYS(ua.created_at) - MOD(TO_DAYS(ua.created_at) -2,7)) AS week, COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                            on s.id=ua.user_id
                                            where abs(DATEDIFF(ua.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id
                                            GROUP BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) 
                                            ORDER BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) 
                                            limit 4");
                }
            }
        } elseif ($request['state_id'] != 0 && $request['district_id'] != 0 && $request['block_id'] != 0) {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request['date']);
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                                on s.id=ua.user_id
                                                where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    } else {
                        $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                                on s.id=ua.user_id
                                                where s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    }
                } else {
                    if (\Auth::user()->roles[0]['id'] == 10) {
                        $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                                on s.id=ua.user_id
                                                where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    } else {
                        $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                                on s.id=ua.user_id
                                                where s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                    }
                }
            } else {
                if (\Auth::user()->roles[0]['id'] == 10) {
                    $assessmentGraph = DB::select("select FROM_DAYS( TO_DAYS(ua.created_at) - MOD(TO_DAYS(ua.created_at) -2,7)) AS week, COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                            on s.id=ua.user_id
                                            where s.cadre_id in (107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and abs(DATEDIFF(ua.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                                            GROUP BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) 
                                            ORDER BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) 
                                            limit 4");
                } else {
                    $assessmentGraph = DB::select("select FROM_DAYS( TO_DAYS(ua.created_at) - MOD(TO_DAYS(ua.created_at) -2,7)) AS week, COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                            on s.id=ua.user_id
                                            where abs(DATEDIFF(ua.created_at,now())) between 0 and 30 and s.state_id = $request->state_id and s.district_id = $request->district_id and s.block_id = $request->block_id
                                            GROUP BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) 
                                            ORDER BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) 
                                            limit 4");
                }
            }
        } else {
            if ($request->type == "overall") {
                if ($request->has('date') && $request['date'] > 0) {
                    $values = explode(" ", $request['date']);
                    $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                                on s.id=ua.user_id
                                                where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                } else {
                    $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                                on s.id=ua.user_id
                                                GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
                                                ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");
                }
            } else {
                $assessmentGraph = DB::select("select FROM_DAYS( TO_DAYS(ua.created_at) - MOD(TO_DAYS(ua.created_at) -2,7)) AS week, COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
                                            on s.id=ua.user_id
                                            where abs(DATEDIFF(ua.created_at,now())) between 0 and 30
                                            GROUP BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) 
                                            ORDER BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) 
                                            limit 4");
            }
        }
        return ['status' => True, 'data' => $assessmentGraph, 'code' => 200];
    }

    public function appOpenedCount(Request $request)
    { //commented code in mails
        // ($request->all());
        /* 
            select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
            join subscribers s on s.id = sa.user_id
            where action = "user_home_page_visit" and sa.created_at >= DATE_ADD(NOW(),INTERVAL -3 MONTH)
            GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
            having count(*) >= 3 and count(*) < 5
        */

        /* select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
            join subscribers s on s.id = sa.user_id
            where action = "user_home_page_visit" and sa.created_at >= DATE_ADD(NOW(),INTERVAL -3 MONTH)
            GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
            having count(*) >= 5 and count(*) < 7 
        */

        /* select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
            join subscribers s on s.id = sa.user_id
            where action = "user_home_page_visit" and sa.created_at >= DATE_ADD(NOW(),INTERVAL -3 MONTH)
            GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
            having count(*) >= 7 and count(*) < 9
        */

        /* select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
            join subscribers s on s.id = sa.user_id
            where action = "user_home_page_visit" and sa.created_at >= DATE_ADD(NOW(),INTERVAL -3 MONTH)
            GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
            having count(*) >= 10
        */
        $curr_start_of_week = Carbon::now()->startOfWeek();
        $day_7_before = Carbon::now()->startOfWeek()->subDays(7);
        $day_14_before = Carbon::now()->startOfWeek()->subDays(14);
        $day_21_before = Carbon::now()->startOfWeek()->subDays(21);

        if ($request['type'] == "monthly") {
            $last_4_month_3_to_5 = DB::select("select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
                                join subscribers s on s.id = sa.user_id
                                where action = 'user_home_page_visit' and sa.created_at >= DATE_ADD(NOW(),INTERVAL -3 MONTH)
                                GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
                                having count(*) >= 3 and count(*) < 5");
            $last_4_month_5_to_7 = DB::select("select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
                                join subscribers s on s.id = sa.user_id
                                where action = 'user_home_page_visit' and sa.created_at >= DATE_ADD(NOW(),INTERVAL -3 MONTH)
                                GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
                                having count(*) >= 5 and count(*) < 7 ");
            $last_4_month_7_to_9 = DB::select("select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
                                join subscribers s on s.id = sa.user_id
                                where action = 'user_home_page_visit' and sa.created_at >= DATE_ADD(NOW(),INTERVAL -3 MONTH)
                                GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
                                having count(*) >= 7 and count(*) < 9");
            $last_4_month_10 = DB::select("select SQL_NO_CACHE YEAR(sa.created_at), concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)) as date,MONTH(sa.created_at), COUNT(*) as subscriber_count FROM subscriber_activities sa
                                join subscribers s on s.id = sa.user_id
                                where action = 'user_home_page_visit' and sa.created_at >= DATE_ADD(NOW(),INTERVAL -3 MONTH)
                                GROUP BY YEAR(sa.created_at),concat(LEFT(MONTHNAME(sa.created_at),3),'-' ,RIGHT(YEAR(sa.created_at),2)),MONTH(sa.created_at)
                                having count(*) >= 10");
            $current_month = Carbon::now()->startOfMonth();
            $before_1_month = Carbon::now()->startOfMonth()->subMonth(1);
            $before_2_month = Carbon::now()->startOfMonth()->subMonth(2);
            $before_3_month = Carbon::now()->startOfMonth()->subMonth(3);


            $month_3_to_5 = collect();
            $month_5_to_7 = collect();
            $month_7_to_9 = collect();
            $month_10 = collect();
            $month_3_to_5->push(['month' => date('M-y', strtotime($current_month)), 'count' => 0, 'type' => '>3-5']);
            $month_3_to_5->push(['month' => date('M-y', strtotime($before_1_month)), 'count' => 0, 'type' => '>3-5']);
            $month_3_to_5->push(['month' => date('M-y', strtotime($before_2_month)), 'count' => 0, 'type' => '>3-5']);
            $month_3_to_5->push(['month' => date('M-y', strtotime($before_3_month)), 'count' => 0, 'type' => '>3-5']);

            $month_5_to_7->push(['month' => date('M-y', strtotime($current_month)), 'count' => 0, 'type' => '>5-7']);
            $month_5_to_7->push(['month' => date('M-y', strtotime($before_1_month)), 'count' => 0, 'type' => '>5-7']);
            $month_5_to_7->push(['month' => date('M-y', strtotime($before_2_month)), 'count' => 0, 'type' => '>5-7']);
            $month_5_to_7->push(['month' => date('M-y', strtotime($before_3_month)), 'count' => 0, 'type' => '>5-7']);

            $month_7_to_9->push(['month' => date('M-y', strtotime($current_month)), 'count' => 0, 'type' => '>7-9']);
            $month_7_to_9->push(['month' => date('M-y', strtotime($before_1_month)), 'count' => 0, 'type' => '>7-9']);
            $month_7_to_9->push(['month' => date('M-y', strtotime($before_2_month)), 'count' => 0, 'type' => '>7-9']);
            $month_7_to_9->push(['month' => date('M-y', strtotime($before_3_month)), 'count' => 0, 'type' => '>7-9']);

            $month_10->push(['month' => date('M-y', strtotime($current_month)), 'count' => 0, 'type' => '>10']);
            $month_10->push(['month' => date('M-y', strtotime($before_1_month)), 'count' => 0, 'type' => '>10']);
            $month_10->push(['month' => date('M-y', strtotime($before_2_month)), 'count' => 0, 'type' => '>10']);
            $month_10->push(['month' => date('M-y', strtotime($before_3_month)), 'count' => 0, 'type' => '>10']);


            foreach ($last_4_month_3_to_5 as $week) {
                $month_3_to_5 = $month_3_to_5->map(function ($item) use ($week) {
                    if ($item['month'] == $week->date) {
                        $item['count'] = $week->subscriber_count;
                    }
                    return $item;
                });
            }
            foreach ($last_4_month_5_to_7 as $week) {
                $month_5_to_7 = $month_5_to_7->map(function ($item) use ($week) {
                    if ($item['month'] == $week->date) {
                        $item['count'] = $week->subscriber_count;
                    }
                    return $item;
                });
            }
            foreach ($last_4_month_7_to_9 as $week) {
                $month_7_to_9 = $month_7_to_9->map(function ($item) use ($week) {
                    if ($item['month'] == $week->date) {
                        $item['count'] = $week->subscriber_count;
                    }
                    return $item;
                });
            }
            foreach ($last_4_month_10 as $week) {
                $month_10 = $month_10->map(function ($item) use ($week) {
                    if ($item['month'] == $week->date) {
                        $item['count'] = $week->subscriber_count;
                    }
                    return $item;
                });
            }
            return ['status' => true, 'data' => ['>3-5' => $month_3_to_5, '>5-7' => $month_5_to_7, '>7-9' => $month_7_to_9, '>10' => $month_10], 'code' => 200];
        } else {
            $last_4_week_3_to_5 = DB::select("SELECT FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) AS WEEK, COUNT(1) as count_data
                        FROM
                            subscriber_activities
                        WHERE ACTION= 'user_home_page_visit'
                        GROUP BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2, 7))
                        HAVING COUNT(1) >= 3 AND COUNT(1) < 5
                        ORDER BY WEEK DESC 
                        LIMIT 4");
            $last_4_week_5_to_7 = DB::select("SELECT FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) AS WEEK, COUNT(1) as count_data
                        FROM
                            subscriber_activities
                        WHERE ACTION= 'user_home_page_visit'
                        GROUP BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2, 7))
                        HAVING COUNT(1) >= 5 AND COUNT(1) < 7
                        ORDER BY WEEK DESC 
                        LIMIT 4");
            $last_4_week_7_to_9 = DB::select("SELECT FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) AS WEEK, COUNT(1) as count_data
                        FROM
                            subscriber_activities
                        WHERE ACTION= 'user_home_page_visit'
                        GROUP BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2, 7))
                        HAVING COUNT(1) >= 7 AND COUNT(1) < 9
                        ORDER BY WEEK DESC 
                        LIMIT 4");
            $last_4_week_10 = DB::select("SELECT FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2,7)) AS WEEK, COUNT(1) as count_data
                        FROM
                            subscriber_activities
                        WHERE ACTION= 'user_home_page_visit'
                        GROUP BY FROM_DAYS( TO_DAYS(created_at) - MOD(TO_DAYS(created_at) -2, 7))
                        HAVING COUNT(1) >= 10
                        ORDER BY WEEK DESC 
                        LIMIT 4");


            $date_3_to_5 = collect();
            $date_5_to_7 = collect();
            $date_7_to_9 = collect();
            $date_10 = collect();
            $date_3_to_5->push(['week' => date('Y-m-d', strtotime($curr_start_of_week)), 'count' => 0, 'type' => '>3-5']);
            $date_3_to_5->push(['week' => date('Y-m-d', strtotime($day_7_before)), 'count' => 0, 'type' => '>3-5']);
            $date_3_to_5->push(['week' => date('Y-m-d', strtotime($day_14_before)), 'count' => 0, 'type' => '>3-5']);
            $date_3_to_5->push(['week' => date('Y-m-d', strtotime($day_21_before)), 'count' => 0, 'type' => '>3-5']);

            $date_5_to_7->push(['week' => date('Y-m-d', strtotime($curr_start_of_week)), 'count' => 0, 'type' => '>5-7']);
            $date_5_to_7->push(['week' => date('Y-m-d', strtotime($day_7_before)), 'count' => 0, 'type' => '>5-7']);
            $date_5_to_7->push(['week' => date('Y-m-d', strtotime($day_14_before)), 'count' => 0, 'type' => '>5-7']);
            $date_5_to_7->push(['week' => date('Y-m-d', strtotime($day_21_before)), 'count' => 0, 'type' => '>5-7']);

            $date_7_to_9->push(['week' => date('Y-m-d', strtotime($curr_start_of_week)), 'count' => 0, 'type' => '>7-9']);
            $date_7_to_9->push(['week' => date('Y-m-d', strtotime($day_7_before)), 'count' => 0, 'type' => '>7-9']);
            $date_7_to_9->push(['week' => date('Y-m-d', strtotime($day_14_before)), 'count' => 0, 'type' => '>7-9']);
            $date_7_to_9->push(['week' => date('Y-m-d', strtotime($day_21_before)), 'count' => 0, 'type' => '>7-9']);

            $date_10->push(['week' => date('Y-m-d', strtotime($curr_start_of_week)), 'count' => 0, 'type' => '>10']);
            $date_10->push(['week' => date('Y-m-d', strtotime($day_7_before)), 'count' => 0, 'type' => '>10']);
            $date_10->push(['week' => date('Y-m-d', strtotime($day_14_before)), 'count' => 0, 'type' => '>10']);
            $date_10->push(['week' => date('Y-m-d', strtotime($day_21_before)), 'count' => 0, 'type' => '>10']);

            foreach ($last_4_week_3_to_5 as $week) {
                $date_3_to_5 = $date_3_to_5->map(function ($item) use ($week) {
                    if ($item['week'] == $week->WEEK) {
                        $item['count'] = $week->count_data;
                    }
                    return $item;
                });
            }
            foreach ($last_4_week_5_to_7 as $week) {
                $date_5_to_7 = $date_5_to_7->map(function ($item) use ($week) {
                    if ($item['week'] == $week->WEEK) {
                        $item['count'] = $week->count_data;
                    }
                    return $item;
                });
            }
            foreach ($last_4_week_7_to_9 as $week) {
                $date_7_to_9 = $date_7_to_9->map(function ($item) use ($week) {
                    if ($item['week'] == $week->WEEK) {
                        $item['count'] = $week->count_data;
                    }
                    return $item;
                });
            }
            foreach ($last_4_week_10 as $week) {
                $date_10 = $date_10->map(function ($item) use ($week) {
                    if ($item['week'] == $week->WEEK) {
                        $item['count'] = $week->count_data;
                    }
                    return $item;
                });
            }
            return ['status' => true, 'data' => ['>3-5' => $date_3_to_5, '>5-7' => $date_5_to_7, '>7-9' => $date_7_to_9, '>10' => $date_10], 'code' => 200];
        }
    }

    public function appOpenedCountWeek(Request $request) //commented code in mails
    {

        $curr_start_of_week = Carbon::now()->startOfWeek();
        Log::info($curr_start_of_week);
        $day_7_before = Carbon::now()->startOfWeek()->subDays(7);
        Log::info($day_7_before);
        $day_14_before = Carbon::now()->startOfWeek()->subDays(14);
        Log::info($day_14_before);
        $day_21_before = Carbon::now()->startOfWeek()->subDays(21);
        Log::info($day_21_before);
        $day_28_before = Carbon::now()->startOfWeek()->subDays(28);
        Log::info($day_28_before);
        if ($request['type'] == "monthly") {
            $current_month = Carbon::now()->startOfMonth();
            $before_1_month = Carbon::now()->startOfMonth()->subMonth(1);
            $before_2_month = Carbon::now()->startOfMonth()->subMonth(2);
            $before_3_month = Carbon::now()->startOfMonth()->subMonth(3);
            $before_4_month = Carbon::now()->startOfMonth()->subMonth(4);

            if (\Auth::user()->roles[0]['id'] == 10) {
                $last_4_month_3_to_5_week_1 = DB::select(" select concat((DATE_FORMAT('$before_4_month','%m-%y')),'/',DATE_FORMAT('$before_3_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_4_month' and '$before_3_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 4) as a");

                $last_4_month_3_to_5_week_2 = DB::select("select concat((DATE_FORMAT('$before_3_month','%m-%y')),'/',DATE_FORMAT('$before_2_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_3_month' and '$before_2_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 4) as a");

                $last_4_month_3_to_5_week_3 = DB::select("select concat((DATE_FORMAT('$before_2_month','%m-%y')),'/',DATE_FORMAT('$before_1_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_2_month' and '$before_1_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 4) as a");

                $last_4_month_3_to_5_week_4 = DB::select("select concat((DATE_FORMAT('$before_1_month','%m-%y')),'/',DATE_FORMAT('$curr_start_of_week','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_1_month' and '$current_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 4) as a");

                /* For 5 to 7 count ----------------------------------------------------------------------------------------------------*/
                $last_4_month_5_to_7_week_1 = DB::select("select concat((DATE_FORMAT('$before_4_month','%m-%y')),'/',DATE_FORMAT('$before_3_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_4_month' and '$before_3_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                $last_4_month_5_to_7_week_2 = DB::select("select concat((DATE_FORMAT('$before_3_month','%m-%y')),'/',DATE_FORMAT('$before_2_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_3_month' and '$before_2_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                $last_4_month_5_to_7_week_3 = DB::select("select concat((DATE_FORMAT('$before_2_month','%m-%y')),'/',DATE_FORMAT('$before_1_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_2_month' and '$before_1_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                $last_4_month_5_to_7_week_4 = DB::select("select concat((DATE_FORMAT('$before_1_month','%m-%y')),'/',DATE_FORMAT('$curr_start_of_week','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_1_month' and '$current_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                /* 7 to 9 count -------------------------------------------------------------------------- */
                $last_4_month_7_to_9_week_1 = DB::select("select concat((DATE_FORMAT('$before_4_month','%m-%y')),'/',DATE_FORMAT('$before_3_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_4_month' and '$before_3_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                $last_4_month_7_to_9_week_2 = DB::select("select concat((DATE_FORMAT('$before_3_month','%m-%y')),'/',DATE_FORMAT('$before_2_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_3_month' and '$before_2_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                $last_4_month_7_to_9_week_3 = DB::select("select concat((DATE_FORMAT('$before_2_month','%m-%y')),'/',DATE_FORMAT('$before_1_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_2_month' and '$before_1_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                $last_4_month_7_to_9_week_4 = DB::select("select concat((DATE_FORMAT('$before_1_month','%m-%y')),'/',DATE_FORMAT('$curr_start_of_week','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_1_month' and '$current_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                /* > 10 count -------------------------------------------------------------------------------*/
                $last_4_month_10_week_1 = DB::select(" select concat((DATE_FORMAT('$before_4_month','%m-%y')),'/',DATE_FORMAT('$before_3_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_4_month' and '$before_3_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");

                $last_4_month_10_week_2 = DB::select("select concat((DATE_FORMAT('$before_3_month','%m-%y')),'/',DATE_FORMAT('$before_2_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_3_month' and '$before_2_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");

                $last_4_month_10_week_3 = DB::select(" select concat((DATE_FORMAT('$before_2_month','%m-%y')),'/',DATE_FORMAT('$before_1_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_2_month' and '$before_1_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");

                $last_4_month_10_week_4 = DB::select("select concat((DATE_FORMAT('$before_1_month','%m-%y')),'/',DATE_FORMAT('$curr_start_of_week','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id = sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$before_1_month' and '$current_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");
            } else {
                $last_4_month_3_to_5_week_1 = DB::select(" select concat((DATE_FORMAT('$before_4_month','%m-%y')),'/',DATE_FORMAT('$before_3_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_4_month' and '$before_3_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 4) as a");

                $last_4_month_3_to_5_week_2 = DB::select("select concat((DATE_FORMAT('$before_3_month','%m-%y')),'/',DATE_FORMAT('$before_2_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_3_month' and '$before_2_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 4) as a");

                $last_4_month_3_to_5_week_3 = DB::select("select concat((DATE_FORMAT('$before_2_month','%m-%y')),'/',DATE_FORMAT('$before_1_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_2_month' and '$before_1_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 4) as a");

                $last_4_month_3_to_5_week_4 = DB::select("select concat((DATE_FORMAT('$before_1_month','%m-%y')),'/',DATE_FORMAT('$curr_start_of_week','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_1_month' and '$current_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 4) as a");

                /* For 5 to 7 count ----------------------------------------------------------------------------------------------------*/
                $last_4_month_5_to_7_week_1 = DB::select("select concat((DATE_FORMAT('$before_4_month','%m-%y')),'/',DATE_FORMAT('$before_3_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_4_month' and '$before_3_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                $last_4_month_5_to_7_week_2 = DB::select("select concat((DATE_FORMAT('$before_3_month','%m-%y')),'/',DATE_FORMAT('$before_2_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_3_month' and '$before_2_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                $last_4_month_5_to_7_week_3 = DB::select("select concat((DATE_FORMAT('$before_2_month','%m-%y')),'/',DATE_FORMAT('$before_1_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_2_month' and '$before_1_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                $last_4_month_5_to_7_week_4 = DB::select("select concat((DATE_FORMAT('$before_1_month','%m-%y')),'/',DATE_FORMAT('$curr_start_of_week','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_1_month' and '$current_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                /* 7 to 9 count -------------------------------------------------------------------------- */
                $last_4_month_7_to_9_week_1 = DB::select("select concat((DATE_FORMAT('$before_4_month','%m-%y')),'/',DATE_FORMAT('$before_3_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_4_month' and '$before_3_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                $last_4_month_7_to_9_week_2 = DB::select("select concat((DATE_FORMAT('$before_3_month','%m-%y')),'/',DATE_FORMAT('$before_2_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_3_month' and '$before_2_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                $last_4_month_7_to_9_week_3 = DB::select("select concat((DATE_FORMAT('$before_2_month','%m-%y')),'/',DATE_FORMAT('$before_1_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_2_month' and '$before_1_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                $last_4_month_7_to_9_week_4 = DB::select("select concat((DATE_FORMAT('$before_1_month','%m-%y')),'/',DATE_FORMAT('$curr_start_of_week','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_1_month' and '$current_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                /* > 10 count -------------------------------------------------------------------------------*/
                $last_4_month_10_week_1 = DB::select(" select concat((DATE_FORMAT('$before_4_month','%m-%y')),'/',DATE_FORMAT('$before_3_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_4_month' and '$before_3_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");

                $last_4_month_10_week_2 = DB::select("select concat((DATE_FORMAT('$before_3_month','%m-%y')),'/',DATE_FORMAT('$before_2_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_3_month' and '$before_2_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");

                $last_4_month_10_week_3 = DB::select(" select concat((DATE_FORMAT('$before_2_month','%m-%y')),'/',DATE_FORMAT('$before_1_month','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_2_month' and '$before_1_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");

                $last_4_month_10_week_4 = DB::select("select concat((DATE_FORMAT('$before_1_month','%m-%y')),'/',DATE_FORMAT('$curr_start_of_week','%m-%y')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$before_1_month' and '$current_month'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");
            }

            $date_3_to_5 = collect();
            $date_5_to_7 = collect();
            $date_7_to_9 = collect();
            $date_10 = collect();

            $date_3_to_5->push(['month' => $last_4_month_3_to_5_week_1[0]->date, 'count' => $last_4_month_3_to_5_week_1[0]->count, 'type' => '>3-5']);
            $date_3_to_5->push(['month' => $last_4_month_3_to_5_week_2[0]->date, 'count' => $last_4_month_3_to_5_week_2[0]->count, 'type' => '>3-5']);
            $date_3_to_5->push(['month' => $last_4_month_3_to_5_week_3[0]->date, 'count' => $last_4_month_3_to_5_week_3[0]->count, 'type' => '>3-5']);
            $date_3_to_5->push(['month' => $last_4_month_3_to_5_week_4[0]->date, 'count' => $last_4_month_3_to_5_week_4[0]->count, 'type' => '>3-5']);
            $date_5_to_7->push(['month' => $last_4_month_5_to_7_week_1[0]->date, 'count' => $last_4_month_5_to_7_week_1[0]->count, 'type' => '>5-7']);
            $date_5_to_7->push(['month' => $last_4_month_5_to_7_week_2[0]->date, 'count' => $last_4_month_5_to_7_week_2[0]->count, 'type' => '>5-7']);
            $date_5_to_7->push(['month' => $last_4_month_5_to_7_week_3[0]->date, 'count' => $last_4_month_5_to_7_week_3[0]->count, 'type' => '>5-7']);
            $date_5_to_7->push(['month' => $last_4_month_5_to_7_week_4[0]->date, 'count' => $last_4_month_5_to_7_week_4[0]->count, 'type' => '>5-7']);

            $date_7_to_9->push(['month' => $last_4_month_7_to_9_week_1[0]->date, 'count' => $last_4_month_7_to_9_week_1[0]->count, 'type' => '>7-9']);
            $date_7_to_9->push(['month' => $last_4_month_7_to_9_week_2[0]->date, 'count' => $last_4_month_7_to_9_week_2[0]->count, 'type' => '>7-9']);
            $date_7_to_9->push(['month' => $last_4_month_7_to_9_week_3[0]->date, 'count' => $last_4_month_7_to_9_week_3[0]->count, 'type' => '>7-9']);
            $date_7_to_9->push(['month' => $last_4_month_7_to_9_week_4[0]->date, 'count' => $last_4_month_7_to_9_week_4[0]->count, 'type' => '>7-9']);

            $date_10->push(['month' => $last_4_month_10_week_1[0]->date, 'count' => $last_4_month_10_week_1[0]->count, 'type' => '>10']);
            $date_10->push(['month' => $last_4_month_10_week_2[0]->date, 'count' => $last_4_month_10_week_2[0]->count, 'type' => '>10']);
            $date_10->push(['month' => $last_4_month_10_week_3[0]->date, 'count' => $last_4_month_10_week_3[0]->count, 'type' => '>10']);
            $date_10->push(['month' => $last_4_month_10_week_4[0]->date, 'count' => $last_4_month_10_week_4[0]->count, 'type' => '>10']);
            // return ['status' => true,'data' => ['>3-5' => $month_3_to_5,'>5-7' => $month_5_to_7,'>7-9' => $month_7_to_9,'>10' => $month_10],'code' => 200];
        } else {
            if (\Auth::user()->roles[0]['id'] == 10) {
                $last_4_week_3_to_5_week_1 = DB::select(" select concat((DATE_FORMAT('$day_28_before','%m-%d')),'/',DATE_FORMAT('$day_21_before','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_28_before' and '$day_21_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 5) as a");

                $last_4_week_3_to_5_week_2 = DB::select("select concat((DATE_FORMAT('$day_21_before','%m-%d')),'/',DATE_FORMAT('$day_14_before','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_21_before' and '$day_14_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 5) as a");

                $last_4_week_3_to_5_week_3 = DB::select("select concat((DATE_FORMAT('$day_14_before','%m-%d')),'/',DATE_FORMAT('$day_7_before','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_14_before' and '$day_7_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 5) as a");

                $last_4_week_3_to_5_week_4 = DB::select("select concat((DATE_FORMAT('$day_7_before','%m-%d')),'/',DATE_FORMAT('$curr_start_of_week','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_7_before' and '$curr_start_of_week'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 5) as a");

                /* For 5 to 7 count ----------------------------------------------------------------------------------------------------*/
                $last_4_week_5_to_7_week_1 = DB::select("select concat((DATE_FORMAT('$day_28_before','%m-%d')),'/',DATE_FORMAT('$day_21_before','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_28_before' and '$day_21_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                $last_4_week_5_to_7_week_2 = DB::select(" select concat((DATE_FORMAT('$day_21_before','%m-%d')),'/',DATE_FORMAT('$day_14_before','%m-%d')) as date,count(1) as count from(select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_21_before' and '$day_14_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                $last_4_week_5_to_7_week_3 = DB::select("select concat((DATE_FORMAT('$day_14_before','%m-%d')),'/',DATE_FORMAT('$day_7_before','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_14_before' and '$day_7_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                $last_4_week_5_to_7_week_4 = DB::select("select concat((DATE_FORMAT('$day_7_before','%m-%d')),'/',DATE_FORMAT('$curr_start_of_week','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_7_before' and '$curr_start_of_week'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                /* 7 to 9 count -------------------------------------------------------------------------- */
                $last_4_week_7_to_9_week_1 = DB::select("select concat((DATE_FORMAT('$day_28_before','%m-%d')),'/',DATE_FORMAT('$day_21_before','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_28_before' and '$day_21_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                $last_4_week_7_to_9_week_2 = DB::select("select concat((DATE_FORMAT('$day_21_before','%m-%d')),'/',DATE_FORMAT('$day_14_before','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_21_before' and '$day_14_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                $last_4_week_7_to_9_week_3 = DB::select(" select concat((DATE_FORMAT('$day_14_before','%m-%d')),'/',DATE_FORMAT('$day_7_before','%m-%d')) as date,count(1) as count from(select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_14_before' and '$day_7_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                $last_4_week_7_to_9_week_4 = DB::select("select concat((DATE_FORMAT('$day_7_before','%m-%d')),'/',DATE_FORMAT('$curr_start_of_week','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_7_before' and '$curr_start_of_week'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                /* > 10 count -------------------------------------------------------------------------------*/
                $last_4_week_10_week_1 = DB::select(" select concat((DATE_FORMAT('$day_28_before','%m-%d')),'/',DATE_FORMAT('$day_21_before','%m-%d')) as date,count(1) as count from(select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_28_before' and '$day_21_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");

                $last_4_week_10_week_2 = DB::select("select concat((DATE_FORMAT('$day_21_before','%m-%d')),'/',DATE_FORMAT('$day_14_before','%m-%d')) as date,count(1) as count from(select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_21_before' and '$day_14_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");

                $last_4_week_10_week_3 = DB::select(" select concat((DATE_FORMAT('$day_14_before','%m-%d')),'/',DATE_FORMAT('$day_7_before','%m-%d')) as date,count(1) as count from(select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_14_before' and '$day_7_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");

                $last_4_week_10_week_4 = DB::select("select concat((DATE_FORMAT('$day_7_before','%m-%d')),'/',DATE_FORMAT('$curr_start_of_week','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities sa
                    join subscribers s on s.id=sa.user_id
                    where s.cadre_id in(107,106,105,104,103,102,101,100,99,98,97,96,95,94,93,92,91,90,89,88,57,17) and sa.created_at BETWEEN '$day_7_before' and '$curr_start_of_week'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");
            } else {
                $last_4_week_3_to_5_week_1 = DB::select(" select concat((DATE_FORMAT('$day_28_before','%m-%d')),'/',DATE_FORMAT('$day_21_before','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_28_before' and '$day_21_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 5) as a");

                $last_4_week_3_to_5_week_2 = DB::select("select concat((DATE_FORMAT('$day_21_before','%m-%d')),'/',DATE_FORMAT('$day_14_before','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_21_before' and '$day_14_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 5) as a");

                $last_4_week_3_to_5_week_3 = DB::select("select concat((DATE_FORMAT('$day_14_before','%m-%d')),'/',DATE_FORMAT('$day_7_before','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_14_before' and '$day_7_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 5) as a");

                $last_4_week_3_to_5_week_4 = DB::select("select concat((DATE_FORMAT('$day_7_before','%m-%d')),'/',DATE_FORMAT('$curr_start_of_week','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_7_before' and '$curr_start_of_week'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 3 AND 5) as a");

                /* For 5 to 7 count ----------------------------------------------------------------------------------------------------*/
                $last_4_week_5_to_7_week_1 = DB::select("select concat((DATE_FORMAT('$day_28_before','%m-%d')),'/',DATE_FORMAT('$day_21_before','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_28_before' and '$day_21_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                $last_4_week_5_to_7_week_2 = DB::select(" select concat((DATE_FORMAT('$day_21_before','%m-%d')),'/',DATE_FORMAT('$day_14_before','%m-%d')) as date,count(1) as count from(select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_21_before' and '$day_14_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                $last_4_week_5_to_7_week_3 = DB::select("select concat((DATE_FORMAT('$day_14_before','%m-%d')),'/',DATE_FORMAT('$day_7_before','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_14_before' and '$day_7_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                $last_4_week_5_to_7_week_4 = DB::select("select concat((DATE_FORMAT('$day_7_before','%m-%d')),'/',DATE_FORMAT('$curr_start_of_week','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_7_before' and '$curr_start_of_week'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 5 AND 7) as a");

                /* 7 to 9 count -------------------------------------------------------------------------- */
                $last_4_week_7_to_9_week_1 = DB::select("select concat((DATE_FORMAT('$day_28_before','%m-%d')),'/',DATE_FORMAT('$day_21_before','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_28_before' and '$day_21_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                $last_4_week_7_to_9_week_2 = DB::select("select concat((DATE_FORMAT('$day_21_before','%m-%d')),'/',DATE_FORMAT('$day_14_before','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_21_before' and '$day_14_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                $last_4_week_7_to_9_week_3 = DB::select(" select concat((DATE_FORMAT('$day_14_before','%m-%d')),'/',DATE_FORMAT('$day_7_before','%m-%d')) as date,count(1) as count from(select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_14_before' and '$day_7_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                $last_4_week_7_to_9_week_4 = DB::select("select concat((DATE_FORMAT('$day_7_before','%m-%d')),'/',DATE_FORMAT('$curr_start_of_week','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_7_before' and '$curr_start_of_week'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) BETWEEN 7 AND 9) as a");

                /* > 10 count -------------------------------------------------------------------------------*/
                $last_4_week_10_week_1 = DB::select(" select concat((DATE_FORMAT('$day_28_before','%m-%d')),'/',DATE_FORMAT('$day_21_before','%m-%d')) as date,count(1) as count from(select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_28_before' and '$day_21_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");

                $last_4_week_10_week_2 = DB::select("select concat((DATE_FORMAT('$day_21_before','%m-%d')),'/',DATE_FORMAT('$day_14_before','%m-%d')) as date,count(1) as count from(select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_21_before' and '$day_14_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");

                $last_4_week_10_week_3 = DB::select(" select concat((DATE_FORMAT('$day_14_before','%m-%d')),'/',DATE_FORMAT('$day_7_before','%m-%d')) as date,count(1) as count from(select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_14_before' and '$day_7_before'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");

                $last_4_week_10_week_4 = DB::select("select concat((DATE_FORMAT('$day_7_before','%m-%d')),'/',DATE_FORMAT('$curr_start_of_week','%m-%d')) as date,count(1) as count from( select  count(1) from subscriber_activities
                    where created_at BETWEEN '$day_7_before' and '$curr_start_of_week'
                    and action = 'user_home_page_visit'
                    GROUP by user_id
                    having count(1) >= 10) as a");
            }
            $date_3_to_5 = collect();
            $date_5_to_7 = collect();
            $date_7_to_9 = collect();
            $date_10 = collect();

            $date_3_to_5->push(['week' => $last_4_week_3_to_5_week_1[0]->date, 'count' => $last_4_week_3_to_5_week_1[0]->count, 'type' => '>3-5']);
            $date_3_to_5->push(['week' => $last_4_week_3_to_5_week_2[0]->date, 'count' => $last_4_week_3_to_5_week_2[0]->count, 'type' => '>3-5']);
            $date_3_to_5->push(['week' => $last_4_week_3_to_5_week_3[0]->date, 'count' => $last_4_week_3_to_5_week_3[0]->count, 'type' => '>3-5']);
            $date_3_to_5->push(['week' => $last_4_week_3_to_5_week_4[0]->date, 'count' => $last_4_week_3_to_5_week_4[0]->count, 'type' => '>3-5']);
            $date_5_to_7->push(['week' => $last_4_week_5_to_7_week_1[0]->date, 'count' => $last_4_week_5_to_7_week_1[0]->count, 'type' => '>5-7']);
            $date_5_to_7->push(['week' => $last_4_week_5_to_7_week_2[0]->date, 'count' => $last_4_week_5_to_7_week_2[0]->count, 'type' => '>5-7']);
            $date_5_to_7->push(['week' => $last_4_week_5_to_7_week_3[0]->date, 'count' => $last_4_week_5_to_7_week_3[0]->count, 'type' => '>5-7']);
            $date_5_to_7->push(['week' => $last_4_week_5_to_7_week_4[0]->date, 'count' => $last_4_week_5_to_7_week_4[0]->count, 'type' => '>5-7']);

            $date_7_to_9->push(['week' => $last_4_week_7_to_9_week_1[0]->date, 'count' => $last_4_week_7_to_9_week_1[0]->count, 'type' => '>7-9']);
            $date_7_to_9->push(['week' => $last_4_week_7_to_9_week_2[0]->date, 'count' => $last_4_week_7_to_9_week_2[0]->count, 'type' => '>7-9']);
            $date_7_to_9->push(['week' => $last_4_week_7_to_9_week_3[0]->date, 'count' => $last_4_week_7_to_9_week_3[0]->count, 'type' => '>7-9']);
            $date_7_to_9->push(['week' => $last_4_week_7_to_9_week_4[0]->date, 'count' => $last_4_week_7_to_9_week_4[0]->count, 'type' => '>7-9']);

            $date_10->push(['week' => $last_4_week_10_week_1[0]->date, 'count' => $last_4_week_10_week_1[0]->count, 'type' => '>10']);
            $date_10->push(['week' => $last_4_week_10_week_2[0]->date, 'count' => $last_4_week_10_week_2[0]->count, 'type' => '>10']);
            $date_10->push(['week' => $last_4_week_10_week_3[0]->date, 'count' => $last_4_week_10_week_3[0]->count, 'type' => '>10']);
            $date_10->push(['week' => $last_4_week_10_week_4[0]->date, 'count' => $last_4_week_10_week_4[0]->count, 'type' => '>10']);
        }
        return ['status' => true, 'data' => ['>3-5' => $date_3_to_5, '>5-7' => $date_5_to_7, '>7-9' => $date_7_to_9, '>10' => $date_10], 'code' => 200];
    }

    // return ['assessmentGraph' => $assessmentGraph];
}
