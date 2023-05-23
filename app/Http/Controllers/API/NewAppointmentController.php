<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointments;
use App\Models\Doctors;
use App\Models\Users;
use Illuminate\Support\Carbon;
use DB;


class NewAppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function createDummyAppointments(Request $request)
    {
        $appointment = $this->__Get_Appointment($request['appointment_id']);
        if($appointment) {
            switch ($appointment['status_id']) {
                case 11:
                    return '<span class="text-danger">Cancelled Appointment.</span>';
                case 1:
                    return '<span class="text-danger">Pending Appointment.</span>';
                case 3:
                    return '<span class="text-danger">Deactivated Appointment.</span>';
                case 8:
                    return '<span class="text-danger">Consultation is in process.</span>';
                case 6:
                    return '<span class="text-danger">Rejected Appointment.</span>';
                case 7:
                    return '<span class="text-danger">Trashed Appointment.</span>';
                case 10:
                    return '<span class="text-danger">Consultation is in process.</span>';
                default:
                    break;
            }
            $table =  new Appointments;
            $table = $table->setTable('appointment'.date('Y', strtotime($appointment['date'])).'s');
            $tablename = $table->getTable();
            $acode = $this->appointmentCodeGenerator($appointment['date']);
            if($appointment['appointment_type_id'] == 1) {
                $doctor_id = 3;
            } elseif($appointment['appointment_type_id'] == 2) {
                $doctor_id = $this->__Get_Doctors();
            } else {
                $doctor_id = 3;
            }
            $response = DB::table($tablename)->insert([
                            'appointment_type_id' => $appointment['appointment_type_id'],
                            'date' => $appointment['date'],
                            'doctor_id' => $doctor_id,
                            'location_id' => Auth::user()->location_id,
                            'room_id' => $appointment['room_id'],
                            'second_doctor_id' => ($request['second_doctor_id'])?$this->__Get_Doctors():null,
                            'status_id' => $appointment['status_id'],
                            'user_id' => $appointment['user_id'],
                            'treatment_id' => $appointment['treatment_id'],
                            'visit_type_id' => $appointment['visit_type_id'],
                            'followup_appointment' => $appointment['followup_appointment'],
                            'course_id' => $appointment['course_id'],
                            'admin_id' => Auth::user()->id,
                            'appointment_code' => $acode,
                            'start_timeslot' => $appointment['start_timeslot'],
                            'end_timeslot' => $appointment['end_timeslot'],
                            'user_remark' => $appointment['remark'],
                            'ainvoice' => $appointment['ainvoice'],
                            'pinvoice' => $appointment['pinvoice'],
                            'is_dummy' => $appointment['appointment_code'],
                            'diagnosis' => $appointment['diagnosis'],
                            'diseases' => $appointment['diseases'],
                            'drug_history' => $appointment['drug_history'],
                            'oe_categories' => $appointment['oe_categories'],
                            'therapies' => $appointment['therapies'],
                            'symptoms' => $appointment['symptoms'],
                            'medicines' => $appointment['medicines'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
            ]);
            return '<span class="text-success">Created</span>';
        } else {
            return '<span class="text-danger">Not Found</span>';
        }
    }

    private function __Get_Appointment($id)
    {
        $year = '20'.substr($id, 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        return $table->where('appointment_code', $id)->where('location_id', 2)->get()->first();
    }

    private function appointmentCodeGenerator($date)
    {
        $table =  new Appointments;
        $table = $table->setTable('appointment'.date('Y', strtotime($date)).'s');
        $tablename = $table->getTable();
        $app = DB::table($tablename)->orderBy('id', 'desc')->take(1)->get();
        if($app->count()):
            $linv = substr($app[0]->appointment_code, 3);
            $counter = intval($linv)+1;
        else: $counter = 1; endif;
        $number = 'S'.date('y', strtotime($date)).str_pad($counter, 5, "0",STR_PAD_LEFT);
        return $number;
    }

    private function __Get_Doctors()
    {
        $doctor = Doctors::where('location_id', 1)->where('status_id', 2)->where('designation_type_id', 2)->inRandomOrder()->first();
        return $doctor->id;
    }
}
