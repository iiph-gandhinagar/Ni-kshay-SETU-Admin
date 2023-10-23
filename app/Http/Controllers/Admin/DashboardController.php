<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AppOpenedCount10Export;
use App\Exports\AppOpenedCount5To7Export;
use App\Exports\AppOpenedCount7To9Export;
use App\Http\Controllers\Controller;
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
use App\Exports\CadreWiseSubscriberExport;
use App\Exports\ChatKeywordExport;
use App\Exports\LeaderBoardExport;
use App\Exports\ModuleUsageExport;
use App\Exports\ChatQuestionExport;
use App\Exports\AppOpenedCountExport;
use App\Models\LbSubscriberRanking;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use DB;

class DashboardController extends Controller
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


        return view('admin.dashboard-components.new-dashboard', ['state_id' => $user_state, 'date' => $request->date, 'state' => $state, 'block' => $block, 'district' => $district]);
    }

    public function getDistrictData(Request $request)
    {
        $district = District::where('state_id', $request->state_id)->get(['id', 'title']);
        return $district;
    }

    public function getBlockData(Request $request)
    {
        $block = Block::where('state_id', $request->state_id)->where('district_id', $request->district)->get(['id', 'title']);
        return $block;
    }


    public function getDashboardDataWithFilters(Request $request)
    {
        if ($request->date != null) {
            $values = explode(" ", $request->date);
            $stateWiseSubscriber = DB::select("SELECT DISTINCT(state_id),St.title as title,count(*) as TotalCount FROM subscribers s left Join state St 
                on St.ID = s.state_id WHERE St.deleted_at IS NULL and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' and s.state_id  not in(37,0)
                GROUP BY state_id ORDER BY count(*) DESC");

            $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
            on cd.ID = s.cadre_id where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 10");

            $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
            on s.id=ua.user_id where DATE_FORMAT(ua.created_at,'%Y-%m-%d') BETWEEN '$values[0]' and '$values[2]'
            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
            on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
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
            where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            ORDER BY ch.hit DESC 
            LIMIT 10");

            $keywordHitCount = DB::select("SELECT DISTINCT(ck.title),ck.hit FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s 
                            on s.id=ckh.subscriber_id 
                            where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                            group by ckh.keyword_id
                            order by ck.hit DESC
                            limit 10");

            $subscriberCount = Subscriber::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->count();
            $assessmetnCount = UserAssessment::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->count();
            $state_level_subscriber = Subscriber::whereDate('updated_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('updated_at', '<=', date('Y-m-d', strtotime($values[2])))->where('country_id', 1)->count();
        } else {
            $stateWiseSubscriber = DB::select("SELECT DISTINCT(state_id),St.title as title,count(*) as TotalCount FROM subscribers s left Join state St 
                on St.ID = s.state_id WHERE St.deleted_at IS NULL and s.state_id != 37 and s.state_id  not in(37,0)
                GROUP BY state_id ORDER BY count(*) DESC");
            foreach ($stateWiseSubscriber as $sub) {
                $sub->todays_subscriber = Subscriber::whereDate('created_at', '=', date('Y-m-d'))->count();
                $sub->percentage = round(($sub->TotalCount * 100) / Subscriber::count(), 2);
            }
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
        $chatbot_usage_count = LbSubscriberRanking::sum('chatbot_usage_count');
        $resource_material_usage_count = LbSubscriberRanking::sum('resource_material_accessed_count');

        $total_time_spent = round((LbSubscriberRanking::sum('mins_spent_count')) / 60, 2);
        return [
            "stateWiseSubscriber" => $stateWiseSubscriber, 'cadreWiseSubscriber' => $cadreWiseSubscriber, 'asessmentGraph' => $assessmentGraph, 'top10Modules' => $actions->toArray(),
            'questionHit' => $questionHitCount, 'keywordHit' => $keywordHitCount, 'subscriber' => $subscriberCount, 'subscriberEnrollToday' => $enrollSubscriber, 'enquiry' => $enquiryCount, 'subscriberAppVistCount' => $subscriberAppVisitCount,
            'assessment' => $assessmetnCount, 'completeAssessmentCount' => $todayCompletedAssessmentCount, 'state_level_subscriber' => $state_level_subscriber
        ];
    }

    public function getDistrictBlockData(Request $request)
    {
        if ($request->date != null) {
            $values = explode(" ", $request->date);
            $districtWiseSubscriber = DB::select("SELECT district_id,d.title as title,count(*) as TotalCount FROM subscribers s Join districts d 
                on d.ID = s.district_id WHERE s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                GROUP BY district_id ORDER BY count(*) DESC");

            $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                on cd.ID = s.cadre_id where s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 10");

            $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
            on s.id=ua.user_id where s.state_id = $request->state_id and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
                on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
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

            // $questionHitCount = DB::select("SELECT DISTINCT(ch.question),ch.hit FROM `chat_questions` ch 
            //         join chat_question_hits cqh on cqh.question_id = ch.id  
            //         join subscribers s on cqh.subscriber_id = s.id
            //         where s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            //         ORDER BY ch.hit DESC 
            //         LIMIT 10");

            // $keywordHitCount = DB::select("SELECT DISTINCT(ck.title),ck.hit FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id 
            // where s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            // group by ckh.keyword_id
            // order by ck.hit DESC
            // limit 10");

            $questionHitCount = DB::select("SELECT ch.question,count(question_id) as hit FROM `chat_question_hits` cqh
                        join chat_questions ch on ch.id = cqh.question_id
                        join subscribers s on s.id = cqh.subscriber_id
                        where s.state_id =  $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        group by question_id
                        order by count(question_id) DESC 
                        LIMIT 10");

            $keywordHitCount = DB::select("SELECT ck.title,count(keyword_id) as hit FROM `chat_keyword_hits` ckh 
                join chat_keywords ck on ck.id=ckh.keyword_id 
                join subscribers s on s.id=ckh.subscriber_id 
                where s.state_id = $request->state_id and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                group by keyword_id
                order by count(keyword_id) DESC
                limit 10");

            $subscriberCount = Subscriber::whereDate('created_at', '>=', $values[0])->whereDate('created_at', '<=', $values[2])->where('state_id', $request->state_id)->count();
            $assessmetnCount = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                ->whereDate('user_assessments.created_at', '>=', date('Y-m-d', strtotime($values[0])))
                ->whereDate('user_assessments.created_at', '<=', date('Y-m-d', strtotime($values[2])))
                ->where('subscribers.state_id', $request->state_id)
                ->count();
            $state_level_subscriber = Subscriber::where('state_id', $request->state_id)->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->where('district_id', 0)->count();
        } else {
            $districtWiseSubscriber = DB::select("SELECT district_id,d.title as title,count(*) as TotalCount FROM subscribers s left Join districts d 
            on d.ID = s.district_id WHERE d.state_id = $request->state_id 
            GROUP BY district_id ORDER BY count(*) DESC");

            $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
            on cd.ID = s.cadre_id where s.state_id = $request->state_id
            GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 10");

            $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
            on s.id=ua.user_id where s.state_id = $request->state_id
            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
            on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = 1
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

            $questionHitCount = DB::select("SELECT ch.question,count(question_id) as hit FROM `chat_question_hits` cqh
                        join chat_questions ch on ch.id = cqh.question_id
                        join subscribers s on s.id = cqh.subscriber_id
                        where s.state_id =  $request->state_id
                        group by question_id
                        order by count(question_id) DESC 
                        LIMIT 10");

            $keywordHitCount = DB::select("SELECT ck.title,count(keyword_id) as hit FROM `chat_keyword_hits` ckh 
                join chat_keywords ck on ck.id=ckh.keyword_id 
                join subscribers s on s.id=ckh.subscriber_id 
                where s.state_id = $request->state_id
                group by keyword_id
                order by count(keyword_id) DESC
                limit 10");

            $subscriberCount = Subscriber::where('state_id', $request->state_id)->count();
            $assessmetnCount = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->where('subscribers.state_id', $request->state_id)->count();
            $state_level_subscriber = Subscriber::where('state_id', $request->state_id)->where('district_id', 0)->count();
        }

        $enrollSubscriber = Subscriber::where('state_id', $request->state_id)->whereDate('created_at', '=', date('Y-m-d'))->count();
        $subscriberAppVisitCount = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
            ->where('subscribers.state_id', $request->state_id)
            ->whereDate('subscriber_activities.created_at', '=', Carbon::now())
            ->distinct('user_id')
            ->count();
        $enquiryCount = Enquiry::join('subscribers', 'subscribers.phone_no', '=', 'enquiries.phone')->where('subscribers.state_id', $request->state_id)->whereDate('enquiries.created_at', '=', date('Y-m-d'))->count();
        $todayCompletedAssessmentCount = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')->where('subscribers.state_id', $request->state_id)->whereDate('user_assessments.created_at', '=', date('Y-m-d'))->count();


        return [
            'districtWiseSubscriber' => $districtWiseSubscriber, 'cadreWiseSubscriber' => $cadreWiseSubscriber, 'asessmentGraph' => $assessmentGraph, 'top10Modules' => $actions->toArray(),
            'questionHit' => $questionHitCount, 'keywordHit' => $keywordHitCount, 'subscriber' => $subscriberCount, 'subscriberEnrollToday' => $enrollSubscriber, 'subscriberAppVistCount' => $subscriberAppVisitCount,
            'assessment' => $assessmetnCount, 'completeAssessmentCount' => $todayCompletedAssessmentCount, 'enquiry' => $enquiryCount, 'state_level_subscriber' => $state_level_subscriber
        ];
    }

    public function getBlockHealthData(Request $request)
    {
        if ($request->date != null) {
            $values = explode(" ", $request->date);
            $blockWiseSubscriber = DB::select("SELECT block_id,b.title as title,count(*) as TotalCount FROM subscribers s Join blocks b 
            on b.ID = s.block_id WHERE b.state_id = $request->state_id and b.district_id = $request->district and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            GROUP BY block_id ORDER BY count(*) DESC");

            $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                on cd.ID = s.cadre_id where s.state_id = $request->state_id and s.district_id = $request->district and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 10");

            $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
            on s.id=ua.user_id where s.state_id = $request->state_id and s.district_id = $request->district and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
                on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id and s.district_id = $request->district and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
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

            // $questionHitCount = DB::select("SELECT DISTINCT(ch.question),ch.hit FROM `chat_questions` ch 
            //         join chat_question_hits cqh on cqh.question_id = ch.id  
            //         join subscribers s on cqh.subscriber_id = s.id
            //         where s.state_id = $request->state_id and s.district_id = $request->district and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            //         ORDER BY ch.hit DESC 
            //         LIMIT 10");

            // $keywordHitCount = DB::select("SELECT DISTINCT(ck.title),ck.hit FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id 
            // where s.state_id = $request->state_id and s.district_id = $request->district and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            // group by ckh.keyword_id
            // order by ck.hit DESC
            // limit 10");

            $questionHitCount = DB::select("SELECT ch.question,count(question_id) as hit FROM `chat_question_hits` cqh
                        join chat_questions ch on ch.id = cqh.question_id
                        join subscribers s on s.id = cqh.subscriber_id
                        where s.state_id =  $request->state_id s.district_id = $request->district and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        group by question_id
                        order by count(question_id) DESC 
                        LIMIT 10");

            $keywordHitCount = DB::select("SELECT ck.title,count(keyword_id) as hit FROM `chat_keyword_hits` ckh 
                join chat_keywords ck on ck.id=ckh.keyword_id 
                join subscribers s on s.id=ckh.subscriber_id 
                where s.state_id = $request->state_id s.district_id = $request->district and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                group by keyword_id
                order by count(keyword_id) DESC
                limit 10");

            $subscriberCount = Subscriber::whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->where('state_id', $request->state_id)->where('district_id', $request->district)->count();
            $assessmetnCount = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                ->where('subscribers.state_id', $request->state_id)
                ->where('subscribers.district_id', $request->district)
                ->whereBetween('user_assessments.created_at', [$values[0], $values[2]])
                ->count();
            $state_level_subscriber = Subscriber::where('state_id', $request->state_id)
                ->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))
                ->where('district_id', $request->district)->where('block_id', 0)->count();
        } else {
            $blockWiseSubscriber = DB::select("SELECT block_id,b.title as title,count(*) as TotalCount FROM subscribers s Join blocks b 
            on b.ID = s.block_id WHERE b.state_id = $request->state_id and b.district_id = $request->district
            GROUP BY block_id ORDER BY count(*) DESC");

            $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
                on cd.ID = s.cadre_id where s.state_id = $request->state_id and s.district_id = $request->district
                GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 10");

            $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
            on s.id=ua.user_id where s.state_id = $request->state_id and s.district_id = $request->district
            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
            on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = 1 and s.district_id = 1
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

            // $questionHitCount = DB::select("SELECT DISTINCT(ch.question),ch.hit FROM `chat_questions` ch 
            //         join chat_question_hits cqh on cqh.question_id = ch.id  
            //         join subscribers s on cqh.subscriber_id = s.id
            //         where s.state_id = $request->state_id and s.district_id = $request->district
            //         ORDER BY ch.hit DESC 
            //         LIMIT 10");

            // $keywordHitCount = DB::select("SELECT DISTINCT(ck.title),ck.hit FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id 
            // where s.state_id = $request->state_id and s.district_id = $request->district
            // group by ckh.keyword_id
            // order by ck.hit DESC
            // limit 10");

            $questionHitCount = DB::select("SELECT ch.question,count(question_id) as hit FROM `chat_question_hits` cqh
                        join chat_questions ch on ch.id = cqh.question_id
                        join subscribers s on s.id = cqh.subscriber_id
                        where s.state_id =  $request->state_id and s.district_id = $request->district
                        group by question_id
                        order by count(question_id) DESC 
                        LIMIT 10");

            $keywordHitCount = DB::select("SELECT ck.title,count(keyword_id) as hit FROM `chat_keyword_hits` ckh 
                join chat_keywords ck on ck.id=ckh.keyword_id 
                join subscribers s on s.id=ckh.subscriber_id 
                where s.state_id = $request->state_id and s.district_id = $request->district
                group by keyword_id
                order by count(keyword_id) DESC
                limit 10");

            $subscriberCount = Subscriber::where('state_id', $request->state_id)->where('district_id', $request->district)->count();
            $assessmetnCount = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                ->where('subscribers.state_id', $request->state_id)
                ->where('subscribers.district_id', $request->district)
                ->count();
            $state_level_subscriber = Subscriber::where('state_id', $request->state_id)->where('district_id', $request->district)->where('block_id', 0)->count();
        }

        $enrollSubscriber = Subscriber::where('state_id', $request->state_id)->where('subscribers.district_id', $request->district)->whereDate('created_at', '=', date('Y-m-d'))->count();
        $subscriberAppVisitCount = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
            ->where('subscribers.state_id', $request->state_id)
            ->where('subscribers.district_id', $request->district)
            ->whereDate('subscriber_activities.created_at', '=', Carbon::now())
            ->distinct('user_id')
            ->count();
        $enquiryCount = Enquiry::join('subscribers', 'subscribers.phone_no', '=', 'enquiries.phone')
            ->where('subscribers.state_id', $request->state_id)
            ->where('subscribers.district_id', $request->district)
            ->whereDate('enquiries.created_at', '=', date('Y-m-d'))
            ->count();
        $todayCompletedAssessmentCount = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
            ->where('subscribers.state_id', $request->state_id)
            ->where('subscribers.district_id', $request->district)
            ->whereDate('user_assessments.created_at', '=', date('Y-m-d'))
            ->count();

        return [
            'blockWiseSubscriber' => $blockWiseSubscriber, 'cadreWiseSubscriber' => $cadreWiseSubscriber, 'asessmentGraph' => $assessmentGraph, 'top10Modules' => $actions->toArray(),
            'questionHit' => $questionHitCount, 'keywordHit' => $keywordHitCount, 'subscriber' => $subscriberCount, 'subscriberEnrollToday' => $enrollSubscriber, 'subscriberAppVistCount' => $subscriberAppVisitCount,
            'assessment' => $assessmetnCount, 'completeAssessmentCount' => $todayCompletedAssessmentCount, 'enquiry' => $enquiryCount, 'state_level_subscriber' => $state_level_subscriber
        ];
    }

    public function getHealthData(Request $request)
    {
        if ($request->date != null) {
            $values = explode(" ", $request->date);
            $subscriberList = DB::select("SELECT health_facility_id,h.health_facility_code as title,count(*) as TotalCount FROM subscribers s Join health_facilities h 
            on h.ID = s.health_facility_id WHERE h.state_id = $request->state_id and h.district_id = $request->district and h.block_id=$request->block and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' 
            GROUP BY health_facility_id ORDER BY count(*) DESC");

            $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
            on cd.ID = s.cadre_id where s.state_id = $request->state_id and s.district_id = $request->district and s.block_id=$request->block and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' 
            GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 10");

            $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
            on s.id=ua.user_id where s.state_id = $request->state_id and s.district_id = $request->district and s.block_id=$request->block and (DATE_FORMAT(ua.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
            on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $request->state_id and s.district_id = $request->district and s.block_id=$request->block and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
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

            // $questionHitCount = DB::select("SELECT DISTINCT(ch.question),ch.hit FROM `chat_questions` ch 
            //     join chat_question_hits cqh on cqh.question_id = ch.id  
            //     join subscribers s on cqh.subscriber_id = s.id
            //     where s.state_id = $request->state_id and s.district_id = $request->district and s.block_id=$request->block and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            //     ORDER BY ch.hit DESC 
            //     LIMIT 10");

            // $keywordHitCount = DB::select("SELECT DISTINCT(ck.title),ck.hit FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id 
            //     where s.state_id = $request->state_id and s.district_id = $request->district and s.block_id=$request->block and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            //     group by ckh.keyword_id
            //     order by ck.hit DESC
            //     limit 10");

            $questionHitCount = DB::select("SELECT ch.question,count(question_id) as hit FROM `chat_question_hits` cqh
                        join chat_questions ch on ch.id = cqh.question_id
                        join subscribers s on s.id = cqh.subscriber_id
                        where s.state_id =  $request->state_id and s.district_id = $request->district and s.block_id=$request->block and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                        group by question_id
                        order by count(question_id) DESC 
                        LIMIT 10");

            $keywordHitCount = DB::select("SELECT ck.title,count(keyword_id) as hit FROM `chat_keyword_hits` ckh 
                join chat_keywords ck on ck.id=ckh.keyword_id 
                join subscribers s on s.id=ckh.subscriber_id 
                where s.state_id = $request->state_id and s.district_id = $request->district and s.block_id=$request->block and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                group by keyword_id
                order by count(keyword_id) DESC
                limit 10");

            $subscriberCount = Subscriber::where('state_id', $request->state_id)->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->where('district_id', $request->district)->where('block_id', $request->block)->count();
            $assessmetnCount = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                ->where('subscribers.state_id', $request->state_id)
                ->where('subscribers.district_id', $request->district)
                ->where('subscribers.block_id', $request->block)
                ->whereBetween('user_assessments.created_at', [$values[0], $values[2]])
                ->count();

            $state_level_subscriber = Subscriber::where('state_id', $request->state_id)->where('district_id', $request->district)->where('block_id', $request->block)->where('health_facility_id', 0)->whereDate('created_at', '>=', date('Y-m-d', strtotime($values[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime($values[2])))->count();
        } else {
            $subscriberList = DB::select("SELECT health_facility_id,h.health_facility_code as title,count(*) as TotalCount FROM subscribers s Join health_facilities h 
            on h.ID = s.health_facility_id WHERE h.state_id = $request->state_id and h.district_id = $request->district and h.block_id=$request->block 
            GROUP BY health_facility_id ORDER BY count(*) DESC");

            $cadreWiseSubscriber = DB::select("SELECT cadre_id,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd 
            on cd.ID = s.cadre_id where s.state_id = $request->state_id and s.district_id = $request->district and s.block_id=$request->block 
            GROUP BY cadre_id,cd.title ORDER BY count(*) DESC LIMIT 10");

            $assessmentGraph = DB::select("select SQL_NO_CACHE YEAR(ua.created_at), concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)) as date,MONTH(ua.created_at), COUNT(*) as subscriber_count FROM user_assessments ua join subscribers s 
            on s.id=ua.user_id where s.state_id = $request->state_id and s.district_id = $request->district and s.block_id=$request->block
            GROUP BY YEAR(ua.created_at),concat(LEFT(MONTHNAME(ua.created_at),3),'-' ,RIGHT(YEAR(ua.created_at),2)),MONTH(ua.created_at)
            ORDER BY YEAR(ua.created_at),MONTH(ua.created_at)");

            $top10Modules = DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s 
            on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = 1 and s.district_id = 1 and s.block_id=1
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

            // $questionHitCount = DB::select("SELECT DISTINCT(ch.question),ch.hit FROM `chat_questions` ch 
            //     join chat_question_hits cqh on cqh.question_id = ch.id  
            //     join subscribers s on cqh.subscriber_id = s.id
            //     where s.state_id = $request->state_id and s.district_id = $request->district and s.block_id=$request->block
            //     ORDER BY ch.hit DESC 
            //     LIMIT 10");

            // $keywordHitCount = DB::select("SELECT DISTINCT(ck.title),ck.hit FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id 
            // where s.state_id = $request->state_id and s.district_id = $request->district and s.block_id=$request->block
            // group by ckh.keyword_id
            // order by ck.hit DESC
            // limit 10");

            $questionHitCount = DB::select("SELECT ch.question,count(question_id) as hit FROM `chat_question_hits` cqh
                        join chat_questions ch on ch.id = cqh.question_id
                        join subscribers s on s.id = cqh.subscriber_id
                        where s.state_id =  $request->state_id and s.district_id = $request->district and s.block_id=$request->block
                        group by question_id
                        order by count(question_id) DESC 
                        LIMIT 10");

            $keywordHitCount = DB::select("SELECT ck.title,count(keyword_id) as hit FROM `chat_keyword_hits` ckh 
                join chat_keywords ck on ck.id=ckh.keyword_id 
                join subscribers s on s.id=ckh.subscriber_id 
                where s.state_id = $request->state_id and s.district_id = $request->district and s.block_id=$request->block
                group by keyword_id
                order by count(keyword_id) DESC
                limit 10");

            $subscriberCount = Subscriber::where('state_id', $request->state_id)->where('district_id', $request->district)->where('block_id', $request->block)->count();
            $assessmetnCount = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
                ->where('subscribers.state_id', $request->state_id)
                ->where('subscribers.district_id', $request->district)
                ->where('subscribers.block_id', $request->block)
                ->count();
            $state_level_subscriber = Subscriber::where('state_id', $request->state_id)->where('district_id', $request->district)->where('block_id', $request->block)->where('health_facility_id', 0)->count();
        }

        $enrollSubscriber = Subscriber::where('state_id', $request->state_id)->where('subscribers.district_id', $request->district)->where('subscribers.block_id', $request->block)->whereDate('created_at', '=', date('Y-m-d'))->count();
        $subscriberAppVisitCount = SubscriberActivity::join('subscribers', 'subscribers.id', '=', 'subscriber_activities.user_id')
            ->where('subscribers.state_id', $request->state_id)
            ->where('subscribers.district_id', $request->district)
            ->where('subscribers.block_id', $request->block)
            ->whereDate('subscriber_activities.created_at', '=', Carbon::now())
            ->distinct('user_id')
            ->count();
        $enquiryCount = Enquiry::join('subscribers', 'subscribers.phone_no', '=', 'enquiries.phone')
            ->where('subscribers.state_id', $request->state_id)
            ->where('subscribers.district_id', $request->district)
            ->where('subscribers.block_id', $request->block)
            ->whereDate('enquiries.created_at', '=', date('Y-m-d'))
            ->count();
        $todayCompletedAssessmentCount = UserAssessment::join('subscribers', 'subscribers.id', '=', 'user_assessments.user_id')
            ->where('subscribers.state_id', $request->state_id)
            ->where('subscribers.district_id', $request->district)
            ->where('subscribers.block_id', $request->block)
            ->whereDate('user_assessments.created_at', '=', date('Y-m-d'))
            ->count();

        return [
            'subscriberList' => $subscriberList, 'cadreWiseSubscriber' => $cadreWiseSubscriber, 'asessmentGraph' => $assessmentGraph, 'top10Modules' => $actions->toArray(),
            'questionHit' => $questionHitCount, 'keywordHit' => $keywordHitCount, 'subscriber' => $subscriberCount, 'subscriberEnrollToday' => $enrollSubscriber, 'subscriberAppVistCount' => $subscriberAppVisitCount,
            'assessment' => $assessmetnCount, 'completeAssessmentCount' => $todayCompletedAssessmentCount, 'enquiry' => $enquiryCount, 'state_level_subscriber' => $state_level_subscriber
        ];
    }


    public function getStates()
    {
        $result = State::get(['title as state', 'id'])->toArray();
        return $result;
    }

    public function getIndicatorValues()
    {
        $indicator = [
            [
                "column_name" => "Corrected Total points",
                "mapping_name" => "corrected_total_points",
                "column_type" => "compiled_index",
                "min_val" => 1,
                "max_val" => 100,
                "view_column_name" => "TB Index",
                "show_column" => 1,
                "contribute_to" => ""
            ],
            [
                "column_name" => "Points (5)",
                "mapping_name" => "points_plhiv",
                "column_type" => "compiled_index",
                "min_val" => 1,
                "max_val" => 5,
                "view_column_name" => "Points PLHIV Received (wts. 5)",
                "show_column" => 1,
                "contribute_to" => ""
            ],
            [
                "column_name" => "Points on UDST (10)",
                "mapping_name" => "points_udst",
                "column_type" => "compiled_index",
                "min_val" => 1,
                "max_val" => 10,
                "view_column_name" => "Points on UDST (wts. 10)",
                "show_column" => 1,
                "contribute_to" => ""
            ],
            [
                "column_name" => "Points on treatment success rate (15 Points)",
                "mapping_name" => "points_sucess_rate",
                "column_type" => "compiled_index",
                "min_val" => 1,
                "max_val" => 15,
                "view_column_name" => "Points on treatment success rate (wts. 15)",
                "show_column" => 1,
                "contribute_to" => ""
            ],
            [
                "column_name" => "Points on TB notified patients with known HIV status (10)",
                "mapping_name" => "points_notify_hiv",
                "column_type" => "compiled_index",
                "min_val" => 1,
                "max_val" => 10,
                "view_column_name" => "Points on TB notified Patients with known HIV status (wts. 10)",
                "show_column" => 1,
                "contribute_to" => ""
            ],
            [
                "column_name" => "Points on TB notification achieved (20)",
                "mapping_name" => "points_notify",
                "column_type" => "compiled_index",
                "min_val" => 1,
                "max_val" => 60,
                "view_column_name" => "Points on TB notification achieved (wts. 20)",
                "show_column" => 1,
                "contribute_to" => ""
            ],
            [
                "column_name" => "Points on DRTB patients treatment initiation regimen (15)",
                "mapping_name" => "points_drtb_patients",
                "column_type" => "compiled_index",
                "min_val" => 1,
                "max_val" => 15,
                "view_column_name" => "Points on DRTB Patients treatment initiation regimen (wts. 15)",
                "show_column" => 1,
                "contribute_to" => ""
            ],
            [
                "column_name" => "Points on chemoprophylaxis (5)",
                "mapping_name" => "points_chemo",
                "column_type" => "compiled_index",
                "min_val" => 1,
                "max_val" => 5,
                "view_column_name" => "Points on chemoprophylaxis (wts. 5)",
                "show_column" => 1,
                "contribute_to" => ""
            ],
            [
                "column_name" => "Points on Beneficiaries paid under Nikshay Poshan Yojana",
                "mapping_name" => "points_npy",
                "column_type" => "compiled_index",
                "min_val" => 1,
                "max_val" => 10,
                "view_column_name" => "Points on Beneficiaries Paid under NikshayPoshan Yojana (wts. 10)",
                "show_column" => 1,
                "contribute_to" => ""
            ],
            [
                "column_name" => "POINTS (Expenditure - 10)",
                "mapping_name" => "points_expenditure",
                "column_type" => "compiled_index",
                "min_val" => 1,
                "max_val" => 10,
                "view_column_name" => "Points Expenditure (wts. 10)",
                "show_column" => 1,
                "contribute_to" => ""
            ]
        ];
        return $indicator;
    }

    public function getLatestTimestamp()
    {
        return [["dt" => "2020-10-01", "quarter" => "Q4", "year" => 2020]];
    }

    public function getLatestTimestampDist()
    {
        return [["dt" => "2020-10-01", "quarter" => "Q4", "year" => 2020]];
    }

    public function getMapData(Request $request)
    {
        if (isset($request->date) && $request->date != '') {
            $values = explode(" ", $request->date);
            $resultPoint = DB::select("SELECT count(*) as points_notify,s.id as state_id,s.title as state FROM `subscribers` sub 
                                    join state s on s.id= sub.state_id 
                                    where (DATE_FORMAT(sub.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' 
                                    group by s.id");
        } else {
            $resultPoint = DB::select('SELECT count(*) as points_notify,s.id as state_id,s.title as state FROM `subscribers` sub 
                                        join state s on s.id= sub.state_id 
                                        group by s.id');

            // $resultPoint = DB::select('SELECT count(*) as points_notify,s.id as state_id,s.title as state FROM state s
            //                             left join subscribers sub on sub.state_id= s.id 
            //                             group by s.id');
        }

        $finalData =  collect($resultPoint)->map(function ($point, $index) {
            $point->quarter = "Q4";
            $point->year = 2020;
            $point->rank = $index + 1;
            return $point;
        });
        $finalResult = $finalData->toArray();
        return $finalResult;
    }
    public function getStateLowerScore()
    {
        return array(
            0 =>
            array(
                'state_id' => '549836',
                'state' => 'Ladakh',
                'indicator' => 8.53571,
            ),
        );
    }

    public function getStateHighScore()
    {
        return array(
            0 =>
            array(
                'state_id' => '21',
                'state' => 'Lakshwadeep',
                'indicator' => 20.0,
            ),
        );
    }

    public function getStateHighChange()
    {
        return array(
            0 =>
            array(
                'state_id' => '7',
                'state' => 'Bihar',
                'indicator' => 6.6590786729,
            ),
        );
    }

    public function getStateLowChange()
    {
        return array(
            0 =>
            array(
                'state_id' => '549836',
                'state' => 'Ladakh',
                'indicator' => -12.1324358239,
            ),
        );
    }

    public function getDistrictMapData(Request $request)
    {
        if (isset($request->date) && $request->date != '') {
            $values = explode(" ", $request->date);
            $districtMap = DB::select("SELECT count(*) as points_notify,s.title as state,s.id as state_id,d.title as district_name,d.id as district_id FROM `subscribers` sub 
            join state s on s.id=sub.state_id 
            join districts d on d.id=sub.district_id
            WHERE sub.state_id = $request->state_id and (DATE_FORMAT(sub.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
            group by d.id,d.title");
        } else {
            $districtMap = DB::select("SELECT count(*) as points_notify,s.title as state,s.id as state_id,d.title as district_name,d.id as district_id FROM `subscribers` sub 
            join state s on s.id=sub.state_id 
            join districts d on d.id=sub.district_id
            WHERE sub.state_id = $request->state_id
            group by d.id,d.title");
        }

        if (count($districtMap) == 0) {
            $districtMap = DB::select("select 0 as points_notify,s.title as state,s.id as state_id,d.title as district_name,d.id as district_id FROM districts d
            join state s on s.id= d.state_id
            where s.id = $request->state_id
            group by d.id,d.title");
        }

        $finalDistrictMap =  collect($districtMap)->map(function ($point, $index) {
            $point->quarter = "Q4";
            $point->year = 2020;
            $point->rank = $index + 1;
            return $point;
        });
        return $finalDistrictMap->toArray();
    }

    public function getDistricts(Request $request)
    {
        $districts = DB::select("SELECT d.state_id,d.id as district_id,s.title as state_name,d.title as district_name  FROM `districts` d join state s on s.id=d.state_id where state_id = $request->state_id");
        return $districts;
    }

    public function exportCadreWiseSubscriber(Request $request): ?BinaryFileResponse
    {

        // $this->authorize('admin.chat-keyword-hit.export');
        return Excel::download(new CadreWiseSubscriberExport($request), 'CadreWiseSubscribers.xlsx');
    }

    public function exportModuleUsage(Request $request): ?BinaryFileResponse
    {

        // $this->authorize('admin.chat-keyword-hit.export');
        return Excel::download(new ModuleUsageExport($request), 'ModuleUsage.xlsx');
    }

    public function exportLeaderboard(Request $request): ?BinaryFileResponse
    {
        return Excel::download(new LeaderBoardExport($request), 'LeaderBoard.xlsx');
    }

    public function exportChatQuestion(Request $request): ?BinaryFileResponse
    {
        return Excel::download(new ChatQuestionExport($request), 'ChatQuestion.xlsx');
    }

    public function exportChatKeyword(Request $request): ?BinaryFileResponse
    {
        return Excel::download(new ChatKeywordExport($request), 'ChatKeyword.xlsx');
    }

    public function exportAppOpenedCount3to5(Request $request): ?BinaryFileResponse
    {
        return Excel::download(new AppOpenedCountExport($request), 'AppOpenedCount3to5.xlsx');
    }

    public function exportAppOpenedCount5to7(Request $request): ?BinaryFileResponse
    {
        return Excel::download(new AppOpenedCount5To7Export($request), 'AppOpenedCount5to7.xlsx');
    }

    public function exportAppOpenedCount7to9(Request $request): ?BinaryFileResponse
    {
        return Excel::download(new AppOpenedCount7To9Export($request), 'AppOpenedCount7to9.xlsx');
    }

    public function exportAppOpenedCount10(Request $request): ?BinaryFileResponse
    {
        return Excel::download(new AppOpenedCount10Export($request), 'AppOpenedCount10.xlsx');
    }
}
