<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use DB;

class AppOpenedCount5To7Export implements FromCollection, WithMapping, WithHeadings
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

        /* SELECT s.name,user_id,count(*) FROM `subscriber_activities` sa
            join subscribers s on s.id = sa.user_id
            where action = "user_home_page_visit" and sa.created_at > DATE_SUB(now(), INTERVAL 8 DAY)
            group by user_id having count(*) >= 5 and count(*) <7
        */

        $app_opened_count_5_to_7 = DB::select("SELECT s.name,s.phone_no,c.title as cadre,s.cadre_type as cadre_type,ca.title as country,st.title as state,d.title as district,b.title as block,h.health_facility_code as health_facility,user_id,count(*) as count_data FROM `subscriber_activities` sa
            join subscribers s on s.id = sa.user_id
            left join cadre c on c.id = s.cadre_id
            left join country ca on ca.id = s.country_id
            left join state st on st.id = s.state_id
            left join districts d on d.id = s.district_id
            left join blocks b on b.id = s.block_id
            left join health_facilities h on h.id = s.health_facility_id
            where action = 'user_home_page_visit' and sa.created_at > DATE_SUB(now(), INTERVAL 8 DAY)
            group by user_id having count(*) >= 5 and count(*) < 7");

        return collect($app_opened_count_5_to_7);
    }

    public function headings(): array
    {
        return [
            'id',
            'Name',
            'Phone_no',
            'Country',
            'Cadre',
            'Cadre Type',
            'State',
            'District',
            'Block',
            'Health Facility',
            'Total Count'
        ];
    }

    public function map($app_opened_count_5_to_7): array
    {
        $this->i++;

        return [
            // $enquiry->id,
            $this->i,
            $app_opened_count_5_to_7->name,
            $app_opened_count_5_to_7->phone_no,
            $app_opened_count_5_to_7->country,
            $app_opened_count_5_to_7->cadre,
            $app_opened_count_5_to_7->cadre_type,
            $app_opened_count_5_to_7->state,
            $app_opened_count_5_to_7->district,
            $app_opened_count_5_to_7->block,
            $app_opened_count_5_to_7->health_facility,
            $app_opened_count_5_to_7->count_data,
        ];
    }
}
