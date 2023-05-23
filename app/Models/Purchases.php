<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Purchases extends Model
{
   use HasApiTokens, Notifiable;

    protected $fillable = [
        'added_by', 'location_id', 'invoice_number', 'supplier_id', 'sub_total', 'discount_desc', 'discount_percent', 'discount_amount', 'tax_percent', 'tax_amount', 'grand_total', 'shipment_number', 'purchase_date', 'bill_copy', 'revised', 'revised_amount', 'remark'];

    public function suppliers()
    {
        return $this->belongsTo('App\Models\Suppliers');
    }

    public function payments()
    {
        return $this->hasOne('App\Models\Payments');
    }

    public function stocks()
    {
        return $this->hasMany('App\Models\Stocks', 'purchase_id');
    }
}
