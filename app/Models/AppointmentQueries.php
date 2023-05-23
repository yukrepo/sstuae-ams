<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class AppointmentQueries extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'location_id', 'user_id', 'apid', 'status_id', 'appointment_type', 'appointment_reason', 'date', 'timeframe', 'remark', 'doctor_name'
    ];
}
