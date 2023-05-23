<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admins;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AdminsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Admins::select('admins.*', 'locations.clinic_name as location', 'admin_types.type as admin_type')
                    ->join('locations', 'locations.id', '=', 'admins.location_id')
                    ->join('admin_types', 'admin_types.id', '=', 'admins.admin_type_id')
                    ->where('admins.status', 1)
                    ->where('admins.id', '!=', 2)
                    ->orderBy('admins.created_at', 'desc')->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:250',
            'email' => 'required|string|max:250',
            'username' => 'required|string|max:15|unique:admins',
            'contact_no' => 'required|numeric',
            'location_id' => 'required|numeric',
            'admin_type_id' => 'required|numeric',
            'password' => 'required',
            'relative_id' => 'nullable'
        ]);
        return Admins::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'username' => $request['username'],
            'contact_no' => $request['contact_no'],
            'location_id' => $request['location_id'],
            'admin_type_id' => $request['admin_type_id'],
            'password' => Hash::make($request['password']),
            'relative_id' => $request['relative_id'],
            'publish' => $request['publish'],
            'status' => 1
        ]);
    }

    public function show($id)
    {
        $record = Admins::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = Admins::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|string|max:250',
            'email' => 'required|string|max:250',
            'username' => 'required|string|max:15|unique:admins,username,'.$record->id,
            'contact_no' => 'required|numeric',
            'location_id' => 'required|numeric',
            'admin_type_id' => 'required|numeric',
            'password' => 'nullable',
            'relative_id' => 'nullable'
        ]);
        if($request['password']): $password = Hash::make($request['password']);
        else: $password = $record['password']; endif;
        $record->update(['name' => $request['name'],
        'email' => $request['email'],
        'username' => $request['username'],
        'contact_no' => $request['contact_no'],
        'location_id' => $request['location_id'],
        'admin_type_id' => $request['admin_type_id'],
        'password' => $password,
        'relative_id' => $request['relative_id'],
        'publish' => $request['publish'] ]);

        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Admins::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function updatePermission(Request $request)
    {
        $record = Admins::findOrFail($request->id);
        $record->update(['reports' => implode(',', $request->reports)]);

        return ['message' => 'Record updated successfully.'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = Admins::where(function($query) use ($search){
                $query->where('type','LIKE',"%$search%");
            })->latest()->paginate(15);
        }
        else{
           $records = '';
        }
        return $records;
    }

    public function search()
    {
        if($search = \Request::get('q')) {
            $records = Admins::where(function($query) use ($search){
                $query->where('type','LIKE',"%$search%");
            })->get();
        }
        else{
           $records = [];
        }
        $result = [];
        foreach($records as $record):
            $result[$record->id] = $record->type;
        endforeach;
        return $result;
    }

    public function getList()
    {
        return Admins::where('status_id', 2)->get();
    }
}
