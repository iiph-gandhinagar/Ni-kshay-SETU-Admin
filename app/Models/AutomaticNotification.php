<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutomaticNotification extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'description',
        'linking_url',
        'subscriber_id',
        'title',
        'type',
        'created_by',
        'successful_count',
        'failed_count',
        'status',
    ];
    
    
    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/automatic-notifications/'.$this->getKey());
    }

    public function admin_user(){
        return $this->belongsTo('Brackets\AdminAuth\Models\AdminUser','created_by','id');
    }
}
