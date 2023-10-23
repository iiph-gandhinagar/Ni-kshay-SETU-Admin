<?php

namespace App\Exports;

use App\Models\FlashSimilarApp;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FlashSimilarAppsExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return FlashSimilarApp::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.flash-similar-app.columns.id'),
            trans('admin.flash-similar-app.columns.title'),
            trans('admin.flash-similar-app.columns.sub_title'),
            trans('admin.flash-similar-app.columns.href'),
            trans('admin.flash-similar-app.columns.href_web'),
            trans('admin.flash-similar-app.columns.href_ios'),
            trans('admin.flash-similar-app.columns.order_index'),
            trans('admin.flash-similar-app.columns.active'),
        ];
    }

    /**
     * @param FlashSimilarApp $flashSimilarApp
     * @return array
     *
     */
    public function map($flashSimilarApp): array
    {
        return [
            $flashSimilarApp->id,
            $flashSimilarApp->title,
            $flashSimilarApp->sub_title,
            $flashSimilarApp->href,
            $flashSimilarApp->href_web,
            $flashSimilarApp->href_ios,
            $flashSimilarApp->order_index,
            $flashSimilarApp->active,
        ];
    }
}
