<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class AppointmentTypes extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'appointment_type', 'status_id'
    ];

}
