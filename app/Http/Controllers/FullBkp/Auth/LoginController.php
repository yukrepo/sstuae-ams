<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Locations;
use App\Models\Admins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $credentials = ['username' => $request->username, 'password' => $request->password, 'location_id' => $request->location_id, 'publish' => 1, 'status' => 1];
        if($request->remember): $remember = 1; else: $remember = ''; endif;
        if (Auth::attempt($credentials, $remember)) {
            return redirect()->intended($this->redirectPath());
        }
        else{
            return back()->withErrors(['username' => 'Invalid user ID or password']);
        }
    }

    public function switchAdmin(Request $request)
    {
        $id = Auth::user();
        $admin = Admins::whereId($id->id)->get()->first();
        $admin->update(['location_id' => $request->location]);
        Auth::logout();
        Auth::login($admin);
        return redirect('/settings/profile');
    }
}
