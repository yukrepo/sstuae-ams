<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Xrays;

class XraysController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Xrays::latest()->where('status_id', '!=', 7)->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'value' => 'required|string|max:250',
            'status_id' => 'required|numeric|max:10'
        ]);
        return Xrays::create([
            'value' => ucwords($request['value']),
            'status_id' => $request['status_id']
        ]);
    }

    public function show($id)
    {
        $record = Xrays::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = Xrays::findOrFail($id);
        $this->validate($request, [
            'value' => 'required|string|max:250|unique:xrays,value,'.$record->id,
            'status_id' => 'nullable|numeric|max:10'
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Xrays::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = Xrays::where(function($query) use ($search){
                $query->where('value','LIKE',"%$search%");
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
            $records = Xrays::where(function($query) use ($search){
                $query->where('value','LIKE',"%$search%");
            })->get();
        }
        else{
           $records = [];
        }
        $result = [];
        foreach($records as $record):
            $result[$record->id] = $record->value;
        endforeach;
        return $result;
    }
}
