<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class StaticRelease extends Model
{
    use SoftDeletes;
use HasTranslations,LogsActivity;
    protected $fillable = [
        'active',
        'bugs_fix',
        'date',
        'features',
        'order_index',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'bugs_fix',
        'features',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['active','bugs_fix','date','features','order_index'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/static-releases/'.$this->getKey());
    }
}
