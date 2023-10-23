<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ModuleMappingToName extends Model
{
    use SoftDeletes,LogsActivity;
    protected $fillable = [
        'module_name',
        'mapping_name',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = [ 'module_name','mapping_name'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/module-mapping-to-names/'.$this->getKey());
    }
}
