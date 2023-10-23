<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyMasterHistory extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'answer',
        'survey_id',
        'survey_question_id',
        'user_id',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/survey-master-histories/'.$this->getKey());
    }

    public function user(){
        return $this->belongsTo('App\Models\Subscriber','user_id','id');
    }

    public function survey_master(){
        return $this->belongsTo('App\Models\SurveyMaster','survey_id','id')->withTrashed();
    }

    public function survey_master_question(){
        return $this->belongsTo('App\Models\SurveyMasterQuestion','survey_question_id','id')->withTrashed();
    }
}
