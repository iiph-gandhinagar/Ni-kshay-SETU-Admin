<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class State extends Model
{
    use SoftDeletes,LogsActivity;
    protected $table = 'state';

    protected $fillable = [
        'title',
        'country_id',
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = [ 'title','country_id'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/states/'.$this->getKey());
    }

    public function country(){
        return $this->belongsTo('App\Models\Country','country_id','id');
    }
}
