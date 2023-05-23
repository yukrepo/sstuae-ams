<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class InsuranceConsultationMaps extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'insurance_id', 'treatment_id', 'discount', 'extra', 'publish'
    ];

    public function treatment()
    {
        return $this->belongsTo('App\Models\Treatments', 'treatment_id');
    }

}
