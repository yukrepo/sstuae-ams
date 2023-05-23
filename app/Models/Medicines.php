<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Medicines extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'category_id', 'barcode', 'unitsize', 'description', 'insurance_price', 'cash_price', 'status_id'
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\Categories');
    }

    public function stocks()
    {
        return $this->hasMany('App\Models\Stocks', 'medicine_id');
    }

    public function stocks_sum()
    {
        return $this->hasMany('App\Models\Stocks', 'medicine_id')->sum('stock');
    }
}
