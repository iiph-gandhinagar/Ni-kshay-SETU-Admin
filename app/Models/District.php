<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class District extends Model
{
    use SoftDeletes,LogsActivity;
    protected $fillable = [
        'state_id',
        'title',
        'country_id',
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = [ 'state_id','title','country_id'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/districts/'.$this->getKey());
    }

    public function state(){
        return $this->belongsTo('App\Models\State','state_id','id');
    }

    public function country(){
        return $this->belongsTo('App\Models\Country','country_id','id');
    }
}
