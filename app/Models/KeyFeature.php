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

class KeyFeature extends Model implements HasMedia
{
    use SoftDeletes;
    use HasTranslations;
    use ProcessMediaTrait;
    use AutoProcessMediaTrait;
    use HasMediaCollectionsTrait;
    use HasMediaThumbsTrait;

    protected $fillable = [
        'active',
        'description',
        'order_index',
        'title',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at',
    
    ];
    // these attributes are translatable
    public $translatable = [
        'description',
        'title',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/key-features/'.$this->getKey());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')
            ->useDisk('s3')
            ->accepts('image/*')
            ->maxFilesize(1 * 1024 * 1024 * 1024)
            ->maxNumberOfFiles(1);

        $this->addMediaCollection('icon_bg')
            ->useDisk('s3')
            ->accepts('image/*')
            ->maxFilesize(1 * 1024 * 1024 * 1024)
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
    }
}
