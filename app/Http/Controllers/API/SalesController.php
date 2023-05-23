<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Medicines;
use App\Models\Appointments;
use App\Models\Stocks;
use App\Models\Invoices;
use App\Models\Sales;
use App\Models\Payments;
use App\Models\SaleMedicines;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use DB;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
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

        if($request['type'] == 3):
            $invoice_number = $this->invoiceGenerator($request['type']);
            $invoice = DB::table($tablename)->insert([
                'admin_id' => Auth::user()->id,
                'user_id' => $request['user_id'],
                'invoice_type' => 3,
                'location_id' => Auth::user()->location_id,
                'invoice_number' =>  $invoice_number,
                'appointment_id' => $request['appointment_id'],
                'amount' => $request['sub_total'],
                'payable_amount' => $request['total'],
                'insurance_id' => $request['insurance_id'],
                'coinsurance' => $request['coinsurance'],
                'coinsurance_value' => $request['coinsurance_value'],
                'txn_id' => $request['txn_id'],
                'remark' => $request['remark'],
                'payment_history' => $payment_history,
                'payment_status' => 1,
                'ins_clearance' => 0,
                'payment_mode' => $request['payment_mode'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'invoice_date' => Carbon::now()
                ]);
        elseif($request['type'] == 4):
            $invoice_number = $this->invoiceGenerator($request['type']);
            $invoice = DB::table($tablename)->insert([
                'admin_id' => Auth::user()->id,
                'user_id' => $request['user_id'],
                'invoice_type' => 4,
                'location_id' => Auth::user()->location_id,
                'invoice_number' =>  $invoice_number,
                'appointment_id' => $request['appointment_id'],
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
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'invoice_date' => Carbon::now()
                ]);
        endif;


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
        /*
        if($request['payment_type'] == 2): $payamount = $request['grand_total']; $spayamount = 0; $pstatus = 1;
        else: $payamount = $request['paid_amount']; $spayamount = $request['grand_total']-$request['paid_amount']; $pstatus = 0; endif;

        Payments::create([
            'payment_type' => $request['payment_type'],
            'sale_id' => $sale_id,
            'firstpay_mode' => $request['payment_mode'],
            'firstpay_amount' => $payamount,
            'secondpay_amount' => $spayamount,
            'firstpay_date' => date('Y-m-d', time()),
            'firstpay_receiver' =>  Auth::user()->id,
            'firstpay_desc' => $request['payment_desc'],
            'pay_status' => $pstatus,
        ]); */

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

        $year = '20'.substr($request['appointment_id'], 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        $apt = $table->where('appointment_code', $request['appointment_id'])->get()->first();
        if($apt->pinvoice):
            $pinvoices = \explode(',',$apt->pinvoice);
            array_push($pinvoices, $invoice_number);
            array_filter($pinvoices);
            $table->where('appointment_code', $request['appointment_id'])->update(['pinvoice' => implode(',',$pinvoices)]);
        else:
            $table->where('appointment_code', $request['appointment_id'])->update(['pinvoice' => $invoice_number]);
        endif;
        return ['invoice' => $invoice_number];
    }

    public function show($id)
    {
        return Sales::where('invoice_number', $id)->first();
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    private function invoiceGenerator($atype = null)
    {
        $table =  new Invoices;
        $table = $table->setTable('invoice'.date('Y', time()).'s');
        $tablename = $table->getTable();
        $app = DB::table($tablename)->orderBy('id', 'desc')->get()->first();
        if($app):
            $linv = substr($app->invoice_number, 8);
            $counter = intval($linv)+1;
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
}
