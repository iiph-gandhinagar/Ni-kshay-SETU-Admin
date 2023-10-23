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

class Symptom extends Model implements HasMedia
{
    use SoftDeletes;
    use ProcessMediaTrait;
    use AutoProcessMediaTrait;
    use HasMediaCollectionsTrait;
    use HasMediaThumbsTrait;
    use HasTranslations,LogsActivity;

    protected $fillable = [
        'category',
        'symptoms_title',

    ];


    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',

    ];
    // these attributes are translatable
    public $translatable = [
        'symptoms_title',

    ];

    protected $appends = ['resource_url'];
    protected static $logAttributes = [ 'category','symptoms_title'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/symptoms/' . $this->getKey());
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->autoRegisterThumb200();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('symptoms_image')
            ->useDisk('s3')
            ->accepts('image/*')
            ->maxNumberOfFiles(1);
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
