<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class ChatKeyword extends Model
{
    use SoftDeletes,LogsActivity;
use HasTranslations;
    protected $fillable = [
        'title',
        'hit',
        'modules',
        'sub_modules',
        'resource_material',
        'custom_ordering',
    
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
    protected static $logAttributes = ['title','hit','modules','sub_modules','resource_material','custom_ordering'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/chat-keywords/'.$this->getKey());
    }
}
