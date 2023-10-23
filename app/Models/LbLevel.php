<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class LbLevel extends Model
{
    use SoftDeletes;
use HasTranslations,LogsActivity;
    protected $fillable = [
        'level',
        'content',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'level',
        'content',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['level','content'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/lb-levels/'.$this->getKey());
    }
}
