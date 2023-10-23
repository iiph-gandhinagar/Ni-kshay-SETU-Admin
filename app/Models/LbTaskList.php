<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class LbTaskList extends Model
{
    use SoftDeletes,LogsActivity;
    protected $fillable = [
        'level',
        'badges',
        'mins_spent',
        'sub_module_usage_count',
        'App_opended_count',
        'chatbot_usage_count',
        'resource_material_accessed_count',
        'total_task',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['level','badges','mins_spent','sub_module_usage_count','App_opended_count','chatbot_usage_count','resource_material_accessed_count','total_task'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/lb-task-lists/'.$this->getKey());
    }

    public function lb_level(){
        return $this->belongsTo('App\Models\LbLevel','level','id')->withTrashed();
    }

    public function lb_badge(){
        return $this->belongsTo('App\Models\LbBadge','badges','id')->withTrashed();
    }
}
