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
use App\Models\Appointments;
use App\Models\Reports;

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

    public function sendFeedbackSMS(Request $request)
    {
        $user = Users::where('id', $request->user_id)->get()->first();
        if($user->mobile):
            $this->sendSMS($user->mobile, $request->text);
            $year = '20'.substr($request->appointment_code, 1, 2);
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            $appointment = $table->where('appointment_code', $request->appointment_code)->get()->first();
            $appointment->update(['fsms' => date('Y-m-d H:i:s', time())]);
            return ['status' => 'success'];
        else:
            return ['status' => 'failed'];
        endif;
    }

    public function getReports()
    {
        return Reports::select('type', 'id', 'name', 'other_enabled')->where('status', 1)->orderBy('type', 'asc')->get();
    }

}
