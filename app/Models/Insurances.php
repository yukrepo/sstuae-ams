<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class Insurances extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'contact_no', 'followup_days', 'remark', 'status_id', 'cash_discount', 'approval_days'
    ];

    public function insured_medicines()
    {
        return $this->hasMany('App\Models\InsuranceMedicineMaps', 'insurance_id');
    }

    public function insured_consultation()
    {
        return $this->hasMany('App\Models\InsuranceConsultationMaps', 'insurance_id');
    }

    public function insured_treatments()
    {
        return $this->hasMany('App\Models\InsuranceTreatmentMaps', 'insurance_id');
    }

}
