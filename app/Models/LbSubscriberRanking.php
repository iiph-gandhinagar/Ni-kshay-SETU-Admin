<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LbSubscriberRanking extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'subscriber_id',
        'level_id',
        'badge_id',
        'mins_spent_count',
        'sub_module_usage_count',
        'App_opended_count',
        'chatbot_usage_count',
        'resource_material_accessed_count',
        'total_task_count',

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
        return url('/admin/lb-subscriber-rankings/' . $this->getKey());
    }

    public function lb_level()
    {
        return $this->belongsTo('App\Models\LbLevel', 'level_id', 'id')->withTrashed();
    }

    public function lb_badge()
    {
        return $this->belongsTo('App\Models\LbBadge', 'badge_id', 'id')->withTrashed();
    }

    public function lb_task_list()
    {
        return $this->belongsTo('App\Models\LbTaskList', 'badge_id', 'badges')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Subscriber', 'subscriber_id', 'id');
    }
}
