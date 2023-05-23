<?php

namespace App\Models;

use DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;

class Availabilities extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'location_id', 'admin_id', 'type', 'date', 'shift_timings', 'block_time_slots', 'remark', 'note', 'hours', 'doctor_id', 'status_id', 'created_at', 'updated_at'
    ];

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }
}
