<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Stocks extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'medicine_id', 'stock', 'manufecturer_id', 'exp_date', 'barcode', 'packsize', 'batch_no', 'location_id', 'foc', 'purchase_id', 'purchase_cost', 'received_stock', 'adjustments', 'reason'
    ];

    public function medicine()
    {
        return $this->belongsTo('App\Models\Medicines');
    }
}
