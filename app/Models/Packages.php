<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Packages extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'location_id', 'type', 'course_code', 'treatments', 'treatments_Summary', 'consultation', 'consultation_Summary', 'cost', 'timeline', 'remark', 'name', 'status_id'];

}
