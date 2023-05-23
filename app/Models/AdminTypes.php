<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class AdminTypes extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'type', 'permissions', 'status_id'
    ];
}
