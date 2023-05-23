<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class Rooms extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'room_name', 'location_id', 'remark', 'status_id'
    ];

}
