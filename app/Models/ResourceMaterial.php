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

class ResourceMaterial extends Model  implements HasMedia
{
    use SoftDeletes,LogsActivity;
    use HasTranslations;
    use ProcessMediaTrait;
    use AutoProcessMediaTrait;
    use HasMediaCollectionsTrait;
    use HasMediaThumbsTrait;
    protected $fillable = [
        'title',
        'type_of_materials',
        'country_id',
        'state',
        'cadre',
        'parent_id',
        'icon_type',
        'index',
        'created_by',
    
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
     protected static $logAttributes = [  'title','type_of_materials','country_id','state', 'cadre','parent_id', 'icon_type','index', 'created_by'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/resource-materials/'.$this->getKey());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('material')
            ->useDisk('s3')
            ->maxFilesize(1 * 1024 * 1024 * 1024)
            ->maxNumberOfFiles(1);

        $this->addMediaCollection('video_thumb')
            ->useDisk('s3')
            ->accepts('image/*')
            ->maxFilesize(100 * 1024 * 1024)
            ->maxNumberOfFiles(1);
    }

    /**
     * Register media conversions
     *
     * @param Media|null $media
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->autoRegisterThumb200();
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\ResourceMaterial', 'parent_id', 'id');
    }

    public function parent_folder()
    {
        return $this->parent()->with(['parent_folder']);
    }

    public function parent_master()
    {
        return $this->belongsTo('App\Models\ResourceMaterial', 'parent_id', 'id');
    }

    public function user(){
        return $this->belongsTo('Brackets\AdminAuth\Models\AdminUser','created_by','id')->with('roles');
    }

    public function autoRegisterThumb200()
    {
        $this->getMediaCollections()->filter->isImage()->each(function ($mediaCollection) {
            $this->addMediaConversion('thumb_200')
                ->width(200)
                ->height(200)
                ->fit('crop', 200, 200)
                ->optimize()
                ->performOnCollections($mediaCollection->getName())
                ->nonQueued();
        });
        //video image thumb/
        $this->addMediaConversion('thumb_200')
            ->width(96)
            ->height(56)
            ->fit('crop', 96, 56)
            ->optimize()
            ->performOnCollections('video_thumb')
            ->nonQueued();
    }
}
