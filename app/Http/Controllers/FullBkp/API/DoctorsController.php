<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Doctors;

class DoctorsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Doctors::select('doctors.*', 'designation_types.designation as designation', 'locations.clinic_name as location')
                        ->join('designation_types', 'designation_types.id', '=', 'doctors.designation_type_id')
                        ->join('locations', 'locations.id', '=', 'doctors.location_id')
                        ->where('doctors.status_id', '!=', 7)
                        ->latest()->paginate(15);

        return Doctors::latest()->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:190',
            'contact_no' => 'required|numeric|digits:11|unique:doctors',
            'qualification' => 'nullable',
            'about' => 'nullable',
            'gender' => 'required|numeric|max:10',
            'location_id' => 'nullable|numeric|max:10',
            'designation_type_id' => 'nullable|numeric|max:10',
            'status_id' => 'nullable|numeric|max:10'
        ]);
        return Doctors::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'contact_no' => $request['contact_no'],
            'qualification' => $request['qualification'],
            'about' => $request['about'],
            'location_id' => $request['location_id'],
            'gender' => $request['gender'],
            'designation_type_id' => $request['designation_type_id'],
            'status_id' => $request['status_id']
        ]);
    }

    public function show($id)
    {
        $doctor = Doctors::select('doctors.*', 'designation_types.designation as designation', 'locations.clinic_name as location')
        ->join('designation_types', 'designation_types.id', '=', 'doctors.designation_type_id')
        ->join('locations', 'locations.id', '=', 'doctors.location_id')
        ->where('doctors.status_id', '!=', 7)->where('doctors.id', $id)->get()->first();
        return $doctor;
    }

    public function update(Request $request, $id)
    {
        $doctor = Doctors::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:190',
            'qualification' => 'nullable',
            'about' => 'nullable',
            'location_id' => 'nullable|numeric|max:10',
            'designation_type_id' => 'nullable|numeric|max:10',
            'status_id' => 'nullable|numeric|max:10',
            'gender' => 'required|numeric|max:10',
            'contact_no' => 'required|numeric|digits:11|unique:doctors,contact_no,'.$doctor->id,
        ]);
        $doctor->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $doctor = Doctors::findOrFail($id);
        $doctor->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $doctors = Doctors::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")->orwhere('contact_no','LIKE',"%$search%")->orwhere('email','LIKE',"%$search%");
            })->latest()->paginate(15);
        }
        else{
           $doctors = '';
        }
        return $doctors;
    }

    public function search()
    {
        if($search = \Request::get('q')) {
            $doctors = Doctors::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")->orwhere('contact_no','LIKE',"%$search%")->orwhere('email','LIKE',"%$search%");
            })->get();
        }
        else{
           $doctors = [];
        }
        $result = [];
        foreach($doctors as $doctor):
            $result[] = $doctor->id.'- '.ucwords($doctor->name).' - '.$doctor->contact_no;
        endforeach;
        return $result;
    }

    public function getList()
    {
        return Doctors::where('status_id', 2)->where('location_id', Auth::user()->location_id)->orderBy('designation_type_id', 'asc')->orderBy('gender', 'asc')->get();
    }

    public function getDoctorsList()
    {
        return Doctors::where('status_id', 2)->where('location_id', Auth::user()->location_id)->where('designation_type_id', 1)->get();
    }

    public function getTherapistsList()
    {
        return Doctors::where('status_id', 2)->where('location_id', Auth::user()->location_id)->where('designation_type_id', 2)->orderBy('gender', 'asc')->get();
    }

    public function getTherapistsArrayList()
    {
        $doctors = Doctors::where('status_id', 2)
                            ->where('location_id', Auth::user()->location_id)
                            ->where('designation_type_id', 2)->get();
        foreach ($doctors as $key => $doctor) {
            $return[$doctor->id] = $doctor->name;
        }
        return $return;
    }
}
