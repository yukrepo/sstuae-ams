<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Treatments;

class TreatmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Treatments::select('treatments.*', 'appointment_types.appointment_type as appointment_type', 'statuses.status as status', 'statuses.css as css')
                    ->join('appointment_types', 'appointment_types.id', '=', 'treatments.appointment_type_id')
                    ->join('statuses', 'statuses.id', '=', 'treatments.status_id')
                    ->where('treatments.status_id', '!=', 7)
                    ->orderBy('treatments.created_at', 'desc')->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'treatment' => 'required|string|max:250',
            'appointment_type_id' => 'required|numeric|max:20',
            'is_it_dual' => 'nullable',
            'timing' => 'required|numeric|max:400',
            'cost' => 'required|numeric|max:400',
            'tax' => 'required|numeric|max:400',
            'remark' => 'nullable',
            'status_id' => 'required|numeric|max:10'
        ]);
        return Treatments::create([
            'treatment' => ucwords($request['treatment']),
            'appointment_type_id' => $request['appointment_type_id'],
            'remark' => $request['remark'],
            'is_it_dual' => $request['is_it_dual'],
            'timing' => $request['timing'],
            'cost' => $request['cost'],
            'status_id' => $request['status_id']
        ]);
    }

    public function show($id)
    {
        $record = Treatments::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = Treatments::findOrFail($id);
        $this->validate($request, [
            'treatment' => 'required|string|max:250|unique:treatments,treatment,'.$record->id,
            'appointment_type_id' => 'required|numeric|max:20',
            'remark' => 'nullable',
            'timing' => 'required|numeric|max:400',
            'cost' => 'required|numeric|max:400',
            'tax' => 'required|numeric|max:400',
            'status_id' => 'required|numeric|max:10'
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Treatments::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = Treatments::where(function($query) use ($search){
                $query->where('treatment','LIKE',"%$search%");
            })->latest()->paginate(15);
        }
        else{
           $records = '';
        }
        return $records;
    }

    public function search(Request $request)
    {
        $conditions = [];
        $search = $request['keyword'];
        if($request['appointment_type_id']): $conditions["treatments.appointment_type_id"] = $request['appointment_type_id']; endif;
        if($request['status_id']): $conditions["treatments.status_id"] = $request['status_id']; endif;
        if($request['is_it_dual']): $conditions["treatments.is_it_dual"] = $request['is_it_dual']; endif;

        return Treatments::select('treatments.*', 'appointment_types.appointment_type as appointment_type', 'statuses.status as status', 'statuses.css as css') ->join('appointment_types', 'appointment_types.id', '=', 'treatments.appointment_type_id')
        ->join('statuses', 'statuses.id', '=', 'treatments.status_id')
        ->where('treatments.status_id', '!=', 7)
        ->where('treatments.treatment','LIKE',"%$search%")->where($conditions)
        ->orderBy('treatments.created_at', 'desc')->latest()->paginate(15);
    }

    public function getList()
    {
        return Treatments::where('status_id', 2)->orderBy('appointment_type_id', 'asc')->orderBy('treatment', 'asc')->get();
    }

    public function getTList()
    {
        return Treatments::where('appointment_type_id', 2)->orderBy('treatment', 'asc')->get();
    }

    public function getSelectList()
    {
        $therapies = Treatments::where('appointment_type_id', 2)->where('status_id', 2)->orderBy('treatment', 'asc')->get();
        $result = [];
        foreach($therapies as $therapy):
            $result[] = ['value' => $therapy->id, 'text' => $therapy->treatment];
        endforeach;
        return $result;
    }

    public function getPresSelectList()
    {
        $therapies = Treatments::where('id', '>=', 6)->where('status_id', 2)->orderBy('treatment', 'asc')->get();
        $result = [];
        foreach($therapies as $therapy):
            $result[] = ['value' => $therapy->id, 'text' => $therapy->treatment];
        endforeach;
        return $result;
    }

    public function getCSelectList()
    {
        $therapies = Treatments::where('appointment_type_id', 1)->where('status_id', 2)->orderBy('treatment', 'asc')->get();
        $result = [];
        foreach($therapies as $therapy):
            $result[] = ['value' => $therapy->id, 'text' => $therapy->treatment];
        endforeach;
        return $result;
    }

    public function getCList(Request $request)
    {
        if(isset($request->vtype) && ($request->vtype == 1))
            return Treatments::where('appointment_type_id', 1)->where('is_followup', 0)->get();
        elseif(isset($request->vtype) && ($request->vtype == 2))
            return Treatments::where('appointment_type_id', 1)->where('is_followup', 1)->get();
        elseif(isset($request->vtype) && ($request->vtype == 3))
            return Treatments::where('appointment_type_id', 1)->where('is_followup', 1)->get();
        else return Treatments::where('appointment_type_id', 1)->get();
    }

    public function getCost($id)
    {
        $cost = Treatments::where('id', $id)->first();
        return $cost->cost;
    }
}
