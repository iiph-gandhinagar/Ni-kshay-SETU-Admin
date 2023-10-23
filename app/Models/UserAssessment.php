<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAssessment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'assessment_id',
        'user_id',
        'total_marks',
        'total_time',
        'obtained_marks',
        'attempted',
        'right_answers',
        'wrong_answers',
        'skipped',
    
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
        return url('/admin/user-assessments/'.$this->getKey());
    }

    public function assessment(){
        return $this->belongsTo('App\Models\Assessment','assessment_id','id');
    }

    public function assessment_with_trashed(){
        return $this->belongsTo('App\Models\Assessment','assessment_id','id')->withTrashed();
    }

    public function user(){
        return $this->belongsTo('App\Models\Subscriber','user_id','id');
    }

    public function assessment_user_quiz_answer(){
        return $this->hasMany('App\Models\UserAssessmentAnswer','assessment_id','assessment_id')->withTrashed();
    }
}
