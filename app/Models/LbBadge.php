<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class LbBadge extends Model
{
    use SoftDeletes;
use HasTranslations,LogsActivity;
    protected $fillable = [
        'level_id',
        'badge',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'badge',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['level_id','badge'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/lb-badges/'.$this->getKey());
    }

    public function lb_level(){
        return $this->belongsTo('App\Models\LbLevel','level_id','id')->withTrashed();
    }
}
