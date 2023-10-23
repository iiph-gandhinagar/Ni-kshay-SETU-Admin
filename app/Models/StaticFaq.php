<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class StaticFaq extends Model
{
    use SoftDeletes,LogsActivity;
use HasTranslations;
    protected $table = 'static_faq';

    protected $fillable = [
        'active',
        'description',
        'order_index',
        'question',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'description',
        'question',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['active','description','order_index','question'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/static-faqs/'.$this->getKey());
    }
}
