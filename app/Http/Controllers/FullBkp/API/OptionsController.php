<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Options;
use Illuminate\Support\Facades\Auth;

class OptionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Options::all();
    }

    public function update(Request $request, $id)
    {
        $record = Options::findOrFail($id);
        $this->validate($request, [
            'value' => 'required|string|max:250',
            'code' => 'required|max:25|unique:oe_categories,code,'.$record->id,
            'status_id' => 'nullable|numeric|max:10'
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function getList()
    {
        $alloptions = Options::all();
        foreach($alloptions as $option){
            $options[$option->name] = $option->value;
        }
        return $options;
    }

    public function updateCourseFormDiscounts(Request $request)
    {
        $this->validate($request, [
            'discount1' => 'required|numeric|max:100',
            'discount2' => 'required|numeric|max:100'
        ]);
        $o1 = Options::where('location_id', Auth::user()->location_id)->where('name', '3-4_appointments_discount')->get()->first();
        $o2 = Options::where('location_id', Auth::user()->location_id)->where('name', '5-+_appointments_discount')->get()->first();
        $o1->update(['value' => $request->discount1.'%']);
        $o2->update(['value' => $request->discount2.'%']);
        return ['status' => 'success'];
    }

}
