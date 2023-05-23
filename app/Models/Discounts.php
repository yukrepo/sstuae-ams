<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Discounts extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'title', 'start_date', 'end_date', 'time_slots', 'value', 'status_id', 'location_id', 'admin_id', 'consultation', 'treatment', 'pharmacy', 'pcourse', 'dcourse'
    ];

    public function location()
    {
        return $this->belongsTo('App\Models\Locations', 'location_id');
    }

}
