<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Appointments;
use App\Models\Insurances;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoices;
use App\Models\Treatments;
use DB;

class InvoiceController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function report($sdate = null, $edate = null)
    {
        $f_total = 0; $f_disc = 0; $f_insurance = 0; $f_cash = 0; $f_visa = 0; $f_credit = 0; $f_epay = 0; $f_cashd = 0;
        $sum_insurance = [0,0,0,0,0,0,0,0,0];
        $sum_cash = [0,0,0,0,0,0,0,0,0];
        $sum_visa = [0,0,0,0,0,0,0,0,0];
        $itypes = [1=> 'Consultation', 2=> 'Treatments', 3=> 'Insured Medicines', 4=> 'Cash Medicines', 5=> 'Course Packages', 6=> 'Course Prescribed', 7=> 'Direct Medicines', 8 => 'Direct Courses'];

        $output = [];
        $columns = [
                'S.No.' => 'sno', 
                'Invoice Type' => 'InvoiceType', 
                'Payment Mode' => 'PaymentMode', 
                'Customer ID' => 'CustomerID', 
                'Customer Name' => 'CustomerName', 
                'Invoice Number' => 'InvoiceNumber', 
                'Total' => 'Total', 
                'Ins Discount' => 'InsDiscount', 
                'Insurance' => 'Insurance', 
                'Paid As' => 'PaidAs', 
                'Cash Discount' => 'CashDiscount', 
                'Cash' => 'Cash', 
                'VISA' => 'VISA', 
                'Txn No' => 'TxnNo', 
                'Credit' => 'Credit', 
                'EPay' => 'EPay', 
                'Invoice Date' => 'InvoiceDate', 
                'Created By' => 'CreatedBy', 
                'Remark' => 'Remark', 
                'Cancel Status' => 'CancelStatus'];
        $sep = ['sno' => '-', 
                'InvoiceType' => '-', 
                'PaymentMode' => '-', 
                'CustomerID' => '-', 
                'CustomerName' => '-', 
                'InvoiceNumber' => '-', 
                'Total' => '-', 
                'InsDiscount' => '-', 
                'Insurance' => '-', 
                'PaidAs' => '-', 
                'CashDiscount' => '-', 
                'Cash' => '-', 
                'VISA' => '-', 
                'TxnNo' => '-', 
                'Credit' => '-', 
                'EPay' => '-', 
                'InvoiceDate' => '-', 
                'CreatedBy' => '-', 
                'Remark' => '-', 
                'CancelStatus' => '-'];

        $year = date('Y', strtotime($sdate));
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();
        $data = $table->select("$tablename.*", 'user_profiles.first_name', 'users.username', 'admins.name as admin', 'insurances.name as insurancer')
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('insurances', 'insurances.id', '=', "$tablename.insurance_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereDate("$tablename.invoice_date",'<=', date('Y-m-d', strtotime($edate)))
                    ->whereDate("$tablename.invoice_date",'>=',date('Y-m-d', strtotime($sdate)))
                    ->orderBy("$tablename.updated_at", 'desc')->get();

         foreach($data as $key => $index)
         {
            if($index->invoice_type == 1):
                if($index->insurance_id == 1): $ins = 0; $pmode = 'Cash';
                else: $ins = $index->inc_deduction_value; $pmode = $index->insurancer; endif;
            elseif($index->invoice_type == 2):
                if($index->insurance_id == 1): $ins = 0; $pmode = 'Cash';
                else: $ins = $index->inc_deduction_value; $pmode = $index->insurancer; endif;
            elseif($index->invoice_type == 3):
                $ins = $index->coinsurance_value; $pmode = $index->insurancer;
            elseif($index->invoice_type == 4):
                $ins = 0; $pmode = 'Cash';
            elseif($index->invoice_type == 5):
                $ins = 0; $pmode = 'Cash';
            elseif($index->invoice_type == 6):
                if($index->insurance_id == 1): $ins = 0; $pmode = 'Cash';
                else: $ins = $index->inc_deduction_value; $pmode = $index->insurancer; endif;
            elseif($index->invoice_type == 7):
                $ins = 0; $pmode = 'Cash';
            elseif($index->invoice_type == 8):
                $ins = 0; $pmode = 'Cash';
            else: $ins = 0; $pmode = 'Cash'; endif;
            if($index->cancel == '0'): $cancel = '-';
            elseif($index->cancel == NULL): $cancel = '-';
            else: $data = json_decode($index->cancel); $cancel = 'Cancelled Reason - '.$data->reason; endif;
            if($index->insurance_id == 1):
                $sum_insurance[$index->invoice_type] = $sum_insurance[$index->invoice_type] + 0;
                if($index->payment_mode == 'cash'):
                    $sum_cash[$index->invoice_type] = $sum_cash[$index->invoice_type] + $index->payable_amount;
                elseif($index->payment_mode == 'visa'):
                    $sum_visa[$index->invoice_type] = $sum_visa[$index->invoice_type] + $index->payable_amount;
                elseif($index->payment_mode == 'cash+visa'):
                    $sum_cash[$index->invoice_type] = $sum_cash[$index->invoice_type] + $index->cash_amount;
                    $sum_visa[$index->invoice_type] = $sum_visa[$index->invoice_type] + $index->visa_amount;
                endif;
            else:
                $sum_insurance[$index->invoice_type] = $sum_insurance[$index->invoice_type] + $index->inc_deduction_value + $index->coinsurance_value;
                if($index->payment_mode == 'cash'):
                    $sum_cash[$index->invoice_type] = $sum_cash[$index->invoice_type] + $index->payable_amount;
                elseif($index->payment_mode == 'visa'):
                    $sum_visa[$index->invoice_type] = $sum_visa[$index->invoice_type] + $index->payable_amount;
                elseif($index->payment_mode == 'cash+visa'):
                    $sum_cash[$index->invoice_type] = $sum_cash[$index->invoice_type] + $index->cash_amount;
                    $sum_visa[$index->invoice_type] = $sum_visa[$index->invoice_type] + $index->visa_amount;
                endif;
            endif;


            if($index->payment_mode == 'cash'): $cash = $index->payable_amount; $visa = 0; $credit = 0; $remark = ''; $epay = 0;
            elseif($index->payment_mode == 'visa'): $visa = $index->payable_amount; $cash = 0; $credit = 0; $remark = ''; $epay = 0;
            elseif($index->payment_mode == 'credit'): $credit = $index->payable_amount; $visa = 0; $cash = 0; $remark = ''; $epay = 0;
            elseif($index->payment_mode == 'epay'): $epay = $index->payable_amount; $visa = 0; $credit = 0; $remark = ''; $cash = 0;
            elseif($index->payment_mode == 'cash+visa'): $cash = $index->payable_amount; $visa = 0; $credit = 0; $remark = $index->payment_history; $epay = 0;
            else: $cash = 0; $credit = 0; $remark = ''; $epay = 0;  $visa = 0;
            endif;
			
            $newdata = ['sno' => ++$key, 
                    'InvoiceType' => $itypes[$index->invoice_type], 
                    'PaymentMode' => $pmode, 
                    'CustomerID' => $index->username, 
                    'CustomerName' => $index->first_name, 
                    'InvoiceNumber' => $index->invoice_number,
                    'Total' => number_format($index->amount, 3), 
                    'InsDiscount' => number_format($index->ins_disc_value, 3), 
                    'Insurance' =>  number_format($ins, 3), 
                    'PaidAs' => $index->payment_mode, 
                    'CashDiscount' => number_format($index->bliss_discount_value, 3), 
                    'Cash' => number_format($cash, 3), 
                    'VISA' => number_format($visa, 3), 
                    'TxnNo' => $index->txn_id, 
                    'Credit' => number_format($credit, 3), 
                    'EPay' => number_format($epay, 3), 
                    'InvoiceDate' => date('d-m-Y H:i A', strtotime($index->invoice_date)), 
                    'CreatedBy' => $index->admin, 
                    'Remark' => $remark, 
                    'CancelStatus' => $cancel];

            $f_total = $f_total + $index->amount;
            $f_disc = $f_disc + $index->ins_disc_value;
            $f_insurance = $f_insurance + $ins;
            $f_cashd = $f_cashd + $index->bliss_discount_value;
			$f_cash = $f_cash + $cash;
            $f_visa = $f_visa + $visa;
            $f_credit = $f_credit + $credit;
            $f_epay = $f_epay + $epay;

            array_push($output, $newdata);
        }

        $total = ['sno' => '-', 
                    'InvoiceType' => 'Total', 
                    'PaymentMode' => '-', 
                    'CustomerID' => '-', 
                    'CustomerName' => '-', 
                    'InvoiceNumber' => '-',
                    'Total' => number_format($f_total, 3), 
                    'InsDiscount' => number_format($f_disc, 3), 
                    'Insurance' =>  number_format($f_insurance, 3), 
                    'PaidAs' => '-', 
                    'CashDiscount' => number_format($f_cashd, 3), 
                    'Cash' => number_format($f_cash, 3), 
                    'VISA' => number_format($f_visa, 3), 
                    'TxnNo' => '-', 
                    'Credit' => number_format($f_credit, 3), 
                    'EPay' => number_format($f_epay, 3), 
                    'InvoiceDate' => '-', 
                    'CreatedBy' => '-', 
                    'Remark' => '-', 
                    'CancelStatus' => '-'];
        
        array_push($output, $total);
        
        array_push($output, $sep);
        array_push($output, $sep);

        $row = ['sno' => '', 'InvoiceType' => '', 'PaymentMode' => '', 'CustomerID' => '', 'CustomerName' => '', 'InvoiceNumber' => 'Invoice Type', 'Total' => 'Sum Of Insurance', 'InsDiscount' => 'Sum of Cash', 'Insurance' => 'Sum Of VISA', 'PaidAs' => '', 'CashDiscount' => '', 'Cash' => '', 'VISA' => '', 'TxnNo' => '', 'Credit' => '', 'EPay' => '', 'InvoiceDate' => '', 'CreatedBy' => '', 'Remark' => '', 'CancelStatus' => ''];
        array_push($output, $row);

        $row = ['sno' => '', 'InvoiceType' => '', 'PaymentMode' => '', 'CustomerID' => '', 'CustomerName' => '', 'InvoiceNumber' => 'Consultation', 'Total' => number_format($sum_insurance[1], 3), 'InsDiscount' => number_format($sum_cash[1], 3), 'Insurance' => number_format($sum_visa[1], 3), 'PaidAs' => '', 'CashDiscount' => '', 'Cash' => '', 'VISA' => '', 'TxnNo' => '', 'Credit' => '', 'EPay' => '', 'InvoiceDate' => '', 'CreatedBy' => '', 'Remark' => '', 'CancelStatus' => ''];
        array_push($output, $row);

        $row = ['sno' => '', 'InvoiceType' => '', 'PaymentMode' => '', 'CustomerID' => '', 'CustomerName' => '', 'InvoiceNumber' => 'Treatments', 'Total' => number_format($sum_insurance[2], 3), 'InsDiscount' => number_format($sum_cash[2], 3), 'Insurance' => number_format($sum_visa[2], 3), 'PaidAs' => '', 'CashDiscount' => '', 'Cash' => '', 'VISA' => '', 'TxnNo' => '', 'Credit' => '', 'EPay' => '', 'InvoiceDate' => '', 'CreatedBy' => '', 'Remark' => '', 'CancelStatus' => ''];
        array_push($output, $row);

        $row = ['sno' => '', 'InvoiceType' => '', 'PaymentMode' => '', 'CustomerID' => '', 'CustomerName' => '', 'InvoiceNumber' => 'Insured Medicines', 'Total' => number_format($sum_insurance[3], 3), 'InsDiscount' => number_format($sum_cash[3], 3), 'Insurance' => number_format($sum_visa[3], 3), 'PaidAs' => '', 'CashDiscount' => '', 'Cash' => '', 'VISA' => '', 'TxnNo' => '', 'Credit' => '', 'EPay' => '', 'InvoiceDate' => '', 'CreatedBy' => '', 'Remark' => '', 'CancelStatus' => ''];
        array_push($output, $row);

        $row = ['sno' => '', 'InvoiceType' => '', 'PaymentMode' => '', 'CustomerID' => '', 'CustomerName' => '', 'InvoiceNumber' => 'Cash Medicines', 'Total' => number_format($sum_insurance[4], 3), 'InsDiscount' => number_format($sum_cash[4], 3), 'Insurance' => number_format($sum_visa[4], 3), 'PaidAs' => '', 'CashDiscount' => '', 'Cash' => '', 'VISA' => '', 'TxnNo' => '', 'Credit' => '', 'EPay' => '', 'InvoiceDate' => '', 'CreatedBy' => '', 'Remark' => '', 'CancelStatus' => ''];
        array_push($output, $row);

        $row = ['sno' => '', 'InvoiceType' => '', 'PaymentMode' => '', 'CustomerID' => '', 'CustomerName' => '', 'InvoiceNumber' => 'Course Package', 'Total' => number_format($sum_insurance[5], 3), 'InsDiscount' => number_format($sum_cash[5], 3), 'Insurance' => number_format($sum_visa[5], 3), 'PaidAs' => '', 'CashDiscount' => '', 'Cash' => '', 'VISA' => '', 'TxnNo' => '', 'Credit' => '', 'EPay' => '', 'InvoiceDate' => '', 'CreatedBy' => '', 'Remark' => '', 'CancelStatus' => ''];
        array_push($output, $row);

        $row = ['sno' => '', 'InvoiceType' => '', 'PaymentMode' => '', 'CustomerID' => '', 'CustomerName' => '', 'InvoiceNumber' => 'Course Prescribed', 'Total' => number_format($sum_insurance[6], 3), 'InsDiscount' => number_format($sum_cash[6], 3), 'Insurance' => number_format($sum_visa[6], 3), 'PaidAs' => '', 'CashDiscount' => '', 'Cash' => '', 'VISA' => '', 'TxnNo' => '', 'Credit' => '', 'EPay' => '', 'InvoiceDate' => '', 'CreatedBy' => '', 'Remark' => '', 'CancelStatus' => ''];
        array_push($output, $row);

        $row = ['sno' => '', 'InvoiceType' => '', 'PaymentMode' => '', 'CustomerID' => '', 'CustomerName' => '', 'InvoiceNumber' => 'Direct Medicines', 'Total' => number_format($sum_insurance[7], 3), 'InsDiscount' => number_format($sum_cash[7], 3), 'Insurance' => number_format($sum_visa[7], 3), 'PaidAs' => '', 'CashDiscount' => '', 'Cash' => '', 'VISA' => '', 'TxnNo' => '', 'Credit' => '', 'EPay' => '', 'InvoiceDate' => '', 'CreatedBy' => '', 'Remark' => '', 'CancelStatus' => ''];
        array_push($output, $row);

        $row = ['sno' => '', 'InvoiceType' => '', 'PaymentMode' => '', 'CustomerID' => '', 'CustomerName' => '', 'InvoiceNumber' => 'Direct Course', 'Total' => number_format($sum_insurance[8], 3), 'InsDiscount' => number_format($sum_cash[8], 3), 'Insurance' => number_format($sum_visa[8], 3), 'PaidAs' => '', 'CashDiscount' => '', 'Cash' => '', 'VISA' => '', 'TxnNo' => '', 'Credit' => '', 'EPay' => '', 'InvoiceDate' => '', 'CreatedBy' => '', 'Remark' => '', 'CancelStatus' => ''];
        array_push($output, $row);

        $row = ['sno' => '', 'InvoiceType' => '', 'PaymentMode' => '', 'CustomerID' => '', 'CustomerName' => '', 'InvoiceNumber' => 'Grand Total', 'Total' => number_format(array_sum($sum_insurance), 3), 'InsDiscount' => number_format(array_sum($sum_cash), 3), 'Insurance' => number_format(array_sum($sum_visa), 3), 'PaidAs' => '', 'CashDiscount' => '', 'Cash' => '', 'VISA' => '', 'TxnNo' => '', 'Credit' => '', 'EPay' => '', 'InvoiceDate' => '', 'CreatedBy' => '', 'Remark' => '', 'CancelStatus' => ''];
        array_push($output, $row);

        return ['json_fields' => $columns, 'records' => $output];
        die;    
    }


    public function cumulativeReport($sdate = null, $edate = null, $iid = null)
    {
        $invoice_types = ['Unknown','Consultation','Treatment','Pharmacy','Pharmacy Cash','Package','Course', 'Direct Medicine', 'Direct Course'];

        $output = [];
        $columns = array(
                'S.No.' => 'sno', 
                'Insurance' => 'Insurance', 
                'Client Name' => 'ClientName', 
                'Client Number' => 'ClientNumber', 
                'Client ID' => 'ClientID', 
                'Company Name' => 'CompanyName', 
                'Policy Number' => 'PolicyNumber', 
                'Expiry Date' => 'ExpiryDate',  
                'Invoice Number' => 'InvoiceNumber', 
                'Invoice Date' => 'InvoiceDate', 
                'Invoice Type' => 'InvoiceType',  
                'Actual Cash Price' => 'ActualCashPrice', 
                'Insurance Discount'  => 'InsuranceDiscount',
                'Ins Price' => 'InsPrice', 
                'Deductable/Co Ins' => 'DeductableCoIns', 
                'Claim Amount' => 'ClaimAmount');

        $insurance = Insurances::where('status_id', 2)->where('id', $iid)->get()->first();

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.date('Y', strtotime($sdate)).'s');
        $itablename = $itable->getTable();

        $invoices = $itable->whereDate("$itablename.invoice_date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$itablename.invoice_date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$itablename.insurance_id", $iid)
                            ->where("$itablename.cancel", 0)
                            ->where("$itablename.location_id", Auth::user()->location_id)
                            ->with('user')
                            ->orderBy("$itablename.invoice_date", 'asc')
                            ->orderBy("$itablename.user_id", 'asc')
                            ->get();

        foreach($invoices as $key => $index)
        {
            if(in_array($index->invoice_type, [1,2,6])):
                $ins = $index->inc_deduction_value;
                $ins_price = $index->inc_deduction_value + $index->payable_amount;
            elseif(in_array($index->invoice_type, [3])):
                $ins = $index->coinsurance_value;
                $ins_price = $index->coinsurance_value + $index->payable_amount;
            else: $ins = 0; $ins_price = 0; endif;

            $newdata = [
                'sno' => ++$key, 
                'Insurance' => $insurance->name,
                'ClientName' => ucwords($index->user->user_profile->first_name.' '.$index->user->user_profile->last_name), 
                'ClientNumber' => $index->user->mobile, 
                'ClientID' => $index->user->username, 
                'CompanyName' => ucwords($index->user->user_profile->company_name), 
                'PolicyNumber' => $index->user->user_document->policy_number, 
                'ExpiryDate' => date('d-M-Y', strtotime($index->user->user_document->expiry_date)),  
                'InvoiceNumber' => $index->invoice_number, 
                'InvoiceDate' => date('d-M-Y', strtotime($index->invoice_date)), 
                'InvoiceType' => $invoice_types[$index->invoice_type],  
                'ActualCashPrice' => number_format($index->amount, 3), 
                'InsuranceDiscount' => number_format($index->ins_disc_value, 3),
                'InsPrice' => number_format($ins_price, 3), 
                'DeductableCoIns' => number_format($index->payable_amount, 3), 
                'ClaimAmount' => number_format($ins, 3)
            ];
                
            array_push($output, $newdata);
        }

        return ['json_fields' => $columns, 'records' => $output];
        die;
    }

    public function creditReport($sdate = null, $edate = null)
    {
        $f_credit = 0;
        $itypes = [1=> 'Consultation', 2=> 'Treatments', 3=> 'Insured Medicines', 4=> 'Cash Medicines', 5=> 'Course Packages', 6=> 'Course Prescribed', 7=> 'Direct Medicines', 8 => 'Direct Courses'];

        $output = [];
        $columns = ['S.No.' => 'sno',
                    'Invoice Type' => 'InvoiceType',
                    'Customer Name' => 'CustomerName',
                    'Invoice Number' => 'InvoiceNumber',
                    'Credit Amount' => 'CreditAmount',
                    'Invoice Date' => 'InvoiceDate',
                    'Created By' => 'CreatedBy',
                    'Remark' => 'Remark' ];
                    
        $year = date('Y', strtotime($sdate));
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();
        $data = $table->select("$tablename.*", 'user_profiles.first_name', 'users.username', 'admins.name as admin', 'insurances.name as insurancer')->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('insurances', 'insurances.id', '=', "$tablename.insurance_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->where("$tablename.payment_mode", 'credit')
                    ->whereDate("$tablename.invoice_date", '<=', date('Y-m-d', strtotime($edate)))
                    ->whereDate("$tablename.invoice_date", '>=',date('Y-m-d', strtotime($sdate)))
                    ->where(function ($query) {
                        $query->whereNull('cancel')
                              ->orWhere('cancel', '=', '0');
                    })
                    ->orderBy("$tablename.updated_at", 'desc')->get();

         foreach($data as $key => $index)
         {
            $credit = $index->payable_amount;

            $newdata = ['sno' => ++$key,
                    'InvoiceType' => $itypes[$index->invoice_type],
                    'CustomerName' => $index->first_name,
                    'InvoiceNumber' =>  $index->invoice_number,
                    'CreditAmount' =>  number_format($credit, 3),
                    'InvoiceDate' => date('d-m-Y H:i A', strtotime($index->created_at)),
                    'CreatedBy' => $index->admin,
                    'Remark' => $index->remark];

            array_push($output, $newdata);
            $f_credit = $f_credit + $credit;
        }

        $ndata = ['sno' => '-',
                'InvoiceType' => 'Total',
                'CustomerName' => '-',
                'InvoiceNumber' =>  '-',
                'CreditAmount' =>  number_format($f_credit, 3),
                'InvoiceDate' => '-',
                'CreatedBy' => '-',
                'Remark' => '-'];
        
        array_push($output, $ndata);
        return ['json_fields' => $columns, 'records' => $output];
        die;    
    }

    public function completeReport($sdate = null, $edate = null)
    {
        $invoice_types = ['Unknown','Consultation','Treatment','Pharmacy','Pharmacy Cash','Package','Course', 'Direct Medicine', 'Direct Course'];
        
        $output = [];
        $columns = [
                'S.No.' => 'sno', 
                'Location' => 'Location', 
                'Invoice Type' => 'InvoiceType', 
                'Invoice Number' => 'InvoiceNumber', 
                'Client Name' => 'ClientName', 
                'Client Number' => 'ClientNumber', 
                'Client ID' => 'ClientID', 
                'Company Name' => 'CompanyName', 
                'Policy Number' => 'PolicyNumber', 
                'Expiry Date' => 'ExpiryDate',  
                'Total Amount' => 'TotalAmount', 
                'Insurance Discount' => 'InsuranceDiscount', 
                'Insurance Payable' => 'InsurancePayable', 
                'Co-Insurance' => 'CoInsurance', 
                'Cash' => 'Cash', 
                'Payment Mode' => 'PaymentMode', 
                'Txn No' => 'TxnNo', 
                'Cancel Status' => 'CancelStatus'];

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.date('Y', strtotime($sdate)).'s');
        $itablename = $itable->getTable();

        $invoices = $itable->where("$itablename.cancel", 0)
                            ->where("$itablename.location_id", Auth::user()->location_id)
                            ->whereDate("$itablename.invoice_date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$itablename.invoice_date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->with(['user', 'location'])
                            ->orderBy("$itablename.invoice_date", 'asc')
                            ->orderBy("$itablename.user_id", 'asc')
                            ->get();

        foreach($invoices as $key => $index)
        {
            if(in_array($index->invoice_type, [1,2,6])):
                $ins = $index->inc_deduction_value;
                $exp_date = date('d-M-Y', strtotime($index->user->user_document->expiry_date));
            elseif(in_array($index->invoice_type, [3])):
                $ins = $index->coinsurance_value;
                $exp_date = date('d-M-Y', strtotime($index->user->user_document->expiry_date));
            else:
                $ins = 0;
                $exp_date = '-';
            endif;
            if($index->cancel == '0'): $cancel = '-';
            elseif($index->cancel == NULL): $cancel = '-';
            else: $data = json_decode($index->cancel); $cancel = 'Cancelled Reason - '.$data->reason; endif;

            $newdata = [
                'sno' => ++$key, 
                'Location' => $index->location->clinic_name, 
                'InvoiceType' => $invoice_types[$index->invoice_type], 
                'InvoiceNumber' => $index->invoice_number, 
                'ClientName' => ucwords($index->user->user_profile->first_name.' '.$index->user->user_profile->last_name), 
                'ClientNumber' => $index->user->mobile, 
                'ClientID' => $index->user->username, 
                'CompanyName' => ucwords($index->user->user_profile->company_name), 
                'PolicyNumber' => $index->user->user_document->policy_number, 
                'ExpiryDate' => $exp_date,  
                'TotalAmount' => number_format($index->amount, 3), 
                'InsuranceDiscount' => number_format($index->ins_disc_value, 3), 
                'InsurancePayable' => number_format($index->inc_deduction_value, 3), 
                'CoInsurance' => number_format($index->coinsurance_value, 3), 
                'Cash' => number_format($index->payable_amount, 3), 
                'PaymentMode' => $index->payment_mode, 
                'TxnNo' => $index->txn_id, 
                'CancelStatus' => $cancel];
            
            array_push($output, $newdata);
        }

        return ['json_fields' => $columns, 'records' => $output];
        die; 
    }

}
