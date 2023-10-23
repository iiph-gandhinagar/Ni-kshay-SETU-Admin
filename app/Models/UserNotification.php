<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class UserNotification extends Model
{
    use SoftDeletes,LogsActivity;
    protected $fillable = [
        'title',
        'description',
        'type',
        'user_id',
        'country_id',
        'state_id',
        'district_id',
        'cadre_type',
        'cadre_id',
        'is_deeplinking',
        'automatic_notification_type',
        'type_title',
        'successful_count',
        'failed_count',
        'status',
        'created_by',
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = [  'title','description','type','user_id','country_id', 'state_id', 'district_id','cadre_type','cadre_id'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/user-notifications/'.$this->getKey());
    }

    public function admin_user(){
        return $this->belongsTo('Brackets\AdminAuth\Models\AdminUser','created_by','id');
    }
}
