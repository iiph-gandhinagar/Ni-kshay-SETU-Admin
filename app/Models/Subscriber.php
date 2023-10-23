<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Brackets\Media\HasMedia\ProcessMediaTrait;
use Brackets\Media\HasMedia\AutoProcessMediaTrait;
use Brackets\Media\HasMedia\HasMediaCollectionsTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Brackets\Media\HasMedia\HasMediaThumbsTrait;

use Spatie\Activitylog\Traits\LogsActivity;
use Config;

class Subscriber extends Model implements HasMedia
{
    use SoftDeletes, Notifiable, LogsActivity;
    use ProcessMediaTrait;
    use AutoProcessMediaTrait;
    use HasMediaCollectionsTrait;
    use HasMediaThumbsTrait;
    protected $fillable = [
        'api_token',
        'name',
        'phone_no',
        'password',
        'cadre_type',
        'is_verified',
        'cadre_id',
        'country_id',
        'block_id',
        'district_id',
        'state_id',
        'health_facility_id',
    ];

    protected $hidden = ['password'];

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    protected $appends = ['resource_url'];
    protected static $logAttributes = [
        'phone_no',
        'cadre_type',
        'cadre_id',
        'country_id',
        'block_id',
        'district_id',
        'state_id',
        'health_facility_id',
    ];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/subscribers/' . $this->getKey());
    }

    public function state()
    {
        return $this->belongsTo('App\Models\State', 'state_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo('App\Models\District', 'district_id', 'id');
    }

    public function block()
    {
        return $this->belongsTo('App\Models\Block', 'block_id', 'id');
    }

    public function health_facility()
    {
        return $this->belongsTo(
            'App\Models\HealthFacility',
            'health_facility_id',
            'id'
        );
    }

    public function cadre()
    {
        return $this->belongsTo('App\Models\Cadre', 'cadre_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id');
    }

    public function routeNotificationForSlack($notification)
    {
        return Config::get('app.GENERAL.slack_notification_webhook');
    }

    public function user_app_version()
    {
        return $this->hasOne('App\Models\UserAppVersion', 'user_id', 'id');
    }

    public function subscriber_activities()
    {
        return $this->hasMany(
            'App\Models\SubscriberActivity',
            'user_id',
            'id'
        )->where('action', 'user_home_page_visit');
    }

    public function lb_subscriber_rankings()
    {
        return $this->hasOne(
            'App\Models\LbSubscriberRanking',
            'subscriber_id',
            'id'
        );
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->autoRegisterThumb200();
    }

    // public function registerMediaCollections(): void
    // {
    //     $this->addMediaCollection('profile_image')
    //         // ->useDisk('s3')
    //         ->maxFilesize(20 * 1024 * 1024)
    //         ->accepts('image/*')
    //         ->maxNumberOfFiles(1);
    // }
    public function autoRegisterThumb200()
    {
        // $this->getMediaCollections()
        //     ->filter->isImage()
        //     ->each(function ($mediaCollection) {
        //         $this->addMediaConversion('thumb_100')
        //             ->width(100)
        //             ->height(100)
        //             ->fit('crop', 100, 100)
        //             ->optimize()
        //             ->performOnCollections('profile_image')
        //             ->nonQueued();
        //     });
        //video image thumb/
        $this->addMediaConversion('thumb_60')
            ->width(60)
            ->height(60)
            ->fit('crop', 60, 60)
            ->optimize()
            ->performOnCollections('profile_image')
            ->nonQueued();

        $this->addMediaConversion('thumb_100')
            ->width(100)
            ->height(100)
            ->fit('crop', 100, 100)
            ->optimize()
            ->performOnCollections('profile_image')
            ->nonQueued();
    }
}
