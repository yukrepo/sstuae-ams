<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Appointments;
use DB;

class CroneController extends Controller
{
     public function __construct()
    {
        //$this->middleware('auth:web');
    }

    public function morningSms()
    {
        $table =  new Appointments;
        $table = $table->setTable('appointment'.date('Y', time()).'s');
        $tablename = $table->getTable();
        $data =  $table->select("$tablename.*", 'users.mobile', 'doctors.name', 'treatments.treatment as reason')
                        ->with('start_time')->with('end_time')
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where("$tablename.date", date('Y-m-d', time()))
                        ->whereIn("$tablename.status_id", [2,4,9])
                        ->where('start_timeslot', '>=', 60)
                        ->orderBy('start_timeslot', 'asc')->get();
        //dd($data);
        foreach ($data as $key => $value) {
            if($value->appointment_type_id == 1):
                $text = 'Gentle Reminder From SSTP, Your appointment '.$value->appointment_code.' with '.$value->name.' is on '. date('d-m-Y', strtotime($value->date)) .' at '. $value->start_time->time.'. Kindly be at Clinic 5 mins earlier. Any Query, call - 24480874, 24484991.';
            elseif($value->appointment_type_id == 2):
                 $text = 'Gentle Reminder From SSTP, Your appointment '.$value->appointment_code.' for '.$value->reason.' is on '. date('d-m-Y', strtotime($value->date)) .' at '. $value->start_time->time.'. Kindly be at Clinic 5 mins earlier. Any Query, call - 24480874, 24484991.';
            endif;
        	if($value->mobile): $this->sendSMS($value->mobile, $text); endif;
           // echo $text.'<br>';
        }
        echo 'done'; die;
    }

    public function eveningSms()
    {
        $table =  new Appointments;
        $table = $table->setTable('appointment'.date('Y', time()).'s');
        $tablename = $table->getTable();
        $data =  $table->select("$tablename.*", 'users.mobile', 'doctors.name', 'treatments.treatment as reason')
                        ->with('start_time')->with('end_time')
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where("$tablename.date", date('Y-m-d', strtotime('+1 day', time())))
                        ->whereIn("$tablename.status_id", [2,4,9])
                        ->where('start_timeslot', '<=', 61)
                        ->orderBy('start_timeslot', 'asc')->get();
        //dd($data);
        foreach ($data as $key => $value) {
            if($value->appointment_type_id == 1):
                $text = 'Gentle Reminder From SSTP, Your appointment '.$value->appointment_code.' with '.$value->name.' is on '. date('d-m-Y', strtotime($value->date)) .' at '. $value->start_time->time.'. Kindly be at Clinic 10 mins earlier. Any Query, call - 24480874, 24484991.';
            elseif($value->appointment_type_id == 2):
                 $text = 'Gentle Reminder From SSTP, Your appointment '.$value->appointment_code.' for '.$value->reason.' is on '. date('d-m-Y', strtotime($value->date)) .' at '. $value->start_time->time.'. Kindly be at Clinic 10 mins earlier. Any Query, call - 24480874, 24484991.';
            endif;
        	if($value->mobile): $this->sendSMS($value->mobile, $text); endif;
           // echo $text.'<br>';
        }
        echo 'done'; die;
    }

    function smsCheck() {
    	/*$textx = 'Gentle Reminder From SSTP, Your appointment S2000444 For Abhyangam is on 02/15/2020 at 03:00 PM. Kindly be at Clinic 10 mins earlier. Any Query, Please Contact +968-24480874';
    	echo '<pre>'; print_r($this->sendSMS('96891915249', $textx)); */
    	die;
    }
}
