<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Translatable\Traits\HasTranslations;
use Brackets\Media\HasMedia\ProcessMediaTrait;
use Brackets\Media\HasMedia\AutoProcessMediaTrait;
use Brackets\Media\HasMedia\HasMediaCollectionsTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Brackets\Media\HasMedia\HasMediaThumbsTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class CgcInterventionsAlgorithm extends Model implements HasMedia
{
    use SoftDeletes;
    use ProcessMediaTrait;
    use AutoProcessMediaTrait;
    use HasMediaCollectionsTrait;
    use HasMediaThumbsTrait;
    use HasTranslations,LogsActivity;
    protected $fillable = [
        'title',
        'node_type',
        'is_expandable',
        'has_options',
        'parent_id',
        'description',
        'time_spent',
        'index',
        'redirect_algo_type',
        'redirect_node_id',
        'header',
        'sub_header',
        'activated',
        'master_node_id',
        'state_id',
        'cadre_id',
        'send_initial_notification',
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'title',
        'description',
        'header',
        'sub_header',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = [ 'title','node_type','is_expandable','has_options','parent_id','description','index','redirect_algo_type','redirect_node_id','header','sub_header','activated'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/cgc-interventions-algorithms/'.$this->getKey());
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\CgcInterventionsAlgorithm','master_node_id','id');
    }

    public function child()
    {
      return $this->hasMany('App\Models\CgcInterventionsAlgorithm', 'parent_id', 'id')->orderBy('index');
    }

    public function children()
    {
      return $this->child()->with(['children','media'])->where('activated',1);
    }

    public function registerMediaCollections(): void {
      $this->addMediaCollection('node_icon')
            ->useDisk('s3')
            ->maxFilesize(20*1024*1024)
            ->accepts('image/*')
            ->maxNumberOfFiles(1);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->autoRegisterThumb200();
    }

    public function autoRegisterThumb200()
    {
        $this->getMediaCollections()->filter->isImage()->each(function ($mediaCollection) {
            $this->addMediaConversion('thumb_200')
                ->width(27)
                ->height(27)
                ->fit('crop', 27, 27)
                ->optimize()
                ->performOnCollections($mediaCollection->getName())
                ->nonQueued();                
        });
    }
}
