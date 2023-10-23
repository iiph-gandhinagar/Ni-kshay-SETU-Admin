<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Log;
use DB;

class CadreWiseSubscriberExport implements FromCollection, WithMapping, WithHeadings
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
        $values = explode(" ", $newRequest['date']);
        $state = $newRequest['state_id'];
        $district = $newRequest['district_id'];
        $block = $newRequest['block_id'];

        if ($newRequest['state_id'] != 0 && $newRequest['district_id'] == 0 && $newRequest['block_id'] == 0) {
            // Log::info($newRequest['date'] == null);
            if ($newRequest->has('date') && $newRequest['date'] > 0) {

                $module_data =  DB::select("SELECT cadre_id,cd.cadre_type,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd
                on cd.ID = s.cadre_id where s.state_id = $state and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                GROUP BY cadre_id,cd.title ORDER BY count(*) DESC");
            } else {
                $module_data =  DB::select("SELECT cadre_id,cd.cadre_type,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd
                on cd.ID = s.cadre_id where s.state_id = $state 
                GROUP BY cadre_id,cd.title ORDER BY count(*) DESC");
            }
        } elseif ($newRequest['state_id'] != 0 && $newRequest['district_id'] != 0 && $newRequest['block_id'] == 0) {
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $module_data =  DB::select("SELECT cadre_id,cd.cadre_type,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd
                on cd.ID = s.cadre_id where s.state_id = $state and s.district_id = $district and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                GROUP BY cadre_id,cd.title ORDER BY count(*) DESC");
            } else {
                $module_data =  DB::select("SELECT cadre_id,cd.cadre_type,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd
                on cd.ID = s.cadre_id where s.state_id = $state and s.district_id = $district
                GROUP BY cadre_id,cd.title ORDER BY count(*) DESC");
            }
        } elseif ($newRequest['state_id'] != 0 && $newRequest['district_id'] != 0 && $newRequest['block_id'] != 0) {
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $module_data =  DB::select("SELECT cadre_id,cd.cadre_type,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd
                on cd.ID = s.cadre_id where s.state_id = $state and s.district_id = $district and s.block_id = $block and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                GROUP BY cadre_id,cd.title ORDER BY count(*) DESC");
            } else {
                $module_data =  DB::select("SELECT cadre_id,cd.cadre_type,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd
                on cd.ID = s.cadre_id where s.state_id = $state and s.district_id = $district and s.block_id = $block
                GROUP BY cadre_id,cd.title ORDER BY count(*) DESC");
            }
        } else {
            // Log::info(request()->date);
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $module_data =  DB::select("SELECT cadre_id,cd.cadre_type,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd
                on cd.ID = s.cadre_id where (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'
                GROUP BY cadre_id,cd.title ORDER BY count(*) DESC");
            } else {
                $module_data =  DB::select("SELECT cadre_id,cd.cadre_type,cd.title as CadreName,count(*) as TotalCadreCount FROM subscribers s left Join cadre cd
                on cd.ID = s.cadre_id
                GROUP BY cadre_id,cd.title ORDER BY count(*) DESC");
            }
        }

        return collect($module_data);
    }

    public function headings(): array
    {
        return [
            'Id',
            'Cadre Type',
            'Cadre Title',
            'TotalCadreCount',
        ];
    }

    public function map($enquiry): array
    {
        $this->i++;

        return [
            // $enquiry->id,
            $this->i,
            $enquiry->cadre_type,
            $enquiry->CadreName,
            $enquiry->TotalCadreCount,
        ];
    }
}
