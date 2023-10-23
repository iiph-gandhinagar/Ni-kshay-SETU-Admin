<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAssessmentAnswer extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'assessment_id',
        'user_id',
        'question_id',
        'answer',
        'is_correct',
        'is_submit',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];

    public function assessment_question(){
        return $this->belongsTo('App\Models\AssessmentQuestion','question_id','id')->withTrashed();
    }
}
