<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class Doctors extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'contact_no', 'email', 'location_id', 'gender', 'designation_type_id', 'status_id', 'about', 'qualification'
    ];

    public function designation_type()
    {
        return $this->belongsTo('App\Models\DesignationTypes', 'designation_type_id');
    }

}
