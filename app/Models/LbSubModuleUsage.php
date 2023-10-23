<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LbSubModuleUsage extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'subscriber_id',
        'module_id',
        'sub_module',
        'total_time',
        'mins_spent',
        'completed_flag',
    
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
        return url('/admin/lb-sub-module-usages/'.$this->getKey());
    }

    public function user(){
        return $this->belongsTo('App\Models\Subscriber','subscriber_id','id');
    }
}