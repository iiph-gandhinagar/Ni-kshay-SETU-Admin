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
use Spatie\Activitylog\Traits\LogsActivity;

class FlashSimilarApp extends Model implements HasMedia
{
    use SoftDeletes;
    use ProcessMediaTrait;
    use AutoProcessMediaTrait;
    use HasMediaCollectionsTrait;
    use HasMediaThumbsTrait,LogsActivity;
    protected $fillable = [
        'title',
        'sub_title',
        'href',
        'href_web',
        'href_ios',
        'order_index',
        'active',
    
    ];
    
    
    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['title','sub_title','href','href_web','href_ios','order_index','active'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/flash-similar-apps/'.$this->getKey());
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('flash_app_icon')
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
