<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ChatQuestionKeyword extends Model
{
    use LogsActivity;
    protected $fillable = [
        'keyword_id',
        'question_id',
    ];

    protected static $logAttributes = ['keyword_id','question_id'];
    protected static $logOnlyDirty = true;


    public function keywords(){
        return $this->belongsTo('App\Models\ChatKeyword','keyword_id','id');
    }
}
