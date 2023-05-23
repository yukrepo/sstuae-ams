<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Laboratories;

class LaboratoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Laboratories::select('laboratories.*', 'lab_categories.value as category')
                            ->join('lab_categories', 'lab_categories.id', '=', 'laboratories.lab_category_id')
                            ->where('laboratories.status_id', '!=', 7)
                            ->latest()->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'value' => 'required|string|max:250',
            'lab_range' => 'nullable',
            'remark' => 'nullable',
            'lab_category_id' => 'required|numeric|max:10',
            'status_id' => 'required|numeric|max:10'
        ]);
        return Laboratories::create([
            'value' => ucwords($request['value']),
            'lab_range' => $request['lab_range'],
            'remark' => $request['remark'],
            'lab_category_id' => $request['lab_category_id'],
            'status_id' => $request['status_id']
        ]);
    }

    public function show($id)
    {
        $record = Laboratories::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = Laboratories::findOrFail($id);
        $this->validate($request, [
            'value' => 'required|string|max:250',
            'lab_range' => 'nullable',
            'remark' => 'nullable',
            'lab_category_id' => 'required|numeric|max:10',
            'status_id' => 'required|numeric|max:10'
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Laboratories::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = Laboratories::where(function($query) use ($search){
                $query->where('value','LIKE',"%$search%")->orwhere('lab_range','LIKE',"%$search%");
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
            $records = Laboratories::where(function($query) use ($search){
                $query->where('value','LIKE',"%$search%")->orwhere('lab_range','LIKE',"%$search%");
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
