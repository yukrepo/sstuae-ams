<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Appointments;
use App\Models\Insurances;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoices;
use App\Models\Stocks;
use App\Models\Doctors;
use App\Models\SaleMedicines;
use App\Models\Medicines;
use App\Models\Treatments;
use DB;

class NewReportsController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function discountReport($sdate = null, $edate = null)
    {
        $f_total = 0; $f_disc = 0; $f_insurance = 0; $f_cash = 0; $f_visa = 0; $f_credit = 0; $f_epay = 0; $f_cashd = 0;

        $itypes = [1=> 'Consultation', 2=> 'Treatments', 3=> 'Insured Medicines', 4=> 'Cash Medicines', 5=> 'Course Packages', 6=> 'Course Prescribed', 7=> 'Direct Medicines', 8 => 'Direct Courses'];
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');

        $columns = array('S.No.', 'Location', 'Invoice Type', 'Customer ID', 'Customer Name', 'Invoice Number', 'Total Bill', 'Paid Amount', 'Discount', 'Discount Value', 'Invoice Date', 'Created By', 'Remark');
        fputcsv($output, $columns);
        $year = date('Y', strtotime($sdate));
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();
        $data = $table->select("$tablename.*", 'user_profiles.first_name', 'users.username', 'admins.name as admin', 'locations.clinic_name')
                ->join('users', 'users.id', '=', "$tablename.user_id")
                ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                ->join('locations', 'locations.id', '=', "$tablename.location_id")
                ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                ->whereBetween("$tablename.invoice_date",[date('Y-m-d', strtotime($sdate)),date('Y-m-d', strtotime($edate))])
                ->whereIn("$tablename.invoice_type", [1,2])
                ->where("$tablename.cancel", '0')
                ->orderBy("$tablename.invoice_date", 'desc')->get();

        foreach($data as $key => $index)
        {
                  
            fputcsv($output, array(++$key, $index->clinic_name, $itypes[$index->invoice_type], $index->username, $index->first_name, $index->invoice_number, number_format($index->amount, 3),  number_format($index->payable_amount, 3), $index->bliss_discount, number_format($index->bliss_discount_value, 3),  date('d-m-Y H:i A', strtotime($index->invoice_date)), $index->admin, $index->remark ));

        }
        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="discount-report.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function drbasedMedicineReport($sdate = null, $edate = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Doctor Based Medicine Sales Custom Report ', ' - ', date('d/M/Y', strtotime($sdate)).' - '.date('d/M/Y', strtotime($edate)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        $doctors = Doctors::where('location_id', Auth::user()->location_id)->where('designation_type_id', 2)->where('status_id', 2)->get();
        foreach ($doctors as $key => $doctor) 
        {
            $columns = array('-', $doctor->name.' Report', '-', '-', '-', '-');
            fputcsv($output, $columns);
            $columns = array('S.No.', 'Medicine', 'Qty', 'Price', 'Cash', 'Insurance', 'Total');
            fputcsv($output, $columns);
            $data = Medicines::orderBy('name', 'asc')->get();
            
            $itable =  new Appointments;
            $table = $itable->setTable('appointment'.date('Y', strtotime($sdate)).'s');
            $tablename = $itable->getTable();

            $invoices = $table->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                    ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                    ->where("$tablename.status_id", 5)
                                    ->where("$tablename.location_id", Auth::user()->location_id)
                                    ->where("$tablename.doctor_id", $doctor->id)
                                    ->where("$tablename.is_dummy", 0)
                                    ->orderBy("$tablename.date", 'asc')
                                    ->get()->pluck('pinvoice')->toArray();
            
            $invoices = array_filter($invoices);

            $invoices = implode(',', $invoices);
            $invoices = explode(',', $invoices);
            $cashinvoices = array_filter($invoices,
                function ($element) {
                  return strpos($element, 'CM') === false;
                }
            );
            $cashinvoices = array_values($cashinvoices);
            $insinvoices = array_filter($invoices,
                function ($element) {
                  return strpos($element, 'IM') === false;
                }
            );
            $insinvoices = array_values($insinvoices);
            
            $total = 0; $qty = 0; $counter = 0; $tc = 0; $ti = 0;
            
            foreach($data as $key => $index)
            {
                $cq = SaleMedicines::join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                                    ->where('sale_medicines.medicine_id', $index->id)
                                    ->where('sales.status', 1)
                                    ->whereIn('sales.invoice_number', $cashinvoices)->sum('sale_medicines.qty');
                $iq = SaleMedicines::join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                                    ->where('sale_medicines.medicine_id', $index->id)
                                    ->where('sales.status', 1)
                                    ->whereIn('sales.invoice_number', $insinvoices)->sum('sale_medicines.qty');
                $tq = $cq+$iq;            
                if($tq >= 1) {
                    fputcsv($output, array(++$counter, ucwords($index->name), $cq+$iq, $index->cash_price, $cq*$index->cash_price, $iq*$index->cash_price, $tq*$index->cash_price));
                    
                    $total = $total+ ($tq*$index->cash_price);
                    $qty = $qty + $tq;
                    $tc = $tc + ($cq*$index->cash_price);
                    $ti = $ti + ($iq*$index->cash_price);
                }
            }
            $columns = array('-', 'Total', $qty, '-', $tc, $ti, $total);
            fputcsv($output, $columns);
        }
        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="dr-based-medicine-sales-custom-report-'.date('d/M/Y', strtotime($sdate)).' - '.date('d/M/Y', strtotime($edate)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }
}
