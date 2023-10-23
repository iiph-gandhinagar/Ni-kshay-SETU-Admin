<?php

namespace App\Exports;

use App\Models\FlashNews;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FlashNewsExport implements FromCollection, WithMapping, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    /**
     * @return Collection
     */
    public function collection()
    {
        $newRequest = $this->request;
        $flashNews = FlashNews::orderby('id', 'asc');
        if ($newRequest->has('from_date') && $newRequest['from_date'] != NULL && $newRequest['from_date'] != 'null') {
            $flashNews =  $flashNews->whereDate('created_at', '>=', date('Y-m-d', strtotime($newRequest->from_date)));
        }
        if ($newRequest->has('to_date') && $newRequest['to_date'] != NULL && $newRequest['to_date'] != 'null') {
            $flashNews = $flashNews->whereDate('created_at', '<=', date('Y-m-d', strtotime($newRequest->to_date)));
        }
        return $flashNews->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.flash-news.columns.id'),
            trans('admin.flash-news.columns.title'),
            trans('admin.flash-news.columns.description'),
            trans('admin.flash-news.columns.source'),
            trans('admin.flash-news.columns.author'),
            trans('admin.flash-news.columns.href'),
            trans('admin.flash-news.columns.publish_date'),
            trans('admin.flash-news.columns.order_index'),
            trans('admin.flash-news.columns.active'),
        ];
    }

    /**
     * @param FlashNews $flashNews
     * @return array
     *
     */
    public function map($flashNews): array
    {
        return [
            $flashNews->id,
            $flashNews->title,
            $flashNews->description,
            $flashNews->source,
            $flashNews->author,
            $flashNews->href,
            $flashNews->publish_date,
            $flashNews->order_index,
            $flashNews->active,
        ];
    }
}
