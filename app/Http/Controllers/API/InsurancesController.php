<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Insurances;
//use function GuzzleHttp\json_encode;
use App\Models\Treatments;
use App\Models\Medicines;
use App\Models\Stocks;
use App\Models\InsuranceConsultationMaps;
use App\Models\InsuranceTreatmentMaps;
use App\Models\InsuranceMedicineMaps;

class InsurancesController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth:api');
    }

    public function index()
    {
        return Insurances::where('status_id', '!=', 7)->with('insured_consultation')->with('insured_medicines')->with('insured_treatments')->orderBy('name', 'asc')->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:250',
            'contact_no' => 'required|numeric|digits:11|unique:insurances',
            'remark' => 'nullable',
            'followup_days' => 'required|numeric|max:90',
            'status_id' => 'required|numeric|max:10'
        ]);
        Insurances::create([
            'name' => $request['name'],
            'contact_no' => $request['contact_no'],
            'remark' => $request['remark'],
            'followup_days' => $request['followup_days'],
            'approval_days' => $request['approval_days'],
            'status_id' => $request['status_id']
        ]);

        return ['message' => 'mapping is done'];

    }

    public function show($id)
    {
        $insurance = Insurances::findOrFail($id);
        return $insurance;
    }

    public function update(Request $request, $id)
    {
        $insurance = Insurances::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:250',
            'remark' => 'nullable',
            'followup_days' => 'required|numeric|max:90',
            'contact_no' => 'required|numeric|digits:11|unique:insurances,contact_no,'.$insurance->id,
            'status_id' => 'nullable|numeric|max:10'
        ]);

        $updates = [
            'name' => $request['name'],
            'contact_no' => $request['contact_no'],
            'followup_days' => $request['followup_days'],
            'remark' => $request['remark'],
            'approval_days' => $request['approval_days'],
            'status_id' => $request['status_id']
        ];
        $insurance->update($updates);
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $insurance = Insurances::findOrFail($id);
        $insurance->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $insurances = Insurances::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")->orwhere('contact_no','LIKE',"%$search%")->orwhere('remark','LIKE',"%$search%");
            })->latest()->paginate(15);
        }
        else{
           $insurances = '';
        }
        return $insurances;
    }

    public function search()
    {
        if($search = \Request::get('q')) {
            $insurances = Insurances::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%");
            })->with('insured_consultation')->with('insured_medicines')->with('insured_treatments')->where('status_id', '!=', 7)->orderBy('name', 'asc')->paginate(15);
        }
        else{
           $insurances = Insurances::where('status_id', '!=', 7)->with('insured_consultation')->with('insured_medicines')->with('insured_treatments')->orderBy('name', 'asc')->paginate(15);
        }
        return $insurances;
    }

    public function getList()
    {
        return Insurances::where('status_id', 2)->orderBy('name', 'asc')->get();
    }

    public function getMappedConsultations($iid = null)
    {
        $consultations = Treatments::where('appointment_type_id', 1)->where('status_id', 2)->orderBy('treatment', 'asc')->get();
        $result = [];
        foreach ($consultations as $key => $consult) {
            $mapped_consult = InsuranceConsultationMaps::where('insurance_id', $iid)->where('treatment_id', $consult->id)->first();
            if($mapped_consult): $discount = $mapped_consult->discount; $rid = $mapped_consult->id; else: $discount = 0; $rid = ''; endif;
            $result[$key] = ['name' => $consult->treatment,
                                'cost' => $consult->cost,
                                'discount' => $discount,
                                'tid' => $consult->id,
                                'insurance_id' => $iid,
                                'rid' => $rid];
        }
        return $result;
    }

    public function updateMappedConsultations(Request $request)
    {
        if($request->id):
            $record = InsuranceConsultationMaps::findOrFail($request->id);
            $record->update(['discount' => $request->discount]);
        else:
            $record = InsuranceConsultationMaps::create([
                            'insurance_id' => $request->insurance_id,
                            'treatment_id' => $request->treatment_id,
                            'discount' => $request->discount
                        ]);
        endif;
        return ['msg' => 'mapping is done'];
    }

    public function getMappedTreatments($iid = null)
    {
        $consultations = Treatments::where('appointment_type_id', 2)->where('status_id', 2)->orderBy('treatment', 'asc')->get();
        $result = [];
        foreach ($consultations as $key => $consult) {
            $mapped_consult = InsuranceTreatmentMaps::where('insurance_id', $iid)->where('treatment_id', $consult->id)->first();
            if($mapped_consult):
                $discount = $mapped_consult->discount;
                $rid = $mapped_consult->id;
                $cd34 = $mapped_consult->cd34;
                $cd56 = $mapped_consult->cd56;
            else:
                $discount = 0;
                $rid = '';
                $cd34 = 0;
                $cd56 = 0;
            endif;
            $result[$key] = ['name' => $consult->treatment,
                                'cost' => $consult->cost,
                                'discount' => $discount,
                                'tid' => $consult->id,
                                'cd34' => $cd34,
                                'cd56' => $cd56,
                                'insurance_id' => $iid,
                                'rid' => $rid];
        }
        return $result;
    }

    public function updateMappedTreatments(Request $request)
    {
        if($request->id):
            $record = InsuranceTreatmentMaps::findOrFail($request->id);
            $record->update(['discount' => $request->discount,
            'cd34' => $request->cd34,
            'cd56' => $request->cd56,]);
        else:
            $record = InsuranceTreatmentMaps::create([
                            'insurance_id' => $request->insurance_id,
                            'treatment_id' => $request->treatment_id,
                            'discount' => $request->discount,
                            'cd34' => $request->cd34,
                            'cd56' => $request->cd56,
                        ]);
        endif;
        return ['msg' => 'mapping is done'];
    }

    public function getMappedMedicines($iid = null)
    {
        $medicines = Medicines::orderBy('name', 'asc')->get();
        $result = [];
        foreach ($medicines as $key => $consult) {
            $mapped_med = InsuranceMedicineMaps::where('insurance_id', $iid)->where('medicine_id', $consult->id)->first();
            if($mapped_med): $rid = $mapped_med->id; else:$rid = ''; endif;
            $result[$key] = ['name' => $consult->name,
                                'medicine_id' => $consult->id,
                                'insurance_id' => $iid,
                                'insurance_price' => $consult->insurance_price,
                                'rid' => $rid];
        }
        return $result;
    }

    public function updateMappedMedicines(Request $request)
    {
        $record = InsuranceMedicineMaps::create([
                            'insurance_id' => $request->insurance_id,
                            'medicine_id' => $request->medicine_id,
                        ]);
        return ['msg' => 'mapping is done'];
    }

    public function updateMapping(Request $request)
    {
        if($request->type == 3):
            $record = InsuranceMedicineMaps::findOrFail($request->id);
            return ['result' => $record->delete()];
        elseif($request->type == 2):
            $record = InsuranceTreatmentMaps::findOrFail($request->id);
            return ['result' => $record->delete()];
        elseif($request->type == 1):
            $record = InsuranceConsultationMaps::findOrFail($request->id);
            return ['result' => $record->delete()];
        endif;
    }

    public function removeCashDiscount($iid = null)
    {
        $record = Insurances::findOrFail($iid);
        $record->update(['cash_discount' => 0]);
        return ['msg' => 'cash discount removed'];
    }

    public function addCashDiscount($iid = null)
    {
        $record = Insurances::findOrFail($iid);
        $record->update(['cash_discount' => 1]);
        return ['msg' => 'cash discount applied'];
    }
}
