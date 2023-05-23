<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Treatments extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'treatment', 'remark', 'is_it_dual', 'timing', 'cost', 'tax', 'points', 'spoints', 'appointment_type_id', 'status_id'
    ];

    public function type()
    {
        return $this->belongsTo('App\Models\AppointmentTypes', 'appointment_type_id');
    }

}
