<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ChatKeywordHit extends Model
{
    use LogsActivity;
    protected $fillable = [
        'keyword_id',
        'subscriber_id',
        'session_token',
    
    ];
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    protected static $logAttributes = ['keyword_id','subscriber_id','session_token'];
    protected static $logOnlyDirty = true;

    public function user(){
        return $this->belongsTo('App\Models\Subscriber','subscriber_id','id');
    }

    public function keyword(){
        return $this->belongsTo('App\Models\ChatKeyword','keyword_id','id');
    }
}
