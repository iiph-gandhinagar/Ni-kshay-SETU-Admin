<?php

namespace App\Exports;

use App\Models\ChatQuestion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Log;

class MarathiFaqExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection()
    {
        Log::info("inside export marathi");
        $chat_questions =  ChatQuestion::where('deleted_at', NULL)
            ->get(['id', 'question->en as question_en', 'answer->en as answer_en']);
        for ($i = 0; $i < count($chat_questions); $i++) {
            $chat_questions[$i]->question_mr = GoogleTranslate::trans($chat_questions[$i]['question_en'], 'mr');
            $chat_questions[$i]->answer_mr = isset($chat_questions[$i]['answer_en']) ? GoogleTranslate::trans($chat_questions[$i]['answer_en'], 'mr') : NULL;
            // Log::info($chat_questions);
        }
        // Log::info($chat_questions);
        return $chat_questions;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            trans('admin.chat-question.columns.id'),
            "question_en",
            'question_mr',
            "answer_en",
            "answer_mr",
        ];
    }

    /**
     * @param ChatQuestion $chatQuestion
     * @return array
     *
     */
    public function map($chatQuestion): array
    {
        $options = array(
            'ignore_errors' => true,
            // other options go here
        );
        return [
            $chatQuestion->id,
            $chatQuestion->question_en,
            $chatQuestion->question_mr,
            \Soundasleep\Html2Text::convert($chatQuestion->answer_en, $options),
            \Soundasleep\Html2Text::convert($chatQuestion->answer_mr, $options),
        ];
    }
}
