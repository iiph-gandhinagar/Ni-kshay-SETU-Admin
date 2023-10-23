<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class TSubModuleMaster extends Model
{
    use SoftDeletes,LogsActivity;
    protected $table = 't_sub_module_master';

    protected $fillable = [
        'name',
        'module_id',
        'existing_module_ref',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = [ 'name','module_id','existing_module_ref'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/t-sub-module-masters/'.$this->getKey());
    }

    public function modules()
    {
        return $this->belongsTo('App\Models\TModuleMaster', 'module_id', 'id');
    }
}
