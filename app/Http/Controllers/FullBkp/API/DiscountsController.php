<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Discounts;
use Illuminate\Support\Facades\Auth;

class DiscountsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Discounts::with('location')->latest()->where('status_id', '!=', 7)->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:250',
            'value' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'type' => 'required',
            'time_slots' => 'nullable',
            'status_id' => 'required|numeric|max:10'
        ]);
        if($request['type'] == 2): $slots = implode(',', $request['time_slots']);
        else: $slots = 0; endif;
        if($request['consultation']) $consult = 1; else $consult = 0;
        if($request['treatment']) $treat = 1; else $treat = 0;
        if($request['pharmacy']) $phm = 1; else $phm = 0;
        return Discounts::create([
            'title' => ucwords($request['title']),
            'value' => $request['value'],
            'start_date' => date('Y-m-d', strtotime($request['start_date'])),
            'end_date' => date('Y-m-d', strtotime($request['end_date'])),
            'time_slots' => $slots,
            'consultation' => $consult,
            'treatment' => $treat,
            'pharmacy' => $phm,
            'status_id' => $request['status_id'],
            'location_id' => $request['location_id'],
            'admin_id' => Auth::user()->id,
        ]);
    }

    public function show($id)
    {
        $record = Discounts::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = Discounts::findOrFail($id);
        $this->validate($request, [
            'title' => 'required|string|max:250',
            'value' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'type' => 'required',
            'time_slots' => 'nullable',
            'status_id' => 'required|numeric|max:10'
        ]);
        if($request['type'] == 2): $slots = implode(',', $request['time_slots']);
        else: $slots = 0; endif;
        if($request['consultation']) $consult = 1; else $consult = 0;
        if($request['treatment']) $treat = 1; else $treat = 0;
        if($request['pharmacy']) $phm = 1; else $phm = 0;
        $record->update([
            'title' => ucwords($request['title']),
            'value' => $request['value'],
            'start_date' => date('Y-m-d', strtotime($request['start_date'])),
            'end_date' => date('Y-m-d', strtotime($request['end_date'])),
            'time_slots' => $slots,
            'consultation' => $consult,
            'treatment' => $treat,
            'pharmacy' => $phm,
            'location_id' => $request['location_id'],
            'status_id' => $request['status_id'] ]);
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Discounts::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = Discounts::where(function($query) use ($search){
                $query->where('clinic_name','LIKE',"%$search%");
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
            $records = Discounts::where(function($query) use ($search){
                $query->where('clinic_name','LIKE',"%$search%");
            })->get();
        }
        else{
           $records = [];
        }
        $result = [];
        foreach($records as $record):
            $result[$record->id] = $record->clinic_name;
        endforeach;
        return $result;
    }

    public function getList()
    {
        return Discounts::where('status_id', 2)->get();
    }

    public function filteredDiscount(Request $request)
    {
        $discounts = []; $result = [];
        if($request['type'] == 'c'):
            $discounts = Discounts::where('status_id', 2)
                                ->where('consultation', 1)
                                ->whereDate('start_date', '<=', $request['date'])
                                ->whereDate('end_date', '>=', $request['date'])
                                ->where('location_id', Auth::user()->location_id)
                                ->get();
        elseif($request['type'] == 't'):
            $discounts = Discounts::where('status_id', 2)
                                ->where('treatment', 1)
                                ->whereDate('start_date', '<=', $request['date'])
                                ->whereDate('end_date', '>=', $request['date'])
                                ->where('location_id', Auth::user()->location_id)
                                ->get();
        elseif($request['type'] == 'dm'):
            $discounts = Discounts::where('status_id', 2)
                                ->where('pharmacy', 1)
                                ->whereDate('start_date', '<=', date('Y-m-d', time()))
                                ->whereDate('end_date', '>=', date('Y-m-d', time()))
                                ->where('location_id', Auth::user()->location_id)
                                ->get();
        endif;
        foreach ($discounts as $discount) {
            if($discount->time_slots == 0): $slots = 0;
            else: $slots = explode(',', $discount->time_slots); array_filter($slots); endif;
            if($slots == 0):
                $result[$discount->id] = ['id' => $discount->id,
                                        'title' => $discount->title,
                                        'value' => $discount->value ];
            elseif(in_array($request['time_slots'], $slots)):
                $result[$discount->id] = ['id' => $discount->id,
                                        'title' => $discount->title,
                                        'value' => $discount->value ];
            endif;
        }
        return $result;
    }
}
