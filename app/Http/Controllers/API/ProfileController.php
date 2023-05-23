<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Models\Admins;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getData()
    {
        $settings = Settings::get();
        $setting = [];
        foreach($settings as $value):
            $setting[$value->name] = $value->value;
        endforeach;

        return $setting;
    }

    public function getActiveUser()
    {
       return Admins::select('admins.*', 'locations.clinic_name as location', 'admin_types.type as role', 'admin_types.permissions as permissions')
                ->join('locations', 'locations.id', '=', 'admins.location_id')
                ->join('admin_types', 'admin_types.id', '=', 'admins.admin_type_id')
                ->where('admins.id', Auth::user()->id)
                ->first();
    }

    public function updatePassword(Request $request)
    {
        $user = Admins::findOrFail(Auth::user()->id);
        $this->validate($request, [
            'old_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!\Hash::check($value, $user->password)) {
                    return $fail(__('Current password is incorrect.'));
                }
            }],
            'new_password' => 'min:6|required',
            'confirm_password' => 'min:6|required_with:new_password|same:new_password'
        ]);

        $user->update(['password' => Hash::make($request['new_password'])]);
        return ['message' => 'updatng data'];
    }
}
