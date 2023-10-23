<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Log;
use DB;

class ChatQuestionExport implements FromCollection, WithMapping, WithHeadings
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

                $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.state_id = $state and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
            } else {
                $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.state_id = $state GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
            }
        } elseif ($newRequest['state_id'] != 0 && $newRequest['district_id'] != 0 && $newRequest['block_id'] == 0) {
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.state_id = $state and s.district_id = $district and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
            } else {
                $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.state_id = $state and s.district_id = $district GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
            }
        } elseif ($newRequest['state_id'] != 0 && $newRequest['district_id'] != 0 && $newRequest['block_id'] != 0) {
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.state_id = $state  and s.district_id = $district and s.block_id = $block and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
            } else {
                $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where s.state_id = $state and s.district_id = $district and s.block_id = $block GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
            }
        } else {
            // Log::info(request()->date);
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
            } else {
                $questionHitCount = DB::select("SELECT  CQ.question, COUNT(CQH.QUESTION_ID) as count_data FROM chat_question_hits CQH  INNER JOIN chat_questions CQ ON CQ.id = CQH.question_id inner join subscribers s on s.id= CQH.subscriber_id GROUP BY CQ.question ORDER BY COUNT(CQ.hit) DESC LIMIT 10");
            }
        }

        return collect($questionHitCount);
    }

    public function headings(): array
    {
        return [
            'id',
            'Question',
            'Total Count'
        ];
    }

    public function map($questionHitCount): array
    {
        $this->i++;

        return [
            // $enquiry->id,
            $this->i,
            json_decode($questionHitCount->question, true)['en'],
            $questionHitCount->count_data,
        ];
    }
}
