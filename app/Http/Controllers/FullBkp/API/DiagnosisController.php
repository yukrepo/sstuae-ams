<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Diagnosis;

class DiagnosisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Diagnosis::latest()->where('status_id', '!=', 7)->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'value' => 'required|string|max:250',
            'status_id' => 'required|numeric|max:10'
        ]);
        return Diagnosis::create([
            'value' => ucwords($request['value']),
            'status_id' => $request['status_id']
        ]);
    }

    public function show($id)
    {
        $record = Diagnosis::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = Diagnosis::findOrFail($id);
        $this->validate($request, [
            'value' => 'required|string|max:250|unique:diagnoses,value,'.$record->id,
            'status_id' => 'nullable|numeric|max:10'
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Diagnosis::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = Diagnosis::where(function($query) use ($search){
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
            $records = Diagnosis::where(function($query) use ($search){
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

    public function getList()
    {
        return Diagnosis::where('status_id', 2)->orderBy('value', 'asc')->get();
    }

    public function getSelectList()
    {
        $diseases = Diagnosis::where('status_id', 2)->orderBy('value', 'asc')->get();;
        $result = [];
        foreach($diseases as $disease):
            $result[] = ['value' => $disease->id, 'text' => $disease->value];
        endforeach;
        return $result;
    }
}
