<?php

namespace App\Exports;

use App\Models\ChatQuestion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ChatQuestionsExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return Collection
     */
    public function collection()
    {
        $chat_questions =  ChatQuestion::with(['question_keywords.keywords'])->where('deleted_at', NULL)
            ->get([
                'id', 'question->en as question_in_english', 'question->hi as question_in_hindi',
                'question->gu as question_in_gujarati', 'answer->en as answer_in_english', 'answer->hi as answer_in_hindi',
                'answer->gu as answer_in_gujarati', 'hit', 'cadre_id', 'category', 'activated'
            ]);
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
            "question in English",
            "question in Hindi",
            "question in Gujarati",
            "answer in English",
            "answer in Hindi",
            "answer in Gujarati",
            trans('admin.chat-question.columns.hit'),
            trans('admin.chat-question.columns.category'),
            trans('admin.chat-question.columns.activated'),
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
            $chatQuestion->question_in_english,
            $chatQuestion->question_in_hindi,
            $chatQuestion->question_in_gujarati,
            \Soundasleep\Html2Text::convert($chatQuestion->answer_in_english, $options),
            \Soundasleep\Html2Text::convert($chatQuestion->answer_in_hindi, $options),
            \Soundasleep\Html2Text::convert($chatQuestion->answer_in_gujarati, $options),
            $chatQuestion->hit,
            $chatQuestion->category,
            $chatQuestion->activated,
        ];
    }
}
