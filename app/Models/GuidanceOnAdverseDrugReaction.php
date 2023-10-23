<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Brackets\Media\HasMedia\ProcessMediaTrait;
use Brackets\Media\HasMedia\AutoProcessMediaTrait;
use Brackets\Media\HasMedia\HasMediaCollectionsTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Brackets\Media\HasMedia\HasMediaThumbsTrait;
use Brackets\Translatable\Traits\HasTranslations;
use Spatie\Activitylog\Traits\LogsActivity;

class GuidanceOnAdverseDrugReaction extends Model implements HasMedia
{
    use SoftDeletes;
    use ProcessMediaTrait;
    use AutoProcessMediaTrait;
    use HasMediaCollectionsTrait;
    use HasMediaThumbsTrait;
    use HasTranslations,LogsActivity;
    protected $fillable = [
        'node_type',
        'is_expandable',
        'has_options',
        'parent_id',
        'index',
        'title',
        'description',
        'time_spent',
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
    protected static $logAttributes = [ 'node_type','is_expandable','has_options','parent_id','index','title','description','redirect_algo_type','redirect_node_id','header','sub_header','activated'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/guidance-on-adverse-drug-reactions/' . $this->getKey());
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\GuidanceOnAdverseDrugReaction','master_node_id','id');
    }

    public function child()
    {
        return $this->hasMany('App\Models\GuidanceOnAdverseDrugReaction', 'parent_id', 'id')->orderBy('index');
    }

    public function children()
    {
        return $this->child()->with(['children', 'media'])->where('activated',1);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('node_icon')
            ->useDisk('s3')
            ->maxFilesize(20 * 1024 * 1024)
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
                ->width(48)
                ->height(48)
                ->fit('crop', 48, 48)
                ->optimize()
                ->performOnCollections($mediaCollection->getName())
                ->nonQueued();
        });
    }
}
