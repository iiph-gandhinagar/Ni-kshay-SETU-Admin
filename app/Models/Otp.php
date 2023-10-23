<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Otp extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'otp_request';
    protected $primaryKey = 'id';

    protected $fillable = ['phone_no','user_id','otp','is_verified','message_body','is_delivered','via'];
    protected $dates = ['deleted_at'];

}
