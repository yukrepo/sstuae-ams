<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class InsuranceMedicineMaps extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'insurance_id', 'medicine_id', 'discount', 'extra', 'publish'
    ];

    public function Medicine()
    {
        return $this->belongsTo('App\Models\Medicines', 'medicine_id');
    }

}
