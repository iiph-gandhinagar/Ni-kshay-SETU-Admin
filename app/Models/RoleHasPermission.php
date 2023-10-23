<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class RoleHasPermission extends Model
{
    use LogsActivity;
    protected $primaryKey = 'permission_id';

    protected $fillable = [
        'permission_id',
        'role_id',
    
    ];
    
    
    protected $dates = [
    
    ];
    public $timestamps = false;
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = [ 'permission_id','role_id'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/role-has-permissions/'.$this->getKey());
    }

    public function permission(){
        return $this->belongsTo('App\Models\Permission','permission_id','id');
    }

    public function role(){
        return $this->belongsTo('App\Models\Role','role_id','id');
    }
}
