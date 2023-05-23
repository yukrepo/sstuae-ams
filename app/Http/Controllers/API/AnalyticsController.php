<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Medicines;
use App\Models\Appointments;
use App\Models\Invoices;
use App\Models\Treatments;
use App\Models\TimeSlots;
use App\Models\SaleMedicines;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use DB;


class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getTopModicines(Request $request)
    {
        $sdate = str_replace('/', '-', $request->s);
        $edate = str_replace('/', '-', $request->e);
        $total = SaleMedicines::selectRaw('sum(qty) as total')->where('return_status', NULL)->whereBetween('created_at', [$sdate, $edate])->pluck('total')->toArray();
        $medicines = SaleMedicines::groupBy('medicine_id')->select('medicine_id')->selectRaw('sum(qty) as total')->where('return_status', NULL)->whereBetween('created_at', [$sdate, $edate])->orderBy('total', 'desc')->get()->take(7);
        $result = [];
        //var_dump($total[0]);
        foreach ($medicines as $key => $medicine) {
            $mval = Medicines::whereId($medicine->medicine_id)->first();
            $percent = number_format((($medicine->total)/$total[0])*100, 0).'%';
            $result[] = array('value' => $mval->name, 'count' => $medicine->total, 'percent' => $percent);
        }
        if(count($result) == 0) {
            $result[] = array('value' => 'No data found', 'count' => 0, 'percent' => '0%');
        }
        return $result;
    }

    public function getTopTreatments(Request $request)
    {
        $sdate = str_replace('/', '-', $request->s);
        $edate = str_replace('/', '-', $request->e);
        $year = '20'.substr($sdate, 2, 2);
        
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $total = $table->where('appointment_type_id', 2)
                            ->where('location_id', Auth::user()->location_id)
                            ->whereBetween('created_at', [$sdate, $edate])
                            ->where("$tablename.is_dummy", '0')
                            ->whereIn('status_id', [2,4,5,9])
                            ->get()->count();

        $appointments =  $table->where('appointment_type_id', 2)
                                ->where('location_id', Auth::user()->location_id)
                                ->whereBetween('created_at', [$sdate, $edate])
                                ->where("$tablename.is_dummy", '0')
                                ->whereIn('status_id', [2,4,5,9])
                                ->groupBy('treatment_id')
                                ->select('treatment_id', DB::raw('count(*) as total'))->orderBy('total', 'desc')->get()->take(7);
        $result = [];
        //var_dump($total[0]);
        foreach ($appointments as $key => $appoint) {
            $mval = Treatments::whereId($appoint->treatment_id)->first();
            $percent = number_format((($appoint->total)/$total)*100, 0).'%';
            $result[] = array('value' => $mval->treatment, 'count' => $appoint->total, 'percent' => $percent);
        }
        if(count($result) == 0) {
            $result[] = array('value' => 'No data found', 'count' => 0, 'percent' => '0%');
        }
        return $result;
    }

    public function revenueData(Request $request)
    {
        $s = str_replace('/', '-', $request->s);
        $e = str_replace('/', '-', $request->e);
        $year = '20'.substr($s, 2, 2);

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.$year.'s');
        $itablename = $itable->getTable();
        $s = strtotime($s);
        $e = strtotime($e);
        $diff = $e - $s;
        $day = $diff/(24*3600);

        $allDate = [];
        $passDate = [];
        $ac = [];
        $at = [];
        $ph = [];
        $cp = [];
        $cs = [];
        $cd = [];
        $ot = [];
        $displayDate = [];
        $ld = '';
        $l = '';
        if($day > 12)
        {
            $day = $diff/(24*3600)+1; //dd($day);
            $date = [];
            $l = 0;
            if($day > 12 && $day <=30){
                $group = intval($day/3);
                $groupRecord = [];
                $l = 3;
                for($i = 0; $i < $group; ++$i){
                    $date[$i]["s"] = date("Y-m-d", $s + $i*3*24*3600);
                    $date[$i]["e"] = date("Y-m-d", $s + ($i*3+2)*24*3600);
                    $displayDate[] = date("j", $s + $i*3*24*3600).date("M", $s + ($i*3+2)*24*3600);
                }
                if($day%3 == 1){
                    $date[$i]["s"] = date("Y-m-d", $e);
                    $date[$i]["e"] = date("Y-m-d", $e);
                    $displayDate[] = date("j M", $e);
                }elseif($day%3 == 2){
                    $date[$i]["s"] = date("Y-m-d", $e - 24*3600);
                    $date[$i]["e"] = date("Y-m-d", $e);
                    $displayDate[] = date("j M", $e - 24*3600);
                }
                
            }elseif($day > 30){
                if($day%10 == 0){
                    $l = $day/10;
                }else{
                    $l = intval($day/10)+1;
                }
                $groupRecord = [];
                $group = intval($day/$l);
                for($i = 0; $i < $group; ++$i){
                    $date[$i]["s"] = date("Y-m-d", $s + $i*$l*24*3600);
                    $date[$i]["e"] = date("Y-m-d", $s + ($i*$l+$l-1)*24*3600);
                    $displayDate[] = date("j M", $s + $i*$l*24*3600);
                    $ld = date("Y-m-d", $s + ($i*$l+$l-1)*24*3600);
                }
                if($day%$l > 0){
                    $date[$i]["s"] = date("Y-m-d", strtotime($ld)+24*3600);
                    $date[$i]["e"] = date("Y-m-d", $e);
                    $displayDate[] = date("j M", strtotime($ld)+24*3600);
                }
            }
            
            
        }
        for($i = 0; $i <= $day; $i++){
            $passDate[] = date("Y-m-d", $s+$i*24*3600);
        }

        foreach ($passDate as $key => $pdate) {
            $temp_ac = $itable->selectRaw('sum(payable_amount) as total')
                                ->whereDate("$itablename.invoice_date",'=', date('Y-m-d', strtotime($pdate)))
                                ->where("$itablename.cancel", 0)
                                ->whereIn("$itablename.invoice_type", [1])
                                ->where("$itablename.location_id", Auth::user()->location_id)
                                ->pluck('total')->toArray()[0];
            $ac[] = number_format($temp_ac, 3);
            $temp_at = $itable->selectRaw('sum(payable_amount) as total')
                                ->whereDate("$itablename.invoice_date",'=', date('Y-m-d', strtotime($pdate)))
                                ->where("$itablename.cancel", 0)
                                ->whereIn("$itablename.invoice_type", [2])
                                ->where("$itablename.location_id", Auth::user()->location_id)
                                ->pluck('total')->toArray()[0];
            $at[] = number_format($temp_at, 3);
            $temp_ph = $itable->selectRaw('sum(payable_amount) as total')
                                ->whereDate("$itablename.invoice_date",'=', date('Y-m-d', strtotime($pdate)))
                                ->where("$itablename.cancel", 0)
                                ->whereIn("$itablename.invoice_type", [3,4,7])
                                ->where("$itablename.location_id", Auth::user()->location_id)
                                ->pluck('total')->toArray()[0];
            $ph[] = number_format($temp_ph, 3);
            $temp_cs = $itable->selectRaw('sum(payable_amount) as total')
                                ->whereDate("$itablename.invoice_date",'=', date('Y-m-d', strtotime($pdate)))
                                ->where("$itablename.cancel", 0)
                                ->whereIn("$itablename.invoice_type", [6])
                                ->where("$itablename.location_id", Auth::user()->location_id)
                                ->pluck('total')->toArray()[0];
            $cs[] = number_format($temp_cs, 3);
            $temp_cp = $itable->selectRaw('sum(payable_amount) as total')
                                ->whereDate("$itablename.invoice_date",'=', date('Y-m-d', strtotime($pdate)))
                                ->where("$itablename.cancel", 0)
                                ->whereIn("$itablename.invoice_type", [5])
                                ->where("$itablename.location_id", Auth::user()->location_id)
                                ->pluck('total')->toArray()[0];
            $cp[] = number_format($temp_cp, 3);
            $temp_cd = $itable->selectRaw('sum(payable_amount) as total')
                                ->whereDate("$itablename.invoice_date",'=', date('Y-m-d', strtotime($pdate)))
                                ->where("$itablename.cancel", 0)
                                ->whereIn("$itablename.invoice_type", [8])
                                ->where("$itablename.location_id", Auth::user()->location_id)
                                ->pluck('total')->toArray()[0];
            $cd[] = number_format($temp_cd, 3);
            $temp_ot = $itable->selectRaw('sum(payable_amount) as total')
                                ->whereDate("$itablename.invoice_date",'=', date('Y-m-d', strtotime($pdate)))
                                ->where("$itablename.cancel", 0)
                                ->whereNotIn("$itablename.invoice_type", [1,2,3,4,5,6,7,8])
                                ->where("$itablename.location_id", Auth::user()->location_id)
                                ->pluck('total')->toArray()[0];
            $ot[] = number_format($temp_ot, 3);
        }
        $data = [ 
                    ['name' => 'Consultation', 'chartType' => 'bar', 'values' => $ac],
                    ['name' => 'Treatment', 'chartType' => 'bar', 'values' => $at],
                    ['name' => 'Pharmacy', 'chartType' => 'line', 'values' => $ph],
                    ['name' => 'Direct Course',  'chartType' => 'line', 'values' => $cd],
                    ['name' => 'Prescribed Course',  'chartType' => 'line', 'values' => $cs],
                    ['name' => 'Packages',  'chartType' => 'line', 'values' => $cp],
                    ['name' => 'Others',  'chartType' => 'bar', 'values' => $ot]            
        ];
        
        return ['data' => $data, 'labels' => $passDate];
        
    }

    public function pharmacyRevenueData(Request $request)
    {
        $s = str_replace('/', '-', $request->s);
        $e = str_replace('/', '-', $request->e);
        $year = '20'.substr($s, 2, 2);

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.$year.'s');
        $itablename = $itable->getTable();
        $s = strtotime($s);
        $e = strtotime($e);
        $diff = $e - $s;
        $day = $diff/(24*3600);

        $allDate = [];
        $passDate = [];
        $ci = [];
        $ii = [];

        $displayDate = [];
        $ld = '';
        $l = '';
        if($day > 12)
        {
            $day = $diff/(24*3600)+1; //dd($day);
            $date = [];
            $l = 0;
            if($day > 12 && $day <=30){
                $group = intval($day/3);
                $groupRecord = [];
                $l = 3;
                for($i = 0; $i < $group; ++$i){
                    $date[$i]["s"] = date("Y-m-d", $s + $i*3*24*3600);
                    $date[$i]["e"] = date("Y-m-d", $s + ($i*3+2)*24*3600);
                    $displayDate[] = date("j", $s + $i*3*24*3600).date("M", $s + ($i*3+2)*24*3600);
                }
                if($day%3 == 1){
                    $date[$i]["s"] = date("Y-m-d", $e);
                    $date[$i]["e"] = date("Y-m-d", $e);
                    $displayDate[] = date("j M", $e);
                }elseif($day%3 == 2){
                    $date[$i]["s"] = date("Y-m-d", $e - 24*3600);
                    $date[$i]["e"] = date("Y-m-d", $e);
                    $displayDate[] = date("j M", $e - 24*3600);
                }
                
            }elseif($day > 30){
                if($day%10 == 0){
                    $l = $day/10;
                }else{
                    $l = intval($day/10)+1;
                }
                $groupRecord = [];
                $group = intval($day/$l);
                for($i = 0; $i < $group; ++$i){
                    $date[$i]["s"] = date("Y-m-d", $s + $i*$l*24*3600);
                    $date[$i]["e"] = date("Y-m-d", $s + ($i*$l+$l-1)*24*3600);
                    $displayDate[] = date("j M", $s + $i*$l*24*3600);
                    $ld = date("Y-m-d", $s + ($i*$l+$l-1)*24*3600);
                }
                if($day%$l > 0){
                    $date[$i]["s"] = date("Y-m-d", strtotime($ld)+24*3600);
                    $date[$i]["e"] = date("Y-m-d", $e);
                    $displayDate[] = date("j M", strtotime($ld)+24*3600);
                }
            }
        }

        for($i = 0; $i <= $day; $i++){
            $passDate[] = date("Y-m-d", $s+$i*24*3600);
        }

        foreach ($passDate as $key => $pdate) {
            $temp_ac = $itable->selectRaw('sum(payable_amount) as total')
                                ->whereDate("$itablename.invoice_date",'=', date('Y-m-d', strtotime($pdate)))
                                ->where("$itablename.cancel", 0)
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$itablename.invoice_type", [3,4,7])
                                ->where("$itablename.location_id", Auth::user()->location_id)
                                ->pluck('total')->toArray()[0];
            $ci[] = number_format($temp_ac, 3);
            $temp_at = $itable->selectRaw('sum(payable_amount) as total')
                                ->whereDate("$itablename.invoice_date",'=', date('Y-m-d', strtotime($pdate)))
                                ->where("$itablename.cancel", 0)
                                ->where("$itablename.insurance_id", '!=', 1)
                                ->whereIn("$itablename.invoice_type", [3,4,7])
                                ->where("$itablename.location_id", Auth::user()->location_id)
                                ->pluck('total')->toArray()[0];
            $ii[] = number_format($temp_at, 3);
        }
        $data = [ 
                    ['name' => 'Cash Patient', 'values' => $ci],
                    ['name' => 'Insurance Patient', 'values' => $ii],            
        ];
        
        return ['data' => $data, 'labels' => $passDate];
    }

    public function consultRevenueData(Request $request)
    {
        $s = str_replace('/', '-', $request->s);
        $e = str_replace('/', '-', $request->e);
        $year = '20'.substr($s, 2, 2);

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.$year.'s');
        $itablename = $itable->getTable();
        $s = strtotime($s);
        $e = strtotime($e);
        $diff = $e - $s;
        $day = $diff/(24*3600);

        $allDate = [];
        $passDate = [];
        $ci = [];
        $ii = [];

        $displayDate = [];
        $ld = '';
        $l = '';
        if($day > 12)
        {
            $day = $diff/(24*3600)+1; //dd($day);
            $date = [];
            $l = 0;
            if($day > 12 && $day <=30){
                $group = intval($day/3);
                $groupRecord = [];
                $l = 3;
                for($i = 0; $i < $group; ++$i){
                    $date[$i]["s"] = date("Y-m-d", $s + $i*3*24*3600);
                    $date[$i]["e"] = date("Y-m-d", $s + ($i*3+2)*24*3600);
                    $displayDate[] = date("j", $s + $i*3*24*3600).date("M", $s + ($i*3+2)*24*3600);
                }
                if($day%3 == 1){
                    $date[$i]["s"] = date("Y-m-d", $e);
                    $date[$i]["e"] = date("Y-m-d", $e);
                    $displayDate[] = date("j M", $e);
                }elseif($day%3 == 2){
                    $date[$i]["s"] = date("Y-m-d", $e - 24*3600);
                    $date[$i]["e"] = date("Y-m-d", $e);
                    $displayDate[] = date("j M", $e - 24*3600);
                }
                
            }elseif($day > 30){
                if($day%10 == 0){
                    $l = $day/10;
                }else{
                    $l = intval($day/10)+1;
                }
                $groupRecord = [];
                $group = intval($day/$l);
                for($i = 0; $i < $group; ++$i){
                    $date[$i]["s"] = date("Y-m-d", $s + $i*$l*24*3600);
                    $date[$i]["e"] = date("Y-m-d", $s + ($i*$l+$l-1)*24*3600);
                    $displayDate[] = date("j M", $s + $i*$l*24*3600);
                    $ld = date("Y-m-d", $s + ($i*$l+$l-1)*24*3600);
                }
                if($day%$l > 0){
                    $date[$i]["s"] = date("Y-m-d", strtotime($ld)+24*3600);
                    $date[$i]["e"] = date("Y-m-d", $e);
                    $displayDate[] = date("j M", strtotime($ld)+24*3600);
                }
            }
        }

        for($i = 0; $i <= $day; $i++){
            $passDate[] = date("Y-m-d", $s+$i*24*3600);
        }

        foreach ($passDate as $key => $pdate) {
            $temp_ac = $itable->selectRaw('sum(payable_amount) as total')
                                ->whereDate("$itablename.invoice_date",'=', date('Y-m-d', strtotime($pdate)))
                                ->where("$itablename.cancel", 0)
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$itablename.invoice_type", [1])
                                ->where("$itablename.location_id", Auth::user()->location_id)
                                ->pluck('total')->toArray()[0];
            $ci[] = number_format($temp_ac, 3);
            $temp_at = $itable->selectRaw('sum(payable_amount) as total')
                                ->whereDate("$itablename.invoice_date",'=', date('Y-m-d', strtotime($pdate)))
                                ->where("$itablename.cancel", 0)
                                ->where("$itablename.insurance_id", '!=', 1)
                                ->whereIn("$itablename.invoice_type", [1])
                                ->where("$itablename.location_id", Auth::user()->location_id)
                                ->pluck('total')->toArray()[0];
            $ii[] = number_format($temp_at, 3);
        }
        $data = [ 
                    ['name' => 'Cash Patient', 'values' => $ci],
                    ['name' => 'Insurance Patient', 'values' => $ii],            
        ];
        
        return ['data' => $data, 'labels' => $passDate];
    }

    public function treatmentRevenueData(Request $request)
    {
        $s = str_replace('/', '-', $request->s);
        $e = str_replace('/', '-', $request->e);
        $year = '20'.substr($s, 2, 2);

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.$year.'s');
        $itablename = $itable->getTable();
        $s = strtotime($s);
        $e = strtotime($e);
        $diff = $e - $s;
        $day = $diff/(24*3600);

        $allDate = [];
        $passDate = [];
        $ci = [];
        $ii = [];

        $displayDate = [];
        $ld = '';
        $l = '';
        if($day > 12)
        {
            $day = $diff/(24*3600)+1; //dd($day);
            $date = [];
            $l = 0;
            if($day > 12 && $day <=30){
                $group = intval($day/3);
                $groupRecord = [];
                $l = 3;
                for($i = 0; $i < $group; ++$i){
                    $date[$i]["s"] = date("Y-m-d", $s + $i*3*24*3600);
                    $date[$i]["e"] = date("Y-m-d", $s + ($i*3+2)*24*3600);
                    $displayDate[] = date("j", $s + $i*3*24*3600).date("M", $s + ($i*3+2)*24*3600);
                }
                if($day%3 == 1){
                    $date[$i]["s"] = date("Y-m-d", $e);
                    $date[$i]["e"] = date("Y-m-d", $e);
                    $displayDate[] = date("j M", $e);
                }elseif($day%3 == 2){
                    $date[$i]["s"] = date("Y-m-d", $e - 24*3600);
                    $date[$i]["e"] = date("Y-m-d", $e);
                    $displayDate[] = date("j M", $e - 24*3600);
                }
                
            }elseif($day > 30){
                if($day%10 == 0){
                    $l = $day/10;
                }else{
                    $l = intval($day/10)+1;
                }
                $groupRecord = [];
                $group = intval($day/$l);
                for($i = 0; $i < $group; ++$i){
                    $date[$i]["s"] = date("Y-m-d", $s + $i*$l*24*3600);
                    $date[$i]["e"] = date("Y-m-d", $s + ($i*$l+$l-1)*24*3600);
                    $displayDate[] = date("j M", $s + $i*$l*24*3600);
                    $ld = date("Y-m-d", $s + ($i*$l+$l-1)*24*3600);
                }
                if($day%$l > 0){
                    $date[$i]["s"] = date("Y-m-d", strtotime($ld)+24*3600);
                    $date[$i]["e"] = date("Y-m-d", $e);
                    $displayDate[] = date("j M", strtotime($ld)+24*3600);
                }
            }
        }

        for($i = 0; $i <= $day; $i++){
            $passDate[] = date("Y-m-d", $s+$i*24*3600);
        }

        foreach ($passDate as $key => $pdate) {
            $temp_ac = $itable->selectRaw('sum(payable_amount) as total')
                                ->whereDate("$itablename.invoice_date",'=', date('Y-m-d', strtotime($pdate)))
                                ->where("$itablename.cancel", 0)
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$itablename.invoice_type", [2,5,6,8])
                                ->where("$itablename.location_id", Auth::user()->location_id)
                                ->pluck('total')->toArray()[0];
            $ci[] = number_format($temp_ac, 3);
            $temp_at = $itable->selectRaw('sum(payable_amount) as total')
                                ->whereDate("$itablename.invoice_date",'=', date('Y-m-d', strtotime($pdate)))
                                ->where("$itablename.cancel", 0)
                                ->where("$itablename.insurance_id", '!=', 1)
                                ->whereIn("$itablename.invoice_type", [2,5,6,8])
                                ->where("$itablename.location_id", Auth::user()->location_id)
                                ->pluck('total')->toArray()[0];
            $ii[] = number_format($temp_at, 3);
        }
        $data = [ 
                    ['name' => 'Cash Patient', 'values' => $ci],
                    ['name' => 'Insurance Patient', 'values' => $ii],            
        ];
        
        return ['data' => $data, 'labels' => $passDate];
    }

    public function highBusinessData(Request $request)
    {
        $sdate = str_replace('/', '-', $request->s);
        $edate = str_replace('/', '-', $request->e);
        $year = '20'.substr($sdate, 2, 2);
        
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $total = $table->where('appointment_type_id', 2)
                            ->where('location_id', Auth::user()->location_id)
                            ->whereBetween('created_at', [$sdate, $edate])
                            ->where("$tablename.is_dummy", '0')
                            ->whereIn('status_id', [2,4,5,9])
                            ->get()->count();

        $appointments =  $table->where('appointment_type_id', 2)
                                ->where('location_id', Auth::user()->location_id)
                                ->whereBetween('created_at', [$sdate, $edate])
                                ->where("$tablename.is_dummy", '0')
                                ->whereIn('status_id', [2,4,5,9])
                                ->groupBy('start_timeslot')
                                ->select('start_timeslot', DB::raw('count(*) as total'))->orderBy('total', 'desc')->get()->take(7);
        $result = [];
        //var_dump($total[0]);
        foreach ($appointments as $key => $appoint) {
            $mval = TimeSlots::whereId($appoint->start_timeslot)->first();
            $percent = number_format((($appoint->total)/$total)*100, 0).'%';
            $result[] = array('value' => $mval->time, 'count' => $appoint->total, 'percent' => $percent);
        }
        if(count($result) == 0) {
            $result[] = array('value' => 'No data found', 'count' => 0, 'percent' => '0%');
        }
        return $result;
    }

    public function lowBusinessData(Request $request)
    {
        $sdate = str_replace('/', '-', $request->s);
        $edate = str_replace('/', '-', $request->e);
        $year = '20'.substr($sdate, 2, 2);
        
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $total = $table->where('appointment_type_id', 2)
                            ->where('location_id', Auth::user()->location_id)
                            ->whereBetween('created_at', [$sdate, $edate])
                            ->where("$tablename.is_dummy", '0')
                            ->whereIn('status_id', [2,4,5,9])
                            ->get()->count();

        $appointments =  $table->where('appointment_type_id', 2)
                            ->where('location_id', Auth::user()->location_id)
                            ->whereBetween('created_at', [$sdate, $edate])
                            ->where("$tablename.is_dummy", '0')
                            ->whereIn('status_id', [2,4,5,9])
                            ->groupBy('start_timeslot')
                            ->select('start_timeslot', DB::raw('count(*) as total'))->orderBy('total', 'asc')->get()->take(7);
        $result = [];
        //var_dump($total[0]);
        foreach ($appointments as $key => $appoint) {
            $mval = TimeSlots::whereId($appoint->start_timeslot)->first();
            $percent = number_format((($appoint->total)/$total)*100, 2).'%';
            $result[] = array('value' => $mval->time, 'count' => $appoint->total, 'percent' => $percent);
        }
        if(count($result) == 0) {
            $result[] = array('value' => 'No data found', 'count' => 0, 'percent' => '0%');
        }
        return $result;
    }

}