<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AppointmentQueries;

class AppointmentQueriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return AppointmentQueries::select('appointment_queries.*', 'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name', 'users.username', 'statuses.status as status', 'statuses.css as css')
                                ->join('locations', 'locations.id', '=', 'appointment_queries.location_id')
                                ->join('statuses', 'statuses.id', '=', 'appointment_queries.status_id')
                                ->join('users', 'users.id', '=', 'appointment_queries.user_id')
                                ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                                ->orderBy('appointment_queries.created_at', 'desc')->paginate(15);
    }

    public function store(Request $request)
    {
        return ['message', 'This action is not required'];
    }

    public function show($id)
    {
        $record = AppointmentQueries::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = AppointmentQueries::findOrFail($id);
        $this->validate($request, [
            'status_id' => 'nullable|numeric|max:10'
        ]);
        $record->update(['status_id' => $request['status_id']]);
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = AppointmentQueries::findOrFail($id);
        $record->delete();
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = AppointmentQueries::where(function($query) use ($search){
                $query->where('appointment_type','LIKE',"%$search%")->orwhere('appointment_reason','LIKE',"%$search%")->orwhere('date','LIKE',"%$search%")->orwhere('timeframe','LIKE',"%$search%");
            })->latest()->paginate(15);
        }
        else{
            $records = '';
        }
        return $records;
    }
}
