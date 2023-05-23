<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Settlements;
use App\Models\Invoices;

class SettlementsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Settlements::select('settlements.*', 'locations.clinic_name as location', 'statuses.status as status', 'statuses.css as css', 'insurances.name as insurancer')
                    ->join('insurances', 'insurances.id', '=', 'settlements.insurance_id')
                    ->join('locations', 'locations.id', '=', 'settlements.location_id')
                    ->join('statuses', 'statuses.id', '=', 'settlements.status_id')
                    ->where('settlements.status_id', '!=', 7)
                    ->orderBy('settlements.receiving_date', 'desc')->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'reference_number' => 'required|string|max:250',
            'remark' => 'nullable',
            'insurance_id' => 'required|numeric',
            'receiving_date' => 'required|date',
            'amount' => 'required',
            'settlement_month' =>'required',
            'receipt' => 'nullable'
        ]);
        return Settlements::create([
            'reference_number' => strtoupper($request['reference_number']),
            'location_id' => Auth::user()->location_id,
            'remark' => $request['remark'],
            'insurance_id' => $request['insurance_id'],
            'receiving_date' => date('Y-m-d', strtotime($request['receiving_date'])),
            'settlement_month' => $request['settlement_month'].'-01',
            'amount' => $request['amount'],
            'receipt' => $request['receipt'],
            'status_id' => 2
        ]);
    }

    public function show($id)
    {
        $record = Settlements::select('settlements.*', 'locations.clinic_name as location', 'statuses.status as status', 'statuses.css as css', 'insurances.name as insurancer')
                            ->join('insurances', 'insurances.id', '=', 'settlements.insurance_id')
                            ->join('locations', 'locations.id', '=', 'settlements.location_id')
                            ->join('statuses', 'statuses.id', '=', 'settlements.status_id')
                            ->where('settlements.status_id', '!=', 7)
                            ->where('settlements.id', $id)
                            ->get()->first();

        $now = time();
        $your_date = strtotime($record->created_at);
        $datediff = $now - $your_date;

        $record->actionable = round($datediff / (60 * 60 * 24));
        $record->invoices = \explode(',', $record->invoices);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = Settlements::findOrFail($id);
        $this->validate($request, [
            'reference_number' => 'required|string|max:250',
            'remark' => 'nullable',
            'insurance_id' => 'required|numeric',
            'receiving_date' => 'required|date',
            'amount' => 'required',
            'settlement_month' =>'required',
            'receipt' => 'nullable'
        ]);
        $record->update([
            'reference_number' => strtoupper($request['reference_number']),
            'location_id' => Auth::user()->location_id,
            'remark' => $request['remark'],
            'insurance_id' => $request['insurance_id'],
            'receiving_date' => date('Y-m-d', strtotime($request['receiving_date'])),
            'settlement_month' => $request['settlement_month'].'-01',
            'amount' => $request['amount'],
            'receipt' => $request['receipt'],
        ]);
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = Settlements::findOrFail($id);
        $record->update(['status_id' => 7]);
        return ['message' => 'record deleted'];
    }

    public function search(Request $request)
    {
        $conditions = [];

        if($request['reference_number']): $conditions["settlements.reference_number"] = $request['reference_number']; endif;
        if($request['insurance_id']): $conditions["settlements.insurance_id"] = $request['insurance_id']; endif;
        if($request['receiving_date']): $conditions["settlements.receiving_date"] = $request['receiving_date']; endif;
        if($request['status_id']): $conditions["settlements.status_id"] = $request['status_id']; endif;

        return Settlements::select('settlements.*', 'locations.clinic_name as location', 'statuses.status as status', 'statuses.css as css', 'insurances.name as insurancer')
                            ->join('insurances', 'insurances.id', '=', 'settlements.insurance_id')
                            ->join('locations', 'locations.id', '=', 'settlements.location_id')
                            ->join('statuses', 'statuses.id', '=', 'settlements.status_id')
                            ->where('settlements.status_id', '!=', 7)
                            ->where($conditions)
                            ->orderBy("settlements.receiving_date", 'desc')->paginate(15);
    }

    public function getList()
    {
        return Settlements::where('status_id', 5)->get();
    }

    public function invoiceSettlement(Request $request)
    {
        $record = Settlements::findOrFail($request->settlement_id);
        if($record->invoices): $invoices = \explode(',', $record->invoices);
        else: $invoices = []; endif;
        $invoices = array_merge($invoices, [$request->invoice_number]);
        $record->update(['invoices' => \implode(',', $invoices) ]);

        $year = '20'.substr($request['invoice_number'], 3, 2);
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');

        $invoice = $table->where('invoice_number', $request['invoice_number'])->get()->first();

        $invoice->update(['ins_clearance' => 1]);

        return ['message' => 'Record updated successfully.'];
    }

    public function allInvoiceSettlement(Request $request)
    {
        $record = Settlements::findOrFail($request->settlement_id);

        $rinvoices = [];
        $conditions = [];
        $sdate = date('Y-m-d', strtotime($record['settlement_month']));

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.date('Y', strtotime($sdate)).'s');
        $itablename = $itable->getTable();

        if($request['insurance_id']): $conditions["$itablename.insurance_id"] = $record['insurance_id']; endif;

        $invoices =  $itable->where("$itablename.location_id", Auth::user()->location_id)
                            ->where($conditions)
                            ->whereMonth("$itablename.invoice_date", date('m', strtotime($sdate)))
                            ->where("$itablename.cancel", 0)
                            ->orderBy("$itablename.created_at", 'desc')->get();

        foreach ($invoices as $invoice) {
            $finvoice = $itable->where('invoice_number', $invoice->invoice_number)->get()->first();
            $finvoice->update(['ins_clearance' => 1]);
            array_push($rinvoices, $invoice->invoice_number);
        }
        $record->update(['invoices' => \implode(',', $rinvoices) ]);

        return ['message' => 'Record updated successfully.'];
    }

    public function invoiceSettlementRollBack(Request $request)
    {
        $record = Settlements::findOrFail($request->settlement_id);
        if($record->invoices): $invoices = \explode(',', $record->invoices);
        else: $invoices = []; endif;
        if (($key = array_search($request['invoice_number'], $invoices)) !== false) {
            unset($invoices[$key]);
        }
        $record->update(['invoices' => \implode(',', $invoices) ]);

        $year = '20'.substr($request['invoice_number'], 3, 2);
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');

        $invoice = $table->where('invoice_number', $request['invoice_number'])->get()->first();

        $invoice->update(['ins_clearance' => 0]);

        return ['message' => 'Record updated successfully.'];
    }
}
