<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Log;
use DB;

class ChatKeywordExport implements FromCollection, WithMapping, WithHeadings
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

                $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id where s.state_id = $state and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by ckh.keyword_id order by  count(*) DESC limit 10");
            } else {
                $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s  on s.id=ckh.subscriber_id  where s.state_id = $state group by ckh.keyword_id order by  count(*) DESC limit 10");
            }
        } elseif ($newRequest['state_id'] != 0 && $newRequest['district_id'] != 0 && $newRequest['block_id'] == 0) {
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id where s.state_id = $state and s.district_id = $district and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by ckh.keyword_id order by  count(*) DESC limit 10");
            } else {
                $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s  on s.id=ckh.subscriber_id  where s.state_id = $state and s.district_id = $district group by ckh.keyword_id order by  count(*) DESC limit 10");
            }
        } elseif ($newRequest['state_id'] != 0 && $newRequest['district_id'] != 0 && $newRequest['block_id'] != 0) {
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id where s.state_id = $state and s.district_id = $district and s.block_id = $block and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by ckh.keyword_id order by  count(*) DESC limit 10");
            } else {
                $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s  on s.id=ckh.subscriber_id  where s.state_id = $state and s.district_id = $district and s.block_id = $block group by ckh.keyword_id order by  count(*) DESC limit 10");
            }
        } else {
            // Log::info(request()->date);
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' group by ckh.keyword_id order by  count(*) DESC limit 10");
            } else {
                $keywordHit = DB::select("SELECT DISTINCT(ck.title),ck.hit,count(*) as count_data FROM `chat_keyword_hits` ckh join chat_keywords ck on ck.id=ckh.keyword_id join subscribers s on s.id=ckh.subscriber_id group by ckh.keyword_id order by  count(*) DESC limit 10");
            }
        }

        return collect($keywordHit);
    }

    public function headings(): array
    {
        return [
            'id',
            'Title',
            'Total Count'
        ];
    }

    public function map($keywordHit): array
    {
        $this->i++;

        return [
            // $enquiry->id,
            $this->i,
            json_decode($keywordHit->title, true)['en'],
            $keywordHit->count_data,
        ];
    }
}
