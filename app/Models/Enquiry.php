<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Enquiry extends Model
{
    use SoftDeletes,LogsActivity;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'ticket_id',
        'priority',
        'status',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = [ 'name','email','phone','subject','message'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/enquiries/'.$this->getKey());
    }

    public function user(){
        return $this->belongsTo('App\Models\Subscriber','phone','phone_no');
    }
}
