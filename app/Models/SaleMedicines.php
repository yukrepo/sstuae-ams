<?php

namespace App\Models;

use DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class SaleMedicines extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'sale_id', 'medicine_id', 'stock_id', 'price', 'qty', 'rowdata', 'return_status', 'return_reason', 'created_at', 'updated_at'];

}
