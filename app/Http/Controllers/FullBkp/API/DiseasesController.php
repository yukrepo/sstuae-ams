<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Diseases;

class DiseasesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Diseases::latest()->where('status_id', '!=', 7)->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'value' => 'required|string|max:250',
            'code' => 'required|string|max:50',
            'status_id' => 'required|numeric|max:10'
        ]);
        return Diseases::create([
            'value' => ucwords($request['value']),
            'code' => strtoupper($request['code']),
            'status_id' => $request['status_id']
        ]);
    }

    public function show($id)
    {
        $record = Diseases::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = Diseases::findOrFail($id);
        $this->validate($request, [
            'value' => 'required|string|max:250',
            'code' => 'required|string|max:250|unique:diseases,code,'.$record->id,
            'status_id' => 'nullable|numeric|max:10'
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Diseases::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = Diseases::where(function($query) use ($search){
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
            $records = Diseases::where(function($query) use ($search){
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
        return Diseases::where('status_id', 2)->orderBy('value', 'asc')->get();
    }

    public function getSelectList()
    {
        $diseases = Diseases::where('status_id', 2)->orderBy('value', 'asc')->get();;
        $result = [];
        foreach($diseases as $disease):
            $result[] = ['value' => $disease->id, 'text' => $disease->code .' - '.$disease->value];
        endforeach;
        return $result;
    }
}
