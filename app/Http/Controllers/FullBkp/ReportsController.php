<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Appointments;
use App\Models\Insurances;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoices;
use App\Models\Stocks;
use App\Models\SaleMedicines;
use App\Models\Medicines;
use App\Models\Treatments;
use DB;

class ReportsController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function fullStockReport()
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $columns = array('S.No.', 'Medicine', 'Batch No', 'Exp Date', 'Cost', 'Total Stock', 'Available Stock');
        fputcsv($output, $columns);

        $data = Stocks::select('stocks.*', 'medicines.name', 'medicines.cash_price')
                        ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')->get();
         foreach($data as $key => $index)
         {
            fputcsv($output, array(++$key, $index->name, $index->batch_no, date('d-m-Y', strtotime($index->exp_date)), $index->cash_price, $index->received_stock, $index->stock ));
         }
         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="full-stock-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function daywaiseInvoiceReport($date = null)
    {
        $f_total = 0; $f_disc = 0; $f_insurance = 0; $f_cash = 0; $f_visa = 0; $f_credit = 0; $f_epay = 0; $f_cashd = 0;
        $sum_insurance = [0,0,0,0,0,0,0,0,0];
        $sum_cash = [0,0,0,0,0,0,0,0,0];
        $sum_visa = [0,0,0,0,0,0,0,0,0];
        $itypes = [1=> 'Consultation', 2=> 'Treatments', 3=> 'Insured Medicines', 4=> 'Cash Medicines', 5=> 'Course Packages', 6=> 'Course Prescribed', 7=> 'Direct Medicines', 8 => 'Direct Courses'];
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $columns = array('S.No.', 'Invoice Type', 'Payment Mode', 'Customer ID', 'Customer Name', 'Invoice Number', 'Total', 'Ins Discount', 'Insurance', 'Paid As', 'Cash Discount', 'Cash', 'VISA', 'Txn No', 'Credit', 'EPay', 'Invoice Date', 'Created By', 'Remark', 'Cancel Status');

        fputcsv($output, $columns);
        $year = date('Y', strtotime($date));
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();
        $data = $table->select("$tablename.*", 'user_profiles.first_name', 'users.username', 'admins.name as admin', 'insurances.name as insurancer')
                    ->join('users', 'users.id', '=', "$tablename.user_id")
                    ->join('insurances', 'insurances.id', '=', "$tablename.insurance_id")
                    ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->where("$tablename.location_id", Auth::user()->location_id)
                    ->whereDate("$tablename.invoice_date", $date)
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
						
            fputcsv($output, array(++$key, $itypes[$index->invoice_type], $pmode, $index->username, $index->first_name, $index->invoice_number, number_format($index->amount, 3), number_format($index->ins_disc_value, 3), number_format($ins, 3),  $index->payment_mode, number_format($index->bliss_discount_value, 3), number_format($cash, 3), number_format($visa, 3), $index->txn_id, number_format($credit, 3), number_format($epay, 3), date('d-m-Y H:i A', strtotime($index->created_at)), $index->admin, $remark, $cancel ));

            $f_total = $f_total + $index->amount;
            $f_disc = $f_disc + $index->ins_disc_value;
            $f_insurance = $f_insurance + $ins;
            $f_cashd = $f_cashd + $index->bliss_discount_value;
			$f_cash = $f_cash + $cash;
            $f_visa = $f_visa + $visa;
            $f_credit = $f_credit + $credit;
            $f_epay = $f_epay + $epay;
        }

        $columns = array('');

        fputcsv($output, $columns);

        $columns = array('', 'Total', '-', '-', '-', '-', number_format($f_total, 3), number_format($f_disc, 3), number_format($f_insurance, 3), '-', number_format($f_cashd, 3), number_format($f_cash, 3), number_format($f_visa, 3), '-', number_format($f_credit, 3), number_format($f_epay, 3), '-', '-', '-');
        fputcsv($output, $columns);

        $columns = array('');
        fputcsv($output, $columns);
        $columns = array('');
        fputcsv($output, $columns);
        $columns = array('');
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'INVOICE TYPE', 'Sum Of Insurance', 'Sum Of Cash', 'Sum Of VISA');
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Consultation', $sum_insurance[1], $sum_cash[1], $sum_visa[1]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Treatments', $sum_insurance[2], $sum_cash[2], $sum_visa[2]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Insured Medicines', $sum_insurance[3], $sum_cash[3], $sum_visa[3]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Cash Medicines', $sum_insurance[4], $sum_cash[4], $sum_visa[4]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Course Package', $sum_insurance[5], $sum_cash[5], $sum_visa[5]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Course Prescribed', $sum_insurance[6], $sum_cash[6], $sum_visa[6]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Direct Medicines', $sum_insurance[7], $sum_cash[7], $sum_visa[7]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Direct Course', $sum_insurance[8], $sum_cash[8], $sum_visa[8]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Grand Total', array_sum($sum_insurance), array_sum($sum_cash), array_sum($sum_visa));
        fputcsv($output, $columns);


        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="daywise-invoice-report.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function monthwaiseInvoiceReport($date = null)
    {
        $f_total = 0; $f_disc = 0; $f_insurance = 0; $f_cash = 0; $f_visa = 0; $f_credit = 0; $f_epay = 0; $f_cashd = 0;
        $sum_insurance = [0,0,0,0,0,0,0,0,0];
        $sum_cash = [0,0,0,0,0,0,0,0,0];
        $sum_visa = [0,0,0,0,0,0,0,0,0];
        $sum_epay = [0,0,0,0,0,0,0,0,0];
        $cancelled_insurance = 0;
        $cancelled_cash = 0;
        $cancelled_visa = 0;
        $cancelled_epay = 0;

        $itypes = [1=> 'Consultation', 2=> 'Treatments', 3=> 'Insured Medicines', 4=> 'Cash Medicines', 5=> 'Course Packages', 6=> 'Course Prescribed', 7=> 'Direct Medicines', 8 => 'Direct Courses'];
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');

        $columns = array('S.No.', 'Invoice Type', 'Payment Mode', 'Customer ID', 'Customer Name', 'Invoice Number', 'Total', 'Ins Discount', 'Insurance', 'Paid As', 'Cash Discount', 'Cash', 'VISA', 'Txn No', 'Credit', 'EPay', 'Invoice Date', 'Created By', 'Remark', 'Cancel Status');
        fputcsv($output, $columns);
        $year = date('Y', strtotime($date));
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();
        $data = $table->select("$tablename.*", 'user_profiles.first_name', 'users.username', 'admins.name as admin', 'insurances.name as insurancer')
                ->join('users', 'users.id', '=', "$tablename.user_id")
                ->join('insurances', 'insurances.id', '=', "$tablename.insurance_id")
                ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                ->where("$tablename.location_id", Auth::user()->location_id)
                ->whereMonth("$tablename.invoice_date", date(('m'), strtotime($date)))
                ->whereYear("$tablename.invoice_date", date(('Y'), strtotime($date)))
                ->orderBy("$tablename.invoice_date", 'desc')->get();

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
            elseif($index->cancel == ''): $cancel = '-';
            elseif($index->cancel == null): $cancel = '-';
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
            if($index->payment_mode == 'epay'):
                $sum_epay[$index->invoice_type] = $sum_epay[$index->invoice_type] + $index->payable_amount;
            endif;

            if(($index->insurance_id >= 2) && (strlen($index->cancel) >= 2)):
                $cancelled_insurance = $cancelled_insurance + $index->payable_amount;
            elseif(($index->payment_mode == 'epay') && (strlen($index->cancel) >= 2)):
                $cancelled_epay = $cancelled_epay + $index->payable_amount;
            elseif(($index->payment_mode == 'visa') && (strlen($index->cancel) >= 2)):
                $cancelled_visa = $cancelled_visa + $index->payable_amount;
            elseif(($index->payment_mode == 'cash') && (strlen($index->cancel) >= 2)):
                $cancelled_cash = $cancelled_cash + $index->payable_amount;
            endif;

            if($index->payment_mode == 'cash'): $cash = $index->payable_amount; $visa = 0; $credit = 0; $remark = ''; $epay = 0;
            elseif($index->payment_mode == 'visa'): $visa = $index->payable_amount; $cash = 0; $credit = 0; $remark = ''; $epay = 0;
            elseif($index->payment_mode == 'credit'): $credit = $index->payable_amount; $visa = 0; $cash = 0; $remark = ''; $epay = 0;
            elseif($index->payment_mode == 'epay'): $epay = $index->payable_amount; $visa = 0; $credit = 0; $remark = ''; $cash = 0;
            elseif($index->payment_mode == 'cash+visa'): $cash = $index->payable_amount; $visa = 0; $credit = 0; $remark = $index->payment_history; $epay = 0;
            else: $cash = 0; $credit = 0; $remark = ''; $epay = 0;  $visa = 0;
            endif;
                  
            fputcsv($output, array(++$key, $itypes[$index->invoice_type], $pmode, $index->username, $index->first_name, $index->invoice_number, number_format($index->amount, 3), number_format($index->ins_disc_value, 3), number_format($ins, 3),  $index->payment_mode, number_format($index->bliss_discount_value, 3), number_format($cash, 3), number_format($visa, 3), $index->txn_id, number_format($credit, 3), number_format($epay, 3), date('d-m-Y H:i A', strtotime($index->created_at)), $index->admin, $remark, $cancel ));

            $f_total = $f_total + $index->amount;
            $f_disc = $f_disc + $index->ins_disc_value;
            $f_insurance = $f_insurance + $ins;
            $f_cashd = $f_cashd + $index->bliss_discount_value;
            $f_cash = $f_cash + $cash;
            $f_visa = $f_visa + $visa;
            $f_credit = $f_credit + $credit;
            $f_epay = $f_epay + $epay;

        }

        $columns = array('');

        fputcsv($output, $columns);

        $columns = array('', 'Total', '-', '-', '-', '-', number_format($f_total, 3), number_format($f_disc, 3), number_format($f_insurance, 3), '-', number_format($f_cashd, 3), number_format($f_cash, 3), number_format($f_visa, 3), '-', number_format($f_credit, 3), number_format($f_epay, 3), '-', '-', '-');
        fputcsv($output, $columns);

        $columns = array('');
        fputcsv($output, $columns);
        $columns = array('');
        fputcsv($output, $columns);
        $columns = array('');
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'INVOICE TYPE', 'Sum Of Insurance', 'Sum Of Cash', 'Sum Of VISA', 'E-Payment');
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Consultation', $sum_insurance[1], $sum_cash[1], $sum_visa[1], $sum_epay[1]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Treatments', $sum_insurance[2], $sum_cash[2], $sum_visa[2], $sum_epay[2]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Insured Medicines', $sum_insurance[3], $sum_cash[3], $sum_visa[3], $sum_epay[3]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Cash Medicines', $sum_insurance[4], $sum_cash[4], $sum_visa[4], $sum_epay[4]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Course Package', $sum_insurance[5], $sum_cash[5], $sum_visa[5], $sum_epay[5]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Course Prescribed', $sum_insurance[6], $sum_cash[6], $sum_visa[6], $sum_epay[6]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Direct Medicines', $sum_insurance[7], $sum_cash[7], $sum_visa[7], $sum_epay[7]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Direct Course', $sum_insurance[8], $sum_cash[8], $sum_visa[8], $sum_epay[8]);
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Total', array_sum($sum_insurance), array_sum($sum_cash), array_sum($sum_visa), array_sum($sum_epay));
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Cancelled Total', number_format($cancelled_insurance, 3), number_format($cancelled_cash, 3), number_format($cancelled_visa, 3), number_format($cancelled_epay, 3));
        fputcsv($output, $columns);

        $columns = array('', '', '', '', '', 'Grand Total', number_format(array_sum($sum_insurance)-$cancelled_insurance, 3), number_format(array_sum($sum_cash)-$cancelled_cash, 3), number_format(array_sum($sum_visa)-$cancelled_visa, 3), number_format(array_sum($sum_epay)-$cancelled_epay, 3));
        fputcsv($output, $columns);

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="monthly-invoice-report.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function medicineStockDayReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Medicine Day Report ', ' - ', date('d-M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Medicine', 'Opening Stock', 'Purchasing', 'Sales', 'Current Stock');
        fputcsv($output, $columns);
        $data = Medicines::leftJoin(DB::raw('(SELECT medicine_id, SUM(stock) as stock_sum FROM stocks GROUP BY medicine_id) AS stocks'), function ($join) { $join->on('stocks.medicine_id', '=', 'medicines.id'); })->orderBy('name', 'asc')->get();

        foreach($data as $key => $index)
        {
            $out = SaleMedicines::join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                                ->whereDate('sale_medicines.created_at', date('Y-m-d', strtotime($date)))
                                ->where('sale_medicines.medicine_id', $index->id)
                                ->where('sales.status', 1)->sum('sale_medicines.qty');
            fputcsv($output, array(++$key, ucwords($index->name), $out, number_format($index->stock_sum) ));
        }
        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="medicine-day-report-'.date('d-M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function medicineStockMonthReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Medicine Month Report ', ' - ', date('M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Medicine', 'Opening Stock', 'Purchasing', 'Sales', 'Current Stock');
        fputcsv($output, $columns);
        $data = Medicines::leftJoin(DB::raw('(SELECT medicine_id, SUM(stock) as stock_sum FROM stocks GROUP BY medicine_id) AS stocks'), function ($join) { $join->on('stocks.medicine_id', '=', 'medicines.id'); })->orderBy('name', 'asc')->get();

        foreach($data as $key => $index)
        {
            $out = SaleMedicines::join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                                ->whereMonth('sale_medicines.created_at', date('m', strtotime($date)))
                                ->whereYear('sale_medicines.created_at', date('Y', strtotime($date)))
                                ->where('sale_medicines.medicine_id', $index->id)
                                ->where('sales.status', 1)->sum('sale_medicines.qty');
            $in = Stocks::join('purchases', 'purchases.id', '=', 'stocks.purchase_id')
                                ->whereMonth('purchases.created_at', date('m', strtotime($date)))
                                ->whereYear('purchases.created_at', date('Y', strtotime($date)))
                                ->where('stocks.medicine_id', $index->id)
                                ->where('purchases.location_id', Auth::user()->location_id)
                                ->sum('stocks.received_stock');
            $op = $index->stock_sum - $in + $out;
            fputcsv($output, array(++$key, ucwords($index->name), $op, $in, $out, number_format($index->stock_sum) ));
        }
        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="medicine-month-report-'.date('M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function medicineStockCustomReport($sdate = null, $edate = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Medicine Custom Stock Report ', ' - ', date('d/M/Y', strtotime($sdate)).' - '.date('d/M/Y', strtotime($edate)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Medicine', 'Opening Stock', 'Purchasing', 'Sales', 'Current Stock');
        fputcsv($output, $columns);
        $data = Medicines::leftJoin(DB::raw('(SELECT medicine_id, SUM(stock) as stock_sum FROM stocks GROUP BY medicine_id) AS stocks'), function ($join) { $join->on('stocks.medicine_id', '=', 'medicines.id'); })->orderBy('name', 'asc')->get();

        foreach($data as $key => $index)
        {
            $out = SaleMedicines::join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                                ->whereDate('sale_medicines.created_at','<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate('sale_medicines.created_at','>=',date('Y-m-d', strtotime($sdate)))
                                ->where('sale_medicines.medicine_id', $index->id)
                                ->where('sales.status', 1)->sum('sale_medicines.qty');
            $in = Stocks::join('purchases', 'purchases.id', '=', 'stocks.purchase_id')
                                ->whereDate('purchases.created_at', '<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate('purchases.created_at', '>=',date('Y-m-d', strtotime($sdate)))
                                ->where('stocks.medicine_id', $index->id)
                                ->where('purchases.location_id', Auth::user()->location_id)
                                ->sum('stocks.received_stock');
            $op = $index->stock_sum - $in + $out;
            fputcsv($output, array(++$key, ucwords($index->name), $op, $in, $out, number_format($index->stock_sum) ));
        }
        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="medicine-custom-stock-report-'.date('d/M/Y', strtotime($sdate)).'-'.date('d/M/Y', strtotime($edate)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function medicineSalesDayReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Medicine Sales Day Report ', ' - ', date('d-M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        //$columns = array('S.No.', 'Medicine', 'Insurance', '-', 'Cash', '-', 'Visa', '-', 'E-Payment', '-', 'Credit', '');
        //fputcsv($output, $columns);
        $columns = array('S.No.', 'Medicine', 'Qty', 'Rate', 'Total');
        fputcsv($output, $columns);
        $data = Medicines::orderBy('name', 'asc')->get();
        $total = 0; $qty = 0; $counter = 0;
        foreach($data as $key => $index)
        {
            $cout = SaleMedicines::join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                        ->whereDate('sale_medicines.created_at', date('Y-m-d', strtotime($date)))
                        ->where('sale_medicines.medicine_id', $index->id)
                        ->where('sales.status', 1)
                        ->sum('sale_medicines.qty');
            if($cout >= 1) {
                fputcsv($output, array(++$counter, ucwords($index->name), $cout, $index->cash_price, $cout*$index->cash_price));
                $total = $total+ ($cout*$index->cash_price);
                $qty = $qty + $cout;
            }
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="medicine-sales-day-report-'.date('d-M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function medicineSalesMonthReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Medicine Sales Month Report ', ' - ', date('M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        //$columns = array('S.No.', 'Medicine', 'Insurance', '-', 'Cash', '-', 'Visa', '-', 'E-Payment', '-', 'Credit', '');
        //fputcsv($output, $columns);
       // $columns = array('-', '-', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount');
        $columns = array('S.No.', 'Medicine', 'Qty', 'Rate', 'Total');
        fputcsv($output, $columns);
        $data = Medicines::leftJoin(DB::raw('(SELECT medicine_id, SUM(stock) as stock_sum FROM stocks GROUP BY medicine_id) AS stocks'), function ($join) { $join->on('stocks.medicine_id', '=', 'medicines.id'); })->orderBy('name', 'asc')->get();
        $total = 0; $qty = 0; $counter = 0;
        foreach($data as $key => $index)
        {
            $cout = SaleMedicines::join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                                ->whereMonth('sale_medicines.created_at', date('m', strtotime($date)))
                                ->whereYear('sale_medicines.created_at', date('Y', strtotime($date)))
                                ->where('sale_medicines.medicine_id', $index->id)
                                ->where('sales.status', 1)->sum('sale_medicines.qty');
            if($cout >= 1) {
                fputcsv($output, array(++$counter, ucwords($index->name), $cout, $index->cash_price, $cout*$index->cash_price));
                $total = $total+ ($cout*$index->cash_price);
                $qty = $qty + $cout;
            }
        }
        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="medicine-sales-month-report-'.date('M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function medicineSalesCustomReport($sdate = null, $edate = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Medicine Sales Custom Report ', ' - ', date('d/M/Y', strtotime($sdate)).' - '.date('d/M/Y', strtotime($edate)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        //$columns = array('S.No.', 'Medicine', 'Insurance', '-', 'Cash', '-', 'Visa', '-', 'E-Payment', '-', 'Credit', '');
        //fputcsv($output, $columns);
        // $columns = array('-', '-', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount');
        $columns = array('S.No.', 'Medicine', 'Qty', 'Rate', 'Total');
        fputcsv($output, $columns);
        $data = Medicines::leftJoin(DB::raw('(SELECT medicine_id, SUM(stock) as stock_sum FROM stocks GROUP BY medicine_id) AS stocks'), function ($join) { $join->on('stocks.medicine_id', '=', 'medicines.id'); })->orderBy('name', 'asc')->get();
        $total = 0; $qty = 0; $counter = 0;
        foreach($data as $key => $index)
        {
            $cout = SaleMedicines::join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                            ->whereDate('sale_medicines.created_at','<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate('sale_medicines.created_at','>=',date('Y-m-d', strtotime($sdate)))
                            ->where('sale_medicines.medicine_id', $index->id)
                            ->where('sales.status', 1)->sum('sale_medicines.qty');
            if($cout >= 1) {
                fputcsv($output, array(++$counter, ucwords($index->name), $cout, $index->cash_price, $cout*$index->cash_price));
                $total = $total+ ($cout*$index->cash_price);
                $qty = $qty + $cout;
            }
        }
        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="medicine-sales-custom-report-'.date('d/M/Y', strtotime($sdate)).' - '.date('d/M/Y', strtotime($edate)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function treatmentOverallDayReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Treatment Overall Day Report ', ' - ', date('d-M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Tretament', 'Insurance', '-', 'Cash', '-', 'Visa', '-', 'E-Payment', '-', 'Credit', '');
        fputcsv($output, $columns);
        $columns = array('-', '-', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount');
        fputcsv($output, $columns);
        $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 2)->where('status_id', 2)->get();

        $year =  date('Y', strtotime($date));
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();

        foreach($data as $key => $index)
        {

            fputcsv($output, array(++$key, ucwords($index->treatment), 0, 0, 0, 0, 0,0,0,0, 0,0 ));
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="treatment-overall-day-report-'.date('d-M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function cumulativeReport($iids = null, $sdate = null, $edate = null)
    {
        $invoice_types = ['Unknown','Consultation','Treatment','Pharmacy','Pharmacy Cash','Package','Course', 'Direct Medicine', 'Direct Course'];
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Insurancewsie Invoice cumulative Report ', ' - ', 'From', date('d/M/Y', strtotime($sdate)), ' To ', date('d/M/Y', strtotime($edate)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $insurances = Insurances::where('status_id', 2)->whereIn('id', explode(',', $iids))->get();

        foreach($insurances as $insurance)
        {
            $columns = array('-', $insurance->name, '-', '-', '-', '-');
            fputcsv($output, $columns);

            $columns = array('S.No.', 'Client Name', 'Client Number', 'Client ID', 'Company Name', 'Policy Number', 'Expiry Date',  'Invoice Number', 'Invoice Date', 'Invoice Type',  'Actual Cash Price', 'Insurance Discount', 'Ins Price', 'Deductable/Co Ins', 'Claim Amount');
            fputcsv($output, $columns);

            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.date('Y', strtotime($sdate)).'s');
            $itablename = $itable->getTable();

            $invoices = $itable->whereDate("$itablename.invoice_date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$itablename.invoice_date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$itablename.insurance_id", $insurance->id)
                                ->where("$itablename.cancel", 0)
                                ->where("$tablename.location_id", Auth::user()->location_id)
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
                fputcsv($output, array(++$key, ucwords($index->user->user_profile->first_name.' '.$index->user->user_profile->last_name), $index->user->mobile, $index->user->username, ucwords($index->user->user_profile->company_name), $index->user->user_document->policy_number, date('d-M-Y', strtotime($index->user->user_document->expiry_date)), $index->invoice_number, date('d-M-Y', strtotime($index->invoice_date)), $invoice_types[$index->invoice_type],  number_format($index->amount, 3), number_format($index->ins_disc_value, 3),  number_format($ins_price, 3),  number_format($index->payable_amount, 3), number_format($ins, 3) ));
            }

            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="insurances-cumulative-invoice-report-'.date('d/M/Y', strtotime($sdate)).'-'.date('d/M/Y', strtotime($edate)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function creditReport()
    {
        $sdate = '2019-10-01'; $edate = '2019-12-31';
        $f_credit = 0;
        $itypes = [1=> 'Consultation', 2=> 'Treatments', 3=> 'Insured Medicines', 4=> 'Cash Medicines', 5=> 'Course Packages', 6=> 'Course Prescribed', 7=> 'Direct Medicines', 8 => 'Direct Courses'];
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $columns = array('S.No.', 'Invoice Type', 'Customer Name', 'Invoice Number', 'Credit Amount', 'Invoice Date', 'Created By', 'Remark');

        fputcsv($output, $columns);
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

            fputcsv($output, array(++$key, $itypes[$index->invoice_type], $index->first_name, $index->invoice_number, number_format($credit, 3), date('d-m-Y H:i A', strtotime($index->created_at)), $index->admin, $index->remark ));

            $f_credit = $f_credit + $credit;
        }

        $columns = array('');

        fputcsv($output, $columns);

        $columns = array('', 'Total', '-', '-', '-', number_format($f_credit, 3), '-', '-', '-');

        fputcsv($output, $columns);
        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="credit-report.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function completeInvoiceReport($date = null)
    {
        $invoice_types = ['Unknown','Consultation','Treatment','Pharmacy','Pharmacy Cash','Package','Course', 'Direct Medicine', 'Direct Course'];
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Invoice Complete Report ', ' - ', date('d/M/Y', strtotime($date)), '');
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $columns = array('S.No.', 'Location', 'Invoice Type', 'Invoice Number', 'Client Name', 'Client Number', 'Client ID', 'Company Name', 'Policy Number', 'Expiry Date',  'Total Amount', 'Insurance Discount', 'Insurance Payable', 'Co-Insurance', 'Cash', 'Payment Mode', 'Txn No', 'Cancel Status');
        fputcsv($output, $columns);

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.date('Y', strtotime($date)).'s');
        $itablename = $itable->getTable();

        $invoices = $itable->whereDate("$itablename.invoice_date", date('Y-m-d', strtotime($date)))
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
                $exp_date = date('d-M-Y', strtotime($index->user->user_document->expiry_date));
            elseif(in_array($index->invoice_type, [3])):
                $ins = $index->coinsurance_value;
                $exp_date = date('d-M-Y', strtotime($index->user->user_document->expiry_date));
            else:
                $ins = 0;
                $exp_date = '-';
            endif;
            if($index->cancel == '0'): $cancel = '-';
            else: $data = json_decode($index->cancel); $cancel = 'Cancelled Reason - '.$data->reason; endif;
            fputcsv($output, array(++$key, $index->location->clinic_name, $invoice_types[$index->invoice_type], $index->invoice_number, ucwords($index->user->user_profile->first_name.' '.$index->user->user_profile->last_name), $index->user->mobile, $index->user->username, ucwords($index->user->user_profile->company_name), $index->user->user_document->policy_number, $exp_date, number_format($index->amount, 3), number_format($index->ins_disc_value, 3), number_format($index->inc_deduction_value, 3), number_format($index->coinsurance_value, 3), number_format($index->payable_amount, 3), $index->payment_mode, $index->txn_id, $cancel ));
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="invoice-complete-report.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;

    }

    public function monthlyCompleteInvoiceReport($date = null)
    {
        $invoice_types = ['Unknown','Consultation','Treatment','Pharmacy','Pharmacy Cash','Package','Course', 'Direct Medicine', 'Direct Course'];
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Invoice Complete Report ', ' - ', date('M-Y', strtotime($date)), '');
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $columns = array('S.No.', 'Location', 'Invoice Type', 'Invoice Date', 'Invoice Number', 'Client Name', 'Client Number', 'Client ID', 'Company Name', 'Policy Number', 'Expiry Date',  'Payable Amount', 'Insurance Payable', 'Insurance Discount', 'Cash Discount');
        fputcsv($output, $columns);

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.date('Y', strtotime($date)).'s');
        $itablename = $itable->getTable();

        $invoices = $itable->whereMonth("$itablename.invoice_date", date('m', strtotime($date)))
                            ->where("$itablename.cancel", 0)
                            ->where("$itablename.location_id", Auth::user()->location_id)
                            ->with('user')
                            ->orderBy("$itablename.invoice_date", 'asc')
                            ->orderBy("$itablename.user_id", 'asc')
                            ->get();

        foreach($invoices as $key => $index)
        {
            if(in_array($index->invoice_type, [1,2,6])): $ins = $index->inc_deduction_value;
            elseif(in_array($index->invoice_type, [3])): $ins = $index->coinsurance_value;
            else: $ins = 0; endif;
            fputcsv($output, array(++$key, $index->location->clinic_name, $invoice_types[$index->invoice_type], date('d-M-Y', strtotime($index->invoice_date)), $index->invoice_number, ucwords($index->user->user_profile->first_name.' '.$index->user->user_profile->last_name), $index->user->mobile, $index->user->username, ucwords($index->user->user_profile->company_name), $index->user->user_document->policy_number, ($index->user->user_document->expiry_date)?date('d-M-Y', strtotime($index->user->user_document->expiry_date)):'---',  number_format($index->amount, 3), number_format($ins, 3), number_format($index->bliss_discount_value, 3), number_format($index->ins_disc_value, 3)));
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="invoice-complete-month-report.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;

    }

    public function detailedStockReport($sdate = null, $edate = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Detailed Stock Report ');
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Medicine', 'Batch No', 'Exp Date', 'FOC', 'Received Stock', 'Available');
        fputcsv($output, $columns);
        $data = Stocks::with('medicine')->where('stocks.location_id', Auth::user()->location_id)
                            ->orderBy('medicine_id', 'asc')
                            ->get();
        foreach($data as $key => $index)
        {
            fputcsv($output, array(++$key, ucwords($index->medicine->name), $index->batch_no, date('d-M-Y', strtotime($index->exp_date)), number_format($index->foc), number_format($index->received_stock), number_format($index->stock) ));
        }
        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="medicine-custom-stock-report'.'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }
}
