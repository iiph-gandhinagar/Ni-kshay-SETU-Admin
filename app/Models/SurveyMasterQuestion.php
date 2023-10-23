<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class SurveyMasterQuestion extends Model
{
    use SoftDeletes;
use HasTranslations,LogsActivity;
    protected $fillable = [
        'active',
        'option1',
        'option2',
        'option3',
        'option4',
        'order_index',
        'question',
        'survey_master_id',
        'type',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'option1',
        'option2',
        'option3',
        'option4',
        'question',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['active','option1','option2','option3','option4','order_index','question','survey_master_id','type'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/survey-master-questions/'.$this->getKey());
    }

    public function survey_master(){
        return $this->belongsTo('App\Models\SurveyMaster','survey_master_id','id')->withTrashed();
    }
}
