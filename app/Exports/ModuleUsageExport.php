<?php

namespace App\Exports;

use App\Models\ModuleMappingToName;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Log;
use DB;

class ModuleUsageExport implements FromCollection, WithMapping, WithHeadings
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

                $module_data =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $state and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY action ORDER BY count(*) DESC");
            } else {
                $module_data =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $state GROUP BY action ORDER BY count(*) DESC");
            }
        } elseif ($newRequest['state_id'] != 0 && $newRequest['district_id'] != 0 && $newRequest['block_id'] == 0) {
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $module_data =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $state and s.district_id = $district and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY action ORDER BY count(*) DESC");
            } else {
                $module_data =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $state and s.district_id = $district GROUP BY action ORDER BY count(*) DESC");
            }
        } elseif ($newRequest['state_id'] != 0 && $newRequest['district_id'] != 0 && $newRequest['block_id'] != 0) {
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $module_data =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $state and s.state_id = $state and s.district_id = $district and s.block_id = $block and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]'  GROUP BY action ORDER BY count(*) DESC");
            } else {
                $module_data =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and s.state_id = $state and s.state_id = $state and s.district_id = $district and s.block_id = $block GROUP BY action ORDER BY count(*) DESC");
            }
        } else {
            // Log::info(request()->date);
            if ($newRequest->has('date') && $newRequest['date'] > 0) {
                $module_data =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' and (DATE_FORMAT(s.created_at,'%Y-%m-%d')) BETWEEN '$values[0]' and '$values[2]' GROUP BY action ORDER BY count(*) DESC");
            } else {
                $module_data =  DB::select("SELECT sa.action,count(*) as TotalCount FROM subscriber_activities sa join subscribers s on s.id = sa.user_id WHERE sa.action LIKE 'module_%' and sa.action Not LIKE 'module_current_assessments' and sa.action Not LIKE 'module_past_assessments' GROUP BY action ORDER BY count(*) DESC");
            }
        }

        $actions = collect([]);
        foreach ($module_data as $items) {
            $mapping_name = ModuleMappingToName::where('module_name', $items->action)->get(['mapping_name']);
            if (isset($mapping_name) && count($mapping_name) > 0) {
                $actions->push(['action' => $mapping_name[0]['mapping_name'], 'TotalCount' => $items->TotalCount]);
            } else {
                continue;
            }
        }

        return collect($actions->toArray());
    }

    public function headings(): array
    {
        return [
            'id',
            'Action',
            'Total Count'
        ];
    }

    public function map($module_data): array
    {
        $this->i++;

        return [
            // $enquiry->id,
            $this->i,
            $module_data['action'],
            $module_data['TotalCount'],
        ];
    }
}
