<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class Assessment extends Model
{
    use SoftDeletes,LogsActivity;
    use HasTranslations;
    protected $fillable = [
        'time_to_complete',
        'country_id',
        'cadre_id',
        'state_id',
        'assessment_title',
        'assessment_type',
        'from_date',
        'to_date',
        'initial_invitation',
        'activated',
        'district_id',
        'cadre_type',
        'created_by',
        'certificate_type',
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'assessment_title',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['time_to_complete', 'country_id','cadre_id','state_id','assessment_title','district_id','cadre_type'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/assessments/'.$this->getKey());
    }
    
    public function assessment_questions()
    {
        return $this->hasMany('App\Models\AssessmentQuestion','assessment_id','id');
    }

    public function user_assessment_result()
    {
        return $this->hasMany('App\Models\UserAssessment','assessment_id','id');
    }

    public function user(){
        return $this->belongsTo('Brackets\AdminAuth\Models\AdminUser','created_by','id')->with('roles');
    }

    public function user_with_trashed(){
        return $this->belongsTo('Brackets\AdminAuth\Models\AdminUser','created_by','id')->with('roles')->withTrashed();
    }

    public function assessment_certificate(){
        return $this->belongsTo("App\Models\AssessmentCertificate",'certificate_type','id');
    }

}