<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlashNewsWebsiteContent extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'source',
        'href',
        'author',
        'publish_date',
    
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
        return url('/admin/flash-news-website-contents/'.$this->getKey());
    }
}
