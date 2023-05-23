<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Stocks;
use App\Models\Suppliers;
use App\Models\Medicines;
use App\Models\SaleMedicines;
use App\Models\Treatments;
use App\Models\Insurances;
use App\Models\Users;
use App\Models\InsuranceConsultationMaps;
use App\Models\InsuranceMedicineMaps;
use App\Models\InsuranceTreatmentMaps;
use App\Models\Appointments;
use Illuminate\Http\Request;

class FullReportController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function stocks()
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $columns = array('S.No.', 'Medicine', 'Batch No', 'Exp Date', 'Cost', 'Total Stock', 'Available Stock');
        fputcsv($output, $columns);

        $suppliers = Suppliers::get();

        $data = Stocks::select('stocks.*', 'medicines.name', 'medicines.cash_price')
                        ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')->orderBy('exp_date', 'asc')->get();
         foreach($data as $key => $index)
         {
            if($index->stock >= 1) {
                fputcsv($output, array(++$key, $index->name, $index->batch_no, date('d-m-Y', strtotime($index->exp_date)), $index->cash_price, $index->received_stock, $index->stock ));
            }
         }
         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="full-stock-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function customers()
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $columns = array('S.No.', 'User ID', 'First Name', 'Last Name', 'Mobile', 'Email', 'Date Of Birth', 'Gender', 'Marrital Status', 'Whatsapp Number', 'Address', 'City', 'Nationality', 'Company', 'Indentity', 'ID Number', 'ID Verfication', 'Insurance', 'Card Number', 'Effective Date', 'Exp Date', 'Deductable', 'Co Insurance', 'Insurance Verification', 'Joined On', 'Status', 'Source', 'Last Consultation', 'Last Consultated With');
        fputcsv($output, $columns);

        $data = Users::with('user_profile')->with('user_document')->with('status')->get();
         foreach($data as $key => $index)
         {
            if($index['user_document']['insurance_id'] == 1) {
                $dd = '-'; $co = '-';
                $efd = '-';
                $exd = '-';
                $vr = 'No Need';
            }
            else {
                $dd = $index['user_document']['consult_deductable'];
                $co = $index['user_document']['co_insurance'];
                $efd = date('d-M-Y', strtotime($index['user_document']['effective_date']));
                $exd = date('d-M-Y', strtotime($index['user_document']['expiry_date']));
                if($index['user_document']['insurance_verified'] == 1): $vr = 'Verified';
                else: $vr = 'Not Verified'; endif;
            }
            if($index['user_document']['identity_verified'] == 1): $ivr = 'Verified';
            else: $ivr = 'Not Verified'; endif;

            if($index['user_profile']['gender'] == 1): $g = 'Male';
            elseif($index['user_profile']['gender'] == 2): $g = 'Female';
            else: $g = 'Unknown'; endif;
            $con = $this->__LastConsultation($index['id']);
            $dr = $this->__LastConsultationWith($index['id']);
            fputcsv($output, array(++$key, $index->username, $index['user_profile']['first_name'], $index['user_profile']['last_name'], $index->mobile, $index['user_profile']['email'], date('d-M-Y', strtotime($index['user_profile']['date_of_birth'])), $g, ucwords($index['user_profile']['married']), $index['user_profile']['contact_no'], $index['user_profile']['address'], $index['user_profile']['city'], $index['user_profile']['nationality']['nationality'], $index['user_profile']['company_name'], $index['user_document']['identity_type']['value'], $index['user_document']['verification_number'], $ivr, $index['user_document']['insurance']['name'], $index['user_document']['policy_number'], $efd, $exd, $dd, $co, $vr, date('d-M-Y', strtotime($index['created_at'])),  $index['status']['status'], $index->source, $con, $dr ));
         }
         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="full-customer-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function treatments()
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $columns = array('S.No.', 'Type', 'Treatment', 'Cost', 'Timing', 'Dual Type', 'Primary Points', 'Secondary Points');
        fputcsv($output, $columns);

        $data = Treatments::where('status_id', 2)->with('type')->orderBy('appointment_type_id', 'asc')->get();
         foreach($data as $key => $index)
         {
            if($index->is_it_dual == 1): $dual_type = 'Dual With Therapist';
            elseif($index->is_it_dual == 2): $dual_type = 'Dual With Doctors';
            else: $dual_type = 'Not A Dual Therapy'; endif;
            fputcsv($output, array(++$key, $index->type->appointment_type, $index->treatment, $index->cost.' OMR', $index->timing.' Mins', $dual_type, $index->points, $index->spoints ));
         }
         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="full-treatments-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function medicines()
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $columns = array('S.No.', 'Category', 'Medicine', 'Unitsize', 'Insurance Price', 'Cash Price');
        fputcsv($output, $columns);

        $data = Medicines::where('status_id', 2)->with('category')->orderBy('category_id', 'asc')->get();
         foreach($data as $key => $index)
         {
            fputcsv($output, array(++$key, $index->category->name, $index->name, $index->unitsize.' '.$index->category->unit, $index->insurance_price, $index->cash_price ));
         }
         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="full-medicines-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function insurances()
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $columns = array('S.No.', 'Name', 'Contact No', 'Followup Days');
        fputcsv($output, $columns);

        $data = Insurances::where('status_id', 2)->get();
         foreach($data as $key => $index)
         {
            fputcsv($output, array(++$key, $index->name, $index->contact_no, $index->followup_days ));
         }
         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="full-insurance-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function insuranceDetail(Request $request)
    {
        $iid = $request->insurance_id;
        $insurance = Insurances::where('status_id', 2)->where('id', $iid)->get()->first();
        if($insurance):
            $memoryToUse = 50*1024*1024*1024*1024;
            $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
            $columns = array('',$insurance->name, 'Followup Days', $insurance->followup_days);
            fputcsv($output, $columns);

            $columns = array('', '');
            fputcsv($output, $columns);

            $columns = array('', 'Covered Consultation');
            fputcsv($output, $columns);

            $columns = array('', '');
            fputcsv($output, $columns);

            $columns = array('SNo', 'Consultation', 'Actual Price', 'Discount', 'Insurance Price');
            fputcsv($output, $columns);

            $data = InsuranceConsultationMaps::where('insurance_id', $iid)->with('treatment')->get();
            foreach($data as $key => $index)
            {
               fputcsv($output, array(++$key, $index->treatment->treatment, number_format($index->treatment->cost, 3), number_format($index->discount, 3), number_format(($index->treatment->cost - $index->discount), 3) ));
            }

            $columns = array('', '');
            fputcsv($output, $columns);

            $columns = array('', 'Covered Treatments');
            fputcsv($output, $columns);

            $columns = array('', '');
            fputcsv($output, $columns);

            $columns = array('SNo', 'Treatment', 'Actual Price', 'Discount', 'Insurance Price', 'Cash Discount', '3 or 4 Appointments', '5+ Appointments');
            fputcsv($output, $columns);

            $data = InsuranceTreatmentMaps::where('insurance_id', $iid)->with('treatment')->get();
            foreach($data as $key => $index)
            {
                if(strpos($index->discount, '%')) {
                    $discount = $index->treatment->cost*str_replace('%', '', $index->discount)/100;
                }
                else {
                    $discount = $index->discount;
                }
               fputcsv($output, array(++$key, $index->treatment->treatment, number_format($index->treatment->cost, 3), $index->discount,  number_format(($index->treatment->cost - $discount), 3), (($index->cash_discount == 1)?'Yes':'No'), (($index->cash_discount == 1)?$index->cd34:'-'), (($index->cash_discount == 1)?$index->cd56:'-') ));
            }

            $columns = array('', '');
            fputcsv($output, $columns);

            $columns = array('', 'Covered Medicines');
            fputcsv($output, $columns);

            $columns = array('', '');
            fputcsv($output, $columns);

            $columns = array('SNo', 'Medicine', 'Actual Price', 'Insurance Price');
            fputcsv($output, $columns);

            $data = InsuranceMedicineMaps::where('insurance_id', $iid)->with('medicine')->get();
            foreach($data as $key => $index)
            {
               fputcsv($output, array(++$key, $index->medicine->name, number_format($index->medicine->cash_price, 3), number_format(($index->medicine->insurance_price), 3) ));
            }

            rewind($output);
            header('Content-type: application/octet-stream');
            header('Content-Disposition: attachment; filename="insurance-covered-report.csv"');
            echo stream_get_contents($output);
            fclose($output);
        else:
            echo 'Invalid URL or unauthorized access';
        endif;
        die;
    }

    public function directMedicineReport($sdate = null, $edate = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Direct Medicine Sales Report ', ' - ', date('d/M/Y', strtotime($sdate)).' - '.date('d/M/Y', strtotime($edate)));
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Medicine', 'Invoice Date', 'Invoice No', 'Client', 'Qty', 'Price', 'Total Amount');
        fputcsv($output, $columns);
        $data = Medicines::orderBy('name', 'asc')->get();
        $count = 1;
        foreach($data as $key => $index)
        {
            $out = SaleMedicines::select('sale_medicines.*', 'sales.invoice_number', 'user_profiles.first_name', 'user_profiles.last_name')->join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                        ->join('users', 'sales.customer_id', '=', 'users.id')
                        ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                        ->whereDate('sale_medicines.created_at','<=', date('Y-m-d', strtotime($edate)))
                        ->whereDate('sale_medicines.created_at','>=',date('Y-m-d', strtotime($sdate)))
                        ->where('sale_medicines.medicine_id', $index->id)
                        ->where('sales.status', 1)->get();

            foreach ($out as $key2 => $value2)
            {
                $name = $value2->first_name.' '.($value2->last_name?$value2->last_name:'');
                //if($key2 == 0) {
                    fputcsv($output, array($count, ucwords($index->name), date('d-M-Y', strtotime($value2->created)), $value2->invoice_number, $name, $value2->qty, $index->cash_price, number_format(($index->cash_price*$value2->qty), 3) ));
                /*}  else {
                    fputcsv($output, array($count, '-', date('d-M-Y', strtotime($value2->created_at)), $value2->invoice_number, $name, $value2->qty, $index->cash_price, number_format(($index->cash_price*$value2->qty), 3) ));
                } */
                $count++;
            }
        }
        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="direct-medicine-report-'.date('d/M/Y', strtotime($sdate)).'-'.date('d/M/Y', strtotime($edate)).'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    private function __LastConsultation($uid = null)
    {
        $last_year = 2019;
        $current_year = date('Y');
        $apdates = [];
        for ($i=$current_year; $i >= $last_year ; $i--) { 
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$i.'s');
            $tablename = $table->getTable();
            $apdate = $table->select("$tablename.date")
                ->where("$tablename.user_id", $uid)
                ->where("$tablename.appointment_type_id", 1)
                ->where("$tablename.status_id", 5)
                ->orderBy('id', 'desc')->get()->first();
            if($apdate):
                array_push($apdates, $apdate->date);
            endif;
        }
        if(count($apdates) >= 1){
            return $apdates[0];
        } else {
            return 'No Appointments';
        }
    }

    private function __LastConsultationWith($uid = null)
    {
        $last_year = 2019;
        $current_year = date('Y');
        $apdates = [];
        for ($i=$current_year; $i >= $last_year ; $i--) { 
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$i.'s');
            $tablename = $table->getTable();
            $apdate = $table->select("doctors.name as name")
                ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                ->where("$tablename.user_id", $uid)
                ->where("$tablename.appointment_type_id", 1)
                ->where("$tablename.status_id", 5)
                ->orderBy("$tablename.id", 'desc')->get()->first();
            if($apdate):
                array_push($apdates, $apdate->name);
            endif;
        }
        if(count($apdates) >= 1){
            return $apdates[0];
        } else {
            return 'Unknown';
        }
    }
}
