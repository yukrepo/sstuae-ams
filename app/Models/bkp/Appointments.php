<?php

namespace App\Models;

use DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class Appointments extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'location_id', 'followup_appointment', 'course_id', 'user_id', 'admin_id', 'appointment_type_id', 'appointment_code', 'treatment_id', 'date', 'start_timeslot', 'end_timeslot', 'user_remark', 'visit_type_id', 'doctor_id', 'second_doctor_id', 'room_id', 'medicines', 'tests', 'symptoms', 'therapies', 'oe_categories', 'diseases', 'diagnosis', 'drug_history', 'reminder_days', 'reminder_date', 'status_id', 'dr_remark', 'cancel_reason', 'activities', 'created_at', 'updated_at'];

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    public function start_time()
    {
        return $this->belongsTo('App\Models\TimeSlots', 'start_timeslot', 'id');
    }

    public function end_time()
    {
        return $this->belongsTo('App\Models\TimeSlots', 'end_timeslot', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Users', 'user_id');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Models\Doctors', 'doctor_id');
    }

    public function user_profile()
    {
        return $this->belongsTo('App\Models\UserProfiles', 'user_id', 'user_id');
    }

    public function treatment()
    {
        return $this->belongsTo('App\Models\Treatments', 'treatment_id');
    }

}
