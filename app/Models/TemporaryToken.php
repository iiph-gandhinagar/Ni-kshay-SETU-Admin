<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemporaryToken extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'temporary_tokens';
    protected $primaryKey = 'id';

    protected $fillable = ['temp_token','phone_no','user_id','name'];
    protected $dates = ['deleted_at'];
}
