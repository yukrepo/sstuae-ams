<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Settlements extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'insurance_id', 'amount', 'reference_number', 'remark', 'receipt', 'receiving_date', 'status_id', 'location_id', 'settlement_month', 'invoices'];

}
