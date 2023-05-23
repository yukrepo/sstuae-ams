<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointments;
use App\Models\Availabilities;
use App\Models\TimeSlots;
use App\Models\Rooms;
use App\Models\Xrays;
use App\Models\Doctors;
use App\Models\Diseases;
use App\Models\Medicines;
use App\Models\Courses;
use App\Models\Symptoms;
use App\Models\OeCategories;
use App\Models\Treatments;
use App\Models\FinancialYears;
use App\Models\Laboratories;
use App\Models\UserDocuments;
use App\Models\Invoices;
use App\Models\Sales;
use App\Models\Users;
use App\Models\SaleMedicines;
use Illuminate\Support\Carbon;
use App\Models\AppointmentQueries;
use DB;
use App\Models\InsuranceTreatmentMaps;
use App\Models\InsuranceConsultationMaps;
use App\Models\Packages;
use App\Models\Settings;

class AppointmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Appointments::select('appointment_queries.*', 'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name', 'statuses.status as status', 'statuses.css as css')
                                ->join('locations', 'locations.id', '=', 'appointment_queries.location_id')
                                ->join('statuses', 'statuses.id', '=', 'appointment_queries.status_id')
                                ->join('users', 'users.id', '=', 'appointment_queries.user_id')
                                ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                                ->orderBy('appointment_queries.created_at', 'desc')->paginate(15);
    }

    public function makeAppointment(Request $request)
    {
        $this->validate($request, [
            //'location_id' => 'required|numeric|max:190',
            'user_id' => 'required|numeric',
            'visit_type_id' => 'required|numeric|digits:1',
            'appointment_type_id' => 'required|string|max:190',
            //'appointment_code' => 'required|string|max:190',
            'date' => 'required|string|max:190',
            'start_time' => 'required|numeric|max:190',
            'end_time' => 'required|numeric|max:190',
            'doctor_id' => 'required|numeric|max:190',
            'room_id' => 'nullable',
            'status_id' => 'required'
        ]);
       /*  $slots = [];
        for($i = $request['start_time']; $i <= $request['end_time']; $i++){
            array_push($slots, $i);
        }
        $time_slots = implode(',',$slots);
        */
        $table =  new Appointments;
        $table = $table->setTable('appointment'.date('Y', strtotime($request['date'])).'s');
        $tablename = $table->getTable();
        $acode = $this->appointmentCodeGenerator($request['date']);
        //return ['aid' => Auth::user()->id, 'ap' => $this->appointmentCodeGenerator($_REQUEST['date'])];
        $response = DB::table($tablename)->insert([
                            'appointment_type_id' => $request['appointment_type_id'],
                            'date' => $request['date'],
                            'doctor_id' => $request['doctor_id'],
                            'location_id' => Auth::user()->location_id,
                            'room_id' => $request['room_id'],
                            'second_doctor_id' => $request['second_doctor_id'],
                            'status_id' => $request['status_id'],
                            'user_id' => $request['user_id'],
                            'treatment_id' => $request['treatment_id'],
                            'visit_type_id' => $request['visit_type_id'],
                            'followup_appointment' => $request['followup_appointment'],
                            'course_id' => $request['course_id'],
                            'admin_id' => Auth::user()->id,
                            'appointment_code' => $acode,
                            'start_timeslot' => $request['start_time'],
                            'end_timeslot' => $request['end_time'],
                            'user_remark' => $request['remark'],
                            'dr_remark' => '',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
        ]);
        if($request['course_id']):
            $year = '20'.substr($request['course_id'], 1, 2);
            $ctable =  new Courses;
            $ctable = $ctable->setTable('course'.$year.'s');
            $ctablename = $ctable->getTable();

            $course = DB::table($ctablename)->where(['course_code' => $request['course_id']])->get()->first();

            $appointments = explode(',', $course->appointments);
            array_push($appointments, $acode);
            $appointments = array_filter($appointments);
            DB::table($ctablename)->where(['course_code' => $request['course_id']])->update(['appointments' => implode(',', $appointments)]);
        endif;
        if(isset($request->rsn) && ($request->rsn >= 1)):
            $query = AppointmentQueries::findOrFail($request->rsn);
            $query->update(['status_id' => 5, 'apid' => $acode]);
        endif;
        if($request->user_id):
            if($request['appointment_type_id'] == 1): $ptyp = 'Consultation'; else: $ptyp = 'Treatment'; endif;
            $user = Users::with('user_profile')->where('id', $request->user_id)->get()->first();
            $start_time = TimeSlots::where('id', $request['start_time'])->get()->first();
            $text = 'Dear '.$user->user_profile->first_name.', Your '.$ptyp.' has been booked on '. date('d-m-Y', strtotime($request->date)) .' at '. $start_time->time.'. Kindly be at Clinic 5 mins earlier. Any Query, call - 24480874, 24484991.';
            $this->sendSMS($user->mobile, $text);
        endif;
        return ['response' => $response];
    }

    public function show($id)
    {
        $year = '20'.substr($id, 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $appointment = $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name', 'user_profiles.address as address', 'user_profiles.city as city', 'users.mobile as mobile', 'user_profiles.address', 'user_profiles.gender', 'users.username', 'user_profiles.married', 'user_profiles.date_of_birth', 'doctors.name as doctor', 'treatments.treatment as reason', 'user_documents.insurance_copy', 'user_documents.policy_number', 'user_documents.insurance_verified', 'insurances.name as insurancer', 'admins.name as admin', 'user_documents.insurance_id')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->join('user_documents', 'user_documents.user_id', '=', 'users.id')
                        ->join('insurances', 'insurances.id', '=', 'user_documents.insurance_id')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where('appointment_code', $id)
                        ->get();
        $appointment[0]['iinvoice'] = ''; $appointment[0]['cinvoice'] = '';
        if($appointment[0]['pinvoice']):
            $invoices = explode(',', $appointment[0]['pinvoice']);
            array_filter($invoices);
            if(count($invoices) == 1):
                $type = substr($appointment[0]['pinvoice'], 0, 2);
                if($type == 'IM'): $appointment[0]['iinvoice'] = $appointment[0]['pinvoice'];
                elseif($type == 'CM'): $appointment[0]['cinvoice'] = $appointment[0]['pinvoice']; endif;
            else:
                $cpos = strpos($appointment[0]['pinvoice'], 'CM');
                if($cpos >= 0)
                $appointment[0]['cinvoice'] = substr($appointment[0]['pinvoice'], strpos($appointment[0]['pinvoice'], 'CM'), 12);
                $ipos = strpos($appointment[0]['pinvoice'], 'IM');
                if($ipos >= 0)
                $appointment[0]['iinvoice'] = substr($appointment[0]['pinvoice'], strpos($appointment[0]['pinvoice'], 'IM'), 12);
            endif;

        else: $appointment[0]['iinvoice'] = ''; $appointment[0]['cinvoice'] = ''; endif;
        if($appointment[0]['course_id']):
            $year = '20'.substr($appointment[0]['course_id'], 1, 2);
            $ctable =  new Courses;
            $ctable = $ctable->setTable('course'.$year.'s');
            $ctablename = $ctable->getTable();

            $course = $ctable->where("$ctablename.course_code", $appointment[0]['course_id'])->get()->first();
            if($course->doctor_id):
                $ctype = 'Prescribed Course';
                $clink = 'courses/view/';
                $doctor = Doctors::findOrFail($course->doctor_id);
                $reason = 'Prescribed By: '.$doctor->name;
            elseif($course->package_id):
                $ctype = 'Package Course';
                $clink = 'course-packages/view/';
                $package = Packages::findOrFail($course->package_id);
                $reason = $package->name;
            else:
                $ctype = 'Direct Course';
                $clink = 'courses/direct-view/';
                $reason = $course->remark;
            endif;
            $appointment[0]['course_type'] = $ctype;
            $appointment[0]['clink'] = $clink;
            $appointment[0]['course_reason'] = $reason;
        endif;
        if($appointment[0]['therapies']):
            $total = 0;
            $therapies = json_decode($appointment[0]['therapies']);
            foreach($therapies as $therapy):
                $total = $total + $therapy->days;
            endforeach;
            $appointment[0]['total_treatments'] = $total;
        else:
            $appointment[0]['total_treatments'] = 0;
        endif;
        $drchk = $this->__IsInDummy($appointment[0]['appointment_code'], $appointment[0]['location_id']);
        if($drchk) {
            $appointment[0]['new_doctor'] = $drchk;
        } else {
            $appointment[0]['new_doctor'] = '';
        }
        return $appointment;
    }

    public function showForm($id)
    {
        $year = '20'.substr($id, 1, 2);
        $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            return $table->select("$tablename.*")
                        ->where('appointment_code', $id)
                        ->get()->first();
    }

    public function getTodaysAppointments()
    {
       /*  if($type == 'c'): $atype = [1];
        elseif($type == 't'): $atype = [2];
        else: $atype = [1,2]; endif;*/
            $table =  new Appointments;
            $table = $table->setTable('appointment'.date('Y').'s');
            $tablename = $table->getTable();
            if(Auth::user()->admin_type_id == 2):
                return $table->select("$tablename.id", "$tablename.date", "$tablename.feedback", "$tablename.fsms",  "$tablename.ainvoice", "$tablename.created_at", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot", "$tablename.status_id", "$tablename.is_dummy", "$tablename.appointment_type_id", "$tablename.user_id", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name', 'treatments.treatment as reason')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where(function ($q) use ($tablename) {
                            $q->where("$tablename.doctor_id", Auth::user()->relative_id)
                            ->orWhere("$tablename.second_doctor_id", Auth::user()->relative_id);
                        })
                        ->where("$tablename.location_id", Auth::user()->location_id)
                        ->whereNotIn("$tablename.status_id", [7,12])
                        ->where('date', date('Y-m-d', time()))
                        ->orderBy('start_timeslot', 'asc')->paginate(15);
            else:
                return $table->select("$tablename.id", "$tablename.date", "$tablename.feedback", "$tablename.fsms", "$tablename.created_at", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot",  "$tablename.user_id", "$tablename.status_id", "$tablename.is_dummy", "$tablename.appointment_type_id", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name', 'doctors.name as doctor', 'treatments.treatment as reason')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where("$tablename.location_id", Auth::user()->location_id)
                        ->whereNotIn("$tablename.status_id", [7,12])
                        ->where('date', date('Y-m-d', time()))
                        ->orderBy('start_timeslot', 'asc')->get();
            endif;
    }

    public function getTodaysActiveAppointments()
    {
        $table =  new Appointments;
        $table = $table->setTable('appointment'.date('Y').'s');
        $tablename = $table->getTable();
        return $table->select("$tablename.id", "$tablename.date", "$tablename.feedback", "$tablename.fsms", "$tablename.created_at", "$tablename.appointment_code", "$tablename.is_dummy", "$tablename.start_timeslot", "$tablename.end_timeslot",  "$tablename.user_id", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name', 'doctors.name as doctor', 'treatments.treatment as reason')
                ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                ->join('users', 'users.id', '=', "$tablename.user_id")
                ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                ->join('locations', 'locations.id', '=', "$tablename.location_id")
                ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                ->where("$tablename.location_id", Auth::user()->location_id)
                ->whereIn("$tablename.status_id", [2,4,8,9,10])
                ->where('date', date('Y-m-d', time()))
                ->orderBy('start_timeslot', 'asc')->get();
    }

    public function getMonthlyAppointments($date= null, $type = null)
    {
            $table =  new Appointments;
            $allreturn = [];
            $subdate = explode('-', $date);
            $table = $table->setTable('appointment'.$subdate[0].'s');
            $tablename = $table->getTable();

            for ($i=1; $i <= 31; $i++) {
                $checkdate = date('Y-m-d', strtotime($subdate[0].'-'.$subdate[1].'-'.$i));
                if($type == 2):
                    $allreturn[$i.'c'] = $table->where('appointment_type_id', 1)
                                                            ->where('location_id', Auth::user()->location_id)
                                                            ->where('date', $checkdate)
                                                            ->where("$tablename.doctor_id", Auth::user()->relative_id)
                                                            ->where("$tablename.is_dummy", '0')
                                                            ->whereIn('status_id', [4,5,9])
                                                            ->get()->count();
                    $allreturn[$i.'t'] = $table->where('appointment_type_id', 2)
                                                            ->where('location_id', Auth::user()->location_id)
                                                            ->where('date', $checkdate)
                                                            ->where("$tablename.doctor_id", Auth::user()->relative_id)
                                                            ->whereIn('status_id', [4,5,9])
                                                            ->where("$tablename.is_dummy", '0')
                                                            ->get()->count();
                else:
                    $allreturn[$i.'c'] = $table->where('appointment_type_id', 1)
                                                            ->where('location_id', Auth::user()->location_id)
                                                            ->where('date', $checkdate)
                                                            ->whereIn('status_id', [4,5,9])
                                                            ->where("$tablename.is_dummy", '0')
                                                            ->get()->count();
                    $allreturn[$i.'t'] = $table->where('appointment_type_id', 2)
                                                            ->where('location_id', Auth::user()->location_id)
                                                            ->where('date', $checkdate)
                                                            ->whereIn('status_id', [4,5,9])
                                                            ->where("$tablename.is_dummy", '0')
                                                            ->get()->count();
                endif;
            }

            /* if($type == 2):
                $records = $table->select("$tablename.*", 'user_profiles.first_name')
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->where("$tablename.location_id", Auth::user()->location_id)
                        ->whereMonth('date', $subdate[1])
                        ->whereYear('date', $subdate[0])
                        ->where("$tablename.doctor_id", Auth::user()->relative_id)
                        ->orderBy('date', 'asc')->get();
            else:
                $records = $table->select("$tablename.*", 'user_profiles.first_name')
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->where("$tablename.location_id", Auth::user()->location_id)
                        ->whereMonth('date', $subdate[1])
                        ->whereYear('date', $subdate[0])
                        ->orderBy('date', 'asc')->get();
            endif;

            foreach($records as $record):
                $allreturn[$record->date][] = $record;
            endforeach;*/
        return $allreturn;
    }

    public function getDailyAppointments($date= null)
    {
        $dr_array = [];
        $all_slots = $this->__getTimeSlots();
        $doctors = $this->__getDoctors();

        $table =  new Appointments;
        $allreturn = [];
        $subdate = explode('-', $date);
        $table = $table->setTable('appointment'.$subdate[0].'s');
        $tablename = $table->getTable();

        $ntable =  new Availabilities;
        $ntable = $ntable->setTable('availability'.$subdate[0].'s');
        $ntablename = $ntable->getTable();

        foreach($doctors as $doctor):
            $id = $doctor->id;
            $booked_slots = $table->select("$tablename.*", 'users.username', 'treatments.treatment', 'statuses.status as status', 'statuses.text as status_text', 'user_profiles.first_name')
                            ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                            ->join('users', 'users.id', '=', "$tablename.user_id")
                            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                            ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->where(function($query2) use ($id)
                                {
                                    $query2->where('doctor_id', $id)
                                    ->orWhere('second_doctor_id', $id);
                                })
                            ->whereNotIn("$tablename.status_id", [6,7,11])
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.is_dummy", '0')
                            ->get();

            $aval_slots = $ntable->where('location_id', Auth::user()->location_id)
                            ->where('doctor_id', $doctor->id)
                            ->where('status_id', 2)
                            ->where('type', 1)
                            ->where('date', date('Y-m-d', strtotime($date)))
                            ->first();

            $blocked_slots = $ntable->where('location_id', Auth::user()->location_id)
                            ->where('doctor_id', $doctor->id)
                            ->where('status_id', 2)
                            ->whereIN('type', [2,3])
                            ->where('date', date('Y-m-d', strtotime($date)))
                            ->first();

            $merge = []; $nmerge = []; $appoint = [];

            if(count($booked_slots) >= 1):
                foreach($booked_slots as $booked_slot){
                    for($i = $booked_slot->start_timeslot; $i <= $booked_slot->end_timeslot; $i++) {
                        array_push($merge, $i);
                        $appoint[$doctor->id][$i]['code'] = $booked_slot->appointment_code;
                        $appoint[$doctor->id][$i]['ucode'] = $booked_slot->username;
                        $appoint[$doctor->id][$i]['uname'] = $booked_slot->first_name;
                        $appoint[$doctor->id][$i]['uid'] = $booked_slot->user_id;
                        $appoint[$doctor->id][$i]['call'] = $booked_slot->call_point;
                        $appoint[$doctor->id][$i]['treatment'] = $booked_slot->treatment;
                        $appoint[$doctor->id][$i]['status'] = $booked_slot->status;
                        $appoint[$doctor->id][$i]['icode'] = ($booked_slot->ainvoice)?$this->__GetIcode($booked_slot->ainvoice):'N';
                    }
                };
            endif;

            if($blocked_slots):
                if($blocked_slots['block_time_slots']): $nmerge = explode(',',$blocked_slots['block_time_slots']);
                else: for ($i=1; $i <= 97; $i++) { $nmerge[] = $i; } endif;
                $reasson = $blocked_slots->remark;
            else: $nmerge = []; endif;

            $all_slots = $this->__getAllTimeSlots();

            if($aval_slots):
                $slots = explode(',',$aval_slots['shift_timings']);
            else: $slots = []; endif;

            foreach($all_slots as $key => $value):
                if(in_array($key, $merge)):
                    if((isset($appoint[$doctor->id][$key-3])) && ($appoint[$doctor->id][$key-3]['code'] == $appoint[$doctor->id][$key]['code'])):
                        $code = '';
                        $code1 = '';
                        $coden1 = '';
                        $code2 = '';
                    elseif((isset($appoint[$doctor->id][$key-2])) && ($appoint[$doctor->id][$key-2]['code'] == $appoint[$doctor->id][$key]['code'])):
                        $code2 = $appoint[$doctor->id][$key]['treatment'];
                        $code = '';
                        $coden1 = '';
                        $code1 = '';
                    elseif((isset($appoint[$doctor->id][$key-1])) && ($appoint[$doctor->id][$key-1]['code'] == $appoint[$doctor->id][$key]['code'])):
                        $code1 = $appoint[$doctor->id][$key]['ucode'];
                        $coden1 = $appoint[$doctor->id][$key]['uname'];
                        $code = '';
                        $code2 = '';
                    else:
                        $code = $appoint[$doctor->id][$key]['code'];
                        $code1 = '';
                        $coden1 = '';
                        $code2 = '';
                    endif;

                    $dr_array[$doctor->id.'-'.$key.'-css'] = 'alert-danger';
                    $dr_array[$doctor->id.'-'.$key.'-code'] = $code;
                    $dr_array[$doctor->id.'-'.$key.'-code1'] = $code1;
                    $dr_array[$doctor->id.'-'.$key.'-code2'] = $code2;
                    $dr_array[$doctor->id.'-'.$key.'-coden1'] = $coden1;
                    $dr_array[$doctor->id.'-'.$key.'-type'] = 'book';
                    $dr_array[$doctor->id.'-'.$key.'-status'] = $appoint[$doctor->id][$key]['status'];
                    $dr_array[$doctor->id.'-'.$key.'-icode'] = $appoint[$doctor->id][$key]['icode'];
                     $dr_array[$doctor->id.'-'.$key.'-call'] = $appoint[$doctor->id][$key]['call'];
                elseif(in_array($key, $slots)):
                    $dr_array[$doctor->id.'-'.$key.'-css'] = 'alert-success';
                    $dr_array[$doctor->id.'-'.$key.'-code'] = '';
                elseif(in_array($key, $nmerge)):
                    $dr_array[$doctor->id.'-'.$key.'-css'] = 'alert-warning';
                    $dr_array[$doctor->id.'-'.$key.'-code'] = $reasson;
                    $dr_array[$doctor->id.'-'.$key.'-camp'] = strtolower(str_replace(' ', '_', $reasson));
                    $dr_array[$doctor->id.'-'.$key.'-type'] = 'block';
                else:
                    $dr_array[$doctor->id.'-'.$key.'-css'] = 'alert-orange';
                    $dr_array[$doctor->id.'-'.$key.'-code'] = '';
                endif;
            endforeach;

        endforeach;

        return $dr_array;
    }

    public function getUpcomingsYearwise($year = null)
    {
        if($year):
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            return $table->select("$tablename.id", "$tablename.date", "$tablename.created_at", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot",  "$tablename.user_id", "$tablename.is_dummy", "$tablename.course_id", "$tablename.appointment_type_id", 'statuses.status as status', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name', 'doctors.name as doctor', 'treatments.treatment as reason')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where("$tablename.location_id", Auth::user()->location_id)
                        ->whereYear('date', $year)
                        ->where('date', '>', date('Y-m-d', time()))
                        ->whereNotIn("$tablename.status_id", [5,7,10])
                        ->orderBy('date', 'asc')->paginate(15);
        else:
            return ['message' => 'Year is missing. No data can be fetched.'];
        endif;
    }

    public function getPersonalUpcomingsYearwise($year = null)
    {
        if($year):
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            return $table->select("$tablename.id", "$tablename.date", "$tablename.created_at", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot",  "$tablename.user_id", "$tablename.is_dummy", "$tablename.course_id", "$tablename.appointment_type_id", 'statuses.status as status', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name', 'treatments.treatment as reason')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where("$tablename.location_id", Auth::user()->location_id)
                        ->where("$tablename.doctor_id", Auth::user()->relative_id)
                        ->whereYear('date', $year)
                        ->where('date', '>', date('Y-m-d', time()))
                        ->whereNotIn("$tablename.status_id", [5,7,10])
                        ->orderBy('date', 'asc')->get();
        else:
            return ['message' => 'Year is missing. No data can be fetched.'];
        endif;
    }

    public function getHistoryYearwise($year = null)
    {
        if($year):
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            if(Auth::user()->admin_type_id == 2):
                return $table->select("$tablename.id", "$tablename.date", "$tablename.feedback", "$tablename.fsms", "$tablename.created_at", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot", "$tablename.course_id", "$tablename.user_id", "$tablename.status_id", "$tablename.appointment_type_id", "$tablename.is_dummy", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name',  'doctors.name as doctor', 'treatments.treatment as reason')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where("$tablename.doctor_id", Auth::user()->relative_id)
                        ->where("$tablename.location_id", Auth::user()->location_id)
                        ->whereYear('date', $year)
                        ->where('date', '<', date('Y-m-d', time()))
                        ->whereNotIn("$tablename.status_id", [1,7])
                        ->orderBy('date', 'desc')->paginate(15);
            else:
                return $table->select("$tablename.id", "$tablename.date", "$tablename.feedback", "$tablename.fsms", "$tablename.created_at", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.is_dummy", "$tablename.end_timeslot", "$tablename.course_id", "$tablename.appointment_type_id", "$tablename.user_id", "$tablename.status_id",  'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name',  'doctors.name as doctor', 'treatments.treatment as reason')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where("$tablename.location_id", Auth::user()->location_id)
                        ->whereYear("$tablename.date", $year)
                        ->where("$tablename.date", '<', date('Y-m-d', time()))
                        ->whereNotIn("$tablename.status_id", [1,7])
                        ->orderBy("$tablename.date", 'desc')->paginate(15);
            endif;
        else:
            return ['message' => 'Year is missing. No data can be fetched.'];
        endif;
    }

    public function getPatientHistoryYearwise($id = null, $year = null)
    {
        if($year && $id):
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            return $table->select("$tablename.id", "$tablename.date", "$tablename.created_at", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot", "$tablename.is_dummy",  "$tablename.user_id", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name',  'doctors.name as doctor', 'treatments.treatment as reason')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->whereYear("$tablename.date", $year)
                        ->where("$tablename.user_id", $id)
                        ->where("$tablename.is_dummy", '0')
                        ->orderBy("$tablename.date", 'desc')->get();
        else:
            return ['message' => 'Year is missing. No data can be fetched.'];
        endif;
    }

    public function getTimings()
    {
        if($_REQUEST['at'] && $_REQUEST['q'] && $_REQUEST['lid'] && $_REQUEST['did']) {
            $year = substr($_REQUEST['q'], 0, 4);
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            $id = $_REQUEST['did'];
            $booked_slots = $table->where('location_id', $_REQUEST['lid'])
                                ->where('appointment_type_id', $_REQUEST['at'])
                                ->where(function($query2) use ($id)
                                    {
                                        $query2->where('doctor_id', $id)
                                        ->orWhere('second_doctor_id', $id);
                                    })
                                ->whereIn('status_id',  [2,4,5,8,9])
                                ->whereDate('date', '=', date('Y-m-d', strtotime($_REQUEST['q'])))
                                ->get();

            $all_slots = $this->__getTimeSlots();

            if(count($booked_slots) >=1):
                $merge = [];
                foreach($booked_slots as $booked_slot){
                    $array1 = explode(',', $booked_slot->time_slots);
                    $merge = array_merge($merge, $array1);
                    $merge = array_map('intval', $merge);
                };
                return array_diff_key($all_slots, array_flip($merge));
            else:
                return $all_slots;
            endif;

        } else {
            return ['Invalid data'];
        }
    }

    public function getDoctorAppointmentTimings()
    {
        if(is_numeric($_REQUEST['lid']) && ($_REQUEST['lid'] >= 1)): // do nothing
        else: $_REQUEST['lid'] = Auth::user()->location_id; endif;
        if($_REQUEST['at'] && $_REQUEST['q'] && $_REQUEST['lid'] && $_REQUEST['did']) {
            $year = substr($_REQUEST['q'], 0, 4);
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            if(!isset($_REQUEST['apid'])): $_REQUEST['apid'] =''; endif;
            $id = $_REQUEST['did'];
            if($_REQUEST['apid']):
                $booked_slots = $table->where('location_id', $_REQUEST['lid'])
                               // ->where('appointment_type_id', $_REQUEST['at'])
                                ->where('appointment_code', '!=', $_REQUEST['apid'])
                                ->whereIn('status_id', [2,4,5,8,9,10])
                                ->where('date', $_REQUEST['q'])
                                ->where(function($query2) use ($id) {
                                    $query2->where('doctor_id', $id)
                                    ->orWhere('second_doctor_id', $id);
                                })
                                ->get();
            else:
                $booked_slots = $table->where('location_id', $_REQUEST['lid'])
                                ->where(function($query2) use ($id)
                                    {
                                        $query2->where('doctor_id', $id)
                                        ->orWhere('second_doctor_id', $id);
                                    })
                                ->whereIn('status_id', [2,4,5,8,9,10])
                                ->where('date', $_REQUEST['q'])
                                ->get();
            endif;
            $all_slots = $this->__getTimeSlots();
            $merge = []; $nmerge = [];
            if(count($booked_slots) >=1):
                foreach($booked_slots as $booked_slot){
                   for($i = $booked_slot->start_timeslot; $i <= $booked_slot->end_timeslot; $i++) {
                        array_push($merge, $i);
                   }
                };
            endif;

            $table =  new Availabilities;
            $table = $table->setTable('availability'.$year.'s');
            $tablename = $table->getTable();

            $aval_slots = $table->where('location_id', $_REQUEST['lid'])
                                ->where('doctor_id', $_REQUEST['did'])
                                ->where('status_id', 2)
                                ->where('type', 1)
                                ->where('date', date('Y-m-d', strtotime($_REQUEST['q'])))
                                ->first();

            if($aval_slots):
                $all_slots = explode(',',$aval_slots['shift_timings']);
            else: $all_slots = []; endif;

            $slots = $this->__getAllTimeSlots();
            $available_slots = [];
            foreach($all_slots as $value):
                if(!in_array($value, array_merge($merge, $nmerge))):
                    $available_slots[$value] = $slots[$value];
                endif;
            endforeach;
            //    return array_diff_key($all_slots, array_flip($merge));

            $return = ['fixed' => array_filter($merge), 'blocked' => [], 'start_slots' => $available_slots, 'aval_slots' => array_keys($available_slots), 'merged' => array_merge($merge, $nmerge)];
            return $return;
        } else {
            return [];
        }
    }

    public function getClosings()
    {
        if(is_numeric($_REQUEST['lid']) && ($_REQUEST['lid'] >= 1)): // do nothing
        else: $_REQUEST['lid'] = Auth::user()->location_id; endif;

        if($_REQUEST['st'] && $_REQUEST['at'] && $_REQUEST['q'] && $_REQUEST['lid'] && $_REQUEST['did']) {
            $year = substr($_REQUEST['q'], 0, 4);
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            if(!isset($_REQUEST['apid'])): $_REQUEST['apid'] =''; endif;
            $id = $_REQUEST['did'];
            if($_REQUEST['apid']):
                $booked_slots = $table->where('location_id', $_REQUEST['lid'])
                                ->where(function($query2) use ($id)
                                    {
                                        $query2->where('doctor_id', $id)
                                        ->orWhere('second_doctor_id', $id);
                                    })
                                ->whereIn('status_id',  [2,4,5,8,9,10])
                                ->where('appointment_code', '!=', $_REQUEST['apid'])
                                ->whereDate('date', '=', date('Y-m-d', strtotime($_REQUEST['q'])))
                                ->get();
            else:
                $booked_slots = $table->where('location_id', $_REQUEST['lid'])
                                ->where(function($query2) use ($id)
                                    {
                                        $query2->where('doctor_id', $id)
                                        ->orWhere('second_doctor_id', $id);
                                    })
                                ->whereIn('status_id',  [2,4,5,8,9,10])
                                ->whereDate('date', '=', date('Y-m-d', strtotime($_REQUEST['q'])))
                                ->get();
            endif;

            $merge = []; $nmerge = [];

            if(count($booked_slots) >=1):
                foreach($booked_slots as $booked_slot){
                   for($i = $booked_slot->start_timeslot; $i <= $booked_slot->end_timeslot; $i++) {
                        array_push($merge, $i);
                   }
                };
            endif;
            sort($merge);
            $table =  new Availabilities;
            $table = $table->setTable('availability'.$year.'s');
            $tablename = $table->getTable();

            $aval_slots = $table->where('location_id', $_REQUEST['lid'])
                                ->where('doctor_id', $_REQUEST['did'])
                                ->where('status_id', 2)
                                ->where('type', 1)
                                ->where('date', date('Y-m-d', strtotime($_REQUEST['q'])))
                                ->first();

            if($aval_slots):
                $all_slots = explode(',',$aval_slots['shift_timings']);
            else: $all_slots = []; endif;

            $slots =$this->__getAllEndTimeSlots();

            $available_slots = [];
            $count = 0;

            $nmerge = array_diff($all_slots, $merge);
            $nmerge = array_unique($nmerge);
            sort($nmerge);
            foreach($nmerge as $value):
                if($value >= $_REQUEST['st']):
                    if(!in_array(($_REQUEST['st']+$count), $nmerge)):
                        break;
                    else:
                        $available_slots[$value] = $slots[$value];
                        $count++;
                    endif;
                endif;
            endforeach;
            //    return array_diff_key($all_slots, array_flip($merge));

            //return [$available_slots, $all_slots, $merge, $nmerge, $_REQUEST['st']];
            return $available_slots;



        } else {
            return ['Invalid data'];
        }
    }

    public function getRooms()
    {
        if(is_numeric($_REQUEST['lid']) && ($_REQUEST['lid'] >= 1)): // do nothing
        else: $_REQUEST['lid'] = Auth::user()->location_id; endif;

        if($_REQUEST['st'] && $_REQUEST['et'] && $_REQUEST['q'] && $_REQUEST['lid']) {
            $year = substr($_REQUEST['q'], 0, 4);
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');

            $allRooms = $this->__getRooms($_REQUEST['lid']);

            $avalrooms = [];
            if(!isset($_REQUEST['apid'])): $_REQUEST['apid'] =''; endif;

            foreach ($allRooms as $key => $room):

                if($_REQUEST['apid']):
                    $booked_rooms = $table->where('location_id', $_REQUEST['lid'])
                                    ->whereIn('status_id', [2,4,9])
                                    ->where('appointment_code', '!=', $_REQUEST['apid'])
                                    ->where('date', $_REQUEST['q'])
                                    ->where('room_id', $key)
                                    ->where(function($query)
                                            {
                                                $query->whereBetween('start_timeslot', [$_REQUEST['st'], $_REQUEST['et']])->orWhereBetween('end_timeslot', [$_REQUEST['st'], $_REQUEST['et']]);
                                            })
                                    ->get()->count();
                    if($booked_rooms == 0):
                        $avalrooms[$key] = $room;
                    endif;
                else:
                    $booked_rooms = $table->where('location_id', $_REQUEST['lid'])
                                        ->whereIn('status_id', [2,4,9])
                                        ->where('date', $_REQUEST['q'])
                                        ->where('room_id', $key)
                                        ->where(function($query)
                                            {
                                                $query->whereBetween('start_timeslot', [$_REQUEST['st'], $_REQUEST['et']])->orWhereBetween('end_timeslot', [$_REQUEST['st'], $_REQUEST['et']]);
                                            })
                                    ->get()->count();
                    if($booked_rooms == 0){
                        $avalrooms[$key] = $room;
                    }
                endif;

            endforeach;

          /*   $all_rooms = $this->__getRooms($_REQUEST['lid']);
            //return $booked_rooms;
            if(count($booked_rooms) >=1):
               // return $booked_rooms;
                $merge = []; $rmerge = [];
                foreach($booked_rooms as $booked_room){
                    $array = explode(',', $booked_room->time_slots);
                    $array_deff = array_diff($slots, $array);
                    if(count($array_deff) >= 1): $rmerge[] = $booked_room->room_id; endif;
                };
                $rmerge = array_filter($rmerge);
                //return ['rmerge' => $rmerge, 'all_rooms' => $all_rooms, 'diff' => array_diff($all_rooms, $rmerge)];

                if($rmerge): $rmerge =  array_flip($rmerge); endif;
                return array_diff_key($all_rooms, $rmerge);
            else:
                return $all_rooms;
            endif;*/
            return $avalrooms;

        } else {
            return ['Invalid data'];
        }
    }

    public function update(Request $request, $id)
    {
        $record = Appointments::findOrFail($id);
        $this->validate($request, [
            'status_id' => 'nullable|numeric|max:10'
        ]);
        $record->update(['status_id' => $request['status_id']]);
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Appointments::findOrFail($id);
        $record->delete();
        return ['message' => 'record deleted'];
    }

    public function cancelAppointment(Request $request)
    {
        $this->validate($request, [
            'reason' => 'required',
        ]);
        $data = ['reason' => $request['reason'],
                        'admin' => Auth::user()->id,
                        'admin_name' => Auth::user()->name,
                        'time' => time()
                    ];

        $year = '20'.substr($request['appointment_code'], 1, 2);
        $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            $appointment = $table->where('appointment_code', $request['appointment_code'])->get();

            DB::table($tablename)
            ->where('appointment_code', $request['appointment_code'])
            ->update(['status_id' => 11, 'cancel_reason' => json_encode($data) ]);
        return ['status' => 'success'];
    }

    public function calledAppointment(Request $request)
    {
        $statuss = ['', 'Not answered', 'Not coming', 'Confirmed'];
        $this->validate($request, [
            'appointment_code' => 'required',
        ]);
        $data = ['admin' => Auth::user()->id,
                    'admin_name' => Auth::user()->name,
                    'time' => date('d M,Y H:i A', time()),
                    'status' => $statuss[$request['call_point']]
                ];

        $year = '20'.substr($request['appointment_code'], 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $appointment = $table->where('appointment_code', $request['appointment_code'])->get()->first();
        
        if($appointment->call_status):
            $cs = json_decode($appointment->call_status);
            array_push($cs, $data);
            $cs2 = json_encode($cs);
        else:
            $cs2 = json_encode([$data]);
        endif;

        DB::table($tablename)
            ->where('appointment_code', $request['appointment_code'])
            ->update(['call_status' => $cs2, 'call_point' => $request['call_point']]);
        return ['status' => 'success'];
    }

    public function AppointmentCallData($apid)
    {
        $year = '20'.substr($apid, 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $appointment = $table->select('call_point', 'call_status', 'appointment_code')->where('appointment_code', $apid)->get()->first();
        $appointment->call_status = ($appointment->call_status)?json_decode($appointment->call_status):[];
        return $appointment;
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = Appointments::where(function($query) use ($search){
                $query->where('appointment_type','LIKE',"%$search%")->orwhere('appointment_reason','LIKE',"%$search%")->orwhere('date','LIKE',"%$search%")->orwhere('timeframe','LIKE',"%$search%");
            })->latest()->paginate(15);
        }
        else{
            $records = '';
        }
        return $records;
    }

    private function __getTimeSlots($barrier = 0)
    {
        $slots = TimeSlots::where('season', 1)
                            ->get();
        foreach($slots as $slot) {
            if($slot->id > $barrier):
                $activeSlots[$slot->id] = $slot->time;
            endif;
        }
        return $activeSlots;
    }

    private function __getAllTimeSlots($barrier = 0)
    {
        $slots = TimeSlots::get();
        foreach($slots as $slot) {
            if($slot->id > $barrier):
                $activeSlots[$slot->id] = $slot->time;
            endif;
        }
        return $activeSlots;
    }

    private function __getEndTimeSlots($barrier = 0)
    {
        $slots = TimeSlots::where('season', 1)
                            ->get();
        foreach($slots as $slot) {
            if($slot->id >= $barrier):
                $activeSlots[$slot->id] = $slot->closing;
            endif;
        }
        return $activeSlots;
    }

    private function __getAllEndTimeSlots($barrier = 0)
    {
        $slots = TimeSlots::get();
        foreach($slots as $slot) {
            $activeSlots[$slot->id] = $slot->closing;
        }
        return $activeSlots;
    }

    private function __getDoctors()
    {
        return Doctors::where('status_id', 2)->get();
    }

    private function __getRooms($lid = null)
    {
        $activeRooms = [];
        $rooms = Rooms::where('status_id', 2)
                        ->where('location_id', $lid)
                        ->get();
        foreach($rooms as $room) {
            $activeRooms[$room->id] = $room->room_name;
        }
        return $activeRooms;
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

    public function showHistory($id) {
        $history = [];
        $year = '20'.substr($id, 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $record =  $table->select("$tablename.*")
                    ->where('appointment_code', $id)
                    ->get()
                    ->first();
        $all_years = FinancialYears::where('year', '<=', $year)->orderBy('year', 'desc')->get();
        foreach($all_years as $ayear):
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$ayear->year.'s');
            $tablename = $table->getTable();
            $res_appoints = $table->select("$tablename.*")
                        ->where('user_id', $record->user_id)
                        ->where('appointment_type_id', 1)
                        ->where('is_dummy', 0)
                        ->whereIn('status_id', [2,4,5,9,10,12])
                        ->where('date', '<', $record->date)
                        ->orderby('date', 'desc')
                        ->get();
            foreach($res_appoints as $appoint):
                $history['symptoms'][$appoint->appointment_code] = ['date' => date('d-M-Y', strtotime($appoint->date)), 'data' => $appoint->symptoms];
                $history['tests'][$appoint->appointment_code] = ['date' => date('d-M-Y', strtotime($appoint->date)), 'data' => $appoint->tests];
                $history['medicines'][$appoint->appointment_code] = ['date' => date('d-M-Y', strtotime($appoint->date)), 'data' => $appoint->medicines];
                $history['therapies'][$appoint->appointment_code] = ['date' => date('d-M-Y', strtotime($appoint->date)), 'data' => $appoint->therapies];
                $history['diagnosis'][$appoint->appointment_code] = ['date' => date('d-M-Y', strtotime($appoint->date)), 'data' => $appoint->diagnosis];
                $history['oe_categories'][$appoint->appointment_code] = ['date' => date('d-M-Y', strtotime($appoint->date)), 'data' => $appoint->oe_categories];
                $history['drug_history'][$appoint->appointment_code] = ['date' => date('d-M-Y', strtotime($appoint->date)), 'data' => $appoint->drug_history];
                $history['remarks'][$appoint->appointment_code] = ['date' => date('d-M-Y', strtotime($appoint->date)), 'remark' => $appoint->dr_remark, 'reminder' => $appoint->reminder];
            endforeach;
        endforeach;
        return $history;
    }

    public function addPrescription(Request $request) {
        $year = '20'.substr($request['appointment_code'], 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        //return $request;

        $appointment = $table->select("$tablename.*")->where('appointment_code', $request['appointment_code'])->first();
        $appointment['dr_remark'] = $request['dr_remark'];
        $medicines = [];
        if(count($request['medicine__id']) >= 1):
            foreach ($request['medicine__id'] as $key => $value):
                if($value):
                    if(isset($request['medicine__remark'][$key])):  $remark = $request['medicine__remark'][$key];
                    else:  $remark = '';  endif;
                    if(isset($request['medicine__days'][$key])):  $medicine__days = $request['medicine__days'][$key];
                    else:  $medicine__days = '';  endif;
                    if(isset($request['medicine__qty'][$key])):  $medicine__qty = $request['medicine__qty'][$key];
                    else:  $medicine__qty = '';  endif;
                    if(isset($request['medicine__dtab'][$key])):  $medicine__dtab = $request['medicine__dtab'][$key];
                    else:  $medicine__dtab = '';  endif;
                   // endif;
                    $record = Medicines::where('id', $value)->first();
                    $medicines[] = ['id' => $value,
                                    'name' => $record->name,
                                    'days' => $medicine__days,
                                    'dtab' => $medicine__dtab,
                                    'qty' => $medicine__qty,
                                    'remark' => $remark ];
                endif;
            endforeach;
            $appointment['medicines'] = json_encode($medicines);
        endif;

        $symptoms = [];
        if(count($request['symptom__id']) >= 1):
            foreach ($request['symptom__id'] as $key => $value):
                if($value):
                    $record = Symptoms::where('id', $value)->first();
                    $symptoms[] = ['id' => $value,
                                   'name' => $record->value,
                                    'remark' => (isset($request['symptom__remark'][$key]))?$request['symptom__remark'][$key]:'-'];
                endif;
            endforeach;
            $appointment['symptoms'] = json_encode($symptoms);
        endif;

        /* $diseases = [];
        if(count($request['disease__id']) >= 1):
            foreach ($request['disease__id'] as $key => $value):
                if($value):
                    $record = Diseases::where('id', $value)->first();
                    $diseases[] = ['id' => $value,
                                    'name' => $record->value,
                                    'remark' => (isset($request['disease__remark'][$key]))?$request['disease__remark'][$key]:'-' ];
                endif;
            endforeach;
            $appointment['diseases'] = json_encode($diseases);
        endif; */

        $diagnosis = [];
        if(count($request['diagnosis__id']) >= 1):
            foreach ($request['diagnosis__id'] as $key => $value):
                if($value):
                    $record = Diseases::where('id', $value)->first();
                    $diagnosis[] = ['id' => $value,
                                    'name' => $record->value,
                                    'code' => $record->code,
                                    'remark' => (isset($request['diagnosis__remark'][$key]))?$request['diagnosis__remark'][$key]:'-' ];
                endif;
            endforeach;
            $appointment['diagnosis'] = json_encode($diagnosis);
        endif;

        $therapies = [];
        if(count($request['therapy__id']) >= 1):
            foreach ($request['therapy__id'] as $key => $value):
                if($value):
                    $record = Treatments::where('id', $value)->first();
                    $therapies[] = ['id' => $value,
                                    'name' => $record->treatment,
                                    'days' => $request['therapy__days'][$key],
                                    'remark' => (isset($request['therapy__remark'][$key]))?$request['therapy__remark'][$key]:'-' ];
                endif;
            endforeach;
            $appointment['therapies'] = json_encode($therapies);
        endif;

        $oe_categories = [];
        if(count($request['oecategory__id']) >= 1):
            foreach ($request['oecategory__id'] as $key => $value):
                if($value):
                    $record = OeCategories::where('id', $value)->first();
                    $oe_categories[] = ['id' => $value,
                                        'name' => $record->value,
                                        'result' => (isset($request['oecategory__result'][$key]))?$request['oecategory__result'][$key]:'-',
                                        'remark' => (isset($request['oecategory__remark'][$key]))?$request['oecategory__remark'][$key]:'-'];
                endif;
            endforeach;
            $appointment['oe_categories'] = json_encode($oe_categories);
        endif;

        $tests = []; $ltests = [];
        if(count($request['test__id']) >= 1):
            foreach ($request['test__id'] as $key => $value):
                if($value):
                    $tests['id'] = $value;
                    $tests['type'] = $request['test__type_id'][$key];
                    if($tests['type'] == 'labtest'):
                        $record = Laboratories::where('id', $value)->first();
                    elseif($tests['type'] == 'xray'):
                        $record = Xrays::where('id', $value)->first();
                    else:
                        $record = '';
                    endif;
                    $tests['name'] = $record->value;
                    if(isset($request['test__remark'][$key])):
                        $tests['remark'] = (isset($request['test__remark'][$key]))?$request['test__remark'][$key]:'-';
                    else:
                        $tests['remark'] = '';
                    endif;
                    if(isset($request['test__result'][$key])):
                        $tests['result'] = (isset($request['test__result'][$key]))?$request['test__result'][$key]:'-';
                    else:
                        $tests['result'] = '';
                    endif;
                    if(isset($request['test__file'][$key])):
                        $name = strtolower($appointment['appointment_code']).'-'.md5($key).'-'.time().'.'.explode('/', explode(':', substr($request['test__file'][$key], 0, strpos($request['test__file'][$key], ';')))[1])[1];
                        \Image::make($request['test__file'][$key])->save(public_path('files/attachments/').$name);
                        $tests['attachment'] = $name;
                    else:
                        $tests['attachment'] = '';
                    endif;
                    $ltests[] = $tests;
                endif;
            endforeach;
            $appointment['tests'] = json_encode($ltests);
        endif;

        $drug_history = [];
        if($request['drug_history']) $drug_history['drug_history'] = $request['drug_history'];
        if($request['general_history']) $drug_history['general_history'] = $request['general_history'];
        if($request['family_history']) $drug_history['family_history'] = $request['family_history'];

        if($drug_history): $appointment['drug_history'] = json_encode($drug_history); endif;

        $appointment['dr_remark'] = $request['dr_remark'];
        $appointment['reminder_days'] = $request['followup'];
        if($request['followup']): $appointment['reminder_date'] = date('Y-m-d', strtotime('+'.$request['followup'].' days')); endif;
        $appointment['status_id'] = $request['status_id'];

        $appointment->update(get_object_vars($appointment));

        return $appointment;
    }

    public function addCourse() {
        $aid = \Request::get('aid');
        $cid = \Request::get('cid');

        $year = '20'.substr($aid, 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $appointment = DB::table($tablename)->where('appointment_code', $aid)->update(['course_id' => $cid]);
        return $appointment;
    }

    public function getSecondTherapist()
    {
        if(is_numeric($_REQUEST['lid']) && ($_REQUEST['lid'] >= 1)): // do nothing
        else: $_REQUEST['lid'] = Auth::user()->location_id; endif;

        if($_REQUEST['st'] && $_REQUEST['et'] && $_REQUEST['q'] && $_REQUEST['lid'] && $_REQUEST['did']) {
            if($_REQUEST['dtype'] == 2):
                $sherapists = Doctors::where('designation_type_id', 1)
                                ->where('location_id', Auth::user()->location_id)
                                ->where('status_id', 2)
                                ->where('id', '!=', $_REQUEST['did'])
                                ->get();
            else:
                $sherapists = Doctors::where('designation_type_id', 2)
                                ->where('location_id', Auth::user()->location_id)
                                ->where('status_id', 2)
                                ->where('id', '!=', $_REQUEST['did'])
                                ->get();
            endif;

            $avtherapists = [];
            if(!isset($_REQUEST['apid'])): $_REQUEST['apid'] =''; endif;
            foreach($sherapists as $sherapist):
                $year = substr($_REQUEST['q'], 0, 4);
                $table =  new Appointments;
                $table = $table->setTable('appointment'.$year.'s');
                $id = $sherapist->id;
                if($_REQUEST['apid']):
                    $booked_slots = $table->where('location_id', $_REQUEST['lid'])
                                    ->where('appointment_code', '!=', $_REQUEST['apid'])
                                    ->whereIn('status_id', [2,4,5,8,9,10])
                                    ->where('date', $_REQUEST['q'])
                                    ->where(function($query)
                                        {
                                            $query->whereBetween('start_timeslot', [$_REQUEST['st'], $_REQUEST['et']])->orWhereBetween('end_timeslot', [$_REQUEST['st'], $_REQUEST['et']]);
                                        })
                                    ->where(function($query2) use ($id)
                                        {
                                            $query2->where('doctor_id', $id)
                                            ->orWhere('second_doctor_id', $id);
                                        })
                                    ->get()->count();
                else:
                    $booked_slots = $table->where('location_id', $_REQUEST['lid'])
                                    ->whereIn('status_id', [2,4,5,8,9,10])
                                    ->where('date', $_REQUEST['q'])
                                    ->where(function($query)
                                        {
                                            $query->whereBetween('start_timeslot', [$_REQUEST['st'], $_REQUEST['et']])->orWhereBetween('end_timeslot', [$_REQUEST['st'], $_REQUEST['et']]);
                                        })
                                    ->where(function($query2) use ($id)
                                        {
                                            $query2->where('doctor_id', $id)
                                            ->orWhere('second_doctor_id', $id);
                                        })
                                    ->get()->count();
                endif;
                if($booked_slots == 0):
                    $table2 =  new Availabilities;
                    $table2 = $table2->setTable('availability'.$year.'s');

                    $aval_slots = $table2->where('location_id', $_REQUEST['lid'])
                                        ->where('doctor_id', $sherapist->id)
                                        ->where('type', 1)
                                        ->where('date', $_REQUEST['q'])
                                        ->get()->first();
                    if($aval_slots):
                        $shift = explode(',', $aval_slots->shift_timings);
                        $looking = range($_REQUEST['st'], $_REQUEST['et']);
                        $check1 = array_intersect($shift, $looking);
                        if(count(array_diff($looking,$check1)) == 0){
                            $avtherapists[] = $sherapist;
                       }
                    endif;
                endif;
            endforeach;
            return $avtherapists;
        } else {
            return [];
        }
    }

    public function changePatient(Request $request)
    {
        $year = '20'.substr($request['appointment_code'], 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        DB::table($tablename)
            ->where('appointment_code', $request['appointment_code'])
            ->update(['user_id' => $request['user_id']]);
        return ['msg' => 'updation successful'];
    }

    public function updateAppointment(Request $request)
    {
        $year = '20'.substr($request['appointment_code'], 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        DB::table($tablename)
            ->where('appointment_code', $request['appointment_code'])
            ->update([
                'doctor_id' => $request['doctor_id'],
                'room_id' => $request['room_id'],
                'date' => $request['date'],
                'second_doctor_id' => $request['second_doctor_id'],
                'treatment_id' => $request['treatment_id'],
                'user_remark' => $request['user_remark'],
                'start_timeslot' => $request['start_time'],
                'end_timeslot' => $request['end_time'],
                'call_point' => 0,
                'call_status' => '',
                'updated_at' => Carbon::now()
            ]);
        return ['msg' => 'updation successful'];
    }

    public function UsersQuickHistory(Request $request)
    {
        $result = [];  $result2 = [];
        $starts = $this->__getAllTimeSlots();
        $ends = $this->__getAllEndTimeSlots();

        $userId = $request['user_id'];
        $date = $request['date'];
        $appointments = [];
        $year =  date('Y', strtotime($date));
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $appointments = $table->select("$tablename.id", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot", "$tablename.status_id", "$tablename.date", 'statuses.status', 'visit_types.visit_type', 'treatments.treatment', 'doctors.name', 'locations.clinic_name')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->where("$tablename.user_id", $userId)
                        ->where("$tablename.appointment_type_id", 1)
                        ->where("$tablename.status_id", 5)
                        ->where("$tablename.is_dummy", '0')
                        ->where('date', '<', $date)
                        ->orderBy('date', 'desc')->take(5)->get();

        if($appointments->count() >= 1):
            foreach ($appointments as $appointment) {
                if($appointment->end_timeslot):
                    $seslot = $starts[$appointment->start_timeslot].' - '.$ends[$appointment->end_timeslot];
                else: $seslot = 'Not Defined'; endif;
                $result[] = ['appointment_code' => $appointment->appointment_code,
                            'treatment' => $appointment->treatment,
                            'date' => $appointment->date,
                            'timeslot' => $seslot,
                            'visit_type' => $appointment->visit_type,
                            'name' => $appointment->name,
                            'status' => $appointment->status,
                            'clinic_name' => $appointment->clinic_name
                            ];
            }
        else:
            $result = [];
        endif;

        if($appointments->count() <= 4):
            $appointments2 = [];
            $remaining = 5 - $appointments->count();
            $table =  new Appointments;
            $year = $year - 1;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            $appointments2 = $table->select("$tablename.id", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot", "$tablename.status_id", "$tablename.date", 'statuses.status', 'visit_types.visit_type', 'appointment_types.appointment_type',  'treatments.treatment', 'doctors.name', 'locations.clinic_name')
                            ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                            ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                            ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                            ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                            ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                            ->join('locations', 'locations.id', '=', "$tablename.location_id")
                            ->where("$tablename.user_id", $userId)
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.status_id", 5)
                            ->where("$tablename.is_dummy", '0')
                            ->where('date', '<', $date)
                            ->orderBy('date', 'desc')->take($remaining)->get();

            if($appointments2->count() >= 1):
                foreach ($appointments2 as $appointment) {
                    $result2[] = ['appointment_code' => $appointment->appointment_code,
                                'treatment' => $appointment->treatment,
                                'date' => $appointment->date,
                                'visit_type' => $appointment->visit_type,
                                'timeslot' => $starts[$appointment->start_timeslot].' - '.$ends[$appointment->end_timeslot],
                                'name' => $appointment->name,
                                'status' => $appointment->status,
                                'clinic_name' => $appointment->clinic_name
                                ];
                }
            else:
                $result2 = [];
            endif;
        endif;

        return array_merge($result, $result2);
    }

    public function getAvailableDoctors(Request $request)
    {
        $doctors = [];
        return [];
    }

    public function checkFollowup(Request $request)
    {
        $year = '20'.substr($request['appointment_code'], 1, 2);

        $user = UserDocuments::select('insurances.followup_days')
                        ->where('user_documents.user_id', $request['user_id'])
                        ->join('insurances', 'insurances.id', '=', 'user_documents.insurance_id')
                        ->get()->first();

        $days = $user['followup_days'];

        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        if(Schema::hasTable($tablename)):
            $appointment =  $table->where('appointment_code', $request['appointment_code'])
                                ->where('status_id', 5)
                                ->whereIn('visit_type_id', [1,3])
                                ->where('appointment_type_id', 1)
                                ->where('user_id', $request['user_id'])
                                ->get()->first();
            if(!$appointment): return ['status' => 4]; endif;
            $diff = date_diff(date_create(date('Y-m-d', strtotime($request['date']))), date_create($appointment->date));
            if($diff->days <= $days): return ['status' => 1, $diff->days];
            elseif($diff->days <= 90): return ['status' => 2, $diff->days];
            else: return ['status' => 3, $diff->days]; endif;
        else:
            return ['status' => 4];
        endif;
    }

    public function getByCourse(Request $request)
    {
        //return $request;
        $syear = date('Y', strtotime($request['start_date']));
        $eyear = date('Y', strtotime($request['end_date']));
        if($syear == $eyear): $year = $syear; else: $year = $syear; endif;
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        return $table->select("$tablename.id", "$tablename.date",  "$tablename.ainvoice", "$tablename.created_at", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot", "$tablename.status_id", "$tablename.user_id", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'treatments.treatment as reason')
                    ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                    ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                    ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('locations', 'locations.id', '=', "$tablename.location_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereIN("$tablename.status_id", [2,4,5,8,9])
                    ->where('course_id', $request['ccode'])
                    ->where("$tablename.is_dummy", '0')
                    ->orderBy('date', 'asc')->get();
    }

    public function getPackageAppointments($ccode = null)
    {
        $year = '20'.substr($ccode, 1, 2);
        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $course = $table->where('course_code', $ccode)->first();

        $start = $this->__getAllTimeSlots();
        $end = $this->__getAllEndTimeSlots();

        $return = [];
        foreach (explode(',', $course['appointments']) as $key => $apid):
            $ayear = '20'.substr($apid, 1, 2);
            $table2 =  new Appointments;
            $table2 = $table2->setTable('appointment'.$ayear.'s');
            $tablename = $table2->getTable();
            $app = $table2->select("$tablename.date", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot", "$tablename.status_id", 'appointment_types.appointment_type as appointment_type',  'treatments.treatment as reason')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->whereIN("$tablename.status_id", [2,4,5,8,9])
                        ->where('appointment_code', $apid)
                        ->first();
            $return[$key]['code'] = $apid;
            $return[$key]['date'] = $app->date;
            $return[$key]['reason'] = $app->reason;
            $return[$key]['time'] = $start[$app->start_timeslot].' - '.$end[$app->end_timeslot];
        endforeach;
        return $return;
    }

    public function search(Request $request)
    {
        $conditions = [];
        $year = date('Y');

        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        if($request['status_id']): $conditions["$tablename.status_id"] = $request['status_id']; endif;
        if($request['appointment_code']): $conditions["$tablename.appointment_code"] = $request['appointment_code']; endif;
        if($request['doctor_id']): $conditions["$tablename.doctor_id"] = $request['doctor_id']; endif;
        if($request['user_id']): $conditions["$tablename.user_id"] = $request['user_id']; endif;
        if($request['treatment_id']): $conditions["$tablename.treatment_id"] = $request['treatment_id']; endif;

        if(count($conditions) == 0): return []; endif;

        if(Auth::user()->admin_type_id == 2):
            $return = $table->select("$tablename.id", "$tablename.date", "$tablename.feedback", "$tablename.fsms", "$tablename.ainvoice", "$tablename.created_at", "$tablename.appointment_type_id", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot", "$tablename.status_id", "$tablename.user_id", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name', 'treatments.treatment as reason')
                    ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                    ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                    ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('locations', 'locations.id', '=', "$tablename.location_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->where("$tablename.doctor_id", Auth::user()->relative_id)
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->where("$tablename.is_dummy", '0')
                    ->where('date', date('Y-m-d', time()))
                    ->where($conditions)
                    ->orderBy('start_timeslot', 'asc')->get();
        else:
            $return = $table->select("$tablename.id", "$tablename.date", "$tablename.created_at", "$tablename.feedback", "$tablename.fsms", "$tablename.appointment_code",  "$tablename.appointment_type_id", "$tablename.status_id", "$tablename.start_timeslot", "$tablename.end_timeslot",  "$tablename.user_id", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name', 'doctors.name as doctor', 'treatments.treatment as reason')
                    ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                    ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                    ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                    ->join('locations', 'locations.id', '=', "$tablename.location_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->where('date', date('Y-m-d', time()))
                    ->where("$tablename.is_dummy", '0')
                    ->where($conditions)
                    ->orderBy('start_timeslot', 'asc')->get();
        endif;
        return $return;
    }

    public function __invoiceExist($icode = null)
    {
        $year = '20'.substr($icode, 3, 2);
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $invoice = $table->where('invoice_number', $icode)
                        ->get()->first();
        if($invoice): return true; else: return false; endif;
    }

    public function getBulkAppointments(Request $request)
    {
        $start = $this->__getAllTimeSlots();
        $end = $this->__getAllEndTimeSlots();

        $return = [];
        $appoints = explode(',', $request['appointments']);
        $appoints = array_filter($appoints);
        foreach ($appoints as $key => $apid):
            $ayear = '20'.substr($apid, 1, 2);
            $table2 =  new Appointments;
            $table2 = $table2->setTable('appointment'.$ayear.'s');
            $tablename = $table2->getTable();
            $app = $table2->select("$tablename.*", "statuses.status", "statuses.css", 'doctors.name as doctor',  'treatments.treatment as reason')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->whereIn("$tablename.status_id", [2,4,5,8,9,10])
                        ->where('appointment_code', $apid)
                        ->with('user')->get()->first();
            if($app):
                $tstart = '-'; $tend = '-';
                if($app->start_timeslot) $tstart = $start[$app->start_timeslot];
                if($app->end_timeslot) $tend = $end[$app->end_timeslot];
                $return[] = ['appointment_code' => $apid,
                                'date' => $app->date,
                                'reason' => $app->reason,
                                'created' => $app->created_at,
                                'status_id' => $app->status_id,
                                'comment' => $app->comment,
                                'status' => $app->status,
                                'css' => $app->css,
                                'treatment_id' => $app->treatment_id,
                                'username' => $app->user->username,
                                'first_name' => $app->user->user_profile->first_name,
                                'doctor' => $app->doctor,
                                'invoice' => $app->ainvoice,
                                'time' => $tstart.' - '.$tend];
            endif;
        endforeach;
        return $return;
    }

    public function getBulkCashAppointments(Request $request)
    {
        $start = $this->__getAllTimeSlots();
        $end = $this->__getAllEndTimeSlots();
        $return = [];
        $counter = [];
        $appoints = explode(',', $request['appointments']);
        $appoints = array_filter($appoints);
        $count = 0;
        foreach ($appoints as $key => $apid):
            $ayear = '20'.substr($apid, 1, 2);
            $table2 =  new Appointments;
            $table2 = $table2->setTable('appointment'.$ayear.'s');
            $tablename = $table2->getTable();
            $app = $table2->select("$tablename.*", 'treatments.treatment as reason', 'treatments.cost')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->whereIn("$tablename.status_id", [2,4,5,8,9,10])
                        ->where('appointment_code', $apid)
                        ->whereNull('ainvoice')
                        ->get()->first();
            if($app):
                $return[$count]['appointment_code'] = $apid;
                $return[$count]['date'] = $app->date;
                $return[$count]['reason'] = $app->reason;
                $return[$count]['cost'] = $app->cost;
                $return[$count]['time'] = $start[$app->start_timeslot].' - '.$end[$app->end_timeslot];
                array_push($counter, $count);
                $count++;
            endif;
        endforeach;
        return ['appointments' => $return, 'countlist' => $counter];
    }

    public function getBulkInsuranceAppointments(Request $request)
    {
        $start = $this->__getAllTimeSlots();
        $end = $this->__getAllEndTimeSlots();

        $return = [];
        $appoints = explode(',', $request['appointments']);
        $appoints = array_filter($appoints);
        foreach ($appoints as $key => $apid):
            $ayear = '20'.substr($apid, 1, 2);
            $table2 =  new Appointments;
            $table2 = $table2->setTable('appointment'.$ayear.'s');
            $tablename = $table2->getTable();
            $app = $table2->select("$tablename.*", "statuses.status", "statuses.css", 'doctors.name as doctor',  'treatments.treatment as reason')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->whereIn("$tablename.status_id", [2,4,5,8,9,10])
                        ->where('appointment_code', $apid)
                        ->get()->first();

            if($app):
                $return[] = ['appointment_code' => $apid,
                                'date' => $app->date,
                                'reason' => $app->reason,
                                'created' => $app->created_at,
                                'status' => $app->status,
                                'css' => $app->css,
                                'doctor' => $app->doctor,
                                'invoice' => $app->ainvoice,
                                'time' => $start[$app->start_timeslot].' - '.$end[$app->end_timeslot]];
            endif;

        endforeach;
        return $return;
    }

    public function getInsuranceTreatments(Request $request)
    {
        $id = $request['appointment_code'];
        $return = []; $total = 0;
        $year = '20'.substr($id, 1, 2);
        $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            $appointment = $table->select("$tablename.*", 'user_documents.insurance_id')
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('user_documents', 'user_documents.user_id', '=', 'users.id')
                        ->where('appointment_code', $id)
                        ->whereIn("$tablename.status_id", [2,4,5,9])
                        ->get()->first();
            if($appointment):
                $treatments = json_decode($appointment->therapies);
                $count = 1; $tcount = 0;
                $d3 = ''; $d5 = '';
                foreach ($treatments as $treatment) {
                    $price = $this->getTreatmentMappedInsurances($treatment->id, $appointment->insurance_id);
                    if($price) {
                        if($price->cash_discount == 1):
                            if($treatment->days >= 5):
                                $discount = ($treatment->days)*($price->cost)*(str_replace('%', '', $price->cd56))/100;
                            else:
                                $discount = ($treatment->days)*($price->cost)*(str_replace('%', '', $price->discount))/100;
                            endif;
                        else:
                            if(strpos($price->discount, '%')) {
                                $discount = $treatment->days*$price->cost*str_replace('%', '', $price->discount)/100;
                            }
                            else {
                                $discount = $treatment->days*$price->discount;
                            }
                        endif;
                        $qty = ($treatment->approved)?$treatment->approved:$treatment->days;
                        $return[] = ['treatment' => $treatment->name,
                                        'cost' => $price->cost,
                                        'qty' => $qty,
                                        'discount' => $discount,
                                        'subtotal' => $price->cost*$qty,
                                        'total' => ($price->cost*$qty) -  $discount ];
                        $total = $total + (($price->cost*$qty) -  $discount);
                        $count++;
                        $d3 = '';
                        $d5 = '';
                        $tcount = $tcount + $qty;
                    }
                }

                $code = explode('-', $request['invoice_number']);
                $year = '20'.substr($code[1], 0, 2);
                $itable =  new Invoices;
                $itable = $itable->setTable('invoice'.$year.'s');
                $invoice = $itable->where('invoice_number', $request['invoice_number'])->get()->first();
                $discount_value = ''; $discount = '';
                /* if(($invoice->ins_disc == 1) & ($count >= 3)){
                    if($count >= 5) {
                        $discount = '5+ Appointments Cash Discount';
                        if(strpos($d5, '%')) { $discount_value = $total*str_replace('%', '', $d5)/100; }
                        else{$discount_value = $d5;}
                    } else {
                        $discount = '3-4 Appointments Cash Discount';
                        if(strpos($d3, '%')) { $discount_value = $total*str_replace('%', '', $d3)/100; }
                        else{$discount_value = $d3;}
                    }
                } */
                $gtotal = $total;

            endif;

            return ['treatments' => $return, 'treatments_count' => $tcount, 'total' => $gtotal, 'sub_total' => $total, 'discount' => $discount, 'discount_value' => $discount_value];
    }

    public function getCashInvoicingTreatments(Request $request)
    {
        $id = $request['appointment_code'];
        $return = []; $total = 0;
        $year = '20'.substr($id, 1, 2);
        $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            $appointment = $table->select("$tablename.*", 'user_documents.insurance_id')
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('user_documents', 'user_documents.user_id', '=', 'users.id')
                        ->where('appointment_code', $id)
                        ->whereIn("$tablename.status_id", [2,4,5,9])
                        ->get()->first();
            if($appointment):
                $treatments = json_decode($appointment->therapies);
                $count = 1; $tcount = 0;
                $d3 = ''; $d5 = '';
                foreach ($treatments as $treatment) {
                    $therapy = Treatments::where('id', $treatment->id)->get()->first();
                    $return[] = ['treatment' => $treatment->name,
                                        'cost' => $therapy->cost,
                                        'qty' => $treatment->days,
                                        'subtotal' => $therapy->cost*$treatment->days,
                                        'total' => ($therapy->cost*$treatment->days) ];
                        $total = $total + (($therapy->cost*$treatment->days) );
                        $count++;
                        $d3 = '';
                        $d5 = '';
                        $tcount = $tcount + $treatment->days;
                }

                if($request['invoice_number']):
                    $code = explode('-', $request['invoice_number']);
                    $year = '20'.substr($code[1], 0, 2);
                    $itable =  new Invoices;
                    $itable = $itable->setTable('invoice'.$year.'s');
                    $invoice = $itable->where('invoice_number', $request['invoice_number'])->get()->first();
                    $discount_value = ''; $discount = '';
                    /* if(($invoice->ins_disc == 1) & ($count >= 3)){
                        if($count >= 5) {
                            $discount = '5+ Appointments Cash Discount';
                            if(strpos($d5, '%')) { $discount_value = $total*str_replace('%', '', $d5)/100; }
                            else{$discount_value = $d5;}
                        } else {
                            $discount = '3-4 Appointments Cash Discount';
                            if(strpos($d3, '%')) { $discount_value = $total*str_replace('%', '', $d3)/100; }
                            else{$discount_value = $d3;}
                        }
                    } */

                else:
                    $discount_value = ''; $discount = '';
                endif;
                $gtotal = $total;
            endif;

            return ['treatments' => $return, 'treatments_count' => $tcount, 'total' => $gtotal, 'sub_total' => $total, 'discount' => $discount, 'discount_value' => $discount_value];
    }

    public function setInsuranceTreatments(Request $request)
    {
        $start = $this->__getAllTimeSlots();
        $end = $this->__getAllEndTimeSlots();
        $return = []; $total = 0; $atreats = [];
        $treatments = $request['treatments'];
        foreach($treatments as $treatment) {
            $atreats[$treatment['id']] = $treatment['days'];
        }
        foreach ($request['appointments'] as $key => $value)
        {
            $id = $value['appointment_code'];
            $year = '20'.substr($id, 1, 2);
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            $appointment = $table->select("$tablename.*", 'treatments.treatment as reason')
                                    ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                                    ->where('appointment_code', $id)
                                    ->get()->first();

            if($appointment):

                $count = 1;
                $d3 = ''; $d5 = '';
                if($appointment['treatment_id'] == 56):
                    $price = $this->getConsultMappedInsurances($appointment['treatment_id'], $request['insurance_id']);
                    if($price):

                        if(strpos($price->discount, '%')) {
                            $discount = $price->cost*str_replace('%', '', $price->discount)/100;
                        }
                        else {
                            $discount = $price->discount;
                        }
                        $return[] = ['treatment' => $appointment->reason,
                                        'cost' => $price->cost,
                                        'apcode' => $appointment->appointment_code,
                                        'datetime' => date('d-m-Y', strtotime($appointment->date)).' '. $start[$appointment->start_timeslot].' - '.$end[$appointment->end_timeslot],
                                        'treatment_id' => $appointment->treatment_id,
                                        'discount' => $discount,
                                        'total' => round($price->cost -  $discount, 3) ];
                        $total = $total + ($price->cost  -  $discount);
                        $count++;
                        $d3 = '';
                        $d5 = '';
                    endif;
                else:
                    $price = $this->getTreatmentMappedInsurances($appointment['treatment_id'], $request['insurance_id']);
                    if($price) {
                        if(($price->cash_discount == 1) && isset($atreats[$appointment['treatment_id']]) && ($atreats[$appointment['treatment_id']] >= 5)):
                            $discount = $price->cost*str_replace('%', '', $price->cd56)/100;
                        else:
                            if(strpos($price->discount, '%')) {
                                $discount = $price->cost*str_replace('%', '', $price->discount)/100;
                            }
                            else {
                                $discount = $price->discount;
                            }
                        endif;
                        $return[] = ['treatment' => $appointment->reason,
                                        'cost' => $price->cost,
                                        'apcode' => $appointment->appointment_code,
                                        'datetime' => date('d-m-Y', strtotime($appointment->date)).' '. $start[$appointment->start_timeslot].' - '.$end[$appointment->end_timeslot],
                                        'treatment_id' => $appointment->treatment_id,
                                        'discount' => $discount,
                                        'total' => round($price->cost -  $discount, 3) ];
                        $total = $total + ($price->cost  -  $discount);
                        $count++;
                        $d3 = '';
                        $d5 = '';
                    }
                endif;


                $code = explode('-', $request['invoice_number']);
                $year = '20'.substr($code[1], 0, 2);
                $itable =  new Invoices;
                $itable = $itable->setTable('invoice'.$year.'s');
                $invoice = $itable->where('invoice_number', $request['invoice_number'])->get()->first();
                $discount_value = ''; $discount = '';
                /* if(($invoice->ins_disc == 1) & ($count >= 3)){
                    if($count >= 5) {
                        $discount = '5+ Appointments Cash Discount';
                        if(strpos($d5, '%')) { $discount_value = $total*str_replace('%', '', $d5)/100; }
                        else{$discount_value = $d5;}
                    } else {
                        $discount = '3-4 Appointments Cash Discount';
                        if(strpos($d3, '%')) { $discount_value = $total*str_replace('%', '', $d3)/100; }
                        else{$discount_value = $d3;}
                    }
                } */
            endif;
        }
        $gtotal = $total;
        return ['treatments' => $return, 'total' => number_format($gtotal,3), 'sub_total' => number_format($gtotal,3), 'discount' => $discount, 'discount_value' => $discount_value];
    }

    private function getTreatmentMappedInsurances($tid, $iid)
    {
        return InsuranceTreatmentMaps::select('insurance_treatment_maps.*', 'treatments.cost', 'insurances.cash_discount')
                                        ->join('treatments', 'treatments.id', '=', 'insurance_treatment_maps.treatment_id')
                                        ->join('insurances', 'insurances.id', '=', 'insurance_treatment_maps.insurance_id')
                                        ->where('insurance_treatment_maps.insurance_id', $iid)
                                        ->where('insurance_treatment_maps.treatment_id', $tid)
                                        ->get()
                                        ->first();
    }

    private function getConsultMappedInsurances($tid, $iid)
    {
        return InsuranceConsultationMaps::select('insurance_consultation_maps.*', 'treatments.cost', 'insurances.cash_discount')
                                        ->join('treatments', 'treatments.id', '=', 'insurance_consultation_maps.treatment_id')
                                        ->join('insurances', 'insurances.id', '=', 'insurance_consultation_maps.insurance_id')
                                        ->where('insurance_consultation_maps.insurance_id', $iid)
                                        ->where('insurance_consultation_maps.treatment_id', $tid)
                                        ->get()
                                        ->first();
    }

    public function upcomingSearch(Request $request)
    {
        $conditions = [];
        $year = $request['year'];

        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        if($request['date']): $conditions["$tablename.date"] = $request['date']; endif;
        if($request['appointment_code']): $conditions["$tablename.appointment_code"] = $request['appointment_code']; endif;
        if($request['doctor_id']): $conditions["$tablename.doctor_id"] = $request['doctor_id']; endif;
        if($request['user_id']): $conditions["$tablename.user_id"] = $request['user_id']; endif;
        if($request['treatment_id']): $conditions["$tablename.treatment_id"] = $request['treatment_id']; endif;

        if(count($conditions) == 0): return []; endif;

        return $table->select("$tablename.id", "$tablename.date", "$tablename.created_at", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot",  "$tablename.user_id", 'statuses.status as status', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name', 'doctors.name as doctor', 'treatments.treatment as reason')
            ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
            ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
            ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
            ->join('users', 'users.id', '=', "$tablename.user_id")
            ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
            ->join('locations', 'locations.id', '=', "$tablename.location_id")
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
            ->where('date', '>', date('Y-m-d', time()))
            ->where("$tablename.location_id", Auth::user()->location_id)
            ->where($conditions)
            ->whereNotIn("$tablename.status_id", [5,7,10])
            ->orderBy("$tablename.date", 'desc')->paginate(15);
    }

    public function historySearch(Request $request)
    {
        $conditions = [];
        $year = $request['year'];

        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        if($request['date']): $conditions["$tablename.date"] = $request['date']; endif;
        if($request['appointment_code']): $conditions["$tablename.appointment_code"] = $request['appointment_code']; endif;
        if($request['doctor_id']): $conditions["$tablename.doctor_id"] = $request['doctor_id']; endif;
        if($request['user_id']): $conditions["$tablename.user_id"] = $request['user_id']; endif;
        if($request['treatment_id']): $conditions["$tablename.treatment_id"] = $request['treatment_id']; endif;

        if(count($conditions) == 0): return []; endif;

        return $table->select("$tablename.id", "$tablename.date", "$tablename.created_at", "$tablename.feedback", "$tablename.fsms", "$tablename.appointment_code",  "$tablename.appointment_type_id", "$tablename.course_id", "$tablename.start_timeslot", "$tablename.end_timeslot", "$tablename.status_id",  "$tablename.user_id", "$tablename.is_dummy", 'statuses.status as status', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name', 'doctors.name as doctor', 'treatments.treatment as reason')
                ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                ->join('users', 'users.id', '=', "$tablename.user_id")
                ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                ->join('locations', 'locations.id', '=', "$tablename.location_id")
                ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                ->where('date', '<', date('Y-m-d', time()))
                ->where("$tablename.location_id", Auth::user()->location_id)
                ->where($conditions)
                ->whereNotIn("$tablename.status_id", [1,7])
                ->orderBy("$tablename.date", 'desc')->paginate(15);
    }

    public function drHistorySearch(Request $request)
    {
        $conditions = [];
        $year = $request['year'];

        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        if($request['date']): $conditions["$tablename.date"] = $request['date']; endif;
        if($request['appointment_code']): $conditions["$tablename.appointment_code"] = $request['appointment_code']; endif;
        if($request['user_id']): $conditions["$tablename.user_id"] = $request['user_id']; endif;
        if($request['treatment_id']): $conditions["$tablename.treatment_id"] = $request['treatment_id']; endif;

        if(count($conditions) == 0): return []; endif;

        return $table->select("$tablename.id", "$tablename.date", "$tablename.created_at", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot",  "$tablename.user_id", 'statuses.status as status', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name', 'doctors.name as doctor', 'treatments.treatment as reason')
                ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                ->join('users', 'users.id', '=', "$tablename.user_id")
                ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                ->join('locations', 'locations.id', '=', "$tablename.location_id")
                ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                ->where('date', '<', date('Y-m-d', time()))
                ->where("$tablename.location_id", Auth::user()->location_id)
                ->where("$tablename.doctor_id", Auth::user()->relative_id)
                ->where("$tablename.appointment_type_id", 1)
                ->where($conditions)
                ->whereNotIn("$tablename.status_id", [1,7])
                ->orderBy("$tablename.date", 'desc')->paginate(15);
    }

    public function getInsMedListbyApt($id)
    {
        $mresponse = [];
        $year = '20'.substr($id, 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $appointment = $table->where('appointment_code', $id)->get()->first();
        $minvoices = explode(',', $appointment->pinvoice);
        $minvoices = array_filter($minvoices);
        foreach ($minvoices as $key => $minv) {
            $sale = Sales::where('invoice_number', $minv)->get()->first();
            $salesmed = SaleMedicines::where('sale_id', $sale->id)->get();
            foreach ($salesmed as $smed) {
                $rd = json_decode($smed->rowdata);
                $sm[] = ['name' => $rd->name, 'qty' => $rd->qty, 'batch_no' => $rd->batch_no];
            }
            $mresponse[$key] = ['invoice' => $minv,
                                'data' => $sm];
        }
        return $mresponse;
    }

    public function getcourseAptListbyApt($id)
    {
        $cdetails = [];
        $year = '20'.substr($id, 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $appointment = $table->where('appointment_code', $id)->get()->first();

        if($appointment->course_id):
            $cyear = '20'.substr($appointment->course_id, 1, 2);
            $ctable =  new Courses;
            $ctable = $ctable->setTable('course'.$cyear.'s');
            $course = $ctable->where('course_code', $appointment->course_id)->get()->first();

            $appointments = explode(',', $course->appointments);
            $appointments = array_filter($appointments);

            // = $table->whereIn('appointment_code', $appointments)->get();
            $cdetails = $table->select("$tablename.id", "$tablename.date", "$tablename.created_at", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.comment", "$tablename.end_timeslot",  "$tablename.user_id", "$tablename.course_id", 'doctors.name as doctor', 'statuses.status as status', 'statuses.css as status_css', 'treatments.treatment as reason')->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->whereNotIn("$tablename.status_id", [6,7,10,8,11])
                        ->whereIn("$tablename.appointment_code", $appointments)
                        ->orderBy('date', 'asc')->get();
            return ['appointments' => $cdetails, 'course' => $course->course_code];
        endif;
        return $cdetails;
    }

    public function closeAppointment(Request $request)
    {
        $year = '20'.substr($request['appointment_code'], 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        DB::table($tablename)
            ->where('appointment_code', $request['appointment_code'])
            ->update(['status_id' => 5]);
        return ['status' => 'success'];
    }

    public function getDashboardAppointments()
    {
        $table =  new Appointments;
        $table = $table->setTable('appointment'.date('Y').'s');
        $tablename = $table->getTable();

        if(Auth::user()->admin_type_id == 6) {
            return $table->select("$tablename.id", "$tablename.date",  "$tablename.course_id", "$tablename.created_at", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot", "$tablename.status_id", "$tablename.user_id", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css',  'user_profiles.first_name', 'user_profiles.last_name', 'doctors.name as doctor', 'treatments.treatment as reason')
                    ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->join('user_documents', 'user_documents.user_id', '=', 'users.id')
                    ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereNotNull("$tablename.course_id")
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->where("$tablename.doctor_id", Auth::user()->relative_id)
                    ->whereIn("$tablename.status_id", [2,4,9])
                    ->where('date', date('Y-m-d', time()))
                    ->orderBy('start_timeslot', 'asc')->get();
        } else {
            return $table->select("$tablename.id", "$tablename.date",  "$tablename.course_id", "$tablename.created_at", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot", "$tablename.status_id", "$tablename.user_id", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css',  'user_profiles.first_name', 'user_profiles.last_name', 'doctors.name as doctor', 'treatments.treatment as reason')
                    ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->join('user_documents', 'user_documents.user_id', '=', 'users.id')
                    ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereNotNull("$tablename.course_id")
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereIn("$tablename.status_id", [2,4,9])
                    ->where('date', date('Y-m-d', time()))
                    ->orderBy('start_timeslot', 'asc')->get();
        }
    }

    public function shiftInCourse(Request $request)
    {
        $year = '20'.substr($request['course_code'], 1, 2);
        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();

        if(!Schema::hasTable($tablename)) {
            return ['status' => 'error', 'message' => 'Course ID does not exist'];
        }
        $tablename = $table->getTable();

        $course = DB::table($tablename)->where('course_code', $request['course_code'])->get()->first();

        if($request['user_id'] != $course->user_id) return ['status' => 'error', 'message' => 'Patient ID is not same'];

        $appointments = explode(',', $course->appointments);
        $appointments = array_merge($appointments, [$request['apid']]);
        DB::table($tablename)->where('course_code', $request['course_code'])->update(['appointments' => implode(',',$appointments)]);



        $year = '20'.substr($request['apid'], 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        $appointment = DB::table($tablename)->where('appointment_code', $request['apid'])->get()->first();

        DB::table($tablename)
            ->where('appointment_code', $request['apid'])
            ->update(['course_id' => $request['course_code']]);

        return ['status' => 'success'];
    }

    private function __AddActivity($apcode, $admin, $message)
    {
        $year = '20'.substr($apcode, 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        $appointment = DB::table($tablename)->where('appointment_code', $apcode)->get()->first();

        $activity = ['admin' => Auth::user()->name, 'datetime' => date('Y-m-d h:i:sa', time()), 'reason' => $message];

        if($appointment->activities):
            $activity2 = json_decode($appointment->activities);
            $activities = array_push($activity2, $activity);
            DB::table($tablename)->where('appointment_code', $apcode)->update(['activities' => $activities]);
        else:
            $activities = json_encode($activity);
            DB::table($tablename)->where('appointment_code', $apcode)->update(['activities' => $activities]);
        endif;
        return true;
    }

    public function transferAppointmentInvoice(Request $request) 
    {
        $year = '20'.substr($request['appointment_code'], 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $table->where('appointment_code', $request['appointment_code'])->update(['ainvoice' => $request['invoice_number']]);

        return ['status' => 'success'];
    }

    public function updateComment(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required',
        ]);
        $year = '20'.substr($request['appointment_code'], 1, 2);
        $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            $appointment = $table->where('appointment_code', $request['appointment_code'])->get();

            DB::table($tablename)
            ->where('appointment_code', $request['appointment_code'])
            ->update(['status_id' => 5, 'comment' => ($request['comment'] == 'other')?$request['other']:$request['comment']]);
        if($request['comment'] == 'other'): $this->__AddSuggestion($request['other']); endif;
        return ['status' => 'success'];
    }

    private function __AddSuggestion($comment) {
        $activity = [$comment];
        $comments = Settings::where('name', 'suggestions')->get()->first();
        if($comments->value):
            $activity2 = json_decode($comments->value);
            $activities = array_merge($activity2, $activity);
        else:
            $activities = $activity;
        endif;
        $comments->update(['value' => json_encode($activities)]);
    }

    private function __GetIcode($id = null)
    {
        $code = explode('-', $id);
        $year = '20'.substr($code[1], 0, 2);
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();
        $data = $table->select("$tablename.*")->where('invoice_number', $id)->get()->first();
        if($data) {
            if($data->insurance_id >= 2) { return 'I';}
            else { return 'C';}
        } else {
            return 'N';
        }
    }

    private function __IsInDummy($apid = null, $lid = null)
    {
        if($lid == 1) return false;
        $table =  new Appointments;
        $table = $table->setTable('appointment2021s');
        $tablename = $table->getTable();
        $appointment = $table->where('is_dummy', $apid)->get()->count();
        if($appointment) return 'Dr. Madhu Harihar'; 
        else return false;
    }
}
