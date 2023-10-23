<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFeedbackDetail extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'subscriber_id',
        'feedback_id',
        'ratings',
        'review',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/user-feedback-details/'.$this->getKey());
    }

    public function user(){
        return $this->belongsTo('App\Models\Subscriber','subscriber_id','id');
    }

    public function feedback_question(){
        return $this->belongsTo('App\Models\UserFeedbackQuestion','feedback_id','id')->withTrashed();
    }
}
