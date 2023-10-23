<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Cadre extends Model
{
    use LogsActivity;
    protected $table = 'cadre';

    protected $fillable = [
        'title',
        'cadre_type',
    ];
    
    
    protected $dates = [
    
    ];
    public $timestamps = false;
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = ['title','cadre_type'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/cadres/'.$this->getKey());
    }
}
