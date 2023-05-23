<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class UserDevices extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'user_id', 'deviceId', 'device_type', 'token','notification','extra', 'app_version', 'os', 'model'];

    public function users()
    {
        return $this->belongsTo('App\Models\Users');
    }

}
