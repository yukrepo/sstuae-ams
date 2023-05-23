<?php

namespace App\Models;

use DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'location_id', 'user_id', 'admin_id', 'invoice_type', 'invoice_number', 'appointment_id', 'amount', 'payable_amount', 'payment_history', 'bliss_discount', 'bliss_discount_value', 'insurance_id', 'tax_value', 'payment_status', 'approved', 'invoice_date', 'revise_history', 'reason',  'reference', 'rtype', 'rcount', 'cancel', 'rawdata', 'ins_clearance', 'created_at', 'updated_at'];

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Users')->with('user_profile')->with('user_document');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Locations');
    }
}
