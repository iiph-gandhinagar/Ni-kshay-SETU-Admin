<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ChatQuestionHit extends Model
{
    use LogsActivity;
    protected $fillable = [
        'question_id',
        'subscriber_id',
        'session_token',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['question_id','subscriber_id','session_token'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/chat-question-hits/'.$this->getKey());
    }

    public function user(){
        return $this->belongsTo('App\Models\Subscriber','subscriber_id','id');
    }

    public function questions(){
        return $this->belongsTo('App\Models\ChatQuestion','question_id','id');
    }

    public function questions_with_trashed(){
        return $this->belongsTo('App\Models\ChatQuestion','question_id','id')->withTrashed();
    }
}
