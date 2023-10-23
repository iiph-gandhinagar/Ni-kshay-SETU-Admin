<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class SurveyMaster extends Model
{
    use SoftDeletes;
use HasTranslations,LogsActivity;
    protected $table = 'survey_master';

    protected $fillable = [
        'title',
        'country_id',
        'cadre_id',
        'state_id',
        'district_id',
        'cadre_type',
        'order_index',
        'active',
        'send_initial_notification',
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'title',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['title','country_id','cadre_id','state_id','district_id','cadre_type','active','order_index','send_initial_notification'];
    protected static $logOnlyDirty = true;


    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/survey-masters/'.$this->getKey());
    }

    public function survey_history(){
        return $this->hasMany('App\Models\SurveyMasterHistory','survey_id','id');
    }
}
