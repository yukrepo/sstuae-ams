<?php

namespace App\Http\Controllers;

use App\Models\Stocks;
use App\Models\Medicines;
use App\Models\Sales;
use App\Models\Appointments;
use App\Models\Invoices;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Models\Locations;
use App\Models\Admins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
       // $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    public function makeSlots()
    {
        /*$table =  new Appointments;
        $table = $table->setTable('appointment2019s');
        $tablename = $table->getTable();
        $tablename2 = 'appointment2020s';

        $appointments = DB::table($tablename)->where('date', '>=', date('Y-m-d', strtotime('2020-01-01')))->where('status_id', '!=', 11)->get();

        foreach ($appointments as $key => $appointment) {
            echo $key.'-'.$appointment->course_id.'--'.$appointment->appointment_code.' - '; 
            $acode = $this->appointmentCodeGenerator($appointment->date);
			//echo $acode.'<br>'; 
            //die;
            $response = DB::table($tablename2)->insert([
                            'appointment_type_id' => $appointment->appointment_type_id,
                            'date' => $appointment->date,
                            'doctor_id' => $appointment->doctor_id,
                            'location_id' => $appointment->location_id,
                            'room_id' => $appointment->room_id,
                            'second_doctor_id' => $appointment->second_doctor_id,
                            'status_id' => $appointment->status_id,
                            'user_id' => $appointment->user_id,
                            'treatment_id' => $appointment->treatment_id,
                            'visit_type_id' => $appointment->visit_type_id,
                            'followup_appointment' => $appointment->followup_appointment,
                            'course_id' => $appointment->course_id,
                            'admin_id' => $appointment->admin_id,
                            'appointment_code' => $acode,
                            'start_timeslot' => $appointment->start_timeslot,
                            'end_timeslot' => $appointment->end_timeslot,
                            'ainvoice' => $appointment->ainvoice,
                            'user_remark' => $appointment->user_remark,
                            'dr_remark' => '',
                            'created_at' => $appointment->created_at,
                            'updated_at' => $appointment->updated_at
            ]);
            echo $acode.'<br>'; 
        }*/
        /* $sales = Sales::where('status', 1)->get();
        foreach($sales as $sale):
            $code = explode('-', $sale->invoice_number);
            $year = '20'.substr($code[1], 0, 2);
            $table =  new Invoices;
            $table = $table->setTable('invoice'.$year.'s');
            $invoice = $table->where('invoice_number', $sale->invoice_number)->get()->first();

            if($invoice->invoice_type == 3):
                $sale->insurance = $invoice->insurance_id;
                $sale->iam = $invoice->coinsurance_value;
                $sale->am2 = 0;
                $sale->am1 = $invoice->payable_amount;
                $sale->mode = $invoice->payment_mode;
            else:
                $sale->insurance = 0;
                $sale->iam = 0;
                $sale->mode = $invoice->payment_mode;
                if($invoice->payment_mode == 'cash+visa'): $sale->am1 = $invoice->cash_amount; $sale->am2 = $invoice->visa_amount;
                else: $sale->am1 = $invoice->payable_amount; $sale->am2 = 0;
                endif;
            endif;
            Sales::where('id', $sale->id)->update(['insurance' => $sale->insurance,
                            'iam' => $sale->iam,
                            'am2' => $sale->am2,
                            'mode' => $sale->mode,
                            'am1' => $sale->am1]);
        endforeach; */
       /* $table =  new Invoices;
        $table = $table->setTable('invoice2020s');
        $invoices = $table->whereNull('invoice_date')->get();
        foreach ($invoices as $key => $invoice) {
            $table->where('id', $invoice->id)->update(['invoice_date' => $invoice->created_at, 'updated_at' => $invoice->updated_at]);
            echo $invoice->invoice_number.'<br>';
        }
        echo 'dnoe'; die;*/
        /* $data = Aapoints::get();
        foreach ($data as $key => $value) {
            $date = explode('-',$value->apdate);
            Aapoints::where('apid', $value->apid)->update(['apdate' => '20'.$date[2].'-'.$date[1].'-'.$date[0]]);
            echo $value->apid.'<br>';
        }
        echo 'all done'; die; */
        /* $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $columns = array('ID', 'Name', 'Mobile', 'Nationality', 'Gender', 'DOB', '2019 - Visited', '2019 - Waiting', '2018 - Visited', '2018 - Waiting', '2017 - Visited', '2017 - Waiting');
        fputcsv($output, $columns);

        $data = Ausers::where('id', '>', 3000)->get();
        foreach($data as $key => $index)
        {
            $v19 = Aapoints::whereYear('apdate', 2019)->where('uid', $index->id)->where('status', 'Visited')->get()->count();
            $w19 = Aapoints::whereYear('apdate', 2019)->where('uid', $index->id)->where('status', 'Waiting')->get()->count();
            $v18 = Aapoints::whereYear('apdate', 2018)->where('uid', $index->id)->where('status', 'Visited')->get()->count();
            $w18 = Aapoints::whereYear('apdate', 2018)->where('uid', $index->id)->where('status', 'Waiting')->get()->count();
            $v17 = Aapoints::whereYear('apdate', 2017)->where('uid', $index->id)->where('status', 'Visited')->get()->count();
            $w17 = Aapoints::whereYear('apdate', 2017)->where('uid', $index->id)->where('status', 'Waiting')->get()->count();
            fputcsv($output, array($index->id, $index->name, $index->mobile, $index->nation, $index->gender, $index->dob, $v19, $w19, $v18, $w18, $v17, $w17));
        }
        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="appointments-report.csv"');
        echo stream_get_contents($output);
        fclose($output);
        echo 'done';
        die;
 */
        /* $slots = Medicines::get();

        foreach ($slots as $key => $slot) {
            $count = $key + 1;
            $stock = Stocks::create([
                        'purchase_id' => 1,
                        'medicine_id' => $slot->id,
                        'location_id' => 1,
                        'stock' => 50,
                        'selling_cost' => $slot->cash_price,
                        'purchase_cost' => $slot->cash_price,
                        'exp_date' => date('Y-m-d', strtotime('2019-12-25')),
                        'batch_no' => 'SST'.date('y', time()).str_pad($count, 3, "0", STR_PAD_LEFT),
                        'foc' => 0,
                        //'manufecturer_id' => $request['manufecturer_id'],
                ]);
            echo $count.'<br>';
        }
        die;
 */


        /* $range=range(strtotime("06:00"),strtotime("22:00"),10*60);
        foreach($range as $time){
                echo date("h:i A",$time);
            TimeSlots::create([
                'time' => date("h:i A",$time),
                'season' => 0
            ]);
            echo "<br>";
        } */
        die;
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

    public function emailcheck()
    {
        $data = ['subject' => 'Prescription Request For Appointment ID : S20001512',
                        'view' => 'prescription_mail',
                        'name' => 'Ankit kumar',
                        'apcode' => 'S202122',
                        'link' => 'prescription-emailview/dsfgsdfgsdfg'];
        Mail::to('ankit.engr11@gmail.com')->send(new SendMail($data));
        echo 11; die;
    }

    public function switchAdmin(Request $request)
    {
        $id = Auth::user();
        $admin = Admins::whereId($id->id)->get()->first();
        $admin->update(['location_id' => $request->location]);
        Auth::logout();
        Auth::login($admin);
        return redirect('/settings/profile');
    }

}
