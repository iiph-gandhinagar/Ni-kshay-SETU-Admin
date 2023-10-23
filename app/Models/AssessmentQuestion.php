<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class AssessmentQuestion extends Model
{
    use SoftDeletes,LogsActivity;
use HasTranslations;
    protected $fillable = [
        'assessment_id',
        'question',
        'option1',
        'option2',
        'option3',
        'option4',
        'correct_answer',
        'order_index',
        'category'
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'question',
        'option1',
        'option2',
        'option3',
        'option4',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['assessment_id','question','option1','option2','option3','option4','correct_answer','order_index'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/assessment-questions/'.$this->getKey());
    }
    
    public function assessment(){
        return $this->belongsTo('App\Models\Assessment','assessment_id','id');
    }

    public function assessment_with_trashed(){
        return $this->belongsTo('App\Models\Assessment','assessment_id','id')->withTrashed();
    }
}
