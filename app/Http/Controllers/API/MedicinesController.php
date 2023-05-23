<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Medicines;
use DB;
class MedicinesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Medicines::select('medicines.*', 'categories.name as category_name', 'categories.unit')
                        ->join('categories', 'categories.id', '=', 'medicines.category_id')
                        ->orderBy('medicines.name', 'asc')->where('medicines.status_id', '!=', '7')->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
             'name' => 'required|string|max:190',
             'description' => 'nullable',
             'unitsize' => 'required|numeric',
             'insurance_price' => 'required|numeric',
             'cash_price' => 'required|numeric',
             'status_id' => 'required|numeric',
             'category_id' => 'required'
        ]);

        $medicine =  Medicines::create([
                        'name' => $request['name'],
                        'description' => $request['description'],
                        'unitsize' => $request['unitsize'],
                        'insurance_price' => $request['insurance_price'],
                        'cash_price' => $request['cash_price'],
                        'status_id' => $request['status_id'],
                        'category_id' => $request['category_id'],
        ]);
        //$id = $medicine->id;
        $barcode = $this->__generateBarcode($medicine->id);
        //$medicine = Medicines::findOrFail($id);
        $medicine->update(['barcode' => $barcode]);
        return ['message' => 'medicine created'];
    }

    public function show($id)
    {
        //$medicine = Medicines::findOrFail($id);
        $medicine = Medicines::select('medicines.*', 'categories.name as category_name', 'categories.unit')
                        ->join('categories', 'categories.id', '=', 'medicines.category_id')
                        ->where('medicines.id', $id)->get();
        return $medicine;
    }

    public function update(Request $request, $id)
    {
        $medicine = Medicines::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:190',
            'description' => 'nullable',
            'unitsize' => 'required|numeric',
            'insurance_price' => 'required|numeric',
            'cash_price' => 'required|numeric',
            'status_id' => 'required|numeric',
            'category_id' => 'required'
       ]);
       $medicine->update([
                       'name' => $request['name'],
                       'description' => $request['description'],
                       'unitsize' => $request['unitsize'],
                       'insurance_price' => $request['insurance_price'],
                        'cash_price' => $request['cash_price'],
                        'status_id' => $request['status_id'],
                       'category_id' => $request['category_id'],
       ]);

       return ['message' => 'medicine details updated'];
    }

    public function destroy($id)
    {
        $medicine = Medicines::findOrFail($id);
        $medicine->delete();
        return ['message' => 'record deleted'];
    }

    public function listItems()
    {
        $medicines = Medicines::select('medicines.*', 'categories.name as category_name')
           ->join('categories', 'categories.id', '=', 'medicines.category_id')
           ->get();
        $result = [];
        foreach($medicines as $medicine):
            $result[] =['value' => $medicine->id, 'text' => $medicine->name.' - '.$medicine->barcode.' - IN '.$medicine->category_name];
        endforeach;
        return $result;
    }

    public function getFullList()
    {
        return Medicines::latest()->get();
    }

    public function getDetail($id)
    {
        return Medicines::findOrFail($id);
    }

    public function search()
    {
        if($search = \Request::get('q')) {
            $medicine = Medicines::select('medicines.*', 'categories.name as category_name', 'categories.unit')
            ->join('categories', 'categories.id', '=', 'medicines.category_id')
            ->orderBy('medicines.name', 'asc')->where('medicines.status_id', '!=', '7')->where(function($query) use ($search){
                $query->where('medicines.name','LIKE',"%$search%")->orwhere('medicines.barcode','LIKE',"%$search%");
            })->paginate(15);
        }
        else{
           $medicine = Medicines::select('medicines.*', 'categories.name as category_name', 'categories.unit')
           ->join('categories', 'categories.id', '=', 'medicines.category_id')
           ->orderBy('medicines.name', 'asc')->where('medicines.status_id', '!=', '7')->paginate(15);
        }
        return $medicine;
    }

    public function findByName() {
        $allmed = [];
        if($search = \Request::get('q')) {
            $medicine = Medicines::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%");
            })->get();
        }
        else{
           $medicine = Medicines::latest()->get();
        }
        foreach ($medicine as $key => $med) {
            $allmed[$med->id] = $med->name;
        }
        return $allmed;
    }

    public function getFullSelectList()
    {
        $medicines = Medicines::orderBy('name', 'asc')->get();
        $result = [];
        foreach($medicines as $medicine):
            $result[] = ['value' => $medicine->id, 'text' => $medicine->name.' - '.$medicine->barcode];
        endforeach;
        return $result;
    }

    public function getFullISelectList($iid = null)
    {
        $medicines = Medicines::select('medicines.*')
                                ->join('insurance_medicine_maps', 'medicines.id', '=', 'insurance_medicine_maps.medicine_id')
                                ->where('insurance_medicine_maps.insurance_id', $iid)
                                ->orderBy('medicines.name', 'asc')->get();
        $result = [];
        foreach($medicines as $medicine):
            $result[] = ['value' => $medicine->id, 'text' => $medicine->name.' - '.$medicine->barcode];
        endforeach;
        return $result;
    }

    public function getMedStockList() {
        return  Medicines::leftJoin(DB::raw('(SELECT medicine_id, SUM(stock) as stock_sum FROM stocks GROUP BY medicine_id) AS stocks'), function ($join) {
            $join->on('stocks.medicine_id', '=', 'medicines.id');
        })->get();
    }

    private function __generateBarcode($id = null)
    {
        return date('y', time()).str_pad($id, 4, "0", STR_PAD_LEFT);
    }

}
