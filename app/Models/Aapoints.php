<?php

namespace App\Models;

use DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class Aapoints extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['apdate', 'uid', 'status'];

}
