<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Locations;
use Illuminate\Support\Facades\Auth;

class LocationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Locations::latest()->where('status_id', '!=', 7)->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'clinic_name' => 'required|string|max:250',
            'city' => 'required|string|max:250',
            'address' => 'required',
            'status_id' => 'required|numeric|max:10'
        ]);
        return Locations::create([
            'clinic_name' => ucwords($request['clinic_name']),
            'address' => $request['address'],
            'city' => $request['city'],
            'status_id' => $request['status_id']
        ]);
    }

    public function show($id)
    {
        $record = Locations::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = Locations::findOrFail($id);
        $this->validate($request, [
            'clinic_name' => 'required|string|max:250|unique:locations,value,'.$record->id,
            'city' => 'required|string|max:250',
            'address' => 'required',
            'status_id' => 'nullable|numeric|max:10'
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Locations::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = Locations::where(function($query) use ($search){
                $query->where('clinic_name','LIKE',"%$search%");
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
            $records = Locations::where(function($query) use ($search){
                $query->where('clinic_name','LIKE',"%$search%");
            })->get();
        }
        else{
           $records = [];
        }
        $result = [];
        foreach($records as $record):
            $result[$record->id] = $record->clinic_name;
        endforeach;
        return $result;
    }

    public function getList()
    {
        return Locations::where('status_id', 2)->get();
    }

    public function getNonList()
    {
        return Locations::where('status_id', 2)->where('id', '!=', Auth::user()->location_id)->get();
    }
}
