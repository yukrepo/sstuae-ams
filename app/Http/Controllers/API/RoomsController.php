<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Rooms;

class RoomsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Rooms::select('rooms.*', 'locations.clinic_name as location', 'statuses.status as status', 'statuses.css as css')
                    ->join('locations', 'locations.id', '=', 'rooms.location_id')
                    ->join('statuses', 'statuses.id', '=', 'rooms.status_id')
                    ->where('rooms.status_id', '!=', 7)
                    ->orderBy('rooms.created_at', 'desc')->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'room_name' => 'required|string|max:250',
            'location_id' => 'required|numeric|max:20',
            'remark' => 'nullable',
            'status_id' => 'required|numeric|max:10'
        ]);
        return Rooms::create([
            'room_name' => ucwords($request['room_name']),
            'location_id' => $request['location_id'],
            'remark' => $request['remark'],
            'status_id' => $request['status_id']
        ]);
    }

    public function show($id)
    {
        $record = Rooms::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = Rooms::findOrFail($id);
        $this->validate($request, [
            'room_name' => 'required|string|max:250|unique:rooms,room_name,'.$record->id,
            'location_id' => 'required|numeric|max:20',
            'remark' => 'nullable',
            'status_id' => 'required|numeric|max:10'
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Rooms::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = Rooms::where(function($query) use ($search){
                $query->where('room_name','LIKE',"%$search%");
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
            $records = Rooms::where(function($query) use ($search){
                $query->where('room_name','LIKE',"%$search%");
            })->get();
        }
        else{
           $records = [];
        }
        $result = [];
        foreach($records as $record):
            $result[$record->id] = $record->room_name;
        endforeach;
        return $result;
    }

    public function getList()
    {
        return Rooms::where('status_id', 2)->get();
    }
}
