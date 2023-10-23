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

class TourSlide extends Model implements HasMedia
{
    use SoftDeletes;
    use HasTranslations;
    use ProcessMediaTrait;
    use AutoProcessMediaTrait;
    use HasMediaCollectionsTrait;
    use HasMediaThumbsTrait,LogsActivity;

    protected $fillable = [
        'tour_id',
        'title',
        'description',
        'type',
    
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
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['tour_id','title','description','type'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/tour-slides/'.$this->getKey());
    }

    public function tour(){
        return $this->belongsTo('App\Models\Tour','tour_id','id');
    }

    public function registerMediaCollections(): void {
        
      $this->addMediaCollection('tour_image')
            ->useDisk('s3')
            ->maxFilesize(20*1024*1024)
            ->accepts('image/*')
            ->maxNumberOfFiles(1);

        $this->addMediaCollection('tour_video')
            ->useDisk('s3')
            ->maxFilesize(20*1024*1024)
            ->accepts('video/mp4','video/x-m4v','video/gif')
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
