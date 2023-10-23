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

class CgcIntervention extends Model implements HasMedia
{
    use SoftDeletes;
    use ProcessMediaTrait;
    use AutoProcessMediaTrait;
    use HasMediaCollectionsTrait;
    use HasMediaThumbsTrait;

    protected $fillable = [
        'chapter_title',
        'video_title',
        'description',
        'time_spent',
        'assessment_id',
        'reference_title',
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/cgc-interventions/'.$this->getKey());
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('chapter_video')
            ->useDisk('s3')
            ->accepts('video/mp4','video/x-m4v')
            ->maxFilesize(1*1024*1024*1024)
            ->maxNumberOfFiles(10);

        $this->addMediaCollection('reference_links')
            ->useDisk('s3')
            ->accepts('application/pdf')
            ->maxFilesize(100*1024*1024)
            ->maxNumberOfFiles(1);

        $this->addMediaCollection('video_image')
            ->useDisk('s3')
            ->accepts('image/*')
            ->maxFilesize(50*1024*1024)
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
                ->width(200)
                ->height(200)
                ->fit('crop', 1176, 662)
                ->optimize()
                ->performOnCollections($mediaCollection->getName())
                ->nonQueued();
        });
    }

    public function assessment(){
        return $this->belongsTo('App\Models\Assessment','assessment_id','id');
    }
}
