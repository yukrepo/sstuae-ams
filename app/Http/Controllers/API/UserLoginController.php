<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Models\UserProfiles;
use App\Models\UserDocuments;
use App\Models\UserDevices;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Models\Nationalities;

header('Access-Control-Allow-Origin:*');

class UserLoginController extends Controller
{

    function index(Request $request)
    {
        $user = Users::where('username', $request->username)->where('status_id', 2)->get()->first();
        if ($user) {
            if (!\Hash::check($request->password, $user->password)) {
                return [ 'status' => 'error', 'message' => 'You have entered wrong password'];
            }
            if($user->api_token == null) $user->setNewApiToken();
            $this->__deviceCheck($request->deviceId, $request->device_type, $request->os, $request->model,  $request->app_version, $user->id, $request->fcm_token);
            $customer = Users::with('user_profile')->with('user_document')->with('user_device')->where('username', $request->username)->get()->first();;
            return ['status' => 'success', 'message' => 'You logged in successfully.', 'data' => ['customer' => $customer] ];
        }
        else{
            return [ 'status' => 'error', 'message' => 'Mobile number is not registered with us. Please contact administrator.'];
        }
    }

    function mobileLogin(Request $request)
    {
        $user = Users::where('mobile', $request->mobile)->where('status_id', 2)->get()->first();
        if ($user) {
            if (!\Hash::check($request->password, $user->password)) {
                return [ 'status' => 'error', 'message' => 'You have entered wrong password'];
            }
            if($user->api_token == null) $user->setNewApiToken();
            $this->__deviceCheck($request->deviceId, $request->device_type, $request->os, $request->model,  $request->app_version, $user->id, $request->fcm_token);
            $customer = Users::with('user_profile')->with('user_document')->with('user_device')->where('id', $user->id)->get()->first();
            return ['status' => 'success', 'message' => 'You logged in successfully.', 'data' => ['customer' => $customer] ];
        }
        else{
            return [ 'status' => 'error', 'message' => 'Mobile number is not registered with us. Please contact administrator.'];
        }
    }

    function switchLogin(Request $request)
    {
        $user = Users::where('mobile', $request->mobile)->where('mobile', $request->mobile)->where('status_id', 2)->get()->first();
        if ($user) {
            if (!\Hash::check($request->password, $user->password)) {
                return [ 'status' => 'error', 'message' => 'You have entered wrong password'];
            }
            if($user->api_token == null) $user->setNewApiToken();
            $this->__deviceCheck($request->deviceId, $request->device_type, $request->os, $request->model,  $request->app_version, $user->id, $request->fcm_token);
            $customer = Users::with('user_profile')->with('user_document')->with('user_device')->where('mobile', $user->mobile)->get()->first();;
            return ['status' => 'success', 'message' => 'You logged in successfully.', 'data' => ['customer' => $customer] ];
        }
        else{
            return [ 'status' => 'error', 'message' => 'Invalid user ID. Please contact administrator.'];
        }
    }

    public function forgotPasswordOtp(Request $request)
    {
        $user = Users::where('mobile', $request->mobile)->where('status_id', 2)->get()->first();
        if ($user) {
            $otp = rand(100000, 999999);
            $message = $otp.' is your OTP to recover your password for Sri Sri Tattva Login.';
            $this->sendSMS($user->mobile, $message);
            //if($response['err']): return ['status' => 'error', 'message' => $response['err']];
            //else:
            return ['status' => 'success', 'message' => 'An OTP has been send to your registered mobile number', 'data' => ['otp' => $otp] ];
            //endif;
        }
        else{
            return [ 'status' => 'error', 'message' => 'Mobile number is not registered with us. Please contact administrator.'];
        }
    }

    public function updatePassword(Request $request)
    {
        $user = Users::where('mobile', $request->mobile)->where('status_id', 2)->get()->first();
        if ($user) {
            $user->update(['password' => Hash::make($request['new_password'])]);

            return ['status' => 'success', 'message' => 'Your password has been updated successfully. Please login now.'];
        }
        else{
            return [ 'status' => 'error', 'message' => 'Mobile number is not registered with us. Please contact administrator.'];
        }
    }

    private function __deviceCheck($did = null, $dtype = null, $os = null, $model = null, $app_version = null, $uid = null,  $fcstoken = null)
    {
    	$check = UserDevices::where('user_id', $uid)->get()->first();
    	if($check):
    		UserDevices::where('user_id', $uid)->update([
                                        'token' => $fcstoken,
                                        'deviceId' => $did,
                                        'os' => $os,
                                        'model' => $model,
                                        'app_version' => $app_version,
                                        'device_type' => $dtype]);
    	else:
    		UserDevices::create([
                'token' => $fcstoken,
                'deviceId' => $did,
                'user_id' => $uid,
                'notification' => 1,
                'os' => $os,
                'model' => $model,
                'app_version' => $app_version,
                'device_type' => $dtype]);
    	endif;
    	return true;
    }

    public function OtpCheckForRegister(Request $request) {
        $user = Users::where('mobile', $request->mobile)->get()->first();
        if($user):
            $message = 'You are already registered with us. Please call on +968 24484991 or whatsapp us on +968 99314166 for Your Login ID.';
            return ['status' => 'error', 'message' => $message ];
        else:
            $otp = rand(100000, 999999);
            $message = $otp.' is your OTP for registration for Sri Sri Tattva Login.';
            $this->sendSMS($request->mobile, $message);
           // if($response['err']): return ['status' => 'error', 'message' => $response['err']];
           // else:
            return ['status' => 'success', 'message' => 'An OTP has been send to your registered mobile number', 'data' => ['otp' => $otp] ];
            //endif;
        endif;
    }

    public function register(Request $request) {
        $usercode = $this->userCodeGenerator();

        $user = Users::create([
            'mobile' => $request['mobile'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'username' => $usercode,
            'status_id' => 2,
            'source' => $request['source'],
            'api_token' => Str::random(60)
        ]);
        $user_profile = UserProfiles::create([
            'user_id' => $user['id'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'date_of_birth' => date('Y-m-d', strtotime($request['date_of_birth'])),
            'email' => $request['email'],
            'address' => $request['address'],
            'city' => $request['city'],
            'nationality_id' => ($request['nationality_id'] >= 1)?$request['nationality_id']:142,
            'country_id' => 1
        ]);

        UserDocuments::create([
            'user_id' => $user['id'],
            'insurance_id' => 1,
            'insurance_verified' => 1,
            'identity_type_id' => 1,
            'identity_verified' => 0,
        ]);

        $message = 'Welcome to Sri Sri Tattva Panchakarma, Your login ID is '.$usercode.' and password as you have entered.';
        $this->sendSMS($request['mobile'], $message);

        return ['status' => 'success', 'message' => 'Your registration has been completed successfully. Your login details will be send to you by SMS.'];
    }

    public function webRegister(Request $request) {
        $usercode = $this->userCodeGenerator();
        $request->mobile = str_replace('-', '', $request->mobile);
        $request->contact_no = str_replace('-', '', $request->contact_no);
        $register = $this->validate($request, [
            'first_name' => 'required|string|max:190',
            'last_name' => 'nullable',
            'mobile' => 'required|numeric|digits:11',
            'gender' => 'required|numeric|digits:1',
            'married' => 'nullable',
            'insurance_id' => 'required',
            'identity_type_id' => 'required',
            'nationality_id' => 'required',
        ]);
        return $register->errors();
        $user = Users::create([
            'mobile' => $request['mobile'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'username' => $usercode,
            'status_id' => 2,
            'source' => 'web',
            'api_token' => Str::random(60)
        ]);
        $user_profile = UserProfiles::create([
            'user_id' => $user['id'],
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'date_of_birth' => date('Y-m-d', strtotime($request['date_of_birth'])),
            'gender' => $request['gender'],
            'email' => $request['email'],
            'address' => $request['address'],
            'city' => $request['city'],
            'nationality_id' => ($request['nationality_id'] >= 1)?$request['nationality_id']:142,
            'country_id' => 1
        ]);

        UserDocuments::create([
            'user_id' => $user['id'],
            'insurance_id' => 1,
            'insurance_verified' => 1,
            'identity_type_id' => 1,
            'identity_verified' => 0,
        ]);

        $message = 'Welcome to Sri Sri Tattva Panchakarma, Your login ID is '.$usercode.' and password as you have entered.';
        $this->sendSMS($request['mobile'], $message);

        return ['status' => 'success', 'message' => 'Your registration has been completed successfully. Your login details will be send to you by SMS.'];
    }

    private function userCodeGenerator()
    {
        $from = date('Y-01-01', time());
        $to = date('Y-12-31', time());;
        $user = Users::whereBetween('created_at', [$from, $to])->latest()->take(1)->get();
        if($user->count()):
            $linv = substr($user[0]->username, 3);
            $counter = intval($linv)+1;
        else: $counter = 1; endif;
        $number = 'S'.date('y').str_pad($counter, 4, "0",STR_PAD_LEFT);
        return $number;
    }

    public function getNationalities(Request $request)
    {
        $nationalities = Nationalities::where('id', '<=', 200)->get();
        $response = ['data' => ['nationalities' => $nationalities], 'status' => 'success', 'message' => 'Nationalities list has been fetched.'];
        return $response;
    }

    public function findCustomer(Request $request)
    {
        $user = Users::where('username', $request->username)->where('status_id', 2)->get()->first();
        if ($user) {
            $customer = Users::with('user_profile')->with('user_document')->with('user_device')->where('username', $request->username)->get()->first();
            return ['status' => 'success', 'message' => 'You logged in successfully.', 'data' => ['customer' => $customer] ];
        }
        else{
            return [ 'status' => 'error', 'message' => 'Invalid user ID. Please contact administrator.'];
        }

    }

}
