<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class InsuranceMedicinesMaps extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'insurance_id', 'medicine_id', 'discount', 'extra', 'publish'
    ];

}
