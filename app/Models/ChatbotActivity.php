<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ChatbotActivity extends Model
{
    use SoftDeletes,LogsActivity;
    protected $table = 'chatbot_activity';

    protected $fillable = [
        'user_id',
        'action',
        'payload',
        'plateform',
        'ip_address',
        'tag_id',
        'question_id',
        'like',
        'dislike',
        'response',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['user_id','action','payload','plateform','ip_address','tag_id','question_id','like','dislike','response'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/chatbot-activities/'.$this->getKey());
    }

    public function user(){
        return $this->belongsTo('App\Models\Subscriber','user_id','id');
    }

    public function tag(){
        return $this->belongsTo('App\Models\TTrainingTag','tag_id','id');
    }
}
