<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class AppConfig extends Model
{
    use SoftDeletes,LogsActivity;
    use HasTranslations;
    protected $table = 'app_config';

    protected $fillable = [
        'key',
        'value_json',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'value_json',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = [ 'key','value_json'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/app-configs/'.$this->getKey());
    }
}
