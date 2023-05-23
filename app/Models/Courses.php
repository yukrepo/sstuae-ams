<?php

namespace App\Models;

use DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'location_id', 'admin_id', 'package_id', 'amount', 'consult_code', 'course_code', 'doctor_id', 'user_id', 'remark', 'start_date', 'end_date', 'appointments', 'status_id', 'pstatus', 'invoice', 'ins_approval', 'reason', 'approval_code', 'reapproval_code', 'approval_history', 'insurance_id', 'created_at', 'updated_at'];

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Users', 'user_id');
    }
}
