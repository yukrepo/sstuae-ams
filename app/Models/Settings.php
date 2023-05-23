<?php

namespace App\Models;

use DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['value'];

}
