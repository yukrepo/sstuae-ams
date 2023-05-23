<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'category_id', 'model_no', 'image', 'description'
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\Categories');
    }

    public function stocks()
    {
        return $this->hasMany('App\Models\Stocks', 'product_id');
    }
}
