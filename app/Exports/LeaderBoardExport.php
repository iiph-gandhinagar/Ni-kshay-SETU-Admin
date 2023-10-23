<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Log;
use DB;

class LeaderBoardExport implements FromCollection, WithMapping, WithHeadings
{
    protected $request;
    protected $i = 0;

    public function __construct($request)
    {
        $this->request = $request;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $newRequest = $this->request;
        // Log::info("date--->" . $newRequest['date']);
        $values = explode(" ", $newRequest['date']);
        $state = $newRequest['state_id'];
        $district = $newRequest['district_id'];
        $block = $newRequest['block_id'];

        if ($newRequest['state_id'] != 0 && $newRequest['district_id'] == 0 && $newRequest['block_id'] == 0) {
            // Log::info($newRequest['date'] != 'null');
            if ($newRequest->has('date') && $newRequest['date'] > 0) {

                $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id  join lb_levels l on l.id = lb.level_id where s.state_id = $state and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by `cadre_type`,lb.level_id ");
            } else {
                $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id  join lb_levels l on l.id = lb.level_id where s.state_id = $state group by `cadre_type`,lb.level_id ");
            }
        } elseif ($newRequest['state_id'] != 0 && $newRequest['district_id'] != 0 && $newRequest['block_id'] == 0) {
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id  join lb_levels l on l.id = lb.level_id where s.state_id = $state and s.district_id = $district and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by `cadre_type`,lb.level_id ");
            } else {
                $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id  join lb_levels l on l.id = lb.level_id where s.state_id = $state and s.district_id = $district group by `cadre_type`,lb.level_id ");
            }
        } elseif ($newRequest['state_id'] != 0 && $newRequest['district_id'] != 0 && $newRequest['block_id'] != 0) {
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id  join lb_levels l on l.id = lb.level_id where s.state_id = $state and s.district_id = $district and s.block_id = $block and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by `cadre_type`,lb.level_id ");
            } else {
                $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id  join lb_levels l on l.id = lb.level_id where s.state_id = $state and s.district_id = $district and s.block_id = $block group by `cadre_type`,lb.level_id ");
            }
        } else {
            // Log::info(request()->date);
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id  join lb_levels l on l.id = lb.level_id where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by `cadre_type`,lb.level_id ");
            } else {
                $leaderBoardLevel = DB::select("SELECT cadre_type,l.level,count(*) as count_data,lb.level_id FROM `subscribers` s JOIN lb_subscriber_rankings lb on lb.subscriber_id = s.id  join lb_levels l on l.id = lb.level_id  group by `cadre_type`,lb.level_id ");
            }
        }

        return collect($leaderBoardLevel);
    }

    public function headings(): array
    {
        return [
            'id',
            'Cadre Type',
            'Level',
            'Total Count'
        ];
    }

    public function map($leaderBoardLevel): array
    {
        $this->i++;

        return [
            // $enquiry->id,
            $this->i,
            $leaderBoardLevel->cadre_type,
            json_decode($leaderBoardLevel->level, true)['en'],
            $leaderBoardLevel->count_data,
        ];
    }
}
