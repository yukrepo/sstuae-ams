<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Models\Locations;
use App\Models\UserProfiles;
use App\Models\Appointments;
use App\Models\AppointmentQueries;
use App\Models\UserDocuments;
use App\Models\Insurances;
use App\Models\FinancialYears;
use App\Models\Nationalities;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\UserDevices;
use App\Mail\SendMail;

header('Access-Control-Allow-Origin:*');

class FrontController extends Controller
{
    private $userdata, $all_financial_years;

    public function __construct(Request $request)
    {
        $this->userdata = Users::with('user_profile')
                                    ->with('user_document')
                                    ->with('user_device')
                                    ->where('users.api_token', $request->api_token)
                                    ->get()
                                    ->first();

        $this->all_financial_years = FinancialYears::select('year')->orderBy('year', 'asc')->get();
    }

    public function dashboard()
    {
        $status = 'success';
        $message = 'Dashboard data has been received successfully.';
        $uappoints = 0; $happoints = 0; $lappointments = [];
        foreach($this->all_financial_years as $year) {
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year->year.'s');
            $tablename = $table->getTable();
            $lappointments[] = $table->select("$tablename.date", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot", 'statuses.status as status', 'statuses.css as status_css', 'appointment_types.appointment_type as appointment_type',  'treatments.treatment as reason')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->with('start_time')->with('end_time')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where("$tablename.user_id", $this->userdata->id)
                        ->whereYear('date', $year->year)
                        ->where('date', '>=', date('Y-m-d', time()))
                        ->where('date', '<=', date('Y-m-d', strtotime('+7 days')))
                        ->whereNotIn("$tablename.status_id", [1,3,7,11,12])
                        ->orderBy('date', 'asc')->get();

            $acount = $table->where("$tablename.user_id", $this->userdata->id)
            ->whereIn("$tablename.status_id", [2,4,5,8,9,10])->whereYear('date', $year->year)
            ->where('date', '>=', date('Y-m-d', time()))->get()->count();
            $uappoints = $uappoints + $acount;

            $hcount = $table->where("$tablename.user_id", $this->userdata->id)
            ->whereIn("$tablename.status_id", [2,4,5,8,9,10])->whereYear('date', $year->year)
            ->where('date', '<', date('Y-m-d', time()))->get()->count();
            $happoints = $happoints + $hcount;
        }
        $relatives = Users::with('user_profile')->where('id', '!=', $this->userdata->id)->where('mobile', $this->userdata->mobile)->where('status_id', 2)->get();
        $data = ['offer' => 'offer1.jpeg', 'uappointments' => $uappoints, 'happointments' => $happoints, 'upcoming_appointments' => $lappointments, 'relatives' => $relatives];
        $response = ['status' => $status, 'message' => $message, 'data' => $data];
        return $response;
    }

    function switchLogin(Request $request)
    {
        $user = Users::where('username', $request->username)->where('mobile', $this->userdata->mobile)->where('status_id', 2)->get()->first();
        if ($user) {
            if($user->api_token == null) $user->setNewApiToken();
            $this->__deviceUpdate($user->id, $request->deviceId, $request->device_type, $request->os, $request->model,  $request->app_version, $this->userdata->id, $request->fcm_token);
            $customer = Users::with('user_profile')->with('user_document')->with('user_device')->where('username', $user->username)->get()->first();;
            return ['status' => 'success', 'message' => 'You logged in successfully.', 'data' => ['customer' => $customer] ];
        }
        else{
            return [ 'status' => 'error', 'message' => 'Invalid user ID. Please contact administrator.'];
        }
    }

    public function data()
    {
        $status = 'success';
        $message = 'User details has been received successfully.';
        $data = ['customer' => $this->userdata];
        $response = ['status' => $status, 'message' => $message, 'data' => $data];
        return $response;
    }

    public function appointments()
    {
        $status = 'success';
        $message = 'Appointments have been fetched successfully.';
        $uappointments = []; $happointments = [];
        foreach($this->all_financial_years as $year) {
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year->year.'s');
            $tablename = $table->getTable();
            $uappointments[] = $table->select("$tablename.date", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot", 'statuses.status as status', 'statuses.css as status_css', 'appointment_types.appointment_type as appointment_type',  'treatments.treatment as reason')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->with('start_time')->with('end_time')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where("$tablename.user_id", $this->userdata->id)
                        ->whereYear('date', $year->year)
                        ->where('date', '>=', date('Y-m-d', time()))
                        ->whereNotIn("$tablename.status_id", [1,3,7,11,12])
                        ->orderBy('date', 'asc')->get();
            $happointments[] = $table->select("$tablename.date", "$tablename.appointment_code", "$tablename.start_timeslot", "$tablename.end_timeslot", 'statuses.status as status', 'statuses.css as status_css', 'appointment_types.appointment_type as appointment_type',  'treatments.treatment as reason')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->with('start_time')->with('end_time')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where("$tablename.user_id", $this->userdata->id)
                        ->where('date', '<', date('Y-m-d', time()))
                        ->whereNotIn("$tablename.status_id", [1,3,7,11,12])
                        ->orderBy('date', 'asc')->get();
        }
        $queries = AppointmentQueries::whereIn('status_id', [1,2,4,5])
        							->where('user_id', $this->userdata->id)->orderBy('created_at', 'desc')->get();
        $data = ['queries' => $queries, 'happointments' => $happointments, 'uappointments' => $uappointments];
        $response = ['status' => $status, 'message' => $message, 'data' => $data];
        return $response;
    }

    public function viewAppointment(Request $request)
    {
        $status = 'success';
        $message = 'Appointment detail has been fetched successfully.';
        $year = '20'.substr($request['appointment_code'], 1, 2);
        $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();
            $appointment = $table->select("$tablename.*", 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'doctors.name as doctor', 'treatments.treatment as reason')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->with('start_time')->with('end_time')
                        ->where('appointment_code', $request['appointment_code'])
                        ->get();
            $appointment[0]['iinvoice'] = ''; $appointment[0]['cinvoice'] = '';
            if($appointment[0]['pinvoice']):
                $invoices = explode(',', $appointment[0]['pinvoice']);
                array_filter($invoices);
                if(count($invoices) == 1):
                    $type = substr($appointment[0]['pinvoice'], 0, 2);
                    if($type == 'IM'): $appointment[0]['iinvoice'] = $appointment[0]['pinvoice'];
                    elseif($type == 'CM'): $appointment[0]['cinvoice'] = $appointment[0]['pinvoice']; endif;
                else:
                    $cpos = strpos($appointment[0]['pinvoice'], 'CM');
                    if($cpos >= 0)
                    $appointment[0]['cinvoice'] = substr($appointment[0]['pinvoice'], strpos($appointment[0]['pinvoice'], 'CM'), 12);
                    $ipos = strpos($appointment[0]['pinvoice'], 'IM');
                    if($ipos >= 0)
                    $appointment[0]['iinvoice'] = substr($appointment[0]['pinvoice'], strpos($appointment[0]['pinvoice'], 'IM'), 12);
                endif;

            else: $appointment[0]['iinvoice'] = ''; $appointment[0]['cinvoice'] = ''; endif;
        $data = ['appointment' => $appointment[0]];
        $response = ['status' => $status, 'message' => $message, 'data' => $data];
        return $response;
    }

    public function updateProfilePic(Request $request)
    {
        if($request->image){
            $image = $request->file('image');
            $photo = strtolower($this->userdata->username).'-'.time().'.'.$image->getClientOriginalExtension();
            \Image::make($request->image)->save(public_path('images/profile/').$photo);
        }
        else{
            $photo = 'avtar.jpg';
        }
        UserProfiles::where('user_id', $this->userdata->id)->update(['image' => $photo]);
        $customer = Users::with('user_profile')->with('user_document')->with('user_device')->where('users.api_token', $request->api_token)->get()->first();
        $response = ['status' => 'success', 'message' => 'Profile Image has been updated successfully.', 'data' => ['customer' => $customer]];
        return $response;
    }

    public function updateCompleteProfile(Request $request)
    {
        UserProfiles::where('user_id', $this->userdata->id)
                        ->update(['first_name' => $request['first_name'],
                                'last_name' => $request['last_name'],
                                'date_of_birth' => date('Y-m-d', strtotime($request['date_of_birth'])),
                                'gender' => $request['gender'],
                                'email' => $request['email'],
                                'contact_no' => $request['contact_no'],
                                'company_name' => $request['company_name'],
                                'address' => $request['address'],
                                'city' => $request['city'],
                                'married' => $request['married'],
                                'zipcode' => $request['zipcode'],
                                'nationality_id' => $request['nationality_id']]);
        if($request->insurance_id == 1):
            UserDocuments::where('user_id', $this->userdata->id)
                                ->update(['insurance_id' => 1,
                                            'policy_number' => '',
                                            'insurance_verified' => 1,
                                            'insurance_copy' => '',
                                            'effective_date' => '',
                                            'consult_deductable' => 0,
                                            'co_insurance' => 0,
                                            'expiry_date' => '']);
        elseif($request->insurance_copy):
            $image = $request->file('insurance_copy');
            $photo = strtolower($this->userdata->username.'-'.$request->policy_number).'-'.time().'.'.$image->getClientOriginalExtension();
            \Image::make($request->insurance_copy)->save(public_path('files/docs/').$photo);

            UserDocuments::where('user_id', $this->userdata->id)
                            ->update(['insurance_id' => $request->insurance_id,
                                'policy_number' => $request->policy_number,
                                'insurance_verified' => 0,
                                'insurance_copy' => $photo,
                                'effective_date' => date('Y-m-d', strtotime($request->effective_date)),
                                'consult_deductable' => $request->consult_deductable,
                                'co_insurance' => $request->co_insurance,
                                'expiry_date' => date('Y-m-d', strtotime($request->expiry_date)) ]);


        else:
            UserDocuments::where('user_id', $this->userdata->id)
                            ->update(['insurance_id' => $request->insurance_id,
                                    'policy_number' => $request->policy_number,
                                    'insurance_verified' => 0,
                                    'effective_date' => date('Y-m-d', strtotime($request->effective_date)),
                                    'consult_deductable' => $request->consult_deductable,
                                    'co_insurance' => $request->co_insurance,
                                    'expiry_date' => date('Y-m-d', strtotime($request->expiry_date)) ]);

        endif;
        if($request->identity_copy):
            $image = $request->file('identity_copy');
            $photo = strtolower($this->userdata->username.'-'.$request->verification_number).'-'.time().'.'.$image->getClientOriginalExtension();
            \Image::make($request->identity_copy)->save(public_path('files/docs/').$photo);

            UserDocuments::where('user_id', $this->userdata->id)
                            ->update(['identity_type_id' => $request->identity_type_id,
                                'verification_number' => $request->verification_number,
                                'identity_verified' => 0,
                                'identity_copy' => $photo ]);


        else:
            UserDocuments::where('user_id', $this->userdata->id)
                            ->update(['identity_type_id' => $request->identity_type_id,
                                    'verification_number' => $request->verification_number,
                                    'identity_verified' => 0 ]);

        endif;
        $customer = Users::with('user_profile')->with('user_document')->with('user_device')->where('users.api_token', $request->api_token)->get()->first();
        $response = ['status' => 'success', 'message' => 'Profile details have been updated successfully.', 'data' => ['customer' => $customer]];
        return $response;
    }

    public function updateProfile(Request $request)
    {
        UserProfiles::where('user_id', $this->userdata->id)
                        ->update(['first_name' => $request['first_name'],
                                'last_name' => $request['last_name'],
                                'date_of_birth' => date('Y-m-d', strtotime($request['date_of_birth'])),
                                'gender' => $request['gender'],
                                'email' => $request['email'],
                                'contact_no' => $request['contact_no'],
                                'company_name' => $request['company_name'],
                                'address' => $request['address'],
                                'city' => $request['city'],
                                'married' => $request['married'],
                                'zipcode' => $request['zipcode'],
                                'nationality_id' => $request['nationality_id']]);

        $customer = Users::with('user_profile')->with('user_document')->with('user_device')->where('users.api_token', $request->api_token)->get()->first();
        $response = ['status' => 'success', 'message' => 'Profile details have been updated successfully.', 'data' => ['customer' => $customer]];
        return $response;
    }

    public function updateInsurance(Request $request)
    {
        if($request->insurance_id == 1):
            UserDocuments::where('user_id', $this->userdata->id)
                                ->update(['insurance_id' => 1,
                                            'policy_number' => '',
                                            'insurance_verified' => 1,
                                            'insurance_copy' => '',
                                            'effective_date' => '',
                                            'consult_deductable' => 0,
                                            'co_insurance' => 0,
                                            'expiry_date' => '']);
        elseif($request->insurance_copy):
            $image = $request->file('insurance_copy');
            $photo = strtolower($this->userdata->username.'-'.$request->policy_number).'-'.time().'.'.$image->getClientOriginalExtension();
            \Image::make($request->insurance_copy)->save(public_path('files/docs/').$photo);

            UserDocuments::where('user_id', $this->userdata->id)
                            ->update(['insurance_id' => $request->insurance_id,
                                'policy_number' => $request->policy_number,
                                'insurance_verified' => 0,
                                'insurance_copy' => $photo,
                                'effective_date' => date('Y-m-d', strtotime($request->effective_date)),
                                'consult_deductable' => $request->consult_deductable,
                                'co_insurance' => $request->co_insurance,
                                'expiry_date' => date('Y-m-d', strtotime($request->expiry_date)) ]);


        else:
            UserDocuments::where('user_id', $this->userdata->id)
                            ->update(['insurance_id' => $request->insurance_id,
                                    'policy_number' => $request->policy_number,
                                    'insurance_verified' => 0,
                                    'effective_date' => date('Y-m-d', strtotime($request->effective_date)),
                                    'consult_deductable' => $request->consult_deductable,
                                    'co_insurance' => $request->co_insurance,
                                    'expiry_date' => date('Y-m-d', strtotime($request->expiry_date)) ]);

        endif;
        $customer = Users::with('user_profile')->with('user_document')->with('user_device')->where('users.api_token', $request->api_token)->get()->first();
        $response = ['status' => 'success', 'message' => 'Profile details have been updated successfully.', 'data' => ['customer' => $customer]];
        return $response;
    }

    public function updateDocument(Request $request)
    {
        if($request->identity_copy):
            $image = $request->file('identity_copy');
            $photo = strtolower($this->userdata->username.'-'.$request->verification_number).'-'.time().'.'.$image->getClientOriginalExtension();
            \Image::make($request->identity_copy)->save(public_path('files/docs/').$photo);

            UserDocuments::where('user_id', $this->userdata->id)
                            ->update(['identity_type_id' => $request->identity_type_id,
                                'verification_number' => $request->verification_number,
                                'identity_verified' => 0,
                                'identity_copy' => $photo ]);


        else:
            UserDocuments::where('user_id', $this->userdata->id)
                            ->update(['identity_type_id' => $request->identity_type_id,
                                    'verification_number' => $request->verification_number,
                                    'identity_verified' => 0 ]);

        endif;
        $customer = Users::with('user_profile')->with('user_document')->with('user_device')->where('users.api_token', $request->api_token)->get()->first();
        $response = ['status' => 'success', 'message' => 'Profile details have been updated successfully.', 'data' => ['customer' => $customer]];
        return $response;
    }

    public function postAppointment(Request $request)
    {
        AppointmentQueries::create([
            'appointment_type' => $request['appointment_type'],
            'appointment_reason' => $request['appointment_reason'],
            'date' => $request['date'],
            'user_id' => $this->userdata->id,
            'location_id' => $request['location_id'],
            'status_id' => 1,
            'timeframe' => $request['timeframe'],
            'remark' => $request['remark'],
            'doctor_name' => $request['doctor_name']
        ]);
        $response = ['status' => 'success', 'message' => 'Appointment query has been added successfully. One of our executive will contact you shortly.'];
        return $response;
    }



    public function getInsurances(Request $request)
    {
        $insurances = Insurances::where('status_id', 2)->get();
        $response = ['data' => ['insurances' => $insurances], 'status' => 'success', 'message' => 'Insurances list has been fetched.'];
        return $response;
    }

	public function getLocations(Request $request)
    {
        $locations = Locations::where('status_id', 2)->get();
        $response = ['data' => ['locations' => $locations], 'status' => 'success', 'message' => 'Locations list has been fetched.'];
        return $response;
    }

    public function changePassword(Request $request)
    {
        $user = Users::findOrFail($this->userdata->id);
        if (!\Hash::check($request['old_password'], $user->password)) {
            $response = ['status' => 'error', 'message' => 'Current Password is not correct.'];
            return $response;
        }

        $user->update(['password' => Hash::make($request['new_password'])]);

        $customer = Users::with('user_profile')->with('user_document')->with('user_device')->where('users.api_token', $request->api_token)->get()->first();
        $response = ['status' => 'success', 'message' => 'Password has been updated successfully.', 'data' => ['customer' => $customer]];
        return $response;
    }

    public function toggleNotification(Request $request)
    {
        if($request['notification'] == 1):
            UserDevices::where('user_id', $this->userdata->id)
                    ->update(['token' => $request['fcm_token'], 'notification' => 1 ]);
        else:
            UserDevices::where('user_id', $this->userdata->id)
                    ->update(['token' => $request['fcm_token'], 'notification' => 0 ]);
        endif;
        $customer = Users::with('user_profile')->with('user_document')->with('user_device')->where('users.api_token', $request->api_token)->get()->first();
        $response = ['status' => 'success', 'message' => 'Notification settings have been updated successfully.', 'data' => ['customer' => $customer]];
        return $response;
    }

    public function updateToken(Request $request)
    {
        UserDevices::where('user_id', $this->userdata->id)
                    ->update(['token' => $request['fcm_token'],
                                'deviceId' => $request['deviceId'],
                                'os' => $request['os'],
                                'model' => $request['model'],
                                'app_version' => $request['app_version'],
                                'device_type' => $request['device_type'] ]);
        $customer = Users::with('user_profile')->with('user_document')->with('user_device')->where('users.api_token', $request->api_token)->get()->first();
        $response = ['status' => 'success', 'message' => 'Token has been updated successfully.', 'data' => ['customer' => $customer]];
        return $response;
    }

	public function emailInvoice(Request $request)
    {
        if($this->userdata->user_profile->email):
            $encoded_link = ['time' => time(),
                            'user_id' => $this->userdata->id,
                            'invoice_number' => $request->invoice_number];
            $data = ['subject' => 'Invoice Request For Invoice No :'.$request->invoice_number,
                        'view' => 'invoice_mail',
                        'invoice_number' => $request->invoice_number,
                        'name' => $this->userdata->user_profile->first_name.' '.$this->userdata->user_profile->last_name,
                        'link' => '/print/invoice-view/'.base64_encode(json_encode($encoded_link))];
            Mail::to($this->userdata->email)->send(new SendMail($data));

			$response = ['status' => 'success', 'message' => 'Invoice has been emailed on your registered email Id.'];
		else:
			$response = ['status' => 'error', 'message' => 'To get invoice, Please update email in your profile.'];
		endif;
        return $response;
    }

	public function emailPrescription(Request $request)
    {
        if($this->userdata->user_profile->email):
            $encoded_link = ['time' => time(),
                            'user_id' => $this->userdata->id,
                            'appointment_id' => $request->appointment_code];
            $data = ['subject' => 'Prescription Request For Appointment ID :'.$request->appointment_code,
                        'view' => 'prescription_mail',
                        'apcode' => $request->appointment_code,
                        'name' => $this->userdata->user_profile->first_name.' '.$this->userdata->user_profile->last_name,
                        'link' => '/print/prescription-view/'.base64_encode(json_encode($encoded_link))];
            Mail::to($this->userdata->email)->send(new SendMail($data));
			$response = ['status' => 'success', 'message' => 'Prescription has been emailed on your registered email Id.'];
		else:
			$response = ['status' => 'error', 'message' => 'To get Prescription, Please update email in your profile.'];
		endif;
        return $response;
    }

    private function __deviceUpdate($nuid = null, $did = null, $dtype = null, $os = null, $model = null, $app_version = null, $uid = null,  $fcstoken = null)
    {
        $check = UserDevices::where('user_id', $uid)->get()->first();
        if($check):
            $check->update(['user_id', $nuid]);
        else:
            UserDevices::create([
                'token' => $fcstoken,
                'deviceId' => $did,
                'user_id' => $nuid,
                'notification' => 1,
                'os' => $os,
                'model' => $model,
                'app_version' => $app_version,
                'device_type' => $dtype]);
        endif;
        return true;
    }

}
