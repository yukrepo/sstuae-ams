<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'clinic_name', 'city', 'address', 'status_id', 'lat', 'lng'
    ];

}
