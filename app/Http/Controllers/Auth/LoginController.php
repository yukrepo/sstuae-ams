<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Locations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admins;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        //dd(session()); die;
        $locations = Locations::all()->where('status_id', 2);
        return view('auth/login', compact('locations'));
    }

    public function login(Request $request)
    {
        //echo $request->username; die;
        if($request->username == 'admin'):
            $credentials = ['username' => $request->username, 'password' => $request->password, 'location_id' => 1, 'publish' => 1, 'status' => 1];
        elseif($request->username == 'develop'):
            $credentials = ['username' => $request->username, 'password' => $request->password, 'location_id' => 1, 'publish' => 1, 'status' => 1];
        else:
            $credentials = ['username' => $request->username, 'password' => $request->password, 'location_id' => 1, 'publish' => 1, 'status' => 1];
        endif;
        
        //$credentials = ['username' => $request->username, 'password' => $request->password, 'location_id' => $request->location_id, 'publish' => 1, 'status' => 1];
        
        if($request->remember): $remember = 1; else: $remember = ''; endif;
        
        if (Auth::attempt($credentials, $remember)) {
            return redirect()->intended($this->redirectPath());
        }
        else{
            return back()->withErrors(['username' => 'Invalid user ID or password']);
        }
    }
}
