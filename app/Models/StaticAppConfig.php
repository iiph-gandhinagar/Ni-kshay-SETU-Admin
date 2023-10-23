<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class StaticAppConfig extends Model
{
    use SoftDeletes;
use HasTranslations,LogsActivity;
    protected $table = 'static_app_config';

    protected $fillable = [
        'key',
        'type',
        'value_json',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'value_json',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['key','type','value_json'];
    protected static $logOnlyDirty = true;


    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/static-app-configs/'.$this->getKey());
    }
}
