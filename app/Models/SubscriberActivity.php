<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriberActivity extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'action',
        'ip_address',
        'plateform',
        'payload',
    
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
        return url('/admin/subscriber-activities/'.$this->getKey());
    }

    public function user(){
        return $this->belongsTo('App\Models\Subscriber','user_id','id');
    }
}
