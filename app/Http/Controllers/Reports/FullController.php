<?php

namespace App\Http\Controllers\Reports;

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

class FullController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function stocks()
    {
        $columns = ['S.No.' => 'sno', 
                    'Medicine' => 'medicine', 
                    'Batch No' => 'batch_no',
                    'Exp Date' => 'exp_date',
                    'Cost' => 'cost',
                    'Total Stock' => 'total_stock',
                    'Available Stock' => 'available_stock'];

        $output = [];

        $data = Stocks::select('stocks.*', 'medicines.name', 'medicines.cash_price')
                        ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')->orderBy('exp_date', 'asc')->get();
         foreach($data as $key => $index)
         {
            if($index->stock >= 1) {
                $newdata = ['sno' => ++$key, 
                            'medicine' => $index->name, 
                            'batch_no' => $index->batch_no,
                            'exp_date' => date('d-m-Y', strtotime($index->exp_date)),
                            'cost' => number_format($index->cash_price, 3),
                            'total_stock' => $index->received_stock,
                            'available_stock' => $index->stock];
                array_push($output, $newdata);
            }
        }
        return ['json_fields' => $columns, 'records' => $output];
    }

    public function customers($sdate = null, $edate = null)
    {
        $output = [];
        $columns = array('S.No.' => 'sno',
                        'User ID' => 'user_id',
                        'First Name' => 'first_name',
                        'Last Name' => 'last_name',
                        'Mobile' => 'mobile',
                        'Email' => 'email',
                        'Date Of Birth' => 'date_Of_birth',
                        'Gender' => 'gender',
                        'Marrital Status' => 'marrital_status',
                        'Whatsapp Number' => 'whatsapp_number',
                        'Address' => 'address',
                        'City' => 'city',
                        'Nationality' => 'nationality',
                        'Company' => 'company',
                        'Indentity' => 'indentity',
                        'ID Number' => 'id_number',
                        'ID Verfication' => 'id_verfication',
                        'Insurance' => 'insurance',
                        'Card Number' => 'card_number',
                        'Effective Date' => 'effective_date',
                        'Exp Date' => 'exp_date',
                        'Deductable' => 'deductable',
                        'Co Insurance' => 'coinsurance',
                        'Insurance Verification' => 'insurance_verification',
                        'Joined On' => 'joined_on',
                        'Status' => 'status',
                        'Source' => 'source',
                        'Last Consultation' => 'last_consultation',
                        'Last Consultated With' => 'last_consultated_with');

        $data = Users::with('user_profile')->with('user_document')->with('status')
                        ->whereDate("created_at",'<=', date('Y-m-d', strtotime($edate)))
                        ->whereDate("created_at",'>=',date('Y-m-d', strtotime($sdate)))->get();
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

            $newdata = ['S.No.' => ++$key,
                        'user_id' => $index->username,
                        'first_name' => $index['user_profile']['first_name'],
                        'last_name' => $index['user_profile']['last_name'],
                        'mobile' => $index->mobile,
                        'email' => $index['user_profile']['email'],
                        'date_Of_birth' => date('d-M-Y', strtotime($index['user_profile']['date_of_birth'])),
                        'gender' => $g,
                        'marrital_status' => ucwords($index['user_profile']['married']),
                        'whatsapp_number' => $index['user_profile']['contact_no'],
                        'address' => $index['user_profile']['address'],
                        'city' => $index['user_profile']['city'],
                        'nationality' => $index['user_profile']['nationality']['nationality'],
                        'company' => $index['user_profile']['company_name'],
                        'indentity' => $index['user_document']['identity_type']['value'],
                        'id_number' => $index['user_document']['verification_number'],
                        'id_verfication' => $ivr,
                        'insurance' => $index['user_document']['insurance']['name'],
                        'card_number' => $index['user_document']['policy_number'],
                        'effective_date' => $efd,
                        'exp_date' => $exd,
                        'deductable' => $dd,
                        'coinsurance' => $co,
                        'insurance_verification' => $vr,
                        'joined_on' => date('d-M-Y', strtotime($index['created_at'])),
                        'status' => $index['status']['status'],
                        'source' => $index->source,
                        'last_consultation' => $con,
                        'last_consultated_with' => $dr];
            array_push($output, $newdata);
        }

        return ['json_fields' => $columns, 'records' => $output];
    }

    public function treatments()
    {
        $output = [];
        $columns = array('S.No.' => 'sno', 'Type' => 'type', 'Treatment' => 'treatment', 'Cost' => 'cost', 'Timing' => 'timing', 'Dual Type' => 'dual_type', 'Primary Points' => 'primary_points', 'Secondary Points' => 'secondary_points');

        $data = Treatments::where('status_id', 2)->with('type')->orderBy('appointment_type_id', 'asc')->get();
        foreach($data as $key => $index)
        {
            if($index->is_it_dual == 1): $dual_type = 'Dual With Therapist';
            elseif($index->is_it_dual == 2): $dual_type = 'Dual With Doctors';
            else: $dual_type = 'Not A Dual Therapy'; endif;
            $newdata = ['sno' => ++$key, 
                        'type'=> $index->type->appointment_type,
                        'treatment'=> $index->treatment,
                        'cost' => $index->cost.' OMR',
                        'timing' => $index->timing.' Mins',
                        'dual_type' => $dual_type,
                        'primary_points' => $index->points,
                        'secondary_points' => $index->spoints];
            array_push($output, $newdata);
        }

        return ['json_fields' => $columns, 'records' => $output];
    }

    public function medicines()
    {
        $output = [];
        $columns = array('S.No.' => 'sno', 'Category' => 'category', 'Medicine' => 'medicine', 'Unitsize' => 'unitsize', 'Insurance Price' => 'insurance_price', 'Cash Price' => 'cash_price');

        $data = Medicines::where('status_id', 2)->with('category')->orderBy('category_id', 'asc')->get();

        foreach($data as $key => $index)
        {
            $newdata = ['sno' => ++$key,
                        'category' => $index->category->name,
                        'medicine' => $index->name,
                        'unitsize' => $index->unitsize.' '.$index->category->unit,
                        'insurance_price' => number_format($index->insurance_price, 3),
                        'cash_price' => number_format($index->cash_price, 3) ];
            array_push($output, $newdata);
        }

        return ['json_fields' => $columns, 'records' => $output];
    }

    public function insurances()
    {
        $output = [];
        $columns = array('S.No.' => 'sno', 'Name' => 'name', 'Contact No' => 'contact_no', 'Followup Days' => 'followup_days');
        $data = Insurances::where('status_id', 2)->get();

        foreach($data as $key => $index)
        {
            $newdata = ['sno' => ++$key, 
                        'name' => $index->name, 
                        'contact_no' => $index->contact_no, 
                        'followup_days' =>  $index->followup_days];
            array_push($output, $newdata);
        }
        
        return ['json_fields' => $columns, 'records' => $output];
    }

    public function insuranceDetail($iid)
    {
        $insurance = Insurances::where('status_id', 2)->where('id', $iid)->get()->first();
        if($insurance):
            $output = [];
            $columns = ['Id' => 'id', 'Insurance Name' => 'name', 'Followup Days' => 'followup', 'Status' => 'status', 'created On' => 'created'];
            $newdata = ['id' => $insurance->id,
                        'name' => $insurance->name,
                        'followup' => $insurance->followup_days,
                        'status' => ($insurance->status_id == 2)?'Active':'Deactive',
                        'created' => date('d-m-Y', strtotime($insurance->created_at))];
            array_push($output, $newdata);
            array_push($output, ['id' => '-','name' => '', 'followup' => '', 'status' => '', 'created' => '']);

            array_push($output, ['id' => 'SNo','name' => 'Covered Consultation', 'followup' => 'Actual Price', 'status' => 'Discount', 'created' => 'Insurance Price']);

            $data = InsuranceConsultationMaps::where('insurance_id', $iid)->with('treatment')->get();
            foreach($data as $key => $index)
            {
                array_push($output, ['id' => ++$key,
                                    'name' => $index->treatment->treatment, 
                                    'followup' => number_format($index->treatment->cost, 3),
                                    'status' => number_format($index->discount, 3),
                                    'created' => number_format(($index->treatment->cost - $index->discount), 3)]);
            }

            array_push($output, ['id' => '-','name' => '', 'followup' => '', 'status' => '', 'created' => '']);

            array_push($output, ['id' => 'SNo','name' => 'Covered Treatment', 'followup' => 'Actual Price', 'status' => 'Discount', 'created' => 'Insurance Price']);

            $data = InsuranceTreatmentMaps::where('insurance_id', $iid)->with('treatment')->get();
            foreach($data as $key => $index)
            {
                if(strpos($index->discount, '%')) {
                    $discount = $index->treatment->cost*str_replace('%', '', $index->discount)/100;
                }
                else {
                    $discount = $index->discount;
                }
            
                array_push($output, ['id' => ++$key,
                                    'name' => $index->treatment->treatment, 
                                    'followup' => number_format($index->treatment->cost, 3),
                                    'status' => number_format($index->discount, 3),
                                    'created' => number_format(($index->treatment->cost - $index->discount), 3)]);
            }

            array_push($output, ['id' => '-','name' => '', 'followup' => '', 'status' => '', 'created' => '']);

            array_push($output, ['id' => 'SNo','name' => 'Covered Medicines', 'followup' => 'Actual Price', 'status' => 'Discount', 'created' => 'Insurance Price']);

            $data = InsuranceMedicineMaps::where('insurance_id', $iid)->with('medicine')->get();
            foreach($data as $key => $index)
            {
                array_push($output, ['id' => ++$key,
                                    'name' => $index->medicine->name, 
                                    'followup' => number_format($index->medicine->cash_price, 3),
                                    'status' => number_format(($index->medicine->cash_price - $index->medicine->insurance_price), 3),
                                    'created' => number_format($index->medicine->insurance_price, 3)]);

            }

            return ['json_fields' => $columns, 'records' => $output];
        else:
            return ['json_fields' => [], 'records' => []];
        endif;
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
                ->whereIn("$tablename.status_id", [2,4,5,9])
                ->where("$tablename.is_dummy", 0)
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
                ->whereIn("$tablename.status_id", [2,4,5,9])
                ->where("$tablename.is_dummy", 0)
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
