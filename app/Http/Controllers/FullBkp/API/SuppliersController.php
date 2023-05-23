<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Suppliers;

class SuppliersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Suppliers::latest()->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:250',
            'company_name' => 'required|string|max:250',
            'email' => 'required|email|max:190',
            'city' => 'required|string|max:190',
            'connection_source' => 'required|string|max:300',
            'address' => 'nullable',
            'contact_no' => 'required|numeric|digits:10|unique:suppliers',
            'remark' => 'nullable',
            'status_id' => 'required'
        ]);
        return Suppliers::create([
            'name' => $request['name'],
            'company_name' => $request['company_name'],
            'connection_source' => $request['connection_source'],
            'contact_no' => $request['contact_no'],
            'email' => $request['email'],
            'address' => $request['address'],
            'city' => $request['city'],
            'status_id' => $request['status_id']
        ]);
    }

    public function show($id)
    {
        $supplier = Suppliers::findOrFail($id);
        return $supplier;
    }

    public function update(Request $request, $id)
    {
        $supplier = Suppliers::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:250',
            'company_name' => 'required|string|max:250',
            'email' => 'required|email|max:190',
            'city' => 'required|string|max:190',
            'connection_source' => 'required|string|max:300',
            'address' => 'nullable',
            'contact_no' => 'required|numeric|digits:10|unique:suppliers,contact_no,'.$supplier->id,
            'remark' => 'nullable',
            'status_id' => 'required'
        ]);
        $supplier->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $supplier = Suppliers::findOrFail($id);
        $supplier->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $suppliers = Suppliers::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")->orwhere('company_name','LIKE',"%$search%")->orwhere('company_name','LIKE',"%$search%")->orwhere('email','LIKE',"%$search%")->orwhere('contact_no','LIKE',"%$search%")->orwhere('city','LIKE',"%$search%")->orwhere('address','LIKE',"%$search%")->orwhere('connection_source','LIKE',"%$search%");
            })->latest()->paginate(15);
        }
        else{
           $suppliers = '';
        }
        return $suppliers;
    }

    public function search()
    {
        if($search = \Request::get('q')) {
            $suppliers = Suppliers::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")->orwhere('company_name','LIKE',"%$search%")->orwhere('company_name','LIKE',"%$search%")->orwhere('email','LIKE',"%$search%")->orwhere('contact_no','LIKE',"%$search%")->orwhere('city','LIKE',"%$search%")->orwhere('address','LIKE',"%$search%")->orwhere('connection_source','LIKE',"%$search%");
            })->get();
        }
        else{
           $suppliers = [];
        }
        $result = [];
        foreach($suppliers as $supplier):
            $result[] = $supplier->id.'- '.ucwords($supplier->name).' - '.$supplier->contact_no;
        endforeach;
        return $result;
    }

    public function getFullSelectList()
    {
        $suppliers = Suppliers::latest()->get();
        $result = [];
        foreach($suppliers as $supplier):
            $result[] = ['value' => $supplier->id, 'text' => $supplier->name.' - '.$supplier->contact_no];
        endforeach;
        return $result;
    }
}
