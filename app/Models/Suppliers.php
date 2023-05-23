<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'contact_no', 'company_name', 'email', 'city', 'address', 'connection_source', 'remark', 'status_id'
    ];

}
