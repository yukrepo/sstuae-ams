<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdminTypes;

class AdminTypesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return AdminTypes::where('status_id', '!=', 7)->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|string|max:250',
            //'permissions' => 'required',
            'status_id' => 'required|numeric|max:10'
        ]);
        return AdminTypes::create([
            'type' => ucwords($request['type']),
            'status_id' => $request['status_id']
        ]);
    }

    public function show($id)
    {
        $record = AdminTypes::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = AdminTypes::findOrFail($id);
        $this->validate($request, [
            'type' => 'required|string|max:250|unique:admin_types,type,'.$record->id,
            'status_id' => 'nullable|numeric|max:10',
         //   'permissions' => 'required',
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = AdminTypes::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = AdminTypes::where(function($query) use ($search){
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
            $records = AdminTypes::where(function($query) use ($search){
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
        return AdminTypes::where('status_id', 2)->where('id','!=', 1)->get();
    }
}
