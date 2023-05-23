<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use DB;

use App\Models\Invoices;
use App\Models\InvoicePayments;

class InvoicePaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {       
        $code = explode('-', $request->invoice_number);
        $year = '20'.substr($code[1], 0, 2);
        $table =  new InvoicePayments;
        $return = []; 
        for ($i = $year-1; $i <= $year+1; $i++) { 
            $table = $table->setTable('payment'.$i.'s');
            $tablename = $table->getTable();
            $data = [];
            if(Schema::hasTable($tablename)) {
                $data = $table->select("$tablename.*", 'admins.name as admin')
                            ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                            ->where("$tablename.invoice_no", $request->invoice_number)
                            ->get()->toArray();
            }
            $return = array_merge($return, $data);
        }
        $paid = array_sum(array_column($return, 'amount'));

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.$year.'s');
        $itablename = $itable->getTable();
        $invoice = $itable->select("$itablename.*")
                            ->where('invoice_number', $request->invoice_number)
                            ->first();

        $total = $invoice->payable_amount;
        return ['payments' => $return, 'remaining' => $total-$paid];
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required',
            'payment_mode' => 'required',
            'invoice_number' => 'required'
        ]);

        $table =  new InvoicePayments;
        $table = $table->setTable('payment'.date('Y', time()).'s');
        $tablename = $table->getTable();

        $payment = DB::table($tablename)->insert([
            'location_id' => Auth::user()->location_id,
            'admin_id' => Auth::user()->id,
            'invoice_no' =>  $request['invoice_number'],
            'amount' => $request['amount'],
            'mode' => $request['payment_mode'],
            'cash_amount' => ($request['cash_amount'])?$request['cash_amount']:0,
            'visa_amount' => ($request['visa_amount'])?$request['visa_amount']:0,
            'received' => ($request['received'])?$request['received']:0,
            'returnable' => ($request['returnable'])?$request['returnable']:0,
            'date' => date('Y-m-d', time()),
            'txnid' => $request['txn_id'],
            'remark' => $request['remark'],
            'status_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        $type = 0;
        if($request->remaing_balance == $request->amount) {
            $year = '20'.substr($request['invoice_number'], 3, 2);
            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.$year.'s');
            $itablename = $itable->getTable();

            $invoice = $itable->where('invoice_number', $request['invoice_number'])->first();
            $invoice->update(['payment_status' =>  3, 'updated_at' => Carbon::now()]);

            $type = 1;
        }
        elseif($request->remaing_balance > $request->amount) {
            $year = '20'.substr($request['invoice_number'], 3, 2);
            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.$year.'s');
            $itablename = $itable->getTable();

            $invoice = $itable->where('invoice_number', $request['invoice_number'])->first();
            $invoice->update(['payment_status' =>  2, 'updated_at' => Carbon::now()]);

            $type = 2;
        }
        return ['invoice' => $request['invoice_number'], 'type' => $type];
    }

    public function calcel(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
            'payment_id' => 'required',
        ]);

        $table =  new InvoicePayments;
        $table = $table->setTable('payment'.date('Y', strtotime($request['date'])).'s');
        $tablename = $table->getTable();

        $reason = ['reason' => $request['reason'], 'admin_id' => Auth::user()->id, 'admin_name' => Auth::user()->name, 'timestamp' => date('Y-m-d', time())];
        $txn = $table->where('id', $request['payment_id'])->first();
        $txn->update(['reason' => json_encode($reason)]);
        

        $year = '20'.substr($txn->invoice_no, 3, 2);
        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.$year.'s');
        $itablename = $itable->getTable();

        $invoice = $itable->where('invoice_number', $txn->invoice_no)->first();
        $invoice->update(['payment_status' =>  2, 'updated_at' => Carbon::now()]);

        $txn->delete();

        return ['status' => 'success'];
    }

}
