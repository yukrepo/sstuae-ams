<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Packages;
use Illuminate\Support\Facades\Auth;
use App\Models\Treatments;

class PackagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Packages::select('packages.*', 'locations.clinic_name as location', 'statuses.status as status', 'statuses.css as css')
                    ->join('locations', 'locations.id', '=', 'packages.location_id')
                    ->join('statuses', 'statuses.id', '=', 'packages.status_id')
                    ->where('packages.status_id', '!=', 7)
                    ->orderBy('packages.created_at', 'desc')->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:250',
            'timeline' => 'required|numeric|min:3',
            'remark' => 'nullable',
            'status_id' => 'required|numeric|max:10'
        ]);

        $tcount = 0; $tcost = 0; $ccount = 0; $ccost = 0; $rowdata = [];

        foreach ($request['therapy__id'] as $key => $detail) {
            if($detail):
                $tcount = $tcount + $request['therapy__count'][$key];
                if($request['therapy__type'][$key] == 1){
                    $cost = $this->__getCost($detail);
                    $total = $cost * $request['therapy__count'][$key];
                    $tcost = $tcost + $total;
                } else {
                    $cost = $this->__getCost($detail);
                    $total = 0;
                }
                $cost = $this->__getCost($detail);
                $total = $cost * $request['therapy__count'][$key];
                $rowdata[] = ['count' => $request['therapy__count'][$key],
                            'cost' => $cost,
                            'name' => $this->__getName($detail),
                            'type' => $request['therapy__type'][$key],
                            'id' => $request['therapy__id'][$key],
                            'total' => $total ];
            endif;
        }
        $treatments_Summary = json_encode($rowdata);
        $rowdata = [];
        foreach ($request['consult__id'] as $key => $detail) {
            if($detail):
                $ccount = $ccount + $request['consult__count'][$key];
                if($request['consult__type'][$key] == 1){
                    $cost = $this->__getCost($detail);
                    $total = $cost * $request['consult__count'][$key];
                    $ccost = $ccost + $total;
                } else {
                    $cost = $this->__getCost($detail);
                    $total = 0;
                }
                $cost = $this->__getCost($detail);
                $total = $cost * $request['consult__count'][$key];
                $rowdata[] = ['count' => $request['consult__count'][$key],
                            'cost' => $cost,
                            'name' => $this->__getName($detail),
                            'type' => $request['consult__type'][$key],
                            'id' => $request['consult__id'][$key],
                            'total' => $total ];
            endif;
        }
        $consultation_Summary = json_encode($rowdata);
        if($request['cost']): $finalcost = $request['cost'];
        else: $finalcost = $tcost+$ccost; endif;
        return Packages::create([
            'name' => ucwords($request['name']),
            'location_id' => Auth::user()->location_id,
            'remark' => $request['remark'],
            'cost' => $finalcost,
            'type' => 1,
            'timeline' => $request['timeline'],
            'treatments' => $tcount,
            'consultation' => $ccount,
            'treatments_Summary' => $treatments_Summary,
            'consultation_Summary' => $consultation_Summary,
            'status_id' => $request['status_id']
        ]);
    }

    public function getCost(Request $request){
        $tcount = 0; $tcost = 0; $ccount = 0; $ccost = 0; $rowdata = [];

        foreach ($request['therapy__id'] as $key => $detail) {
            if($detail):
                if($request['therapy__type'][$key] == 1){
                    $cost = $this->__getCost($detail);
                    $total = $cost * $request['therapy__count'][$key];
                    $tcost = $tcost + $total;
                } else {
                    $cost = $this->__getCost($detail);
                    $total = 0;
                }
                $cost = $this->__getCost($detail);
                $total = $cost * $request['therapy__count'][$key];
                $rowdata[] = ['count' => $request['therapy__count'][$key],
                            'cost' => $cost,
                            'name' => $this->__getName($detail),
                            'type' => $request['therapy__type'][$key],
                            'id' => $request['therapy__id'][$key],
                            'total' => $total ];
            endif;
        }
        foreach ($request['consult__id'] as $key => $detail) {
            if($detail):
                if($request['consult__type'][$key] == 1){
                    $cost = $this->__getCost($detail);
                    $total = $cost * $request['consult__count'][$key];
                    $ccost = $ccost + $total;
                } else {
                    $cost = $this->__getCost($detail);
                    $total = 0;
                }
                $cost = $this->__getCost($detail);
                $total = $cost * $request['consult__count'][$key];
                $rowdata[] = ['count' => $request['consult__count'][$key],
                            'cost' => $cost,
                            'name' => $this->__getName($detail),
                            'type' => $request['consult__type'][$key],
                            'id' => $request['consult__id'][$key],
                            'total' => $total ];
            endif;
        }
        return $tcost+$ccost;
    }

    public function show($id)
    {
        $treatments = []; $consultations = [];
        $record = Packages::select('packages.*', 'statuses.status', 'statuses.css')
                            ->join('statuses', 'statuses.id', '=', 'packages.status_id')
                            ->where('packages.id', $id)
                            ->get()->first();
        $record->treatments_Summary = json_decode($record->treatments_Summary);
        $record->consultation_Summary = json_decode($record->consultation_Summary);
        foreach ($record->treatments_Summary as $value) {
            if(!in_array($value->id, array_keys($treatments)))
            $treatments[$value->id] = $value->name;
        }
        foreach ($record->consultation_Summary as $value) {
            if(!in_array($value->id, array_keys($consultations)))
            $consultations[$value->id] = $value->name;
        }
        $record->treatmentlists = $treatments;
        $record->consultationlists = $consultations;
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = Packages::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:250',
            'remark' => 'nullable',
            'status_id' => 'required|numeric|max:10'
        ]);
        $tcount = 0; $tcost = 0; $ccount = 0; $ccost = 0; $rowdata = [];

        foreach ($request['therapy__id'] as $key => $detail) {
            if($detail):
                $tcount = $tcount + $request['therapy__count'][$key];
                if($request['therapy__type'][$key] == 1){
                    $cost = $this->__getCost($detail);
                    $total = $cost * $request['therapy__count'][$key];
                    $tcost = $tcost + $total;
                } else {
                    $cost = $this->__getCost($detail);
                    $total = 0;
                }
                $cost = $this->__getCost($detail);
                $total = $cost * $request['therapy__count'][$key];
                $rowdata[] = ['count' => $request['therapy__count'][$key],
                            'cost' => $cost,
                            'name' => $this->__getName($detail),
                            'type' => $request['therapy__type'][$key],
                            'id' => $request['therapy__id'][$key],
                            'total' => $total ];
            endif;
        }
        $treatments_Summary = json_encode($rowdata);
        $rowdata = [];
        foreach ($request['consult__id'] as $key => $detail) {
            if($detail):
                $ccount = $ccount + $request['consult__count'][$key];
                if($request['consult__type'][$key] == 1){
                    $cost = $this->__getCost($detail);
                    $total = $cost * $request['consult__count'][$key];
                    $ccost = $ccost + $total;
                } else {
                    $cost = $this->__getCost($detail);
                    $total = 0;
                }
                $cost = $this->__getCost($detail);
                $total = $cost * $request['consult__count'][$key];
                $rowdata[] = ['count' => $request['consult__count'][$key],
                            'cost' => $cost,
                            'name' => $this->__getName($detail),
                            'type' => $request['consult__type'][$key],
                            'id' => $request['consult__id'][$key],
                            'total' => $total ];
            endif;
        }
        $consultation_Summary = json_encode($rowdata);
        if($request['cost']): $finalcost = $request['cost'];
        else: $finalcost = $tcost+$ccost; endif;
        $record->update([
            'name' => ucwords($request['name']),
            'remark' => $request['remark'],
            'cost' => $finalcost,
            'type' => 1,
            'timeline' => $request['timeline'],
            'treatments' => $tcount,
            'consultation' => $ccount,
            'treatments_Summary' => $treatments_Summary,
            'consultation_Summary' => $consultation_Summary,
            'status_id' => $request['status_id']
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Packages::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = Packages::where(function($query) use ($search){
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
            $records = Packages::where(function($query) use ($search){
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
        return Packages::where('status_id', 2)->get();
    }

    public function __getCost($id)
    {
        $cost = Treatments::where('id', $id)->first();
        return $cost->cost;
    }

    public function getDetails($id)
    {
        $package = Packages::where('id', $id)->first();
        $booking1s = json_decode($package->treatments_Summary);
        $booking2s = json_decode($package->consultation_Summary);
        $appoint1s = []; $appoint2s = [];
        foreach($booking1s as $booking):
            for ($i=1; $i <= $booking->count ; $i++) {
                $appoint1s[] = ['tid' => $booking->id,
                            'tname' => $booking->name,
                            'ttype' => 't',
                            'type' => $booking->type,
                        ];
            }
        endforeach;
        foreach($booking2s as $booking):
            for ($i=1; $i <= $booking->count ; $i++) {
                $appoint2s[] = ['tid' => $booking->id,
                            'tname' => $booking->name,
                            'ttype' => 'c',
                            'type' => $booking->type,
                        ];
            }
        endforeach;
        $package->bookings = array_merge($appoint1s, $appoint2s);
        return $package;
    }

    public function __getName($id)
    {
        $cost = Treatments::where('id', $id)->first();
        return $cost->treatment;
    }
}
