<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class ChatQuestion extends Model
{
    use SoftDeletes;
    use HasTranslations,LogsActivity;
    protected $fillable = [
        'question',
        'answer',
        'hit',
        'cadre_id',
        'category',
        'activated',
        'like_count',
        'dislike_count',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'question',
        'answer',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['question','answer','hit','cadre_id','category','activated','like_count','dislike_count'];
    protected static $logOnlyDirty = true;


    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/chat-questions/'.$this->getKey());
    }
    
    public function keywords(){
        return $this->hasMany('App\Models\ChatQuestionKeyword','question_id','id');
    }

    public function question_keywords(){
        return $this->hasMany('App\Models\ChatQuestionKeyword','question_id','id');
    }
}
