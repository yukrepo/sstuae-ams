<?php

namespace App\Models;

use DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'added_by', 'type', 'invoice_number', 'customer_id', 'sub_total', 'payable_amount', 'remark', 'pay_history', 'revised', 'revised_amount', 'mode', 'insurance', 'iam', 'am1', 'am2', 'status', 'created_at', 'updated_at'];

}
