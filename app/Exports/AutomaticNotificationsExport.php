<?php

namespace App\Exports;

use App\Models\AutomaticNotification;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AutomaticNotificationsExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return AutomaticNotification::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.automatic-notification.columns.description'),
            trans('admin.automatic-notification.columns.id'),
            trans('admin.automatic-notification.columns.linking_url'),
            trans('admin.automatic-notification.columns.subscriber_id'),
            trans('admin.automatic-notification.columns.title'),
            trans('admin.automatic-notification.columns.type'),
        ];
    }

    /**
     * @param AutomaticNotification $automaticNotification
     * @return array
     *
     */
    public function map($automaticNotification): array
    {
        return [
            $automaticNotification->description,
            $automaticNotification->id,
            $automaticNotification->linking_url,
            $automaticNotification->subscriber_id,
            $automaticNotification->title,
            $automaticNotification->type,
        ];
    }
}
