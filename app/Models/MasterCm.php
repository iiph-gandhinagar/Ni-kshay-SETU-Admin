<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class MasterCm extends Model
{
    use SoftDeletes;
use HasTranslations,LogsActivity;
    protected $fillable = [
        'title',
        'description',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'description',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = [  'title','description'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/master-cms/'.$this->getKey());
    }
}
