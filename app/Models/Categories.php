<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'remark', 'unit', 'status_id'
    ];

    public function medicines()
    {
        return $this->hasMany('App\Models\Medicines', 'category_id');
    }
}
