<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDeviceToken extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_device_tokens';
    protected $primaryKey = 'id';

    protected $fillable = ['user_id', 'device_id', 'notification_token', 'is_active'];
    protected $dates = ['deleted_at'];
}
