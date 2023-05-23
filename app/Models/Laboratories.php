<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class Laboratories extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'value', 'lab_category_id', 'lab_range', 'remark', 'status_id'
    ];

}
