<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class Tour extends Model
{
    use SoftDeletes;
    use HasTranslations,LogsActivity;
    protected $fillable = [
        'title',
        'active',
        'default',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'title',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['title','active','default'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/tours/'.$this->getKey());
    }
}
