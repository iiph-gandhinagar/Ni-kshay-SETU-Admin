<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class HealthFacility extends Model
{
    use SoftDeletes,LogsActivity;
    protected $fillable = [
        'country_id',
        'state_id',
        'district_id',
        'block_id',
        'health_facility_code',
        'DMC',
        'TRUNAT',
        'CBNAAT',
        'X_RAY',
        'ICTC',
        'LPA_Lab',
        'CONFIRMATION_CENTER',
        'Tobacco_Cessation_clinic',
        'ANC_Clinic',
        'Nutritional_Rehabilitation_centre',
        'De_addiction_centres',
        'ART_Centre',
        'District_DRTB_Centre',
        'NODAL_DRTB_CENTER',
        'IRL',
        'Pediatric_Care_Facility',
        'longitude',
        'latitude',
    
    ];
    
    
    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    
    ];
    
    protected $appends = ['resource_url'];
    protected static $logAttributes = [ 'country_id','state_id','district_id','block_id','health_facility_code','DMC','TRUNAT','CBNAAT','X_RAY','ICTC','LPA_Lab','CONFIRMATION_CENTER','Tobacco_Cessation_clinic','ANC_Clinic','Nutritional_Rehabilitation_centre','De_addiction_centres','ART_Centre','District_DRTB_Centre','NODAL_DRTB_CENTER','IRL','Pediatric_Care_Facility','longitude','latitude'];
    protected static $logOnlyDirty = true;

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/health-facilities/'.$this->getKey());
    }

    public function state(){
        return $this->belongsTo('App\Models\State','state_id','id');
    }

    public function district(){
        return $this->belongsTo('App\Models\District','district_id','id');
    }

    public function block(){
        return $this->belongsTo('App\Models\Block','block_id','id');
    }

    public function country(){
        return $this->belongsTo('App\Models\Country','country_id','id');
    }
}
