<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Invoices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use DB;

use function GuzzleHttp\json_decode;

class InvoicesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index($year = null)
    {
        if($year):
            $table =  new Invoices;
            $table = $table->setTable('invoice'.$year.'s');
            $tablename = $table->getTable();
            return $table->select("$tablename.*", 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name as admin')
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereIn("$tablename.invoice_type", [1,2])
                    ->orderBy("$tablename.created_at", 'desc')->paginate(15);
        else:
            return ['message' => 'Year is missing. No data can be fetched.'];
        endif;
    }

    public function pharmacyInvoices($year = null)
    {
        if($year):
            $table =  new Invoices;
            $table = $table->setTable('invoice'.$year.'s');
            $tablename = $table->getTable();
            return $table->select("$tablename.*", 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name as admin')
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereIn("$tablename.invoice_type", [3,4])
                    ->orderBy("$tablename.created_at", 'desc')->paginate(15);

        else:
            return ['message' => 'Year is missing. No data can be fetched.'];
        endif;
    }

    public function dPharmacyInvoices($year = null)
    {
        if($year):
            $table =  new Invoices;
            $table = $table->setTable('invoice'.$year.'s');
            $tablename = $table->getTable();
            return $table->select("$tablename.*", 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name as admin')
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->where("$tablename.invoice_type", 7)
                    ->orderBy("$tablename.created_at", 'desc')->paginate(15);
        else:
            return ['message' => 'Year is missing. No data can be fetched.'];
        endif;
    }

    public function createDirectInvoice(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'sub_total' => 'required',
            'payment_mode' => 'required',
            'total' => 'required'
        ]);

        $table =  new Invoices;
        $table = $table->setTable('invoice'.date('Y', time()).'s');
        $tablename = $table->getTable();

        if($request['payment_mode'] == 'cash+visa'):
            $payment_history = 'Paid By Cash : OMR'.$request['cash_amount'].'<br>'.'Paid by VISA : OMR'.$request['visa_amount'];
        else:
            $payment_history = '';
        endif;

        $invoice_number = $this->invoiceGenerator(7);
        $invoice = DB::table($tablename)->insert([
                'admin_id' => Auth::user()->id,
                'user_id' => $request['user_id'],
                'invoice_type' => 7,
                'location_id' => Auth::user()->location_id,
                'invoice_number' =>  $invoice_number,
                'amount' => $request['sub_total'],
                'payable_amount' => $request['total'],
                'insurance_id' => 1,
                'ins_disc' => '',
                'ins_disc_value' => 0,
                'inc_deduction' => '',
                'inc_deduction_value' =>0,
                'coinsurance' => '',
                'coinsurance_value' => 0,
                'bliss_discount' => $request['offered_reason'],
                'bliss_discount_value' => $request['offered_value'],
                'txn_id' => $request['txn_id'],
                'remark' => $request['remark'],
                'payment_history' => $payment_history,
                'payment_status' => 1,
                'ins_clearance' => 0,
                'payment_mode' => $request['payment_mode'],
                'invoice_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
                ]);

        $sale = Sales::create([
            'customer_id' => $request['user_id'],
            'added_by' => Auth::user()->id,
            'type' => 2,
            'invoice_number' =>  $invoice_number,
            'sub_total' => $request['sub_total'],
            'payable_amount' => $request['total'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        $sale_id = $sale->id;

        foreach ($request['medicine__id'] as $key => $detail) {
            if($detail):
                $rowdata = ['qty' => $request['medicine__qty'][$key],
                            'batch_no' => $request['medicine__batch_no'][$key],
                            'stock_id' => $request['medicine__stock_id'][$key],
                            'cost' => $request['medicine__cost'][$key],
                            'stock' => $request['medicine__stock'][$key],
                            'name' => $request['medicine__name'][$key],
                            'id' => $request['medicine__id'][$key],
                            'total' => $request['medicine__total'][$key] ];

                SaleMedicines::create([
                        'sale_id' =>$sale_id,
                        'medicine_id' => $detail,
                        'stock_id' => $request['medicine__stock_id'][$key],
                        'price' => $request['medicine__cost'][$key],
                        'qty' => $request['medicine__qty'][$key],
                        'rowdata' => json_encode($rowdata),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                ]);

                $stock = Stocks::findOrFail($request['medicine__stock_id'][$key]);
                $qty = $stock['stock'] - $request['medicine__qty'][$key];
                $stock->update(['stock' => $qty]);

            endif;
        }
        return $invoice_number;
    }

    public function courseInvoices($year = null)
    {
        if($year):
            $table =  new Invoices;
            $table = $table->setTable('invoice'.$year.'s');
            $tablename = $table->getTable();
            return $table->select("$tablename.*", 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name as admin')
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereIn("$tablename.invoice_type", [5,6,8])
                    ->orderBy("$tablename.created_at", 'desc')->paginate(15);
        else:
            return ['message' => 'Year is missing. No data can be fetched.'];
        endif;
    }

    public function getInvoicesBycourses(Request $request)
    {
        $return = [];
        foreach ($request['invoices'] as $key => $value):

            $code = explode('-', $value);
            $year = '20'.substr($code[1], 0, 2);
            $table =  new Invoices;
            $table = $table->setTable('invoice'.$year.'s');
            $tablename = $table->getTable();
            $invoice = $table->select("$tablename.*", 'admins.name as admin')
                            ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                            ->where('invoice_number', $value)
                        ->first();
            if($invoice->payment_status == 2 || $invoice->payment_status == 3):
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

    public function store(Request $request)
    {
        $this->validate($request, [
            'sub_total' => 'required',
            'payment_mode' => 'required',
            'total' => 'required'
        ]);

        //return $request;
        $year = '20'.substr($request['appointment_id'], 1, 2);
        $atable =  new Appointments;
        $atable = $atable->setTable('appointment'.$year.'s');
        $aptype = $atable->where('appointment_code', $request['appointment_id'])->get()->first();

        $table =  new Invoices;
        $table = $table->setTable('invoice'.date('Y', time()).'s');
        $tablename = $table->getTable();

        $invoice_number = $this->invoiceGenerator($aptype->appointment_type_id);

        if($aptype->appointment_type_id == 1): $type = 1;
        elseif($aptype->appointment_type_id == 2): $type = 2;
        else: $type = 7; endif;
        if(isset($request['showdiscount']) && ($request['showdiscount'] == 1)) $disc = 1; else $disc = 0;

        if($request['insured'] != 1):
            $request['insurance_id'] = 1;
            $request['insured_discount_reason'] = '';
            $request['insured_discount'] = '0';
            $request['insured_deduction_reason'] = '';
            $request['insured_deduction'] = '0';
        endif;

        $invoice = DB::table($tablename)->insert([
            'admin_id' => Auth::user()->id,
            'user_id' => $request['user_id'],
            'invoice_type' => $type,
            'location_id' => Auth::user()->location_id,
            'invoice_number' =>  $invoice_number,
            'appointment_id' => $request['appointment_id'],
            'amount' => $request['sub_total'],
            'payable_amount' => $request['total'],
            'bliss_discount' => $request['offered_reason'],
            'bliss_discount_value' => $request['offered_value'],
            'insurance_id' => $request['insurance_id'],
            'ins_disc' => $request['insured_discount_reason'],
            'ins_disc_value' => $request['insured_discount'],
            'inc_deduction' => $request['insured_deduction_reason'],
            'inc_deduction_value' => $request['insured_deduction'],
            'txn_id' => $request['txn_id'],
            'remark' => $request['remark'],
            'payment_history' => '',
            'payment_status' => 1,
            'showdiscount' => $disc,
            'visa_amount' => $request['visa_amount'],
            'cash_amount' => $request['cash_amount'],
            'received' => $request['received'],
            'returnable' => $request['returnable'],
            'ins_clearance' => 0,
            'payment_mode' => $request['payment_mode'],
            'invoice_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ]);

            if($aptype->treatment_id == 56): $sts = 9;
            elseif($aptype->appointment_type_id == 1): $sts = 8;
            else: $sts = 9; endif;
        $atable->where('appointment_code', $request['appointment_id'])->update(['ainvoice' => $invoice_number, 'status_id' => $sts]);

        return ['invoice' => $invoice_number];
    }

    public function modify(Request $request)
    {
        $this->validate($request, [
            'sub_total' => 'required',
            'payment_mode' => 'required',
            'total' => 'required'
        ]);

        $code = explode('-', $request['invoice_number']);
        $year = '20'.substr($code[1], 0, 2);

        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');

        $tablename = $table->getTable();

        if(isset($request['showdiscount']) && ($request['showdiscount'] == 1)) $disc = 1; else $disc = 0;

        if($request['insured'] != 1):
            $request['insurance_id'] = 1;
            $request['insured_discount_reason'] = '';
            $request['insured_discount'] = '0';
            $request['insured_deduction_reason'] = '';
            $request['insured_deduction'] = '0';
        endif;

        $invoice = DB::table($tablename)->where('invoice_number', $request['invoice_number'])->get()->first();
        if($invoice->revise_history):
            $oldrevise = json_decode($invoice->revise_history);
            $revise = ['admin_id' =>  Auth::user()->id, 'admin_name' =>  Auth::user()->name, 'datetime' => date('d-M-Y h:i A', time())];
            array_push($oldrevise, $revise);
        else:
            $oldrevise[] = ['admin_id' =>  Auth::user()->id, 'admin_name' =>  Auth::user()->name, 'datetime' => date('d-M-Y h:i A', time())];
        endif;

        DB::table($tablename)
                ->where('invoice_number', $request['invoice_number'])
                ->update([
                    'location_id' => Auth::user()->location_id,
                    'amount' => $request['sub_total'],
                    'payable_amount' => $request['total'],
                    'bliss_discount' => $request['offered_reason'],
                    'bliss_discount_value' => $request['offered_value'],
                    'insurance_id' => $request['insurance_id'],
                    'ins_disc' => $request['insured_discount_reason'],
                    'ins_disc_value' => $request['insured_discount'],
                    'inc_deduction' => $request['insured_deduction_reason'],
                    'inc_deduction_value' => $request['insured_deduction'],
                    'txn_id' => $request['txn_id'],
                    'remark' => $request['remark'],
                    'payment_history' => '',
                    'payment_status' => 1,
                    'showdiscount' => $disc,
                    'ins_clearance' => 0,
                    'visa_amount' => $request['visa_amount'],
                    'cash_amount' => $request['cash_amount'],
                    'received' => $request['received'],
                    'returnable' => $request['returnable'],
                    'revise_history' => json_encode($oldrevise),
                    'payment_mode' => $request['payment_mode'],
                    'invoice_date' => date('Y-m-d H:i:s', strtotime($request['invoice_date'])),
                    'updated_at' => Carbon::now()
                ]);

        return ['invoice' => $request['invoice_number']];
    }

    public function icmodify(Request $request)
    {
        $this->validate($request, [
            'invoice_date' => 'required',
        ]);

        $code = explode('-', $request['invoice_number']);
        $year = '20'.substr($code[1], 0, 2);

        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();

        $invoice = DB::table($tablename)->where('invoice_number', $request['invoice_number'])->get()->first();

        if($invoice->revise_history):
            $oldrevise = json_decode($invoice->revise_history);
            $revise = ['admin_id' =>  Auth::user()->id, 'admin_name' =>  Auth::user()->name, 'datetime' => date('d-M-Y h:i A', time()), 'remark' => 'Invoice date has been changed From '.$invoice->invoice_date.' to '.$request['invoice_date'] ];
            array_push($oldrevise, $revise);
        else:
            $oldrevise = ['admin_id' =>  Auth::user()->id, 'admin_name' =>  Auth::user()->name, 'datetime' => date('d-M-Y h:i A', time()), 'remark' => 'Invoice date has been changed From '.$invoice->invoice_date.' to '.$request['invoice_date'] ];
        endif;

        DB::table($tablename)
                ->where('invoice_number', $request['invoice_number'])
                ->update(['invoice_date' => date('Y-m-d H:i:s', strtotime($request['invoice_date'])) ]);

        return ['invoice' => $request['invoice_number']];
    }

    public function storeCourseInvoice(Request $request)
    {
        $this->validate($request, [
            'sub_total' => 'required',
            'payment_mode' => 'required',
            'total' => 'required'
        ]);

        $table =  new Invoices;
        $table = $table->setTable('invoice'.date('Y', time()).'s');
        $tablename = $table->getTable();

        $invoice_number = $this->invoiceGenerator(5);

        $invoice = DB::table($tablename)->insert([
            'admin_id' => Auth::user()->id,
            'user_id' => $request['user_id'],
            'course_code' => $request['course_code'],
            'invoice_type' => 5,
            'location_id' => Auth::user()->location_id,
            'invoice_number' =>  $invoice_number,
            'amount' => $request['sub_total'],
            'payable_amount' => $request['being_paid'],
            'txn_id' => $request['txn_id'],
            'remark' => $request['remark'],
            'payment_history' => '',
            'payment_status' => $request['payment_type'],
            'ins_clearance' => 0,
            'insurance_id' => 1,
            'payment_mode' => $request['payment_mode'],
            'invoice_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ]);

        $cyear = '20'.substr($request['course_code'], 1, 2);
        $ctable =  new Courses;
        $ctable = $ctable->setTable('course'.$cyear.'s');
        $ctable->where('course_code', $request['course_code'])->update(['invoice' => $invoice_number, 'pstatus' => $request['payment_type'], 'status_id' => 9]);

        //return $request;
        foreach (array_filter(explode(',',$request['appointments'])) as $appointment):
            $year = '20'.substr($appointment, 1, 2);
            $atable =  new Appointments;
            $atable = $atable->setTable('appointment'.$year.'s');
            $aptype = $atable->where('appointment_code', $appointment)->get()->first();
            if($aptype->appointment_type_id == 1):
                $atable->where('appointment_code', $appointment)->update(['ainvoice' => $invoice_number, 'status_id' => 8]);
            elseif($aptype->appointment_type_id == 2):
                $atable->where('appointment_code', $appointment)->update(['ainvoice' => $invoice_number, 'status_id' => 9]);
            endif;
        endforeach;

        return ['invoice' => $invoice_number];
    }

    public function storeScheduleCourseInvoice(Request $request)
    {
        //return $request;
        $this->validate($request, [
            'sub_total' => 'required',
            'payment_mode' => 'required',
            'total' => 'required'
        ]);
        $pstatus = 1;
        $table =  new Invoices;
        $table = $table->setTable('invoice'.date('Y', time()).'s');
        $tablename = $table->getTable();

        $invoice_number = $this->invoiceGenerator(6);

        if($request['payment_mode'] == 'cash+visa'):
            $payment_history = 'Paid By Cash : OMR'.$request['cash_amount'].'<br>'.'Paid by VISA : OMR'.$request['visa_amount'];
        else:
            $payment_history = '';
        endif;
        if($request['payment_type'] == 2):
            $partialpay[] = ['paid_amount' => $request['paying_now'],
                            'balance' => round($request['total'] - $request['paying_now'], 3),
                            'pay_mode' => $request['payment_mode'],
                            'txn' => ($request['txn_id'])?$request['txn_id']:'-',
                            'date' => date('Y-m-d', time())];
            $partialpay = json_encode($partialpay);
            $pstatus = 2;
        else: $partialpay = '';   endif;
        $rawdata = [];
        foreach($request['apt__apcode'] as $key => $appointment):
            $rawdata[$key] = ['apcode' => $appointment,
                                'cost' => $request['apt__cost'][$key],
                                'datetime' => $request['apt__datetime'][$key],
                                'reason' => $request['apt__reason'][$key]];
        endforeach;
        $invoice = DB::table($tablename)->insert([
            'admin_id' => Auth::user()->id,
            'user_id' => $request['user_id'],
            'course_code' => $request['course_code'],
            'invoice_type' => 6,
            'location_id' => Auth::user()->location_id,
            'invoice_number' =>  $invoice_number,
            'amount' => $request['sub_total'],
            'payable_amount' => $request['total'],
            'txn_id' => $request['txn_id'],
            'remark' => $request['remark'],
            'insurance_id' => $request['insurance_id'],
            'payment_status' => $pstatus,
            'approved' => 1,
            'ins_clearance' => 0,
            'rawdata' => json_encode($rawdata),
            'payment_history' => $payment_history,
            'partialpay' => $partialpay,
            'bliss_discount' => $request['offered_reason'],
            'bliss_discount_value' => $request['offered_value'],
            'payment_mode' => $request['payment_mode'],
            'visa_amount' => $request['visa_amount'],
            'cash_amount' => $request['cash_amount'],
            'received' => $request['received'],
            'returnable' => $request['returnable'],
            'invoice_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ]);

        $cyear = '20'.substr($request['course_code'], 1, 2);
        $ctable =  new Courses;
        $ctable = $ctable->setTable('course'.$cyear.'s');

        $course = $ctable->where('course_code', $request['course_code'])->get()->first();
        if($course->invoice):
            $invoices = \explode(',',$course->invoice);
            array_push($invoices, $invoice_number);
            $invoices = array_filter($invoices);
            $ctable->where('course_code', $request['course_code'])->update(['invoice' => implode(',',$invoices), 'pstatus' => $request['payment_type']]);
        else:
            $ctable->where('course_code', $request['course_code'])->update(['invoice' => $invoice_number, 'pstatus' => $request['payment_type']]);
        endif;

        foreach ($request['apt__apcode'] as $key => $appointment):
            $year = '20'.substr($appointment, 1, 2);
            $atable =  new Appointments;
            $atable = $atable->setTable('appointment'.$year.'s');
            //$aptype = $atable->where('appointment_code', $appointment)->get()->first();
            $atable->where('appointment_code', $appointment)->update(['ainvoice' => $invoice_number, 'status_id' => 9]);
        endforeach;

        return ['invoice' => $invoice_number];
    }

    public function updateCashCourseInvoice(Request $request)
    {
        //return $request;
        $this->validate($request, [
            'sub_total' => 'required',
            'payment_mode' => 'required',
            'total' => 'required',
            'invoice_number' => 'required',
            'invoice_date'=>'required'
        ]);

        $code = explode('-', $request['invoice_number']);
        $year = '20'.substr($code[1], 0, 2);

        $invoice_number = $request['invoice_number'];
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();

        $invoice = $table->where('invoice_number', $request['invoice_number'])->get()->first();
        $pstatus = 1;
        if($request['payment_mode'] == 'cash+visa'):
            $payment_history = 'Paid By Cash : OMR'.$request['cash_amount'].'<br>'.'Paid by VISA : OMR'.$request['visa_amount'];
        else:
            $payment_history = '';
        endif;
        if($request['payment_type'] == 2):
            $partialpay[] = ['paid_amount' => $request['paying_now'],
                            'balance' => round($request['total'] - $request['paying_now'], 3),
                            'pay_mode' => $request['payment_mode'],
                            'txn' => ($request['txn_id'])?$request['txn_id']:'-',
                            'date' => date('Y-m-d', time())];
            $partialpay = json_encode($partialpay);
            $pstatus = 2;
        else: $partialpay = '';   endif;
        $rawdata = json_encode($request['rawdata']);

        $invoice->update([
            'admin_id' => Auth::user()->id,
            'amount' => $request['sub_total'],
            'payable_amount' => $request['total'],
            'txn_id' => $request['txn_id'],
            'remark' => $request['remark'],
            'insurance_id' => 1,
            'payment_status' => $pstatus,
            'approved' => 1,
            'ins_clearance' => 0,
            'rawdata' => $rawdata,
            'payment_history' => $payment_history,
            'partialpay' => $partialpay,
            'bliss_discount' => $request['offered_reason'],
            'bliss_discount_value' => $request['offered_value'],
            'payment_mode' => $request['payment_mode'],
            'visa_amount' => $request['visa_amount'],
            'cash_amount' => $request['cash_amount'],
            'received' => $request['received'],
            'returnable' => $request['returnable'],
            'invoice_date' => date('Y-m-d H:i:s', strtotime($request['invoice_date'])),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ]);

        $cyear = '20'.substr($request['course_code'], 1, 2);
        $ctable =  new Courses;
        $ctable = $ctable->setTable('course'.$cyear.'s');

        $course = $ctable->where('course_code', $request['course_code'])->get()->first();
        $ctable->where('course_code', $request['course_code'])->update(['invoice' => $invoice_number, 'status_id' => 9, 'pstatus' => $request['payment_type']]);

        $allappointments = array_filter(explode(',', $course->appointments));
        if(count($allappointments) >= 1):
            foreach($allappointments as $key => $appointment):
                $year = '20'.substr($appointment, 1, 2);
                $atable =  new Appointments;
                $atable = $atable->setTable('appointment'.$year.'s');
                //$aptype = $atable->where('appointment_code', $appointment)->get()->first();
                $atable->where('appointment_code', $appointment)->update(['ainvoice' => $invoice_number, 'status_id' => 9]);
            endforeach;
        endif;
        return ['invoice' => $invoice_number];
    }

    public function updateInsuranceCourseInvoice(Request $request)
    {
        //return $request;
        $this->validate($request, [
            'invoice_date'=>'required'
        ]);

        $code = explode('-', $request['invoice_number']);
        $year = '20'.substr($code[1], 0, 2);

        $invoice_number = $request['invoice_number'];
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();

        $invoice = $table->where('invoice_number', $request['invoice_number'])->get()->first();

        $rawdata = json_encode($request['rawdata']);

        $invoice->update([
            'amount' => $request['sub_total'],
            'payable_amount' => $request['sub_total'] - $request['ins_amount'],
            'payment_status' => 2,
            'approved' => 1,
            'ins_clearance' => 0,
            'ins_disc' => $request['ins_disc'],
            'ins_disc_value' => $request['ins_disc_value'],
            'inc_deduction_value' => $request['ins_amount'],
            'inc_deduction' => '',
            'rawdata' => $rawdata,
            'invoice_date' => date('Y-m-d H:i:s', strtotime($request['invoice_date'])),
            'payment_mode' => 1,
            'payment_type' => 3,
        ]);

        $cyear = '20'.substr($request['course_code'], 1, 2);
        $ctable =  new Courses;
        $ctable = $ctable->setTable('course'.$cyear.'s');

        $course = $ctable->where('course_code', $request['course_code'])->get()->first();
        $ctable->where('course_code', $request['course_code'])->update(['invoice' => $invoice_number, 'status_id' => 9, 'pstatus' => $request['payment_type']]);

        foreach(explode(',', $course->appointments) as $key => $appointment):
            $year = '20'.substr($appointment, 1, 2);
            $atable =  new Appointments;
            $atable = $atable->setTable('appointment'.$year.'s');
            //$aptype = $atable->where('appointment_code', $appointment)->get()->first();
            $atable->where('appointment_code', $appointment)->update(['ainvoice' => $invoice_number, 'status_id' => 9]);
        endforeach;

        return ['invoice' => $invoice_number];
    }

    public function storeIScheduleCourseInvoice(Request $request)
    {
        //return $request;
        $this->validate($request, [
            'sub_total' => 'required',
            'total' => 'required',
            'ins_amount' => 'required',
        ]);

        $code = explode('-', $request['invoice_number']);
        $year = '20'.substr($code[1], 0, 2);
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();
        $invoice = $table->where('invoice_number', $request['invoice_number'])->get()->first();

        /*  if($request['payment_mode'] == 'cash+visa'):
            $payment_history = 'Paid By Cash : OMR'.$request['cash_amount'].'<br>'.'Paid by VISA : OMR'.$request['visa_amount'];
        else:
            $payment_history = '';
        endif;
        if($request['payment_type'] == 2):
            $partialpay = ['paid_amount' => $request['paying_now'],
                            'balance' => round($request['total'] - $request['paying_now'], 3),
                            'date' => date('Y-m-d H:i A', time())];
            $partialpay = json_encode($partialpay);
        else: $partialpay = '';   endif;
        $rawdata = [];
        foreach($request['apt__apcode'] as $key => $appointment):
            $rawdata[$key] = ['apcode' => $appointment,
                                'cost' => $request['apt__cost'][$key],
                                'datetime' => $request['apt__datetime'][$key],
                                'reason' => $request['apt__reason'][$key]];
        endforeach;*/

        $invoice->update([
            'amount' => $request['sub_total'],
            'payable_amount' => $request['sub_total'] - $request['ins_amount'],
            'payment_status' => 2,
            'approved' => 1,
            'ins_clearance' => 0,
            'inc_deduction_value' => $request['ins_amount'],
            'inc_deduction' => 'Discount',
            'rawdata' => json_encode($request['appointments']),
            'payment_mode' => 1,
            'payment_type' => 3,
            'invoice_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ]);

        foreach ($request['appointments'] as $key => $appointment):
            $year = '20'.substr($appointment['apcode'], 1, 2);
            $atable =  new Appointments;
            $atable = $atable->setTable('appointment'.$year.'s');
            $atable->where('appointment_code', $appointment['apcode'])->update(['ainvoice' => $request['invoice_number'], 'status_id' => 9]);
        endforeach;

        return ['invoice' =>  $request['invoice_number']];
    }

    public function show($id)
    {
        $detail = [];
        $code = explode('-', $id);
        $year = '20'.substr($code[1], 0, 2);
        $table =  new Invoices;
            $table = $table->setTable('invoice'.$year.'s');
            $tablename = $table->getTable();
            $data = $table->select("$tablename.*", 'admins.name as admin', 'insurances.name as insurancer')
                            ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                            ->join('insurances', 'insurances.id', '=', "$tablename.insurance_id")
                            ->where('invoice_number', $id)
                            ->get()
                            ->first();
            $details = [];
            $apcodes = [];
            if($data->rawdata) {
                $rawdata = json_decode($data->rawdata);
                foreach ($rawdata as $key => $value) {
                    if(isset($value->treatment)):
                        $detail[] = $value;
                    else:
                        if(isset($value->apcode) && (!in_array($value->apcode, $apcodes))) {
                            array_push($apcodes, $value->apcode); $push = 1;
                        } else {
                            $push = 0;
                        }
                        if($push == 1) {
                            $detail[] = $value;
                        }
                    endif;
                }
            }
            if(count($apcodes) >= 1): $data->table_type = 1; else: $data->table_type = 0; endif;
            if($data->partialpay): $data->partialpay = json_decode($data->partialpay); else: $data->partialpay = []; endif;
            $data->details = $detail;
        return $data;

    }

    public function update(Request $request, $id)
    {
        //
    }

    public function updatePharmacyInvoice(Request $request)
    {
        $this->validate($request, [
            'reason_type' => 'required',
            'reason' => 'required',
        ]);
        $year = '20'.substr($request['invoice_number'], 3, 2);
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();

        $invoice = $table->where('invoice_number', $request['invoice_number'])->get()->first();
        $invoice->update([
            'amount' => $request['sub_total'],
            'payable_amount' => $request['total'],
            'coinsurance_value' => $request['coinsurance_value'],
            'bliss_discount_value' => $request['offered_value'],
            'rtype' => $request['reason_type'],
	        'rcount' => $invoice->rcount+1,
            'reason' =>  $request['reason'],
            'invoice_date' => date('Y-m-d H:i:s', strtotime($request['invoice_date'])),
            'updated_at' => Carbon::now()
        ]);

        $sale = Sales::where('id', $request['sale_id'])->get()->first();
        $sale->update([
            'sub_total' => $request['sub_total'],
            'payable_amount' => $request['total'],
            'revised' => 1
        ]);

        foreach ($request['medicine__spid'] as $key => $detail) {
            if($detail && ($request['medicine__rqty'] >= 1)):
                if($request['medicine__rqty'][$key] == $request['medicine__qty'][$key]):
                    SaleMedicines::where('id', $detail)->delete();
                    $stock = Stocks::findOrFail($request['medicine__stock_id'][$key]);
                    $qty = $stock['stock'] + $request['medicine__rqty'][$key];
                    $stock->update(['stock' => $qty]);
                elseif($request['medicine__rqty'][$key] < $request['medicine__qty'][$key]):
                    $rqty = $request['medicine__qty'][$key] - $request['medicine__rqty'][$key];
                    $smed = SaleMedicines::where('id', $detail)->get()->first();

                    $rdata = json_decode($smed->rowdata);

                    $rdata->qty = $rqty;
                    $rdata->total = $request['medicine__total'][$key];

                    SaleMedicines::where('id', $detail)
                        ->update([
                            'qty' => $rqty,
                            'rowdata' => json_encode($rdata),
                            'updated_at' => Carbon::now()
                    ]);

                    $stock = Stocks::findOrFail($request['medicine__stock_id'][$key]);
                    $qty = $stock['stock'] + $request['medicine__rzqty'][$key];
                    $stock->update(['stock' => $qty]);
                endif;
            endif;
        }
        return ['invoice modified'];
    }

    public function updateInPharmacyInvoice(Request $request)
    {
        $this->validate($request, [
            'payment_mode' => 'required',
            'total' => 'required',
            'invoice_date' => 'required',
        ]);
        $year = '20'.substr($request['invoice_number'], 3, 2);
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();

        $invoice = $table->where('invoice_number', $request['invoice_number'])->get()->first();
        if($request['payment_mode'] == 'cash'):
            $cash = $request['total']; $visa = 0;
            $received =$request['received'];
            $returnable =$request['returnable'];
        elseif($request['payment_mode'] == 'visa'): $cash = 0; $visa = $request['total']; $received = 0; $returnable = 0;
        elseif($request['payment_mode'] == 'cash+visa'):
            $cash = $request['cash_amount']; $visa = $request['visa_amount'];
            $received =$request['received'];
            $returnable =$request['returnable'];
        else: $cash = 0; $visa = 0; $received = 0; $returnable = 0; endif;
        $invoice->update([
            'amount' => $request['sub_total'],
            'payable_amount' => $request['total'],
            'coinsurance_value' => $request['coinsurance_value'],
            'bliss_discount_value' => $request['offered_value'],
            'rtype' => $request['reason_type'],
	        'rcount' => $invoice->rcount+1,
            'reason' => $request['reason'],
            'received' => $received,
            'returnable' => $returnable,
            'cash_amount' => $cash,
            'visa_amount' => $visa,
            'payment_mode' => $request['payment_mode'],
            'txn_id' =>  $request['txn_id'],
            'invoice_date' => date('Y-m-d H:i:s', strtotime($request['invoice_date'])),
            'updated_at' => Carbon::now()
        ]);

        $sale = Sales::where('id', $request['sale_id'])->get()->first();
        $sale->update([
            'sub_total' => $request['sub_total'],
            'payable_amount' => $request['total'],
            'revised' => 1
        ]);

        foreach ($request['medicine__id'] as $key => $detail) {
            if($detail):
                $rowdata = ['qty' => $request['medicine__qty'][$key],
                            'batch_no' => $request['medicine__batch_no'][$key],
                            'stock_id' => $request['medicine__stock_id'][$key],
                            'cost' => $request['medicine__cost'][$key],
                            'stock' => $request['medicine__stock'][$key],
                            'name' => $request['medicine__name'][$key],
                            'id' => $request['medicine__id'][$key],
                            'total' => $request['medicine__total'][$key] ];

                SaleMedicines::create([
                        'sale_id' =>$sale->id,
                        'medicine_id' => $detail,
                        'stock_id' => $request['medicine__stock_id'][$key],
                        'price' => $request['medicine__cost'][$key],
                        'qty' => $request['medicine__qty'][$key],
                        'rowdata' => json_encode($rowdata),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                ]);

                $stock = Stocks::findOrFail($request['medicine__stock_id'][$key]);
                $qty = $stock['stock'] - $request['medicine__qty'][$key];
                $stock->update(['stock' => $qty]);

            endif;
        }

        return ['invoice modified'];
    }

    public function cancelInvoice(Request $request)
    {
        $this->validate($request, [
            'reason' => 'required',
        ]);

        $year = '20'.substr($request['invoice_number'], 3, 2);
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');

        $invoice = $table->where('invoice_number', $request['invoice_number'])->get()->first();

       // return $invoice;
        $cancel = ['reason' => $request['reason'],
                        'admin' => Auth::user()->id,
                        'admin name' => Auth::user()->name,
                        'date-time' => date('d-m-Y H:i A', time()) ];

        $invoice->update(['cancel' =>  json_encode($cancel),
                            'updated_at' => Carbon::now() ]);

        if(($invoice->invoice_type == 1) && ($invoice->appointment_id)):

            $year = '20'.substr($invoice->appointment_id, 1, 2);
            $atable =  new Appointments;
            $atable = $atable->setTable('appointment'.$year.'s');
            $atablename = $atable->getTable();
            $record1 = DB::table($atablename)->where('appointment_code', $invoice->appointment_id)->get()->first();
           // if(in_array($record1, [4,5,8,9,10])):
                DB::table($atablename)->where('appointment_code', $invoice->appointment_id)->update(['ainvoice' => '', 'status_id' => 2]);
            //endif;

        elseif(($invoice->invoice_type == 2) && ($invoice->appointment_id)):

            $year = '20'.substr($invoice->appointment_id, 1, 2);
            $atable =  new Appointments;
            $atable = $atable->setTable('appointment'.$year.'s');
            $atablename = $atable->getTable();
            $record2 = DB::table($atablename)->where('appointment_code', $invoice->appointment_id)->get()->first();
            if(in_array($record2->status_id, [4,5,8,9,10])):
                DB::table($atablename)->where('appointment_code', $invoice->appointment_id)->update(['ainvoice' => '', 'status_id' => 2]);
            endif;

        elseif(($invoice->invoice_type == 5) && ($invoice->course_code)):

            $year = '20'.substr($invoice->course_code, 1, 2);
            $atable =  new Courses;
            $atable = $atable->setTable('course'.$year.'s');
            $atablename = $atable->getTable();
            $record3 = DB::table($atablename)->where('course_code', $invoice->course_code)->get()->first();
            if(in_array($record3->status_id, [4,5,8,9,10])):
                DB::table($atablename)->where('course_code', $invoice->course_code)->update(['invoice' => '', 'status_id' => 2]);
            endif;

        elseif(($invoice->invoice_type == 6) && ($invoice->course_code)):

            $year = '20'.substr($invoice->course_code, 1, 2);
            $atable =  new Courses;
            $atable = $atable->setTable('course'.$year.'s');
            $atablename = $atable->getTable();
            $record4 = DB::table($atablename)->where('course_code', $invoice->course_code)->get()->first();
            $invs = explode(',', $record4->invoice);
            $invd = [$invoice->invoice_number];
            $inva = array_diff($invs, $invd);
            //if(in_array($record4->status_id, [4,5,8,9,10])):
            DB::table($atablename)->where('course_code', $invoice->course_code)->update(['invoice' => implode(',',$inva), 'status_id' => 2]);
            $appointments = explode(',', $record4->appointments);

            foreach ($appointments as $appointment) {
                $aptable =  new Appointments;
                $year = '20'.substr($appointment, 1, 2);
                $aptable = $aptable->setTable('appointment'.$year.'s');
                $aptablename = $aptable->getTable();
                $aprecord = DB::table($aptablename)->where('appointment_code', $appointment)->get()->first();
                if($aprecord->ainvoice == $invoice->invoice_number):
                    DB::table($aptablename)->where('appointment_code', $appointment)->update(['ainvoice' => NULL, 'status_id' => 2]);
                endif;
            }
            // endif;
        endif;

        return ['status' => 'completed'];
    }

    public function cancelPharmacyInvoice(Request $request)
    {
        $this->validate($request, [
            'reason' => 'required',
        ]);
        $year = '20'.substr($request['invoice_number'], 3, 2);
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');

        $invoice = $table->where('invoice_number', $request['invoice_number'])->get()->first();
        $cancel = ['reason' => $request['reason'],
        'admin' => Auth::user()->id,
        'admin name' => Auth::user()->name,
        'date-time' => date('d-m-Y H:i A', time()) ];

        $invoice->update(['cancel' =>  json_encode($cancel),
                            'updated_at' => Carbon::now() ]);

        if(in_array($invoice->invoice_type, [3,4])):

            $year = '20'.substr($invoice->appointment_id, 1, 2);
            $atable =  new Appointments;
            $atable = $atable->setTable('appointment'.$year.'s');
            $atablename = $atable->getTable();

            $appointment = $atable->where('appointment_code', $invoice->appointment_id)->get()->first();
            $pinv = explode(',', $appointment->pinvoice);
            array_filter($pinv);
            $remov = [$request['invoice_number']];
            $pinvoice = array_diff($pinv, $remov);
            DB::table($atablename)
                ->where('appointment_code', $invoice->appointment_id)
                ->update(['pinvoice' => implode(',', $pinvoice)]);

        endif;


        $sale = Sales::where('invoice_number', $request['invoice_number'])->get()->first();
        $sale->update(['status' => 0]);

        $medicines =  SaleMedicines::where('sale_id', $sale->id)->get();

        foreach($medicines as $key => $detail) {
            if($detail->return_status != 1):

                SaleMedicines::where('id', $detail->id)->update(['return_status' => 1]);

                $stock = Stocks::findOrFail($detail->stock_id);
                $qty = $stock['stock'] + $detail->qty;
                $stock->update(['stock' => $qty]);

            endif;
        }
        return ['invoice cancelled'];
    }

    public function dpsearch($year = null)
    {
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();

        if($search = \Request::get('q')) {
            $return = $table->select("$tablename.*", 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name as admin')
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->where("$tablename.location_id", Auth::user()->location_id)
                        ->where("$tablename.invoice_type", 7)
                        ->where(function($query) use ($search, $tablename){
                            $query->where("$tablename.invoice_number",'LIKE',"%$search%")->orwhere('user_profiles.first_name','LIKE',"%$search%")->orwhere('users.username','LIKE',"%$search%");
                        })->orderBy("$tablename.updated_at", 'desc')->paginate(15);
        }
        else {
            $return = $table->select("$tablename.*", 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name as admin')
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->where("$tablename.location_id", Auth::user()->location_id)
                        ->where("$tablename.invoice_type", 7)
                        ->orderBy("$tablename.updated_at", 'desc')->paginate(15);
        }
        return $return;
    }

    public function searchAppInvoices(Request $request)
    {
        $conditions = [];
        $year = $request['year'];

        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();

        if($request['invoice_number']): $conditions["$tablename.invoice_number"] = $request['invoice_number']; endif;
        if($request['insurance_id']): $conditions["$tablename.insurance_id"] = $request['insurance_id']; endif;
        if($request['user_id']): $conditions["$tablename.user_id"] = $request['user_id']; endif;
        if($request['invoice_type']): $conditions["$tablename.invoice_type"] = $request['invoice_type']; endif;

        //return $dconditions;
        if($request['date']):
            return $table->select("$tablename.*", 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name as admin')
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereIn("$tablename.invoice_type", [1,2])
                    ->where($conditions)
                    ->whereDate("$tablename.created_at", date('Y-m-d', strtotime($request['date'])))
                    ->orderBy("$tablename.created_at", 'desc')->paginate(15);
        else:
            return $table->select("$tablename.*", 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name as admin')
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereIn("$tablename.invoice_type", [1,2])
                    ->where($conditions)
                    ->orderBy("$tablename.created_at", 'desc')->paginate(15);
        endif;

    }

    public function searchPharmacyInvoices(Request $request)
    {
        $conditions = [];
        $year = $request['year'];

        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();

        if($request['invoice_number']): $conditions["$tablename.invoice_number"] = $request['invoice_number']; endif;
        if($request['insurance_id']): $conditions["$tablename.insurance_id"] = $request['insurance_id']; endif;
        if($request['user_id']): $conditions["$tablename.user_id"] = $request['user_id']; endif;
        if($request['invoice_type']): $conditions["$tablename.invoice_type"] = $request['invoice_type']; endif;

        //return $dconditions;
        if($request['date']):
            return $table->select("$tablename.*", 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name as admin')
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereIn("$tablename.invoice_type", [3,4])
                    ->where($conditions)
                    ->whereDate("$tablename.created_at", date('Y-m-d', strtotime($request['date'])))
                    ->orderBy("$tablename.created_at", 'desc')->paginate(15);
        else:
            return $table->select("$tablename.*", 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name as admin')
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereIn("$tablename.invoice_type", [3,4])
                    ->where($conditions)
                    ->orderBy("$tablename.created_at", 'desc')->paginate(15);
        endif;

    }

    public function searchDPharmacyInvoices(Request $request)
    {
        $conditions = [];
        $year = $request['year'];

        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();

        if($request['invoice_number']): $conditions["$tablename.invoice_number"] = $request['invoice_number']; endif;
        if($request['user_id']): $conditions["$tablename.user_id"] = $request['user_id']; endif;

        //return $dconditions;
        if($request['date']):
            return $table->select("$tablename.*", 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name as admin')
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereIn("$tablename.invoice_type", [7])
                    ->where($conditions)
                    ->whereDate("$tablename.created_at", date('Y-m-d', strtotime($request['date'])))
                    ->orderBy("$tablename.created_at", 'desc')->paginate(15);
        else:
            return $table->select("$tablename.*", 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name as admin')
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereIn("$tablename.invoice_type", [7])
                    ->where($conditions)
                    ->orderBy("$tablename.created_at", 'desc')->paginate(15);
        endif;

    }

    public function searchCourseInvoices(Request $request)
    {
        $conditions = [];
        $year = $request['year'];

        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();

        if($request['invoice_number']): $conditions["$tablename.invoice_number"] = $request['invoice_number']; endif;
        if($request['insurance_id']): $conditions["$tablename.insurance_id"] = $request['insurance_id']; endif;
        if($request['user_id']): $conditions["$tablename.user_id"] = $request['user_id']; endif;
        if($request['invoice_type']): $conditions["$tablename.invoice_type"] = $request['invoice_type']; endif;

        //return $dconditions;
        if($request['date']):
            return $table->select("$tablename.*", 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name as admin')
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereIn("$tablename.invoice_type", [5,6,8])
                    ->where($conditions)
                    ->whereDate("$tablename.created_at", date('Y-m-d', strtotime($request['date'])))
                    ->orderBy("$tablename.created_at", 'desc')->paginate(15);
        else:
            return $table->select("$tablename.*", 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name as admin')
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereIn("$tablename.invoice_type", [5,6,8])
                    ->where($conditions)
                    ->orderBy("$tablename.created_at", 'desc')->paginate(15);
        endif;

    }

    public function getInvoicesForSettlement(Request $request)
    {
        $conditions = [];
        $sdate = date('Y-m-d', strtotime($request['month']));

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.date('Y', strtotime($sdate)).'s');
        $itablename = $itable->getTable();

        if($request['insurance_id']): $conditions["$itablename.insurance_id"] = $request['insurance_id']; endif;
        if($request['actionable'] > 7):
            $invoices =  $itable->join('admins', 'admins.id', '=', "$itablename.admin_id")
                            ->where("$itablename.location_id", Auth::user()->location_id)
                            ->where($conditions)
                            ->whereIn("$itablename.invoice_number", $request['invoices'])
                            ->with('user')
                            ->whereMonth("$itablename.invoice_date", date('m', strtotime($sdate)))
                            ->where("$itablename.cancel", 0)
                            ->orderBy("$itablename.created_at", 'desc')->get()->all();
            return ['invoices' => $invoices, 'total' => 0, 'ctotal' => 0];
        else:
            $invoices = $itable->join('admins', 'admins.id', '=', "$itablename.admin_id")
                            ->where("$itablename.location_id", Auth::user()->location_id)
                            ->where($conditions)
                            ->with('user')
                            ->whereMonth("$itablename.invoice_date", date('m', strtotime($sdate)))
                            ->where("$itablename.cancel", 0)
                            ->orderBy("$itablename.created_at", 'desc')->get()->all();

            $isum1 = $itable->where("$itablename.location_id", Auth::user()->location_id)
                            ->where($conditions)
                            ->whereMonth("$itablename.invoice_date", date('m', strtotime($sdate)))
                            ->where("$itablename.cancel", 0)->sum('inc_deduction_value');

            $isum2 = $itable->where("$itablename.location_id", Auth::user()->location_id)
                            ->where($conditions)
                            ->whereMonth("$itablename.invoice_date", date('m', strtotime($sdate)))
                            ->where("$itablename.cancel", 0)->sum('coinsurance_value');

            $icsum1 = $itable->where("$itablename.location_id", Auth::user()->location_id)
                            ->where($conditions)
                            ->whereMonth("$itablename.invoice_date", date('m', strtotime($sdate)))
                            ->where("$itablename.cancel", 0)->where("$itablename.ins_clearance", 1)->sum('inc_deduction_value');

            $icsum2 = $itable->where("$itablename.location_id", Auth::user()->location_id)
                            ->where($conditions)
                            ->whereMonth("$itablename.invoice_date", date('m', strtotime($sdate)))
                            ->where("$itablename.cancel", 0)->where("$itablename.ins_clearance", 1)->sum('coinsurance_value');
            return ['invoices' => $invoices, 'total' => $isum1+$isum2, 'ctotal' => $icsum1+$icsum2];
        endif;
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
        elseif($atype == 8): $code = 'CD-';
        else: $code = 'UI-'; endif;
        $number = $code.date('ym-', time()).str_pad($counter, 4, "0", STR_PAD_LEFT);
        return $number;
    }

    public function searchAll(Request $request)
    {
        $conditions = [];
        $sdate = date('Y-m-d', strtotime($request['sdate']));
        $edate = date('Y-m-d', strtotime($request['edate']));

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.date('Y', strtotime($sdate)).'s');
        $itablename = $itable->getTable();

        if($request['insurance_id']): $conditions["$itablename.insurance_id"] = $request['insurance_id']; endif;
        if($request['status_id']): $conditions["$itablename.status_id"] = $request['status_id']; endif;

        return $itable->join('admins', 'admins.id', '=', "$itablename.admin_id")
                    ->where("$itablename.location_id", Auth::user()->location_id)
                    ->where($conditions)
                    ->with('user')
                    ->whereDate("$itablename.invoice_date",'<=', date('Y-m-d', strtotime($edate)))
                    ->whereDate("$itablename.invoice_date",'>=',date('Y-m-d', strtotime($sdate)))
                    ->where("$itablename.cancel", 0)
                    ->orderBy("$itablename.created_at", 'desc')->paginate(15);
    }

    public function addPayment(Request $request)
    {
        if($request['payment_type'] == 3):
            $year = '20'.substr($request['course_code'], 1, 2);
            $ctable =  new Courses;
            $ctable = $ctable->setTable('course'.$year.'s');
            $course = $ctable->where('course_code', $request['course_code'])->get()->first();
            $course->update(['pstatus' => 3]);
        endif;

        $code = explode('-', $request['invoice_number']);
        $year = '20'.substr($code[1], 0, 2);

        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');

        $tablename = $table->getTable();

        $invoice = DB::table($tablename)->where('invoice_number', $request['invoice_number'])->get()->first();
        //return ['invoice' => $invoice];
        if($invoice->partialpay):
            $phist = json_decode($invoice->partialpay);
        else:
            $phist = [];
        endif;
        if($request['payment_type'] == 2):
            $partialpay = ['paid_amount' => $request['paying_now'],
                            'balance' => round($request['total'] - $request['paying_now'], 3),
                            'pay_mode' => $request['payment_mode'],
                            'txn' => ($request['txn_id'])?$request['txn_id']:'-',
                            'date' => date('Y-m-d', time())];
            array_push($phist, $partialpay);
            DB::table($tablename)->where('invoice_number', $request['invoice_number'])->update(['partialpay' => json_encode($phist)]);
        elseif($request['payment_type'] == 3):
            $partialpay = ['paid_amount' => $request['paying_now'],
                            'balance' => round($request['total'] - $request['paying_now'], 3),
                            'pay_mode' => $request['payment_mode'],
                            'txn' => ($request['txn_id'])?$request['txn_id']:'-',
                            'date' => date('Y-m-d', time())];
            array_push($phist, $partialpay);
            DB::table($tablename)->where('invoice_number', $request['invoice_number'])->update(['partialpay' => json_encode($phist), 'payment_status' => 3]);
        endif;
        return ['status' => 'success'];
    }

    public function getPatientHistoryYearwise($id = null, $year = null)
    {
        if($year && $id):
            $table =  new Invoices;
            $table = $table->setTable('invoice'.$year.'s');
            $tablename = $table->getTable();
            return $table->select("$tablename.*", 'admins.name as admin')
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                        ->where("$tablename.user_id", $id)
                        ->orderBy("$tablename.invoice_date", 'desc')->get();
        else:
            return ['message' => 'Year is missing. No data can be fetched.'];
        endif;
    }
}
