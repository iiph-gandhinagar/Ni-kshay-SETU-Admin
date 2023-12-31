<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentEnrollment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'assessment_id',
        'user_id',
        'response',
        'send_inital_invitation',
    
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
        return url('/admin/assessment-enrollments/'.$this->getKey());
    }
}
