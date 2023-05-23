<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Courses;
use App\Models\Appointments;
use App\Models\Invoices;
use App\Models\Insurances;
use App\Models\Users;
use App\Models\Packages;
use App\Models\TimeSlots;
use App\Models\FinancialYears;
use Illuminate\Support\Carbon;
use DB;

class CoursesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function create(Request $request, $apid = null)
    {
        //return $apid;
        if($apid) {
            $year = '20'.substr($apid, 1, 2);
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            $appointment = $table->where('appointment_code', $apid)->first();

            $year2 = date('Y', time());
            $ctable =  new Courses;
            $ctable = $ctable->setTable('course'.$year2.'s');
            $ctablename = $ctable->getTable();

            $ccode =  $this->courseCodeGenerator();
            $course = DB::table($ctablename)->insertGetId([
                'location_id' => $appointment['location_id'],
                'admin_id' => Auth::user()->id,
                'course_code' => $ccode,
                'doctor_id' => $appointment['doctor_id'],
                'user_id' => $appointment['user_id'],
                'consult_code' => $apid,
                'insurance_id' => 1,
                'remark' => $appointment['dr_remark'],
                'start_date' => date('Y-m-d', time()),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'status_id' => 2 ]);

            $appointments = [];

            foreach($request['course__status_id'] as $key => $status):
                if($status == 2):
                    $appointment_date = date('Y-m-d', strtotime($request['course__date'][$key]));

                    $date_year = \explode('-',$appointment_date);
                    $atable =  new Appointments;
                    $atable = $atable->setTable('appointment'.$date_year[0].'s');
                    $atablename = $atable->getTable();

                    $apcode = $this->appointmentCodeGenerator($appointment_date);

                    $response = DB::table($atablename)->insert([
                        'appointment_type_id' => 2,
                        'date' =>   $appointment_date,
                        'doctor_id' => $request['course__doctor_id'][$key],
                        'location_id' => Auth::user()->location_id,
                        'room_id' =>  $request['course__room_id'][$key],
                        //'second_doctor_id' => $request['second_doctor_id'],
                        'status_id' => 2,
                        'user_id' => $appointment['user_id'],
                        'treatment_id' => $request['course__treatment_id'][$key],
                        'visit_type_id' => 1,
                        // 'followup_appointment' => $request['followup_appointment'],
                        'course_id' => $ccode,
                        'admin_id' => Auth::user()->id,
                        'appointment_code' => $apcode,
                        'start_timeslot' => $request['course__start_timeslot'][$key],
                        'end_timeslot' => $request['course__end_timeslot'][$key],
                        'user_remark' => '',
                        'dr_remark' => '',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                    $appointments[$key] = $apcode;
                endif;
            endforeach;
            //$appointments = array_filter($appointments);
            $appointments = implode(',', $appointments);
            DB::table($ctablename)->where(['course_code' => $ccode])->update(['appointments' => $appointments]);
            return $ccode;
        }
    }

    public function createDirectCourse(Request $request)
    {
        $ctable =  new Courses;
        $ctable = $ctable->setTable('course'.date('Y', time()).'s');
        $ctablename = $ctable->getTable();

        $ccode =  $this->courseCodeGenerator();
        $course = DB::table($ctablename)->insertGetId([
                'location_id' => Auth::user()->location_id,
                'admin_id' => Auth::user()->id,
                'course_code' => $ccode,
                'user_id' => $request['user_id'],
                'insurance_id' => 1,
                'remark' => $request['remark'],
                'pstatus' => 1,
                'start_date' => date('Y-m-d', time()),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'status_id' => 2 ]);

        return $ccode;
    }

    public function createPreapprovalCourse(Request $request)
    {
        if($request['appointment_code']) {
            $apid = $request['appointment_code'];
            $year = '20'.substr($apid, 1, 2);
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            $appointment = $table->where("$tablename.appointment_code", $apid)->get()->first();

            $year2 = date('Y', time());
            $ctable =  new Courses;
            $ctable = $ctable->setTable('course'.$year2.'s');
            $ctablename = $ctable->getTable();

            $ccode =  $this->courseCodeGenerator();
            DB::table($ctablename)->insertGetId([
                'location_id' => $appointment['location_id'],
                'admin_id' => Auth::user()->id,
                'course_code' => $ccode,
                'consult_code' => $request['appointment_code'],
                'doctor_id' => $appointment['doctor_id'],
                'user_id' => $appointment['user_id'],
                'insurance_id' => $request['insurance_id'],
                'remark' => $appointment['dr_remark'],
                'start_date' => date('Y-m-d', time()),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'ins_approval' => 1,
                'status_id' => 2 ]);

            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.date('Y', time()).'s');
            $itablename = $itable->getTable();

            $invoice_number = $this->invoiceGenerator(6);

            DB::table($itablename)->insert([
                'admin_id' => Auth::user()->id,
                'user_id' => $appointment['user_id'],
                'course_code' => $ccode,
                'invoice_type' => 6,
                'location_id' => Auth::user()->location_id,
                'invoice_number' =>  $invoice_number,
                'insurance_id' => $request['insurance_id'],
                'payable_amount' => 0,
                'ins_disc' => $request['cd'],
                'payment_history' => '',
                'ins_clearance' => 0,
                'payment_status' => 0,
                'approved' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
                ]);

            $ctable->where('course_code', $ccode)->update(['invoice' => $invoice_number]);
            DB::table($tablename)->where('appointment_code', $request['appointment_code'])->update(['course_id' => $ccode]);
            return $invoice_number;
        }
    }

    public function createCourseAppointment(Request $request)
    {
        //return $apid;
        if($request['course_id']) {
            $year = '20'.substr($request['course_id'], 1, 2);

            $ctable =  new Courses;
            $ctable = $ctable->setTable('course'.$year.'s');
            $ctablename = $ctable->getTable();

            $course = DB::table($ctablename)->where(['course_code' => $request['course_id']])->get()->first();

            $appointments = explode(',', $course->appointments);

            $appointment_date = date('Y-m-d', strtotime($request['date']));

            $date_year = \explode('-',$appointment_date);
            $atable =  new Appointments;
            $atable = $atable->setTable('appointment'.$date_year[0].'s');
            $atablename = $atable->getTable();

            $apcode = $this->appointmentCodeGenerator($appointment_date);

            $response = DB::table($atablename)->insert([
                'appointment_type_id' => 2,
                'date' =>   $appointment_date,
                'doctor_id' => $request['doctor_id'],
                'location_id' => Auth::user()->location_id,
                'room_id' =>  $request['room_id'],
                'second_doctor_id' => $request['second_doctor_id'],
                'status_id' => 2,
                'user_id' => $request['user_id'],
                'treatment_id' => $request['treatment_id'],
                'visit_type_id' => 1,
                'course_id' => $request['course_id'],
                'admin_id' => Auth::user()->id,
                'appointment_code' => $apcode,
                'start_timeslot' => $request['start_time'],
                'end_timeslot' => $request['end_time'],
                'user_remark' => '',
                'dr_remark' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            array_push($appointments, $apcode);
            $appointments = array_filter($appointments);
            DB::table($ctablename)->where(['course_code' => $request['course_id']])->update(['appointments' => implode(',', $appointments)]);
            return ['appointment created'];
        }
    }

    public function createSchCourseAppointment(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|numeric',
            'date' => 'required|date',
            'treatment_id' => 'required|numeric',
            'appointment_type_id' => 'required|numeric',
            'doctor_id' => 'required|numeric',
            'start_time' => 'required|numeric',
            'end_time' => 'required|numeric',
        ]);

        if($request['course_id']) {
            $year = '20'.substr($request['course_id'], 1, 2);

            $ctable =  new Courses;
            $ctable = $ctable->setTable('course'.$year.'s');
            $ctablename = $ctable->getTable();

            $course = DB::table($ctablename)->where(['course_code' => $request['course_id']])->get()->first();

            $appointments = explode(',', $course->appointments);

            $appointment_date = date('Y-m-d', strtotime($request['date']));

            $date_year = \explode('-',$appointment_date);
            $atable =  new Appointments;
            $atable = $atable->setTable('appointment'.$date_year[0].'s');
            $atablename = $atable->getTable();

            $apcode = $this->appointmentCodeGenerator($appointment_date);

            if($request['appointment_type_id'] == 1): $status = 8;
            else: $status = 9; endif;

            $response = DB::table($atablename)->insert([
                'appointment_type_id' => $request['appointment_type_id'],
                'date' =>   $appointment_date,
                'doctor_id' => $request['doctor_id'],
                'location_id' => Auth::user()->location_id,
                'room_id' =>  $request['room_id'],
                'second_doctor_id' => $request['second_doctor_id'],
                'status_id' => $status,
                'user_id' => $request['user_id'],
                'treatment_id' => $request['treatment_id'],
                'visit_type_id' => 1,
                'course_id' => $request['course_id'],
                'admin_id' => Auth::user()->id,
                'appointment_code' => $apcode,
                'start_timeslot' => $request['start_time'],
                'ainvoice' => $request['ainvoice'],
                'end_timeslot' => $request['end_time'],
                'user_remark' => '',
                'dr_remark' => 'Appointment Under Package',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            array_push($appointments, $apcode);
            $appointments = array_filter($appointments);
            DB::table($ctablename)->where(['course_code' => $request['course_id']])->update(['appointments' => implode(',', $appointments)]);
            return ['appointment created'];
        }
    }

    public function getYearwise($year = null)
    {
        if($year):
            $table =  new Courses;
            $table = $table->setTable('course'.$year.'s');
            $tablename = $table->getTable();
            return $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'user_profiles.first_name', 'user_profiles.last_name', 'doctors.name as doctor', 'locations.clinic_name')
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->where("$tablename.status_id", '!=', 7)
                        ->where("$tablename.package_id", NULL)
                        ->orderBy('id', 'desc')
                        ->paginate(15);
        else:
            return ['message' => 'Year is missing. No data can be fetched.'];
        endif;
    }

    public function getDirectYearwise($year = null)
    {
        if($year):
            $table =  new Courses;
            $table = $table->setTable('course'.$year.'s');
            $tablename = $table->getTable();
            return $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'user_profiles.first_name', 'user_profiles.last_name', 'locations.clinic_name')
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->where("$tablename.status_id", '!=', 7)
                        ->where("$tablename.doctor_id", NULL)
                        ->where("$tablename.package_id", NULL)
                        ->orderBy('id', 'desc')
                        ->paginate(15);
        else:
            return ['message' => 'Year is missing. No data can be fetched.'];
        endif;
    }

    public function getPacksYearwise($year = null)
    {
        if($year):
            $table =  new Courses;
            $table = $table->setTable('course'.$year.'s');
            $tablename = $table->getTable();
            return $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'user_profiles.first_name', 'user_profiles.last_name', 'packages.name as package', 'locations.clinic_name')
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('packages', 'packages.id', '=', "$tablename.package_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->where("$tablename.status_id", '!=', 7)
                        ->where("$tablename.package_id", '!=', NULL)
                        ->orderBy('id', 'desc')
                        ->paginate(15);
        else:
            return ['message' => 'Year is missing. No data can be fetched.'];
        endif;
    }

    private function courseCodeGenerator()
    {
        $date = date('Y-m-d', time());
        $table =  new Courses;
        $table = $table->setTable('course'.date('Y', strtotime($date)).'s');
        $tablename = $table->getTable();
        $app = DB::table($tablename)->orderBy('id', 'desc')->take(1)->get();
        if($app->count()):
            $linv = substr($app[0]->course_code, 3);
            $counter = intval($linv)+1;
        else: $counter = 1; endif;
        $number = 'C'.date('y', strtotime($date)).str_pad($counter, 5, "0",STR_PAD_LEFT);
        return $number;
    }

    public function createSchedulePackage(Request $request, $year = null)
    {
        $this->validate($request, [
            'user_id' => 'required|numeric',
            'package_id' => 'required|numeric',
            'amount' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'remark' => 'nullable',
            'status_id' => 'required|numeric',
        ]);

       // return $request;
        $ctable =  new Courses;
        $ctable = $ctable->setTable('course'.$year.'s');
        $ctablename = $ctable->getTable();

        $ccode = $this->courseCodeGenerator();
        $course = DB::table($ctablename)->insert([
            'location_id' => Auth::user()->location_id,
            'admin_id' => Auth::user()->id,
            'course_code' => $ccode,
            'user_id' => $request['user_id'],
            'package_id' => $request['package_id'],
            'amount' => $request['amount'],
            'remark' => $request['remark'],
            'start_date' => date('Y-m-d', strtotime($request['start_date'])),
            'end_date' => date('Y-m-d', strtotime($request['end_date'])),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'status_id' => $request['status_id'] ]);

        //$appointments = [];

        /* foreach($request['appointment__book_status'] as $key => $status):
            if($status == 2):
                $appointment_date = date('Y-m-d', strtotime($request['appointment__date'][$key]));

                if($request['appointment__type_id'][$key] == 2):
                    $roomId = $request['appointment__rid'][$key];
                else: $roomId = NULL; endif;

                $date_year = \explode('-',$appointment_date);
                $table =  new Appointments;
                $table = $table->setTable('appointment'.$date_year[0].'s');
                $tablename = $table->getTable();

                $apcode = $this->appointmentCodeGenerator($appointment_date);

                $response = DB::table($tablename)->insert([
                    'appointment_type_id' => $request['appointment__type_id'][$key],
                    'date' =>   $appointment_date,
                    'doctor_id' => $request['appointment__doctor_id'][$key],
                    'location_id' => Auth::user()->location_id,
                    'room_id' => $roomId,
                    //'second_doctor_id' => $request['second_doctor_id'],
                    'status_id' => 2,
                    'user_id' => $request['user_id'],
                    'treatment_id' => $request['appointment__tid'][$key],
                    'visit_type_id' => 1,
                   // 'followup_appointment' => $request['followup_appointment'],
                    'course_id' => $ccode,
                    'admin_id' => Auth::user()->id,
                    'appointment_code' => $apcode,
                    'start_timeslot' => $request['appointment__stid'][$key],
                    'end_timeslot' => $request['appointment__etid'][$key],
                    'user_remark' => '',
                    'dr_remark' => '',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                $appointments[] = $apcode;
            endif;
        endforeach; */

       // DB::table($ctablename)->where(['course_code' => $ccode])->update(['appointments' => implode(',', $appointments)]);
        return $ccode;
    }

    public function CoursePackage($ccode = null)
    {
        $year = '20'.substr($ccode, 1, 2);
        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();
        return $table->select('packages.name as package_name', 'packages.treatments_Summary as treatments_Summary', 'packages.consultation_Summary as consultation_Summary')
                        ->leftjoin('packages', 'packages.id', '=', "$tablename.package_id")
                        ->where("$tablename.course_code", $ccode)
                        ->get()->first();
    }

    public function show($ccode = null)
    {
        $year = '20'.substr($ccode, 1, 2);
        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();

        $course = $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'user_profiles.first_name', 'user_profiles.last_name', 'doctors.name as doctor')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->leftjoin('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->where("$tablename.status_id", '!=', 7)
                        ->where("$tablename.course_code", $ccode)
                        ->get()->first();
        if($course):
            $invoices = array_filter(explode(',', $course->invoice));
            $course->invoices = array_filter($invoices);
            $course->invoice_count = count($invoices);
            if($course->invoice_count >= 1) $course->primary_invoice = $course->invoices[0];
            else $course->primary_invoice = '';
            $course->year = $year;
            $cdate = explode('-',$course->end_date);
            if(count($cdate) <= 2):
                $course->end_date = null; $course->show_reapproval = 0;
            elseif(checkdate($cdate[1], $cdate[2], $cdate[0]) != true):
                $course->end_date = null; $course->show_reapproval = 0;
            else:
                $currentDate = date('Y-m-d', time());
                $startDate = date('Y-m-d', strtotime($course->end_date.' - 4 days'));
                $endDate = date('Y-m-d', strtotime($course->end_date.' + 7 days'));
                if (($currentDate >= $startDate) && ($currentDate <= $endDate)){
                    $course->show_reapproval = 1;
                }else{
                    $course->show_reapproval = 0;
                }
            endif;
        else:
            $invoices = [];
           // $course->invoices = [];
        endif;
        return $course;

    }

    public function showDirect($ccode = null)
    {
        $year = '20'.substr($ccode, 1, 2);
        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();

        $course = $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'user_profiles.first_name', 'user_profiles.last_name')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->where("$tablename.status_id", '!=', 7)
                        ->where("$tablename.course_code", $ccode)
                        ->get()->first();

        $invoices = explode(',', $course->invoice);
        $course->invoices = array_filter($invoices);
        return $course;

    }

    public function getPackDetail($ccode = null)
    {
        $year = '20'.substr($ccode, 1, 2);
        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();
        $customer = []; $therapy_package = ''; $invoices = ''; $appointments = ''; $left_therapies = [];
        $course = $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'user_profiles.first_name', 'user_profiles.last_name', 'packages.name as package')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('packages', 'packages.id', '=', "$tablename.package_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->where("$tablename.status_id", '!=', 7)
                        ->where("$tablename.package_id", '!=', NULL)
                        ->where("$tablename.course_code", $ccode)
                        ->first();
                        if($course):
                            $invoices = array_filter(explode(',', $course->invoice));
                            $course->invoices = array_filter($invoices);
                        else:
                            $invoices = [];
                          //  $course->invoices = [];
                        endif;
        if($course):
            $course->appointments_count = count(\explode(',', $course->appointments));
            $customer = Users::whereId($course->user_id)->with(['user_profile', 'user_document', 'status'])->get()->first();
            $therapy_package = $this->__ShowPackage($course->package_id);
            $invoices = array_filter(explode(',', $course->invoice));
            $invoices = $this->__getInvoicesBycourses($invoices);
            $appointments = $this->__getBulkAppointments($course->appointments);
            $booked = array_filter(explode(',', $course->appointments));
            $years = FinancialYears::all();
            if($therapy_package['consultation'] >= 1) {
                foreach($therapy_package['allconsultation'] as $cid => $consult) {
                    $bap_count = 0;
                    foreach($years as $year) {
                        $aptable =  new Appointments;
                        $aptable = $aptable->setTable('appointment'.$year->year.'s');
                        $count = $aptable->whereIn('appointment_code', $booked)->where('treatment_id', $cid)->whereNotIn('status_id', [6,7,11])->get()->count();
                        $bap_count = $bap_count+$count;
                    }
                    $checkno = $consult['count'] - $bap_count;
                    if($checkno <= 0): $checkno = 0; endif;
                    $left_therapies[] = ['appointment_type_id' => 1, 'id' => $cid, 'name' => $consult['name'], 'remaining' => $checkno];
                }
            }
            if($therapy_package['treatments'] >= 1) {
                foreach($therapy_package['alltreatments'] as $tid => $consult) {
                    $bap_count = 0;
                    foreach($years as $year) {
                        $aptable =  new Appointments;
                        $aptable = $aptable->setTable('appointment'.$year->year.'s');
                        $count = $aptable->whereIn('appointment_code', $booked)->where('treatment_id', $tid)->whereNotIn('status_id', [6,7,11])->get()->count();
                        $bap_count = $bap_count+$count;
                    }
                    $checkno = $consult['count'] - $bap_count;
                    if($checkno <= 0): $checkno = 0; endif;
                    $left_therapies[] = ['appointment_type_id' => 2, 'id' => $tid, 'name' => $consult['name'], 'remaining' => $checkno];
                }
            }

        endif;
        return ['course' => $course, 'customer' => $customer, 'therapy_package' => $therapy_package, 'invoices' => $invoices, 'appointments' => $appointments, 'left_therapies' => $left_therapies];
    }

    private function __getBulkAppointments($request)
    {
        $start = $this->__getAllTimeSlots();
        $end = $this->__getAllEndTimeSlots();

        $return = [];
        $appoints = explode(',', $request);
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
                                'status' => $app->status,
                                'css' => $app->css,
                                'username' => $app->user->username,
                                'first_name' => $app->user->user_profile->first_name,
                                'doctor' => $app->doctor,
                                'invoice' => $app->ainvoice,
                                'time' => $tstart.' - '.$tend];
            endif;
        endforeach;
        return $return;
    }

    private function __ShowPackage($id)
    {
        $treatments = []; $consultations = []; $alltreatments = []; $allconsultation = [];
        $record = Packages::select('packages.*', 'statuses.status', 'statuses.css')
                            ->join('statuses', 'statuses.id', '=', 'packages.status_id')
                            ->where('packages.id', $id)
                            ->get()->first();
        $record->treatments_Summary = json_decode($record->treatments_Summary);
        $record->consultation_Summary = json_decode($record->consultation_Summary);

        foreach ($record->treatments_Summary as $value) {
            if(!in_array($value->id, array_keys($treatments)))  $treatments[$value->id] = $value->name;
            if(array_key_exists($value->id, $alltreatments)):
                $count = $value->count + $alltreatments[$value->id]['count'];
                $alltreatments[$value->id] = ['name' => $value->name, 'count' => $count];
            else:
                $alltreatments[$value->id] = ['name' => $value->name, 'count' => $value->count];
            endif;
        }
        foreach ($record->consultation_Summary as $value) {
            if(!in_array($value->id, array_keys($consultations))) $consultations[$value->id] = $value->name;
            if(array_key_exists($value->id, $allconsultation)):
                $count = $value->count + $allconsultation[$value->id]['count'];
                $allconsultation[$value->id] = ['name' => $value->name, 'count' => $count];
            else:
                $allconsultation[$value->id] = ['name' => $value->name, 'count' => $value->count];
            endif;
        }
        $record->treatmentlists = $treatments;
        $record->consultationlists = $consultations;
        $record->alltreatments = $alltreatments;
        $record->allconsultation = $allconsultation;
        return $record;
    }

    private function __getInvoicesBycourses($invoices)
    {
        $return = [];
        foreach ($invoices as $key => $value):

            $code = explode('-', $value);
            $year = '20'.substr($code[1], 0, 2);
            $table =  new Invoices;
            $table = $table->setTable('invoice'.$year.'s');
            $tablename = $table->getTable();
            $invoice = $table->select("$tablename.*", 'admins.name as admin')
                            ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                            ->where('invoice_number', $value)
                        ->first();
            if($invoice && ($invoice->payment_status == 2 || $invoice->payment_status == 3)):
               // var_dump($invoice->partialpay);
                //die;
                if($invoice->partialpay):
                    $partial = json_decode($invoice->partialpay);
                    $paid = 0;
                    foreach($partial as $key => $value):
                        $paid=$paid+$value->paid_amount;
                    endforeach;
                    $invoice->paid = $paid;
                    $invoice->partialpay = json_decode($invoice->partialpay);
                    $invoice->remain_balance = $invoice->payable_amount - $paid;
                else:
                    $invoice->partialpay = [];
                    $invoice->paid = 0;
                    $invoice->remain_balance = $invoice->payable_amount;
                endif;
            else:
                $invoice->paid = 0;
                $invoice->remain_balance = 0;
            endif;
            $return[$key] = $invoice;
        endforeach;
        return $return;
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

    public function createCourse(Request $request)
    {
        $year = '20'.substr($request['active_appointment_id'], 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $appointment = $table->where('appointment_code', $request['active_appointment_id'])->first();

        $appointments = [];

       // return $request;
        $ctable =  new Courses;
        $ctable = $ctable->setTable('course'.date('Y', time()).'s');
        $ctablename = $ctable->getTable();

        $ccode = $this->courseCodeGenerator();

        $course = DB::table($ctablename)->insertGetId([
            'location_id' => $appointment['location_id'],
            'admin_id' => Auth::user()->id,
            'course_code' => $ccode,
            'doctor_id' => $appointment['doctor_id'],
            'user_id' => $appointment['user_id'],
            'consult_code' => $request['active_appointment_id'],
            'insurance_id' => 1,
            'remark' => $appointment['dr_remark'],
            'start_date' => date('Y-m-d', time()),
            'created_at' => Carbon::now(),
            'pstatus' => 1,
            'updated_at' => Carbon::now(),
            'status_id' => 2 ]);

            DB::table($tablename)->where('appointment_code', $request['active_appointment_id'])->update(['course_id' => $ccode]);

        foreach($request['course__status_id'] as $key => $status):
            if($status == 2):
                $appointment_date = date('Y-m-d', strtotime($request['course__date'][$key]));

                $date_year = \explode('-',$appointment_date);
                $table =  new Appointments;
                $table = $table->setTable('appointment'.$date_year[0].'s');
                $tablename = $table->getTable();

                $apcode = $this->appointmentCodeGenerator($appointment_date);
                $response = DB::table($tablename)->insert([
                    'appointment_type_id' => 2,
                    'date' =>   $appointment_date,
                    'doctor_id' => $request['course__doctor_id'][$key],
                    'location_id' => Auth::user()->location_id,
                    'room_id' => $request['course__room_id'][$key],
                    'status_id' => 2,
                    'user_id' => $appointment['user_id'],
                    'treatment_id' => $request['course__treatment_id'][$key],
                    'visit_type_id' => 1,
                    'course_id' => $ccode,
                    'admin_id' => Auth::user()->id,
                    'appointment_code' => $apcode,
                    'start_timeslot' => $request['course__start_timeslot'][$key],
                    'end_timeslot' => $request['course__end_timeslot'][$key],
                    'user_remark' => '',
                    'dr_remark' => '',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                array_push($appointments, $apcode);
            endif;
        endforeach;

        $appointments = array_filter($appointments);
        DB::table($ctablename)->where(['course_code' => $ccode])->update(['appointments' => implode(',', $appointments)]);
        return $ccode;
    }

    public function approveCourseInvoice(Request $request)
    {
        $this->validate($request, [
            'course_code' => 'required',
            'approval_code' => 'required'
        ]);
        $year = '20'.substr($request['course_code'], 1, 2);
        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();

        $insurance = Insurances::where('id', $request['insurance_id'])->get()->first();
        $date = date('Y-m-d', strtotime(' + '.$insurance->approval_days.' days'));
        DB::table($tablename)->where('course_code', $request['course_code'])
                            ->update(['ins_approval' => 2, 'approval_code' => $request['approval_code'], 'end_date' => $date]);

        $year1 = '20'.substr($request['consult_code'], 1, 2);
        $table1 =  new Appointments;
        $table1 = $table1->setTable('appointment'.$year.'s');
        $tablename1 = $table1->getTable();

        DB::table($tablename1)->where('appointment_code', $request['consult_code'])
                            ->update(['therapies' => json_encode($request['treatments'])]);

        return ['status' => 'success'];
    }

    public function reapproveCourseInvoice(Request $request)
    {
        $this->validate($request, [
            'insurance_id' => 'required',
            'approval_code' => 'required'
        ]);
        $year = '20'.substr($request['course_code'], 1, 2);
        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();

        $insurance = Insurances::where('id', $request['insurance_id'])->get()->first();
        $date = date('Y-m-d', strtotime(' + '.$insurance->approval_days.' days'));
        DB::table($tablename)
            ->where('course_code', $request['course_code'])
            ->update(['ins_approval' => 2, 'reapproval_code' => $request['approval_code'], 'end_date' => $date]);
        return ['status' => 'success'];
    }

    public function makeCashCourse(Request $request)
    {
        $year = '20'.substr($request['course_code'], 1, 2);
        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();

        DB::table($tablename)
            ->where('course_code', $request['course_code'])
            ->update(['ins_approval' => 3, 'insurance_id' => 1]);
        return ['status' => 'success'];
    }

    public function closeCourse(Request $request)
    {
        $year = '20'.substr($request['course_code'], 1, 2);
        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();

        DB::table($tablename)
            ->where('course_code', $request['course_code'])
            ->update(['status_id' => 5]);
        return ['status' => 'success'];
    }

    public function shiftCourse(Request $request)
    {
        $year = '20'.substr($request['apid'], 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        $appointment = DB::table($tablename)->where('appointment_code', $request['apid'])->get()->first();

        if($request['user_id'] != $appointment->user_id) return ['status' => 'error', 'message' => 'Patient ID is not same'];
        if($appointment->appointment_type_id != 1) return ['status' => 'error', 'message' => 'This is not consultation'];

        DB::table($tablename)
            ->where('appointment_code', $request['apid'])
            ->update(['course_id' => $request['course_code']]);

            $year = '20'.substr($request['course_code'], 1, 2);
            $table =  new Courses;
            $table = $table->setTable('course'.$year.'s');
            $tablename = $table->getTable();

            DB::table($tablename)
                ->where('course_code', $request['course_code'])
                ->update(['consult_code' => $request['apid'], 'doctor_id' => $appointment->doctor_id, 'insurance_id' => $appointment->insurance_id]);

        return ['status' => 'success'];
    }

    private function invoiceGenerator($atype = null)
    {
        $table =  new Invoices;
        $table = $table->setTable('invoice'.date('Y', time()).'s');
        $tablename = $table->getTable();
        $app = DB::table($tablename)->orderBy('id', 'desc')->get()->first();
        if($app):
            $linv = substr($app->invoice_number, 8);
            $counter = intval($linv) + 1;
        else: $counter = 1; endif;
        if($atype == 1): $code = 'AC-';
        elseif($atype == 2): $code = 'AT-';
        elseif($atype == 3): $code = 'IM-';
        elseif($atype == 4): $code = 'CM-';
        elseif($atype == 5): $code = 'CP-';
        elseif($atype == 6): $code = 'CS-';
        elseif($atype == 7): $code = 'DM-';
        else: $code = 'UI-'; endif;
        $number = $code.date('ym-', time()).str_pad($counter, 4, "0", STR_PAD_LEFT);
        return $number;
    }

    public function prescribedSearch(Request $request)
    {
        $conditions = [];
        $year = $request['year'];

        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();

        if($request['course_code']): $conditions["$tablename.course_code"] = $request['course_code']; endif;
        if($request['doctor_id']): $conditions["$tablename.doctor_id"] = $request['doctor_id']; endif;
        if($request['user_id']): $conditions["$tablename.user_id"] = $request['user_id']; endif;
        if($request['status_id']): $conditions["$tablename.status_id"] = $request['status_id']; endif;

        if(count($conditions) == 0): return []; endif;

        return $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'user_profiles.first_name', 'user_profiles.last_name', 'doctors.name as doctor', 'locations.clinic_name')
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->where("$tablename.status_id", '!=', 7)
                        ->where("$tablename.package_id", NULL)
                        ->where($conditions)
                        ->orderBy('id', 'desc')
                        ->paginate(15);
    }

    public function directSearch(Request $request)
    {
        $conditions = [];
        $year = $request['year'];

        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();

        if($request['course_code']): $conditions["$tablename.course_code"] = $request['course_code']; endif;
        if($request['user_id']): $conditions["$tablename.user_id"] = $request['user_id']; endif;
        if($request['status_id']): $conditions["$tablename.status_id"] = $request['status_id']; endif;

        if(count($conditions) == 0): return []; endif;

        return $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'user_profiles.first_name', 'user_profiles.last_name', 'locations.clinic_name')
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->where("$tablename.status_id", '!=', 7)
                        ->where("$tablename.package_id", NULL)
                        ->where($conditions)
                        ->orderBy('id', 'desc')
                        ->paginate(15);
    }

    public function packagedSearch(Request $request)
    {
        $conditions = [];
        $year = $request['year'];

        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();

        if($request['course_code']): $conditions["$tablename.course_code"] = $request['course_code']; endif;
        if($request['package_id']): $conditions["$tablename.package_id"] = $request['package_id']; endif;
        if($request['user_id']): $conditions["$tablename.user_id"] = $request['user_id']; endif;
        if($request['status_id']): $conditions["$tablename.status_id"] = $request['status_id']; endif;

        if(count($conditions) == 0): return []; endif;

        return $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'user_profiles.first_name', 'user_profiles.last_name', 'packages.name as package', 'locations.clinic_name')
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('packages', 'packages.id', '=', "$tablename.package_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->where("$tablename.status_id", '!=', 7)
                        ->where("$tablename.package_id", '!=', NULL)
                        ->where($conditions)
                        ->orderBy('id', 'desc')
                        ->paginate(15);
    }

    public function checkCourse(Request $request)
    {
        $this->validate($request, ['course_id' => 'required']);
        $year = '20'.substr($request['course_id'], 1, 2);
        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();
        $course =  $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'user_profiles.first_name', 'user_profiles.last_name', 'doctors.name as doctor')
        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
        ->join('users', 'users.id', '=', "$tablename.user_id")
        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
        ->where("$tablename.status_id", '!=', 7)
        ->where("$tablename.course_code", $request['course_id'])
        ->get()->first();
        if($course):
            if($course->package_id): $course->type = 'Course Package';
            elseif($course->consult_code): $course->type = 'Prescribed Course';
            else: $course->type = 'Direct Course'; endif;
        endif;
        return $course;
    }

    public function destroy(Request $request)
    {
        $year = '20'.substr($request['course_code'], 1, 2);
        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();

        $data = ['reason' => $request['reason'],
                        'admin' => Auth::user()->id,
                        'admin_name' => Auth::user()->name,
                        'time' => time()];
        $course = DB::table($tablename)->where('course_code', $request['course_code'])->get()->first();
        //$this->__cancelInvocies($course->);
        //$this->__cancelAppointment($course->);
        DB::table($tablename)->where('course_code', $request['course_code'])->update(['status_id' => 7, 'reason' => json_encode($data)]);

            //return ['status' => 'success'];
    }

    public function updateEndDate(Request $request)
    {
        $year = '20'.substr($request['id'], 1, 2);
        $table =  new Courses;
        $table = $table->setTable('course'.$year.'s');
        $tablename = $table->getTable();

        $data = ['end_date' => date('Y-m-d', strtotime($request['end_date']))];
        $course = DB::table($tablename)->where('course_code', $request['id'])->update($data);
        return ['status' => 'success'];
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

    private function __getAllEndTimeSlots($barrier = 0)
    {
        $slots = TimeSlots::get();
        foreach($slots as $slot) {
            $activeSlots[$slot->id] = $slot->closing;
        }
        return $activeSlots;
    }

    public function getPatientHistoryYearwise($id = null, $year = null)
    {
        if($year && $id):
            $table =  new Courses;
            $table = $table->setTable('course'.$year.'s');
            $tablename = $table->getTable();
            $courses = $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'doctors.name as doctor')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->where("$tablename.status_id", '!=', 7)
                        ->where("$tablename.package_id", NULL)
                        ->where("$tablename.user_id", $id)
                        ->orderBy('id', 'desc')
                        ->get();
            $packages = $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'packages.name as package')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('packages', 'packages.id', '=', "$tablename.package_id")
                        ->where("$tablename.status_id", '!=', 7)
                        ->where("$tablename.package_id", '!=', NULL)
                        ->where("$tablename.user_id", $id)
                        ->orderBy('id', 'desc')
                        ->get();
            $directs = $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->where("$tablename.status_id", '!=', 7)
                        ->where("$tablename.doctor_id", NULL)
                        ->where("$tablename.package_id", NULL)
                        ->where("$tablename.user_id", $id)
                        ->orderBy('id', 'desc')
                        ->get();
            return ['courses' => $courses, 'packages' => $packages, 'directs' => $directs];
        else:
            return [];
        endif;
    }

    public function getOnlyTreatments($id = null, $cid, $iid)
    {
        $year = '20'.substr($id, 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        $course = DB::table($tablename)->where('appointment_code', $id)->get()->first();

        $treatments = json_decode($course->therapies);

        foreach ($treatments as $key => $value) {
            $treatments[$key]->approved = $value->days;
        }
       
        return ['course_code' => $cid, 'treatments' => $treatments, 'consult_code' => $id, 'insurance_id' => $iid, 'approval_code' => ''];
    }
}
