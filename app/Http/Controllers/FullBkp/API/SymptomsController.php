<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Symptoms;

class SymptomsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Symptoms::where('status_id', '!=', 7)->orderBy('value', 'asc')->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'value' => 'required|string|max:250',
            'status_id' => 'required|numeric|max:10'
        ]);
        return Symptoms::create([
            'value' => ucwords($request['value']),
            'status_id' => $request['status_id']
        ]);
    }

    public function show($id)
    {
        $record = Symptoms::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = Symptoms::findOrFail($id);
        $this->validate($request, [
            'value' => 'required|string|max:250|unique:symptoms,value,'.$record->id,
            'status_id' => 'nullable|numeric|max:10'
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Symptoms::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = Symptoms::where(function($query) use ($search){
                $query->where('value','LIKE',"%$search%")->orWhere('code','LIKE',"%$search%");
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
            $records = Symptoms::where(function($query) use ($search){
                $query->where('value','LIKE',"%$search%")->orWhere('code','LIKE',"%$search%");
            })->get();
        }
        else{
           $records = [];
        }
        $result = [];
        foreach($records as $record):
            $result[$record->id] = $record->value.' [ '.$record->code.' ]';
        endforeach;
        return $result;
    }

    public function getList()
    {
        return Symptoms::where('status_id', 2)->orderBy('value', 'asc')->get();
    }

    public function getSelectList()
    {
        $symptoms = Symptoms::where('status_id', 2)->orderBy('value', 'asc')->get();
        $result = [];
        foreach($symptoms as $symptom):
            $result[] = ['value' => $symptom->id, 'text' => $symptom->value];
        endforeach;
        return $result;
    }
}
