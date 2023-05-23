<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
                    'sale_id', 'purchase_id', 'payment_type', 'firstpay_mode', 'firstpay_amount', 'firstpay_date', 'firstpay_receiver', 'secondpay_mode', 'secondpay_amount', 'secondpay_date', 'secondpay_receiver', 'secondpay_desc', 'firstpay_desc', 'pay_status'
            ];

    public function sales()
    {
        return $this->belongsTo('App\Models\Sales');
    }

    public function purchases()
    {
        return $this->belongsTo('App\Models\Purchases');
    }

}
