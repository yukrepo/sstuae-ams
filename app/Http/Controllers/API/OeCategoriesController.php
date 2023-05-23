<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OeCategories;

class OeCategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return OeCategories::where('status_id', '!=', 7)->orderBy('value', 'asc')->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'value' => 'required|string|max:250',
            'code' => 'nullable',
            'status_id' => 'required|numeric|max:10'
        ]);
        return OeCategories::create([
            'value' => ucwords($request['value']),
            'code' => strtoupper($request['code']),
            'status_id' => $request['status_id']
        ]);
    }

    public function show($id)
    {
        $record = OeCategories::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = OeCategories::findOrFail($id);
        $this->validate($request, [
            'value' => 'required|string|max:250|unique:oe_categories,value,'.$record->id,
            'code' => 'nullable',
            'status_id' => 'nullable|numeric|max:10'
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = OeCategories::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = OeCategories::where(function($query) use ($search){
                $query->where('value','LIKE',"%$search%")->where('code','LIKE',"%$search%");
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
            $records = OeCategories::where(function($query) use ($search){
                $query->where('value','LIKE',"%$search%")->where('code','LIKE',"%$search%");
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
        return OeCategories::where('status_id', 2)->orderBy('value', 'asc')->get();
    }

    public function getSelectList()
    {
        $oecategories = OeCategories::where('status_id', 2)->orderBy('value', 'asc')->get();;
        $result = [];
        foreach($oecategories as $oecategory):
            $result[] = ['value' => $oecategory->id, 'text' => $oecategory->value];
        endforeach;
        return $result;
    }
}
