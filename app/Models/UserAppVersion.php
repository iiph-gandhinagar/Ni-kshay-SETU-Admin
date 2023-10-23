<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAppVersion extends Model
{
    use SoftDeletes;
    protected $table = 'user_app_version';

    protected $fillable = [
        'user_id',
        'user_name',
        'app_version',
        'current_plateform',
        'has_ios',
        'has_android',
        'has_web'
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
        return url('/admin/user-app-versions/'.$this->getKey());
    }

    public function user(){
        return $this->belongsTo('App\Models\Subscriber','user_id','id');
    }
}
