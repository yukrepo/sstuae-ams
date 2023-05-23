<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Appointments;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoices;
use App\Models\Treatments;
use App\Models\Doctors;
use App\Models\Insurances;
use DB;

class ConsultReportsController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function consultationOverallDayReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Consultation Overall Day Report ', ' - ', date('d-M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Consultation', 'Insurance', '-', '-', '-', 'Cash', '-', 'Visa', '-', 'E-Payment', '-', 'Credit', '', 'Total', '');
        fputcsv($output, $columns);
        $columns = array('-', '-', 'Qty', 'Amount', 'Deductable Cash', 'Deductable Visa','Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Insurance Discount', 'Cash Discount', 'Receiving');
        fputcsv($output, $columns);
        $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

        $year =  date('Y', strtotime($date));
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.$year.'s');
        $itablename = $itable->getTable();

        foreach($data as $key => $index)
        {
            $record = $table->where('appointment_type_id', 1)->where("date", date('Y-m-d', strtotime($date)))->where('treatment_id', $index->id)->whereIn('status_id', [5,8,10])->get()->count();
            $tcost = Treatments::where('id', $index->id)->get()->first();

            $invoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $idinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.inc_deduction_value");

            $cdinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.bliss_discount_value");


            $cinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $vainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $vinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $eainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'e-payment')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $einvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'e-payment')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $crainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'credit')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $crinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'credit')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $cvinvoice = 0;

            $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.inc_deduction_value");

            $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            fputcsv($output, array(++$key, ucwords($index->treatment), $ciinvoice, $iinvoice+$invoice2+$vinvoice2, $invoice2, $vinvoice2, $cinvoice, $invoice, $vinvoice, $vainvoice, $einvoice, $eainvoice, $crinvoice, $crainvoice, $record, number_format($record*$tcost->cost, 3), $idinvoice, $cdinvoice, ($iinvoice+$invoice+$vainvoice+$eainvoice+$crainvoice)));
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="consultation-overall-day-report-'.date('d-M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function consultationOverallMonthReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Consultation Overall Month Report ', ' - ',  date('M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Consultation', 'Insurance', '-', '-', '-', 'Cash', '-', 'Visa', '-', 'E-Payment', '-', 'Credit', '', 'Total', '');
        fputcsv($output, $columns);
        $columns = array('-', '-', 'Qty', 'Amount', 'Deductable Cash', 'Deductable Visa','Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Insurance Discount', 'Cash Discount', 'Receiving');
        fputcsv($output, $columns);
        $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

        $year =  date('Y', strtotime($date));
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.$year.'s');
        $itablename = $itable->getTable();

        foreach($data as $key => $index)
        {
            $record = $table->where('appointment_type_id', 1)
                            ->whereMonth('date', date('m', strtotime($date)))
                            ->whereYear('date', date('Y', strtotime($date)))
                            ->where('treatment_id', $index->id)->whereIn('status_id', [5,8,10])->get()->count();
            $tcost = Treatments::where('id', $index->id)->get()->first();

            $invoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $idinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.inc_deduction_value");

            $cdinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.bliss_discount_value");


            $cinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $vainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $vinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $eainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'e-payment')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $einvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'e-payment')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $crainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'credit')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $crinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'credit')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $cvinvoice = 0;

            $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.inc_deduction_value");

            $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            fputcsv($output, array(++$key, ucwords($index->treatment), $ciinvoice, $iinvoice+$invoice2+$vinvoice2, $invoice2, $vinvoice2, $cinvoice, $invoice, $vinvoice, $vainvoice, $einvoice, $eainvoice, $crinvoice, $crainvoice, $record, number_format($record*$tcost->cost, 3), $idinvoice, $cdinvoice, ($iinvoice+$invoice+$vainvoice+$eainvoice+$crainvoice)));
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="consultation-overall-month-report-'.date('M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function consultationOverallCustomReport($sdate = null, $edate = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Consultation Overall Custom Report ', ' - ', date('d/M/Y', strtotime($sdate)).' - '.date('d/M/Y', strtotime($edate)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Consultation', 'Insurance', '-', '-', '-', 'Cash', '-', 'Visa', '-', 'E-Payment', '-', 'Credit', '', 'Total', '');
        fputcsv($output, $columns);
        $columns = array('-', '-', 'Qty', 'Amount', 'Deductable Cash', 'Deductable Visa','Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Insurance Discount', 'Cash Discount', 'Receiving');
        fputcsv($output, $columns);
        $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

        $year =  date('Y', strtotime($sdate));
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.$year.'s');
        $itablename = $itable->getTable();

        foreach($data as $key => $index)
        {
            $record = $table->where('appointment_type_id', 1)
                            ->whereDate('date','<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate('date','>=',date('Y-m-d', strtotime($sdate)))
                            ->where('treatment_id', $index->id)->whereIn('status_id', [5,8,10])->get()->count();
            $tcost = Treatments::where('id', $index->id)->get()->first();

            $invoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $idinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.inc_deduction_value");

            $cdinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.bliss_discount_value");


            $cinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $vainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $vinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $eainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'e-payment')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $einvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'e-payment')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $crainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'credit')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $crinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'credit')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $cvinvoice = 0;

            $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.inc_deduction_value");

            $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            fputcsv($output, array(++$key, ucwords($index->treatment), $ciinvoice, $iinvoice+$invoice2+$vinvoice2, $invoice2, $vinvoice2, $cinvoice, $invoice, $vinvoice, $vainvoice, $einvoice, $eainvoice, $crinvoice, $crainvoice, $record, number_format($record*$tcost->cost, 3), $idinvoice, $cdinvoice, ($iinvoice+$invoice+$vainvoice+$eainvoice+$crainvoice)));
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="consultation-overall-custom-report-'.date('d/M/Y', strtotime($sdate)).'-'.date('d/M/Y', strtotime($edate)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function consultationPrescriptionDayReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Prescription Day Report ', ' - ', date('d-M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Appointment ID', 'Doctor', 'Customer ID', 'Customer Name', 'Consultation', 'Symptoms', 'General Examination', 'Lab Test', 'Diagnosis', 'Therapies', 'Medicines', 'Appointment Invoice', 'Medicine Invoice', 'Course ID');
        fputcsv($output, $columns);

        $year =  date('Y', strtotime($date));
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        $record = $table->where('appointment_type_id', 1)->where("date", date('Y-m-d', strtotime($date)))->whereIn('status_id', [5,8,10])->orderBy('doctor_id', 'asc')->get();

        foreach($record as $key => $index)
        {
            $medicines = [];
            if($index->medicines):
                $decode = json_decode($index->medicines);
                foreach($decode as $value): $medicines[] ='('.$value->name.' - '.$value->qty.')'; endforeach;
                $medicines = implode(', ', $medicines);
            else: $medicines = '-';  endif;

            $therapies = [];
            if($index->therapies):
                $decode = json_decode($index->therapies);
                foreach($decode as $value): $therapies[] ='('.$value->name.' - '.$value->days.')'; endforeach;
                $therapies = implode(', ', $therapies);
            else: $therapies = '-';  endif;

            $symptoms = [];
            if($index->symptoms):
                $decode = json_decode($index->symptoms);
                foreach($decode as $value): $symptoms[] ='('.$value->name.')'; endforeach;
                $symptoms = implode(', ', $symptoms);
            else: $symptoms = '-';  endif;

            $oe_categories = [];
            if($index->oe_categories):
                $decode = json_decode($index->oe_categories);
                foreach($decode as $value): $oe_categories[] ='('.$value->name.' - '.$value->result.')'; endforeach;
                $ge = implode(', ', $oe_categories);
            else: $ge = '-';  endif;

            $tests = [];
            if($index->tests):
                $decode = json_decode($index->tests);
                foreach($decode as $value): $tests[] ='('.$value->name.')'; endforeach;
                $lt = implode(', ', $tests);
            else: $lt = '-';  endif;


            $diagnosis = [];
            if($index->diagnosis):
                $decode = json_decode($index->diagnosis);
                foreach($decode as $value): $diagnosis[] ='('.$value->name.' - '.$value->code.')'; endforeach;
                $diagnosis = implode(', ', $diagnosis);
            else: $diagnosis = '-';  endif;

            fputcsv($output, array(++$key, $index->appointment_code, $index->doctor->name, $index->user->username, $index->user_profile->first_name, $index->treatment->treatment, $symptoms, $ge, $lt, $diagnosis, $therapies, $medicines, $index->ainvoice, $index->pinvoice, $index->course_id));
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="prescription-day-report-'.date('d-M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function consultationPrescriptionMonthReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Prescription Month Report ', ' - ', date('M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Appointment ID', 'Doctor', 'Customer ID', 'Customer Name', 'Consultation', 'Symptoms', 'General Examination', 'Lab Test', 'Diagnosis', 'Therapies', 'Medicines', 'Appointment Invoice', 'Medicine Invoice', 'Course ID');
        fputcsv($output, $columns);

        $year =  date('Y', strtotime($date));
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        $record = $table->where('appointment_type_id', 1)->whereMonth('date', date('m', strtotime($date)))
        ->whereYear('date', date('Y', strtotime($date)))->whereIn('status_id', [5,8,10])->orderBy('doctor_id', 'asc')->get();

        foreach($record as $key => $index)
        {
            $medicines = [];
            if($index->medicines):
                $decode = json_decode($index->medicines);
                foreach($decode as $value): $medicines[] ='('.$value->name.' - '.$value->qty.')'; endforeach;
                $medicines = implode(', ', $medicines);
            else: $medicines = '-';  endif;

            $therapies = [];
            if($index->therapies):
                $decode = json_decode($index->therapies);
                foreach($decode as $value): $therapies[] ='('.$value->name.' - '.$value->days.')'; endforeach;
                $therapies = implode(', ', $therapies);
            else: $therapies = '-';  endif;

            $symptoms = [];
            if($index->symptoms):
                $decode = json_decode($index->symptoms);
                foreach($decode as $value): $symptoms[] ='('.$value->name.')'; endforeach;
                $symptoms = implode(', ', $symptoms);
            else: $symptoms = '-';  endif;

            $oe_categories = [];
            if($index->oe_categories):
                $decode = json_decode($index->oe_categories);
                foreach($decode as $value): $oe_categories[] ='('.$value->name.' - '.$value->result.')'; endforeach;
                $ge = implode(', ', $oe_categories);
            else: $ge = '-';  endif;

            $tests = [];
            if($index->tests):
                $decode = json_decode($index->tests);
                foreach($decode as $value): $tests[] ='('.$value->name.')'; endforeach;
                $lt = implode(', ', $tests);
            else: $lt = '-';  endif;


            $diagnosis = [];
            if($index->diagnosis):
                $decode = json_decode($index->diagnosis);
                foreach($decode as $value): $diagnosis[] ='('.$value->name.' - '.$value->code.')'; endforeach;
                $diagnosis = implode(', ', $diagnosis);
            else: $diagnosis = '-';  endif;

            fputcsv($output, array(++$key, $index->appointment_code, $index->doctor->name, $index->user->username, $index->user_profile->first_name, $index->treatment->treatment, $symptoms, $ge, $lt, $diagnosis, $therapies, $medicines, $index->ainvoice, $index->pinvoice, $index->course_id));
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="prescription-month-report-'.date('M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function consultationPrescriptionCustomReport($sdate = null, $edate = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Prescription Custom Report ', ' - ', date('d/M/Y', strtotime($sdate)).' - '.date('d/M/Y', strtotime($edate)));
        fputcsv($output, $header);

        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Appointment ID', 'Doctor', 'Customer ID', 'Customer Name', 'Consultation', 'Symptoms', 'General Examination', 'Lab Test', 'Diagnosis', 'Therapies', 'Medicines', 'Appointment Invoice', 'Medicine Invoice', 'Course ID');
        fputcsv($output, $columns);

        $year =  date('Y', strtotime($sdate));
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        $record = $table->where('appointment_type_id', 1)->whereDate('date','<=', date('Y-m-d', strtotime($edate)))
        ->whereDate('date','>=',date('Y-m-d', strtotime($sdate)))->whereIn('status_id', [5,8,10])->orderBy('doctor_id', 'asc')->get();

        foreach($record as $key => $index)
        {
            $medicines = [];
            if($index->medicines):
                $decode = json_decode($index->medicines);
                foreach($decode as $value): $medicines[] ='('.$value->name.' - '.$value->qty.')'; endforeach;
                $medicines = implode(', ', $medicines);
            else: $medicines = '-';  endif;

            $therapies = [];
            if($index->therapies):
                $decode = json_decode($index->therapies);
                foreach($decode as $value): $therapies[] ='('.$value->name.' - '.$value->days.')'; endforeach;
                $therapies = implode(', ', $therapies);
            else: $therapies = '-';  endif;

            $symptoms = [];
            if($index->symptoms):
                $decode = json_decode($index->symptoms);
                foreach($decode as $value): $symptoms[] ='('.$value->name.')'; endforeach;
                $symptoms = implode(', ', $symptoms);
            else: $symptoms = '-';  endif;

            $oe_categories = [];
            if($index->oe_categories):
                $decode = json_decode($index->oe_categories);
                foreach($decode as $value): $oe_categories[] ='('.$value->name.' - '.$value->result.')'; endforeach;
                $ge = implode(', ', $oe_categories);
            else: $ge = '-';  endif;

            $tests = [];
            if($index->tests):
                $decode = json_decode($index->tests);
                foreach($decode as $value): $tests[] ='('.$value->name.')'; endforeach;
                $lt = implode(', ', $tests);
            else: $lt = '-';  endif;


            $diagnosis = [];
            if($index->diagnosis):
                $decode = json_decode($index->diagnosis);
                foreach($decode as $value): $diagnosis[] ='('.$value->name.' - '.$value->code.')'; endforeach;
                $diagnosis = implode(', ', $diagnosis);
            else: $diagnosis = '-';  endif;

            fputcsv($output, array(++$key, $index->appointment_code, $index->doctor->name, $index->user->username, $index->user_profile->first_name, $index->treatment->treatment, $symptoms, $ge, $lt, $diagnosis, $therapies, $medicines, $index->ainvoice, $index->pinvoice, $index->course_id));
        }


        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="Prescription-custom-report-'.date('d/M/Y', strtotime($sdate)).'-'.date('d/M/Y', strtotime($edate)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function consultationDrbasedDayReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Doctors Consultation Day Report', ' - ', date('d-M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $doctors = Doctors::where('designation_type_id', 1)->where('status_id', 2)->get();
        foreach($doctors as $doctor)
        {
            $columns = array('-', $doctor->name, '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '', '-', '-');
            fputcsv($output, $columns);

            $columns = array('S.No.', 'Consultation', 'Insurance', '-', '-', '-', 'Cash', '-', 'Visa', '-', 'E-Payment', '-', 'Credit', '', 'Total', '');
            fputcsv($output, $columns);
            $columns = array('-', '-', 'Qty', 'Amount', 'Deductable Cash', 'Deductable Visa','Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Insurance Discount', 'Cash Discount', 'Receiving');
            fputcsv($output, $columns);
            $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

            $year =  date('Y', strtotime($date));
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();

            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.$year.'s');
            $itablename = $itable->getTable();

            foreach($data as $key => $index)
            {
                $record = $table->where('appointment_type_id', 1)
                                ->where("date", date('Y-m-d', strtotime($date)))
                                ->where('treatment_id', $index->id)
                                ->where('doctor_id', $doctor->id)
                                ->whereIn('status_id', [5,8,10])
                                ->get()->count();
                $tcost = Treatments::where('id', $index->id)->get()->first();

                $invoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $idinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $cdinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.bliss_discount_value");


                $cinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $vainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");
                $vinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $eainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'e-payment')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");
                $einvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'e-payment')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $crainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'credit')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");
                $crinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'credit')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $cvinvoice = 0;

                $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                fputcsv($output, array(++$key, ucwords($index->treatment), $ciinvoice, $iinvoice+$invoice2+$vinvoice2, $invoice2, $vinvoice2, $cinvoice, $invoice, $vinvoice, $vainvoice, $einvoice, $eainvoice, $crinvoice, $crainvoice, $record, number_format($record*$tcost->cost, 3), $idinvoice, $cdinvoice, ($iinvoice+$invoice+$vainvoice+$eainvoice+$crainvoice)));
            }
            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);

            $data = Treatments::orderBy('treatment', 'asc')->where('is_it_dual', 2)->where('status_id', 2)->get();

            foreach($data as $key => $index)
            {
                $trecords = $table->where('appointment_type_id', 2)
                                ->where("date", date('Y-m-d', strtotime($date)))
                                ->where('second_doctor_id', $doctor->id)
                                ->where('treatment_id', $index->id)
                                ->whereIn('status_id', [2,4,5,9])
                                ->get()->count();

                fputcsv($output, array(++$key, ucwords($index->treatment), $trecords));
            }

            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);

            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="doctors-consultation-day-report-'.date('d-M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function consultationDrbasedMonthReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Consultation Overall Month Report ', ' - ',  date('M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $doctors = Doctors::where('designation_type_id', 1)->where('status_id', 2)->get();
        foreach($doctors as $doctor)
        {
            $columns = array('-', $doctor->name, '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '', '-', '-');
            fputcsv($output, $columns);

            $columns = array('S.No.', 'Consultation', 'Insurance', '-', '-', '-', 'Cash', '-', 'Visa', '-', 'E-Payment', '-', 'Credit', '', 'Total', '');
            fputcsv($output, $columns);
            $columns = array('-', '-', 'Qty', 'Amount', 'Deductable Cash', 'Deductable Visa','Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Insurance Discount', 'Cash Discount', 'Receiving');
            fputcsv($output, $columns);
            $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

            $year =  date('Y', strtotime($date));
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();

            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.$year.'s');
            $itablename = $itable->getTable();

            foreach($data as $key => $index)
            {
                $record = $table->where('appointment_type_id', 1)
                                ->whereMonth('date', date('m', strtotime($date)))
                                ->whereYear('date', date('Y', strtotime($date)))
                                ->where("doctor_id", $doctor->id)
                                ->where('treatment_id', $index->id)->whereIn('status_id', [5,8,10])->get()->count();
                $tcost = Treatments::where('id', $index->id)->get()->first();

                $invoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $idinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $cdinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.bliss_discount_value");


                $cinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $vainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");
                $vinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $eainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'e-payment')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");
                $einvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'e-payment')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $crainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'credit')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");
                $crinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'credit')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $cvinvoice = 0;

                $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                fputcsv($output, array(++$key, ucwords($index->treatment), $ciinvoice, $iinvoice+$invoice2+$vinvoice2, $invoice2, $vinvoice2, $cinvoice, $invoice, $vinvoice, $vainvoice, $einvoice, $eainvoice, $crinvoice, $crainvoice, $record, number_format($record*$tcost->cost, 3), $idinvoice, $cdinvoice, ($iinvoice+$invoice+$vainvoice+$eainvoice+$crainvoice)));
            }

            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);

            $data = Treatments::orderBy('treatment', 'asc')->where('is_it_dual', 2)->where('status_id', 2)->get();

            foreach($data as $key => $index)
            {
                $trecords = $table->where('appointment_type_id', 2)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where('second_doctor_id', $doctor->id)
                                ->where('treatment_id', $index->id)
                                ->whereIn('status_id', [2,4,5,9])
                                ->get()->count();

                fputcsv($output, array(++$key, ucwords($index->treatment), $trecords));
            }

            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);

            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="doctors-consultation-month-report-'.date('M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function consultationDrbasedCustomReport($sdate = null, $edate = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Consultation Overall Custom Report ', ' - ', date('d/M/Y', strtotime($sdate)).' - '.date('d/M/Y', strtotime($edate)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $doctors = Doctors::where('designation_type_id', 1)->where('status_id', 2)->get();
        foreach($doctors as $doctor)
        {
            $columns = array('-', $doctor->name, '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '', '-', '-');
            fputcsv($output, $columns);

            $columns = array('S.No.', 'Consultation', 'Insurance', '-', '-', '-', 'Cash', '-', 'Visa', '-', 'E-Payment', '-', 'Credit', '', 'Total', '');
            fputcsv($output, $columns);
            $columns = array('-', '-', 'Qty', 'Amount', 'Deductable Cash', 'Deductable Visa','Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Insurance Discount', 'Cash Discount', 'Receiving');
            fputcsv($output, $columns);
            $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

            $year =  date('Y', strtotime($sdate));
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();

            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.$year.'s');
            $itablename = $itable->getTable();

            foreach($data as $key => $index)
            {
                $record = $table->where('appointment_type_id', 1)
                                ->whereDate('date','<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate('date','>=',date('Y-m-d', strtotime($sdate)))
                                ->where("doctor_id", $doctor->id)
                                ->where('treatment_id', $index->id)->whereIn('status_id', [5,8,10])->get()->count();
                $tcost = Treatments::where('id', $index->id)->get()->first();

                $invoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $idinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $cdinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.bliss_discount_value");


                $cinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $vainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");
                $vinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $eainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'e-payment')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");
                $einvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'e-payment')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $crainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'credit')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");
                $crinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'credit')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $cvinvoice = 0;

                $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                fputcsv($output, array(++$key, ucwords($index->treatment), $ciinvoice, $iinvoice+$invoice2+$vinvoice2, $invoice2, $vinvoice2, $cinvoice, $invoice, $vinvoice, $vainvoice, $einvoice, $eainvoice, $crinvoice, $crainvoice, $record, number_format($record*$tcost->cost, 3), $idinvoice, $cdinvoice, ($iinvoice+$invoice+$vainvoice+$eainvoice+$crainvoice)));
            }

            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);

            $data = Treatments::orderBy('treatment', 'asc')->where('is_it_dual', 2)->where('status_id', 2)->get();

            foreach($data as $key => $index)
            {
                $trecords = $table->where('appointment_type_id', 2)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where('second_doctor_id', $doctor->id)
                                ->where('treatment_id', $index->id)
                                ->whereIn('status_id', [2,4,5,9])
                                ->get()->count();

                fputcsv($output, array(++$key, ucwords($index->treatment), $trecords));
            }

            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);

            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="doctors-consultation-custom-report-'.date('d/M/Y', strtotime($sdate)).'-'.date('d/M/Y', strtotime($edate)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function consultationInsbasedDayReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Insurances Consultation Day Report', ' - ', date('d-M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $doctors = Insurances::where('status_id', 2)->where('id', '!=', 1)->get();
        foreach($doctors as $doctor)
        {
            $columns = array('-', $doctor->name, '-', '-', '-', '-');
            fputcsv($output, $columns);

            $columns = array('S.No.', 'Consultation', 'Insurance', '-', '-', '-');
            fputcsv($output, $columns);
            $columns = array('-', '-', 'Qty', 'Amount', 'Deductable Cash', 'Deductable Visa');
            fputcsv($output, $columns);
            $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

            $year =  date('Y', strtotime($date));
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();

            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.$year.'s');
            $itablename = $itable->getTable();

            foreach($data as $key => $index)
            {

                $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->whereDate("$itablename.updated_at", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->whereDate("$itablename.updated_at", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereDate("$itablename.updated_at", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereDate("$itablename.updated_at", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                fputcsv($output, array(++$key, ucwords($index->treatment), $ciinvoice, $iinvoice+$invoice2+$vinvoice2, $invoice2, $vinvoice2));
            }
            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="insurance-consultation-day-report-'.date('d-M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function consultationInsbasedMonthReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Insurance Consultation Month Report ', ' - ',  date('M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $doctors = Insurances::where('status_id', 2)->get();
        foreach($doctors as $doctor)
        {
            $columns = array('-', $doctor->name, '-', '-', '-', '-');
            fputcsv($output, $columns);

            $columns = array('S.No.', 'Consultation', 'Insurance', '-', '-', '-');
            fputcsv($output, $columns);
            $columns = array('-', '-', 'Qty', 'Amount', 'Deductable Cash', 'Deductable Visa');
            fputcsv($output, $columns);
            $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

            $year =  date('Y', strtotime($date));
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();

            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.$year.'s');
            $itablename = $itable->getTable();

            foreach($data as $key => $index)
            {
                $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereMonth("$itablename.updated_at", date('m', strtotime($date)))
                                ->whereYear("$itablename.updated_at", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereMonth("$itablename.updated_at", date('m', strtotime($date)))
                                ->whereYear("$itablename.updated_at", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereMonth("$itablename.updated_at", date('m', strtotime($date)))
                                ->whereYear("$itablename.updated_at", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereMonth("$itablename.updated_at", date('m', strtotime($date)))
                                ->whereYear("$itablename.updated_at", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                fputcsv($output, array(++$key, ucwords($index->treatment), $ciinvoice, $iinvoice+$invoice2+$vinvoice2, $invoice2, $vinvoice2));
            }

            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="insurance-consultation-month-report-'.date('M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function consultationInsbasedCustomReport($sdate = null, $edate = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Insurance Consultation Custom Report ', ' - ', date('d/M/Y', strtotime($sdate)).' - '.date('d/M/Y', strtotime($edate)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $doctors = Insurances::where('status_id', 2)->get();
        foreach($doctors as $doctor)
        {
            $columns = array('-', $doctor->name, '-', '-', '-', '-');
            fputcsv($output, $columns);

            $columns = array('S.No.', 'Consultation', 'Insurance', '-', '-', '-');
            fputcsv($output, $columns);
            $columns = array('-', '-', 'Qty', 'Amount', 'Deductable Cash', 'Deductable Visa');
            fputcsv($output, $columns);
            $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

            $year =  date('Y', strtotime($sdate));
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();

            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.$year.'s');
            $itablename = $itable->getTable();

            foreach($data as $key => $index)
            {
                $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->whereDate("$itablename.updated_at",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$itablename.updated_at",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->whereDate("$itablename.updated_at",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$itablename.updated_at",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->whereDate("$itablename.updated_at",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$itablename.updated_at",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->whereDate("$itablename.updated_at",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$itablename.updated_at",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                fputcsv($output, array(++$key, ucwords($index->treatment), $ciinvoice, $iinvoice+$invoice2+$vinvoice2, $invoice2, $vinvoice2));
            }

            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="insurances-consultation-custom-report-'.date('d/M/Y', strtotime($sdate)).'-'.date('d/M/Y', strtotime($edate)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function consultationInsDetailDayReport($iid = null, $date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Insurances Consultation Day Report', ' - ', date('d-M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $doctors = Insurances::where('status_id', 2)->get();
        foreach($doctors as $doctor)
        {
            $columns = array('-', $doctor->name, '-', '-', '-', '-');
            fputcsv($output, $columns);

            $columns = array('S.No.', 'Consultation', 'Insurance', '-', '-', '-');
            fputcsv($output, $columns);
            $columns = array('-', '-', 'Qty', 'Amount', 'Deductable Cash', 'Deductable Visa');
            fputcsv($output, $columns);
            $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

            $year =  date('Y', strtotime($date));
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();

            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.$year.'s');
            $itablename = $itable->getTable();

            foreach($data as $key => $index)
            {

                $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->where("$tablename.date", date('Y-m-d', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                fputcsv($output, array(++$key, ucwords($index->treatment), $ciinvoice, $iinvoice+$invoice2+$vinvoice2, $invoice2, $vinvoice2));
            }
            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="insurance-consultation-day-report-'.date('d-M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function consultationInsDetailMonthReport($iid = null, $date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Insurance Consultation Month Report ', ' - ',  date('M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $doctors = Insurances::where('status_id', 2)->get();
        foreach($doctors as $doctor)
        {
            $columns = array('-', $doctor->name, '-', '-', '-', '-');
            fputcsv($output, $columns);

            $columns = array('S.No.', 'Consultation', 'Insurance', '-', '-', '-');
            fputcsv($output, $columns);
            $columns = array('-', '-', 'Qty', 'Amount', 'Deductable Cash', 'Deductable Visa');
            fputcsv($output, $columns);
            $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

            $year =  date('Y', strtotime($date));
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();

            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.$year.'s');
            $itablename = $itable->getTable();

            foreach($data as $key => $index)
            {
                $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereMonth("$tablename.date", date('m', strtotime($date)))
                                ->whereYear("$tablename.date", date('Y', strtotime($date)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                fputcsv($output, array(++$key, ucwords($index->treatment), $ciinvoice, $iinvoice+$invoice2+$vinvoice2, $invoice2, $vinvoice2));
            }

            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="insurance-consultation-month-report-'.date('M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function consultationInsDetailCustomReport($iid = null, $sdate = null, $edate = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Insurance Consultation Custom Report ', ' - ', date('d/M/Y', strtotime($sdate)).' - '.date('d/M/Y', strtotime($edate)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $doctors = Insurances::where('status_id', 2)->get();
        foreach($doctors as $doctor)
        {
            $columns = array('-', $doctor->name, '-', '-', '-', '-');
            fputcsv($output, $columns);

            $columns = array('S.No.', 'Consultation', 'Insurance', '-', '-', '-');
            fputcsv($output, $columns);
            $columns = array('-', '-', 'Qty', 'Amount', 'Deductable Cash', 'Deductable Visa');
            fputcsv($output, $columns);
            $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

            $year =  date('Y', strtotime($sdate));
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();

            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.$year.'s');
            $itablename = $itable->getTable();

            foreach($data as $key => $index)
            {
                $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", $doctor->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                fputcsv($output, array(++$key, ucwords($index->treatment), $ciinvoice, $iinvoice+$invoice2+$vinvoice2, $invoice2, $vinvoice2));
            }

            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="insurances-consultation-custom-report-'.date('d/M/Y', strtotime($sdate)).'-'.date('d/M/Y', strtotime($edate)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function treatmentCompleteDayReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Treatment Complete Day Report', ' - ', date('d-M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ',' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $doctors = Doctors::where('designation_type_id', 2)->where('status_id', 2)->get();
        foreach($doctors as $doctor)
        {
            $columns = array('-', $doctor->name, '-', '-', '-', '-', '-', '-');
            fputcsv($output, $columns);

            $columns = array('S.No.', 'Date', 'Appointment ID', 'Client ID', 'Client Name', 'Time', 'Type', 'Treatment', 'Status');
            fputcsv($output, $columns);

            $year =  date('Y', strtotime($date));
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();

            $data = $table->select("$tablename.*", 'statuses.status as status', 'user_profiles.first_name', 'user_profiles.last_name', 'users.username', 'treatments.treatment as reason')
                        ->with('start_time')->with('end_time')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where('doctor_id', $doctor->id)
                        ->where('date', date('Y-m-d', strtotime($date)))
                        ->whereIn("$tablename.status_id", [2,4,5,9])
                        ->get();

            foreach($data as $key => $index)
            {
                fputcsv($output, array(++$key, date('d-M-Y', strtotime($date)), $index->appointment_code,  $index->username, ucwords($index->first_name.' '.$index->last_name), $index->start_time->time.' - '.$index->end_time->closing, 'Course', $index->reason, $index->status));
            }
            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
            $sep = array(' ', ' ', ' ', ' ');
            fputcsv($output, $sep);
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="treatments-full-day-report-'.date('d-M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function treatmentCompleteMonthReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Treatment Overall Month Report ', ' - ',  date('M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Treatment', 'Insurance', '-', '-', '-', 'Cash', '-', 'Visa', '-', 'E-Payment', '-', 'Credit', '', 'Total', '');
        fputcsv($output, $columns);
        $columns = array('-', '-', 'Qty', 'Amount', 'Deductable Cash', 'Deductable Visa','Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Qty', 'Amount', 'Insurance Discount', 'Cash Discount', 'Receiving');
        fputcsv($output, $columns);
        $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

        $year =  date('Y', strtotime($date));
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.$year.'s');
        $itablename = $itable->getTable();

        foreach($data as $key => $index)
        {
            $record = $table->where('appointment_type_id', 1)
                            ->whereMonth('date', date('m', strtotime($date)))
                            ->whereYear('date', date('Y', strtotime($date)))
                            ->where('treatment_id', $index->id)->whereIn('status_id', [5,8,10])->get()->count();
            $tcost = Treatments::where('id', $index->id)->get()->first();

            $invoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $idinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.inc_deduction_value");

            $cdinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.bliss_discount_value");


            $cinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $vainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $vinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $eainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'e-payment')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $einvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'e-payment')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $crainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'credit')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $crinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'credit')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $cvinvoice = 0;

            $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.inc_deduction_value");

            $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereMonth("$tablename.date", date('m', strtotime($date)))
                            ->whereYear("$tablename.date", date('Y', strtotime($date)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            fputcsv($output, array(++$key, ucwords($index->treatment), $ciinvoice, $iinvoice+$invoice2+$vinvoice2, $invoice2, $vinvoice2, $cinvoice, $invoice, $vinvoice, $vainvoice, $einvoice, $eainvoice, $crinvoice, $crainvoice, $record, number_format($record*$tcost->cost, 3), $idinvoice, $cdinvoice, ($iinvoice+$invoice+$vainvoice+$eainvoice+$crainvoice)));
        }

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="treatment-overall-month-report-'.date('M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function treatmentCompleteCustomReport($sdata = null, $edate = null)
    {

    }

    public function treatmentDrbasedDayReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Therapists Day Report', ' - ', date('d-M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Treatment');
        $columns2 = array('-', '-');
        $doctors = Doctors::where('designation_type_id', 2)->where('status_id', 2)->orderBy('gender', 'asc')->get();
        foreach($doctors as $doctor)
        {
            array_push($columns, $doctor->name, '-');
            array_push($columns2, 'T.C.P.', 'T.C.S.');
        }
        fputcsv($output, $columns);
        fputcsv($output, $columns2);

        $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 2)->where('status_id', 2)->get();

        $year =  date('Y', strtotime($date));
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        foreach($data as $key => $index)
        {
            $row = array(++$key, ucwords($index->treatment));

            foreach($doctors as $doctor)
            {
                $record = $table->where('appointment_type_id', 2)
                                // ->where("date", date('Y-m-d', strtotime($date)))
                                ->where('treatment_id', $index->id)
                                ->where('doctor_id', $doctor->id)
                                ->whereIn('status_id', [2,4,5,9])
                                ->where('date', date('Y-m-d', strtotime($date)))
                                ->get()->count();

                $record2 = $table->where('appointment_type_id', 2)
                                //   ->where("date", date('Y-m-d', strtotime($date)))
                                ->where('treatment_id', $index->id)
                                ->where('second_doctor_id', $doctor->id)
                                ->whereIn('status_id', [2,4,5,9])
                                ->where('date', date('Y-m-d', strtotime($date)))
                                ->get()->count();

                array_push($row, $record, $record2);

            }

            fputcsv($output, $row);
        }

        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $sep = array('T.C.P. - ', ' Total Count As Primary');
        fputcsv($output, $sep);

        $sep = array('T.C.P. - ', ' Total Count As Secondary');
        fputcsv($output, $sep);

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="therapists-day-report-'.date('d-M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function treatmentDrbasedMonthReport($date = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Therapists Month Report ', ' - ',  date('M-Y', strtotime($date)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Treatment');
        $columns2 = array('-', '-');
        $doctors = Doctors::where('designation_type_id', 2)->where('status_id', 2)->orderBy('gender', 'asc')->get();
        foreach($doctors as $doctor)
        {
            array_push($columns, $doctor->name, '-');
            array_push($columns2, 'T.C.P.', 'T.C.S.');
        }
        fputcsv($output, $columns);
        fputcsv($output, $columns2);

        $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 2)->where('status_id', 2)->get();

        $year =  date('Y', strtotime($date));
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        foreach($data as $key => $index)
        {
            $row = array(++$key, ucwords($index->treatment));

            foreach($doctors as $doctor)
            {
                $record = $table->where('appointment_type_id', 2)
                                // ->where("date", date('Y-m-d', strtotime($date)))
                                ->where('treatment_id', $index->id)
                                ->where('doctor_id', $doctor->id)
                                ->whereIn('status_id', [2,4,5,9])
                                ->whereMonth('date', date('m', strtotime($date)))
                                ->get()->count();

                $record2 = $table->where('appointment_type_id', 2)
                                //   ->where("date", date('Y-m-d', strtotime($date)))
                                ->where('treatment_id', $index->id)
                                ->where('second_doctor_id', $doctor->id)
                                ->whereIn('status_id', [2,4,5,9])
                                ->whereMonth('date', date('m', strtotime($date)))
                                ->get()->count();

                array_push($row, $record, $record2);

            }

            fputcsv($output, $row);
        }

        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $sep = array('T.C.P. - ', ' Total Count As Primary');
        fputcsv($output, $sep);

        $sep = array('T.C.P. - ', ' Total Count As Secondary');
        fputcsv($output, $sep);

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="therapists-month-report-'.date('M-Y', strtotime($date)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function treatmentDrbasedCustomReport($sdate = null, $edate = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Therapists Custom Report ', ' - ',  date('d-m-Y', strtotime($sdate)),' - ',  date('d-m-Y', strtotime($edate)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Treatment');
        $columns2 = array('-', '-');
        $doctors = Doctors::where('designation_type_id', 2)->where('status_id', 2)->orderBy('gender', 'asc')->get();
        foreach($doctors as $doctor)
        {
            array_push($columns, $doctor->name, '-');
            array_push($columns2, 'T.C.P.', 'T.C.S.');
        }
        fputcsv($output, $columns);
        fputcsv($output, $columns2);

        $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 2)->where('status_id', 2)->get();

        $year =  date('Y', strtotime($sdate));
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        foreach($data as $key => $index)
        {
            $row = array(++$key, ucwords($index->treatment));

            foreach($doctors as $doctor)
            {
                $record = $table->where('appointment_type_id', 2)
                                ->where('treatment_id', $index->id)
                                ->where('doctor_id', $doctor->id)
                                ->whereIn('status_id', [2,4,5,9])
                                ->whereDate("date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->get()->count();

                $record2 = $table->where('appointment_type_id', 2)
                                ->where('treatment_id', $index->id)
                                ->where('second_doctor_id', $doctor->id)
                                ->whereIn('status_id', [2,4,5,9])
                                ->whereDate("date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->get()->count();

                array_push($row, $record, $record2);

            }

            fputcsv($output, $row);
        }

        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);

        $sep = array('T.C.P. - ', ' Total Count As Primary');
        fputcsv($output, $sep);

        $sep = array('T.C.S. - ', ' Total Count As Secondary');
        fputcsv($output, $sep);

        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="therapists-custom-report-'.date('d-m-Y', strtotime($sdate)).'-'.date('d-m-Y', strtotime($edate)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

}
