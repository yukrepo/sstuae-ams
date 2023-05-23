<?php

namespace App\Models;

use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Users extends Authenticatable
{
    use Notifiable,HasApiTokens;

    protected $fillable = [
        'username', 'mobile', 'status_id', 'password', 'api_token', 'source', 'admin_id', 'ref_no'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function user_profile()
    {
        return $this->hasOne('App\Models\UserProfiles', 'user_id')->with('nationality');
    }

    public function user_document()
    {
        return $this->hasOne('App\Models\UserDocuments', 'user_id')->with('insurance')->with('identity_type');
    }

    public function user_device()
    {
        return $this->hasOne('App\Models\UserDevices', 'user_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Statuses', 'status_id');
    }

    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    public function setNewApiToken()
    {
        $this->api_token = Str::random(60);
        $this->save();
    }
}
