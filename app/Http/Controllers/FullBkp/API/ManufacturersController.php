<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Manufacturers;

class ManufacturersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Manufacturers::latest()->where('status_id', '!=', 7)->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:250',
            'description' => 'nullable',
            'status_id' => 'required|numeric|max:10'
        ]);
        return Manufacturers::create([
            'name' => ucwords($request['name']),
            'description' => $request['description'],
            'status_id' => $request['status_id']
        ]);
    }

    public function show($id)
    {
        $record = Manufacturers::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = Manufacturers::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:250|unique:manufacturers,name,'.$record->id,
            'description' => 'nullable',
            'status_id' => 'nullable|numeric|max:10'
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Manufacturers::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = Manufacturers::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%");
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
            $records = Manufacturers::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%");
            })->get();
        }
        else{
           $records = [];
        }
        $result = [];
        foreach($records as $record):
            $result[$record->id] = $record->name;
        endforeach;
        return $result;
    }

    public function getList()
    {
        return Manufacturers::where('status_id', 2)->get();
    }
}
