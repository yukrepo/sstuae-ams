<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class UserDocuments extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'user_id', 'insurance_id', 'policy_number', 'insurance_copy','insurance_verified','identity_type_id','verification_number', 'identity_copy','identity_verified', 'pharmacy_deductable', 'consult_deductable', 'treatment_deductable', 'co_insurance', 'effective_date', 'expiry_date', 'concern_form', 'concern_form_status', 'concern_form_verify_date'
    ];

    public function users()
    {
        return $this->belongsTo('App\Models\Users');
    }

    public function insurance()
    {
        return $this->belongsTo('App\Models\Insurances');
    }

    public function identity_type()
    {
        return $this->belongsTo('App\Models\IdentityTypes');
    }

}
