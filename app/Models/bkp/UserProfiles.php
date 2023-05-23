<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class UserProfiles extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'date_of_birth', 'married', 'gender','email','contact_no','address', 'city','zipcode', 'country_id', 'nationality_id', 'company_name', 'image'
    ];

    public function users()
    {
        return $this->belongsTo('App\Models\Users');
    }

    public function nationality()
    {
        return $this->belongsTo('App\Models\Nationalities');
    }

}
