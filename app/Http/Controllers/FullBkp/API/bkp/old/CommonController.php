<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TimeSlots;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Xrays;
use App\Models\Users;
use App\Models\Diagnosis;
use App\Models\Laboratories;
use App\Models\Nationalities;
use App\Models\Configs;
use App\Models\InsuranceConsultationMaps;
use App\Models\InsuranceMedicineMaps;
use App\Models\InsuranceTreatmentMaps;
use App\Models\Options;

class CommonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getTimeSlots()
    {
        return TimeSlots::where('season', 1)->get();
    }

    public function getAllTimeSlots()
    {
        return TimeSlots::get();
    }

    public function getXTestList()
    {
        return Xrays::where('status_id', 2)->orderBy('value', 'asc')->get();
    }

    public function getDTestList()
    {
       return Laboratories::where('status_id', 2)->orderBy('value', 'asc')->get();
    }

    public function getXTestSelectList()
    {
        $xrays = Xrays::where('status_id', 2)->orderBy('value', 'asc')->get();
        $result = [];
        foreach($xrays as $xray):
            $result[] = ['value' => $xray->id, 'text' => $xray->value];
        endforeach;
        return $result;
    }

    public function getDTestSelectList()
    {
        $diagosiss = Laboratories::where('status_id', 2)->orderBy('value', 'asc')->get();
        $result = [];
        foreach($diagosiss as $diagosis):
            $result[] = ['value' => $diagosis->id, 'text' => $diagosis->value];
        endforeach;
        return $result;
    }

    public function getCustomerSelectList()
    {
        $users = Users::select('users.*', 'user_profiles.first_name', 'user_profiles.last_name', 'user_documents.identity_type_id', 'user_documents.verification_number')
                            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                            ->join('user_documents', 'user_documents.user_id', '=', 'users.id')
                            ->where('users.status_id', 2)
                            ->orderBy('first_name', 'asc')->get();
        $result = [];
        foreach($users as $user):
            if($user->identity_type_id == 1): $i = 'C-ID';
            elseif($user->identity_type_id == 2): $i = 'P-ID';
            else: $i = 'UX'; endif;
            $result[] = ['value' => $user->id, 'text' => $user->username.' - '.$user->first_name.' '.$user->last_name.' - '.$user->mobile.' - '.$i.' - '.$user->verification_number];
        endforeach;
        return $result;
    }

    public function NationalityList()
    {
        return Nationalities::latest()->orderBy('nationality', 'asc')->get();
    }

    public function getConsultMappedInsurances(Request $request)
    {
        $data = InsuranceConsultationMaps::select('insurance_consultation_maps.*', 'insurances.name', 'insurances.cash_discount')
                                            ->join('insurances', 'insurances.id', '=', 'insurance_consultation_maps.insurance_id')
                                            ->where('insurance_id', $request['insurance_id'])
                                            ->where('treatment_id', $request['treatment_id'])
                                            ->get()
                                            ->first();
        if($data) return $data; else return array();
    }

    public function getTreatmentMappedInsurances(Request $request)
    {
        $data = InsuranceTreatmentMaps::select('insurance_treatment_maps.*', 'insurances.name')
                                            ->join('insurances', 'insurances.id', '=', 'insurance_treatment_maps.insurance_id')
                                            ->where('insurance_id', $request['insurance_id'])
                                            ->where('treatment_id', $request['treatment_id'])
                                            ->get()
                                            ->first();
        if($data) return $data; else return array();
    }

    public function getMedicineMappedInsurances(Request $request)
    {
        # code...
    }

    public function checkConsultMapping(Request $request)
    {
        # code...
    }

    public function checkTreatmentMapping(Request $request)
    {

    }

    public function checkMedicineMapping(Request $request)
    {
        $data = InsuranceMedicineMaps::where('insurance_id', $request['insurance_id'])
                                    ->where('medicine_id', $request['medicine_id'])
                                    ->get()->first();
        if($data) return ['status' => false]; else return ['status' => true];
    }

    public function getConfigs()
    {
        $configs = Options::where('location_id', Auth::user()->location_id)->get();
        $return = [];
        foreach ($configs as $config) {
            $return[$config->name] = $config->value;
        }
        return $return;
    }

    public function getCourseDiscounts()
    {
        $o1 = Options::where('location_id', Auth::user()->location_id)->where('name', '3-4_appointments_discount')->get()->first();
        $o2 = Options::where('location_id', Auth::user()->location_id)->where('name', '5-+_appointments_discount')->get()->first();
        $return = [['title' => '3 - 4 Appointments discount', 'value' => $o1->value],
                    ['title' => '5 or 5+ Appointments discount', 'value' => $o2->value]];
        return $return;
    }

    public function getCourseFormDiscounts()
    {
        $o1 = Options::where('location_id', Auth::user()->location_id)->where('name', '3-4_appointments_discount')->get()->first();
        $o2 = Options::where('location_id', Auth::user()->location_id)->where('name', '5-+_appointments_discount')->get()->first();
        $return = ['discount1' => str_replace('%', '', $o1->value), 'discount2' =>  str_replace('%', '', $o2->value)];
        return $return;
    }

}
