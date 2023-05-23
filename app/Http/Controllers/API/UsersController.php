<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Users;
use App\Models\UserProfiles;
use App\Models\Locations;
use App\Models\UserDocuments;
use App\Models\FinancialYears;
use DB;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Users::select('users.*', 'user_profiles.first_name', 'user_profiles.last_name', 'user_profiles.date_of_birth as dob', 'user_profiles.gender as gender', 'user_profiles.email as email', 'user_profiles.city as city', 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'user_devices.device_type', 'user_documents.sign')
        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
        ->join('user_documents', 'user_documents.user_id', '=', 'users.id')
        ->leftjoin('user_devices', 'user_devices.user_id', '=', 'users.id')
        ->join('statuses', 'statuses.id', '=', 'users.status_id')
        ->where('users.status_id', '!=', 7)
        ->latest()->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'first_name' => ['required', 'string', 'max:100', 'regex:/^\S*$/u'],
            'last_name' => 'nullable',
            'mobile' => 'required|numeric|digits:9',
            'gender' => 'required|numeric|digits:1',
            'married' => 'nullable',
            'insurance_id' => 'required',
            'identity_type_id' => 'required',
            'nationality_id' => 'required',
        ]);
        $check = Users::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                        ->where('users.mobile', '971'.$request['mobile'])
                        ->where('user_profiles.first_name', $request['first_name'])
                        ->get()->first();
        if($check):
            return Response::json(['first_name' => 'Record exist - '.$check->username, 'mobile' => 'Record already exist'],422);
        endif;
        //return $request;
        $location = Locations::where('id', Auth::user()->location_id)->get()->first();
        $user = Users::create([
                'mobile' => '968'.$request['mobile'],
                'email' => $request['email'],
                'password' => Hash::make('968'.$request['mobile']),
                'username' => $this->userCodeGenerator(),
                'status_id' => 2,
                'admin_id' => Auth::user()->id,
                'source' => $location->clinic_name,
                'api_token' => Str::random(60)
            ]);
        $user_profile = UserProfiles::create([
                'user_id' => $user['id'],
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'date_of_birth' => $request['date_of_birth'],
                'gender' => $request['gender'],
                'email' => $request['email'],
                'contact_no' => $request['contact_no'],
                'company_name' => $request['company_name'],
                'address' => $request['address'],
                'city' => $request['city'],
                'married' => $request['married'],
                'zipcode' => $request['zipcode'],
                'nationality_id' => $request['nationality_id'],
                'country_id' => 1
            ]);

            if($request->insurance_copy && $request['policy_number']){
                $photo = strtolower($user['username']).'-policy-'.time().'.'.explode('/', explode(':', substr($request->insurance_copy, 0, strpos($request->insurance_copy, ';')))[1])[1];
                \Image::make($request->insurance_copy)->save(public_path('files/docs/').$photo);
            }
            else{
                $photo = '';
            }

            if($request->identity_copy && $request['verification_number']){
                $photo2 = strtolower($user['username']).'-id-'.time().'.'.explode('/', explode(':', substr($request->identity_copy, 0, strpos($request->identity_copy, ';')))[1])[1];
                \Image::make($request->identity_copy)->save(public_path('files/docs/').$photo2);
            }
            else{
                $photo2 = '';
            }

            /* if($request['concern_form']){
                $form = 'cform-'.strtolower($user['username']).'-'.time().'.'.explode('/', explode(':', substr($request->concern_form, 0, strpos($request->concern_form, ';')))[1])[1];
                \Image::make($request->concern_form)->save(public_path('files/docs/').$form);
                $cfdate = date('Y-m-d', time());
                $sts = 1;
            }
            else{ */
             //   $sts = '';
            //    $cfdate = '';
             //   $form = '';
            //}

            if($request['policy_number']): $iveryfiy = 1; else: $iveryfiy = 0; endif;
            if($request['verification_number']): $cid = 1; else: $cid = 0; endif;
        UserDocuments::create([
            'user_id' => $user['id'],
            'insurance_id' => $request['insurance_id'],
            'policy_number' => $request['policy_number'],
            'co_insurance' => $request['co_insurance'],
            'treatment_deductable' => $request['treatment_deductable'],
            'consult_deductable' => $request['consult_deductable'],
            'pharmacy_deductable' => $request['pharmacy_deductable'],
            'expiry_date' => $request['expiry_date'],
            'effective_date' => $request['effective_date'],
            'insurance_copy' => $photo,
            'insurance_verified' => $iveryfiy,
            'identity_type_id' => $request['identity_type_id'],
            'verification_number' => $request['verification_number'],
            'identity_copy' => $photo2,
            'identity_verified' => $cid,
         //   'concern_form' => $form,
         //   'concern_form_status' => $sts,
         //   'concern_form_verify_date' => $cfdate
        ]);
        return ['profile' => $user_profile];
    }

    public function show($id)
    {
        $customer = Users::select('user_profiles.*', 'user_documents.*', 'statuses.*', 'users.*', 'countries.country as country', 'insurances.name as insurancer', 'identity_types.value as id_type', 'nationalities.nationality as nationality', 'user_devices.device_type', 'user_documents.sign', 'user_documents.sign_date', 'admins.name as admin')
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->join('user_documents', 'user_documents.user_id', '=', 'users.id')
                    ->leftjoin('user_devices', 'user_devices.user_id', '=', 'users.id')
                    ->leftjoin('admins', 'admins.id', '=', 'users.admin_id')
                    ->leftjoin('statuses', 'statuses.id', '=', 'users.status_id')
                    ->leftjoin('countries', 'countries.id', '=', 'user_profiles.country_id')
                    ->leftjoin('insurances', 'insurances.id', '=', 'user_documents.insurance_id')
                    ->leftjoin('identity_types', 'identity_types.id', '=', 'user_documents.identity_type_id')
                    ->leftjoin('nationalities', 'user_profiles.nationality_id', '=', 'nationalities.id')
                    ->where('users.id', $id)
                    ->get();
        return $customer;
    }

    public function getProfile($id)
    {
        $customer = Users::select('users.*', 'user_profiles.first_name',
                                                'user_profiles.last_name',
                                                'user_profiles.date_of_birth as date_of_birth',
                                                'user_profiles.gender as gender',
                                                'user_profiles.email as email',
                                                'user_profiles.contact_no as contact_no',
                                                'user_profiles.address as address',
                                                'user_profiles.city as city',
                                                'user_profiles.married as married',
                                                'user_profiles.zipcode as zipcode',
                                                'nationalities.nationality as nationality',
                                                'user_profiles.country_id as country_id',
                                                'user_documents.insurance_id as insurance_id',
                                                'user_documents.policy_number as policy_number',
                                                'user_documents.insurance_copy as insurance_copy',
                                                'user_documents.identity_type_id as identity_type_id',
                                                'user_documents.verification_number as verification_number',
                                                'user_documents.pharmacy_deductable as pharmacy_deductable',
                                                'user_documents.consult_deductable as consult_deductable',
                                                'user_documents.treatment_deductable as treatment_deductable',
                                                'user_documents.co_insurance as co_insurance',
                                                'user_documents.effective_date as effective_date',
                                                'user_documents.expiry_date as expiry_date',
                                                'user_documents.identity_copy as identity_copy')
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->join('user_documents', 'user_documents.user_id', '=', 'users.id')
                    ->join('nationalities', 'user_profiles.nationality_id', '=', 'nationalities.id')
                    ->where('users.id', $id)
                    ->take(1)
                    ->get();
        return $customer;
    }

    public function update(Request $request, $id)
    {
        $user = Users::findOrFail($id);
        $this->validate($request, [
            'first_name' => 'required|string|max:190',
            'mobile' => 'required|numeric|digits:9',
            'gender' => 'required|numeric|digits:1',
            'married' => 'nullable',
            'insurance_id' => 'required',
            'identity_type_id' => 'required',
            'nationality_id' => 'required',
        ]);

        $check = Users::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                        ->where('users.mobile', '968'.$request['mobile'])
                        ->where('users.id', '!=', $id)
                        ->where('user_profiles.first_name', $request['first_name'])
                        ->get()->first();
        if($check):
            return Response::json(['first_name' => 'Record exist - '.$check->username, 'mobile' => 'Record already exist'],422);
        endif;

        $user = $user->update([
            'mobile' => '968'.$request['mobile'],
            'email' => $request['email']
        ]);

        $user_profile = UserProfiles::where('user_id', $id)->first();
        $user_profile =  $user_profile->update([
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'date_of_birth' => $request['date_of_birth'],
                'gender' => $request['gender'],
                'email' => $request['email'],
                'contact_no' => $request['contact_no'],
                'address' => $request['address'],
                'city' => $request['city'],
                'company_name' => $request['company_name'],
                //'married' => $request['married'],
                'zipcode' => $request['zipcode'],
                'nationality_id' => $request['nationality_id'],
                'country_id' => 1
            ]);


        $user_document = UserDocuments::where('user_id', $id)->first();
        $user = Users::whereId($id)->get()->first();
        if($request->insurance_copy && $request['policy_number']){
        	$ext = explode('/', explode(':', substr($request->insurance_copy, 0, strpos($request->insurance_copy, ';')))[1]);
        	//var_dump($user);
            $photo = strtolower($user['username']).'-'.time().'.'.$ext[1];
            \Image::make($request->insurance_copy)->save(public_path('files/docs/').$photo);
            $iveryfiy = 1;
        }
        else{
            $photo = $user_document->insurance_copy;
            $iveryfiy = $user_document->insurance_verified;
        }

        if($request->identity_copy && $request['verification_number']){
            $photo2 = strtolower($user['username']).'-'.time().'.'.explode('/', explode(':', substr($request->identity_copy, 0, strpos($request->identity_copy, ';')))[1])[1];
            \Image::make($request->identity_copy)->save(public_path('files/docs/').$photo2);
            $cid = 1;
        }
        else{
            $photo2 = $user_document->identity_copy;
            $cid = $user_document->identity_verified;
        }

        /* if($request['concern_form']){
            $form = 'cform-'.strtolower($user['username']).'-'.time().'.'.explode('/', explode(':', substr($request->concern_form, 0, strpos($request->concern_form, ';')))[1])[1];
            \Image::make($request->concern_form)->save(public_path('files/docs/').$form);
            $cfdate = date('Y-m-d', time());
            $sts = 1;
        }
        else{ */
         //   $sts = 1;
         //   $cfdate = $user_document->concern_form_status;
           // $form = '';
        //}

        if($request['insurance_id'] == 1):
            $user_document = $user_document->update([
                'insurance_id' => 1,
                'policy_number' => null,
                'co_insurance' => null,
                'treatment_deductable' => null,
                'consult_deductable' => null,
                'pharmacy_deductable' => null,
                'expiry_date' => null,
                'effective_date' => null,
                'insurance_copy' => null,
                'insurance_verified' => $iveryfiy,
                'identity_type_id' => $request['identity_type_id'],
                'verification_number' => $request['verification_number'],
                'identity_copy' => $photo2,
                'identity_verified' => $cid,
               // 'concern_form' => $form,
              //  'concern_form_status' => $sts,
               // 'concern_form_verify_date' => $cfdate
            ]);
        else:
            $user_document = $user_document->update([
                'insurance_id' => $request['insurance_id'],
                'policy_number' => $request['policy_number'],
                'co_insurance' => $request['co_insurance'],
                'treatment_deductable' => $request['treatment_deductable'],
                'consult_deductable' => $request['consult_deductable'],
                'pharmacy_deductable' => $request['pharmacy_deductable'],
                'expiry_date' => $request['expiry_date'],
                'effective_date' => $request['effective_date'],
                'insurance_copy' => $photo,
                'insurance_verified' => $iveryfiy,
                'identity_type_id' => $request['identity_type_id'],
                'verification_number' => $request['verification_number'],
                'identity_copy' => $photo2,
                'identity_verified' => $cid,
               // 'concern_form' => $form,
              //  'concern_form_status' => $sts,
               // 'concern_form_verify_date' => $cfdate
            ]);
        endif;
        return ['message' => 'User updated successylluy'];
    }

    public function destroy($id)
    {
        $user = Users::findOrFail($id);
        $user->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function search()
    {
        if($search = \Request::get('q')) {
            $users = Users::select('users.*', 'user_profiles.first_name', 'user_profiles.last_name', 'user_profiles.date_of_birth as dob', 'user_profiles.gender as gender', 'user_profiles.email as email', 'user_profiles.city as city', 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'user_devices.device_type', 'user_documents.sign')
            ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->join('user_documents', 'user_documents.user_id', '=', 'users.id')
            ->leftjoin('user_devices', 'user_devices.user_id', '=', 'users.id')
            ->join('statuses', 'statuses.id', '=', 'users.status_id')
            ->where(function($query) use ($search){
                $query->where('username','LIKE',"%$search%")->orwhere('mobile','LIKE',"%$search%")->orwhere('first_name','LIKE',"%$search%")->orwhere('last_name','LIKE',"%$search%")->orwhere('verification_number','LIKE',"%$search%")->orwhere('email','LIKE',"%$search%");
            })
            ->where('users.status_id', '!=', 7)
            ->latest()->paginate(15);
        }
        else{
           $users = Users::select('users.*', 'user_profiles.first_name', 'user_profiles.last_name', 'user_profiles.date_of_birth as dob', 'user_profiles.gender as gender', 'user_profiles.email as email', 'user_profiles.city as city', 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'user_devices.device_type', 'user_documents.sign')
           ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
           ->leftjoin('user_documents', 'user_documents.user_id', '=', 'users.id')
           ->leftjoin('user_devices', 'user_devices.user_id', '=', 'users.id')
           ->join('statuses', 'statuses.id', '=', 'users.status_id')
           ->where('users.status_id', '!=', 7)
           ->latest()->paginate(15);
        }
        return $users;
    }

    public function csearch(Request $request)
    {
        $this->validate($request, ['user_id' => 'required']);
        $search = $request['user_id'];
        $users = Users::select('users.*', 'user_profiles.first_name', 'user_profiles.last_name', 'user_profiles.date_of_birth as dob', 'user_profiles.gender as gender', 'user_profiles.email as email', 'user_profiles.city as city', 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css', 'user_devices.device_type', 'user_documents.sign')
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->leftjoin('user_devices', 'user_devices.user_id', '=', 'users.id')
                        ->leftjoin('user_documents', 'user_documents.user_id', '=', 'users.id')
                        ->where('users.status_id', '!=', 7)
                        ->join('statuses', 'statuses.id', '=', 'users.status_id')
                        ->where(function($query) use ($search){
                            $query->where('username','LIKE',"%$search%")->orwhere('mobile','LIKE',"%$search%")->orwhere('user_profiles.first_name','LIKE',"%$search%");
                        })->get();
        return $users;
    }

    public function find()
    {
        if($search = \Request::get('q')) {
            $users = Users::where(function($query) use ($search){
                $query->where('mobile','LIKE',"%$search%")->orwhere('username','LIKE',"%$search%");
            })->get();
        }
        else{
           $users = [];
        }
        $result = [];
        foreach($users as $user):
            $result[$user->id] = $user->mobile;
        endforeach;
        return $result;
    }

    public function findByMobile()
    {
        if($search = \Request::get('q')) {
            $user = Users::where(function($query) use ($search){
                $query->where('mobile','LIKE',"%$search%");
            })->get();
        }
        else{
           $user = [];
        }

        return $user;
    }

    public function mergerSearch(Request $request)
    {
        $conditions = [];

        if($request['username']): $conditions['users.username'] = $request['username']; endif;
        if($request['first_name']): $conditions['user_profiles.first_name'] = $request['first_name']; endif;
        if($request['mobile']): $conditions['users.mobile'] = $request['mobile']; endif;
        if($request['email']): $conditions['user_profiles.email'] = $request['email']; endif;

        return Users::select('users.*', 'user_profiles.first_name', 'user_profiles.last_name', 'user_profiles.date_of_birth as dob', 'user_profiles.gender as gender', 'user_profiles.email as email', 'user_profiles.city as city', 'statuses.status as status', 'statuses.text as status_text', 'statuses.css as status_css')
                    ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                    ->join('user_documents', 'user_documents.user_id', '=', 'users.id')
                    ->join('statuses', 'statuses.id', '=', 'users.status_id')
                    ->where($conditions)
                    ->where('users.status_id', '!=', 7)
                    ->get();
    }

    public function mergeRecords(Request $request)
    {
        $years = FinancialYears::all();
        foreach ($request->all_users as $key => $user) {
            if($user != $request->primary_user):
                foreach ($years as $key => $year) {
                    DB::table('appointment'.$year->year.'s')
                            ->where('user_id', $user)
                            ->update(['user_id' => $request->primary_user]);
                    DB::table('invoice'.$year->year.'s')
                            ->where('user_id', $user)
                            ->update(['user_id' => $request->primary_user]);
                    DB::table('course'.$year->year.'s')
                            ->where('user_id', $user)
                            ->update(['user_id' => $request->primary_user]);
                }
                DB::table('users')->where('id', $user)->delete();
                DB::table('user_profiles')->where('user_id', $user)->delete();
                DB::table('user_documents')->where('user_id', $user)->delete();
                DB::table('user_devices')->where('user_id', $user)->delete();
            endif;
        }
        return ['merging done'];
    }

    private function userCodeGenerator()
    {
        $from = date('Y-01-01', time());
        $to = date('Y-12-31', time());;
        $user = Users::whereYear('created_at', date('Y', time()))->latest()->get()->first();
        if($user):
            $linv = substr($user->username, 3);
            $counter = intval($linv)+1;
        else: $counter = 1; endif;
        $number = 'S'.date('y').str_pad($counter, 4, "0",STR_PAD_LEFT);
        return $number;
    }
}
