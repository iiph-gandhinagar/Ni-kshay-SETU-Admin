<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class AppManagementFlag extends Model
{
use HasTranslations,LogsActivity;
    protected $fillable = [
        'variable',
        'value',
        'type',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'value',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = [ 'variable','value','type'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/app-management-flags/'.$this->getKey());
    }
}
