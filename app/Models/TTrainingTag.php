<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class TTrainingTag extends Model
{
    use SoftDeletes;
    use HasTranslations,LogsActivity;
    protected $table = 't_training_tag';

    protected $fillable = [
        'id',
        'tag',
        'pattern',
        'is_fix_response',
        'like_count',
        'dislike_count',
        'response',
        'questions',
        'modules',
        'sub_modules',
        'resource_material',
        'count',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'response',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = [  'id','tag', 'pattern','is_fix_response','like_count','dislike_count','response', 'questions','modules', 'sub_modules','resource_material'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/t-training-tags/'.$this->getKey());
    }
}
