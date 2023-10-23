<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Screening extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'screening';
    protected $primaryKey = 'id';

    protected $fillable = ['user_id','age','weight','height','symptoms_selected','is_tb','symptoms_name'];
    protected $dates = ['deleted_at'];
}
