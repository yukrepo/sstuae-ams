<?php

namespace App\Models;

use DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoicePayments extends Model
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'location_id', 'invoice_no', 'amount', 'reason', 'visa_amount', 'cash_amount', 'received', 'returnable', 'txnid', 'admin_id', 'remark', 'date', 'mode', 'txnid', 'created_at', 'updated_at'];

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Users')->with('user_profile')->with('user_document');
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admins');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Locations');
    }
}
