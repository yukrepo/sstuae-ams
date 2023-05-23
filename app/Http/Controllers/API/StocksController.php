<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Medicines;
use App\Models\Appointments;
use App\Models\Stocks;
use App\Models\SaleMedicines;
use App\Models\Purchases;
use App\Models\Payments;
use Illuminate\Support\Facades\Auth;

class StocksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Stocks::select('stocks.*', 'medicines.name', 'medicines.insurance_price', 'medicines.cash_price', 'purchases.invoice_number as purchase_invoice')
                        ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')
                        ->join('purchases', 'purchases.id', '=', 'stocks.purchase_id')
                        ->orderBy('purchases.created_at', 'desc')
                        ->where('stocks.location_id', Auth::user()->location_id)
                        ->paginate(15);
    }

    public function store(Request $request)
    {

        $this->validate($request, [
             'invoice_number' => 'required',
             'shipment_number' => 'required',
             'purchase_date' => 'required|date',
             'supplier_id' => 'required|numeric',
             'sub_total' => 'required',
             'grand_total' => 'required',
             'bill_copy' => 'required',
             'remark' => 'required',
             'payment_mode' => 'required'
        ]);

        if($request->bill_copy) {
            $photo = time().'.'.explode('/', explode(':', substr($request->bill_copy, 0, strpos($request->bill_copy, ';')))[1])[1];
            \Image::make($request->bill_copy)->save(public_path('files/docs/').$photo);
        }
        else{
            $photo = '';
        }

        $purchase = Purchases::create([
                                'added_by' => Auth::user()->id,
                                'location_id' => Auth::user()->location_id,
                                'invoice_number' => $request['invoice_number'],
                                'supplier_id' => $request['supplier_id'],
                                'sub_total' => $request['sub_total'],
                                'discount_desc' => $request['discount_desc'],
                                'discount_percent' => $request['discount_percent'],
                                'discount_amount' => $request['discount_amount'],
                                'tax_percent' => $request['tax_percent'],
                                'tax_amount' => $request['tax_amount'],
                                'grand_total' => $request['grand_total'],
                                'remark' => $request['remark'],
                                'bill_copy' => $photo,
                                'shipment_number' => $request['shipment_number'],
                                'purchase_date' => date('Y-m-d', strtotime($request['purchase_date']))
        ]);

        if($request['payment_mode'] == 'CREDIT'): $pstatus = 0;
        else: $pstatus = 1; endif;

        Payments::create([
            'location_id' => Auth::user()->location_id,
            'payment_type' => $request['payment_type'],
            'purchase_id' => $purchase['id'],
            'firstpay_mode' => $request['payment_mode'],
            'firstpay_amount' => $request['grand_total'],
            'secondpay_amount' => 0,
            'firstpay_date' => $request['purchase_date'],
            'firstpay_receiver' =>  Auth::user()->id,
            'firstpay_desc' => $request['payment_desc'],
            'pay_status' => $pstatus,
        ]);

        foreach ($request['product_detail__id'] as $key => $detail) {
            if($detail):
                if(isset($request['product_detail__foc'][$key])):
                    $stockF = $request['product_detail__stock'][$key]+$request['product_detail__foc'][$key];
                    $stockRC = $request['product_detail__stock'][$key]+$request['product_detail__foc'][$key];
                    $foc =  $request['product_detail__foc'][$key];
                else:
                    $stockF = $request['product_detail__stock'][$key];
                    $stockRC = $request['product_detail__stock'][$key];
                    $foc =  0;
                endif;
                Stocks::create([
                    'purchase_id' =>$purchase['id'],
                        'medicine_id' => $detail,
                        'location_id' => Auth::user()->location_id,
                        //'packsize' => $request['medicine_detail__packsize'][$key],
                        'stock' => $stockF,
                        'received_stock' => $stockRC,
                        'adjustments' => 0,
                        'purchase_cost' => $request['product_detail__purchase_cost'][$key],
                        'exp_date' => date('Y-m-d', strtotime($request['product_detail__exp_date'][$key])),
                        'batch_no' => $request['product_detail__batch_no'][$key],
                        'foc' => $foc,
                        //'manufecturer_id' => $request['manufecturer_id'],
                ]);
            endif;
        }


        return ['message' => 'medicine created'];
    }

    public function show($id)
    {
        return Stocks::select('stocks.*', 'medicines.name')
                        ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')
                        ->where('stocks.purchase_id', $id)
                        ->get();
    }

    public function showById($id)
    {
        return Stocks::select('stocks.*', 'medicines.name', 'medicines.cash_price', 'medicines.insurance_price')
                            ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')
                            ->where('stocks.id', $id)
                            ->get()->first();
    }

    public function update(Request $request, $id)
    {
        if($request->type == 1):
            $this->validate($request, [
                'purchase_cost' => 'required',
                'exp_date' => 'required',
                'received_stock' => 'required'
            ]);

            $record = Stocks::where('id', $request['id'])->get()->first();
            if($record):
                $data = [];
                $diff = $request->received_stock - $record->received_stock;
                if($record->reason):
                    $data = json_decode($record->reason);
                    $newdata[] = ['date' => date('Y-m-d', time()),
                            'old_data' => ['available' => $record->stock, 'received' => $record->received_stock],
                            'new_data' => ['available' => $record->stock + $diff, 'received' => $record->received_stock + $diff],
                            'title' => 'Stock Modified',
                            'type' => 1,
                            'admin' => Auth::user()->name];
                    $data = array_merge($data, $newdata);
                else:
                    $data[] = ['date' => date('Y-m-d', time()),
                                'old_data' => ['available' => $record->stock, 'received' => $record->received_stock],
                                'new_data' => ['available' => $record->stock + $diff, 'received' => $record->received_stock + $diff],
                                'title' => 'Stock Modified',
                                'type' => 1,
                                'admin' => Auth::user()->name];
                endif;

                Stocks::where('id', $request['id'])->update([
                                        'received_stock' => $record->received_stock + $diff,
                                        'stock' => $record->stock + $diff,
                                        'purchase_cost' => $request->purchase_cost,
                                        'exp_date' => date('Y-m-d', strtotime($request['exp_date'])),
                                        'reason' => json_encode($data)
                                    ]);
            endif;

        elseif($request->type == 2):
            $this->validate($request, [
                'adjustments' => 'required',
                'reason' => 'required'
            ]);

            $record = Stocks::where('id', $request['id'])->get()->first();

            if($record):
                $data = [];
                if($record->reason):
                    $data = json_decode($record->reason);
                    $newdata[] = ['date' => date('Y-m-d', time()),
                            'old_data' => ['available' => $record->stock, 'adjustments' => $record->adjustments],
                            'new_data' => ['available' => $record->stock - $request->adjustments, 'adjustments' => $request->adjustments + $record->adjustments],
                            'title' => 'Adjustment',
                            'reason' => $request->reason,
                            'type' => 2,
                            'admin' => Auth::user()->name];
                    $data = array_merge($data, $newdata);
                else:
                    $data[] = ['date' => date('Y-m-d', time()),
                                'old_data' => ['available' => $record->stock, 'adjustments' => $record->adjustments],
                                'new_data' => ['available' => $record->stock - $request->adjustments, 'adjustments' => $request->adjustments + $record->adjustments],
                                'title' => 'Adjustment',
                                'reason' => $request->reason,
                                'type' => 2,
                                'admin' => Auth::user()->name];
                endif;

                Stocks::where('id', $request['id'])->update([
                                    'adjustments' => $request->adjustments + $record->adjustments,
                                    'stock' => $record->stock - $request->adjustments,
                                    'reason' => json_encode($data)
                                ]);
            endif;
        endif;
        return ['message' => 'updating data'];
    }

    public function destroy($id)
    {
        $customer = Stocks::findOrFail($id);
        //$customer->delete();
        return ['message' => 'record deleted'];
    }

    public function search(Request $request)
    {
        $conditions = [];

        if($request['batch_no']): $conditions['stocks.batch_no'] = $request['batch_no']; endif;
        if($request['medicine_id']): $conditions['stocks.medicine_id'] = $request['medicine_id']; endif;
        if($request['supplier_id']): $conditions['purchases.supplier_id'] = $request['supplier_id']; endif;
        if($request['stock'] == 1): $conditions['stocks.stock'] = 0;
        elseif($request['stock'] == 2): $conditions[] = ['stocks.stock', '>=', 1]; endif;

        if($request['created_at']):
            return Stocks::select('stocks.*', 'medicines.name', 'medicines.insurance_price', 'medicines.cash_price',                                                'purchases.invoice_number as purchase_invoice')
                        ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')
                        ->join('purchases', 'purchases.id', '=', 'stocks.purchase_id')
                        ->orderBy('purchases.created_at', 'desc')
                        ->where('stocks.location_id', Auth::user()->location_id)
                        ->where($conditions)
                        ->whereDate('stocks.created_at', $request['created_at'])
                        ->paginate(15);
        else:
            if(count($conditions) == 0): return []; endif;
            return Stocks::select('stocks.*', 'medicines.name', 'medicines.insurance_price', 'medicines.cash_price',                                                'purchases.invoice_number as purchase_invoice')
                        ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')
                        ->join('purchases', 'purchases.id', '=', 'stocks.purchase_id')
                        ->orderBy('purchases.created_at', 'desc')
                        ->where('stocks.location_id', Auth::user()->location_id)
                        ->where($conditions)
                        ->paginate(15);
        endif;
    }

    public function findByBarcode()
    {
        if($search = \Request::get('q')) {
            $stock = Stocks::select('stocks.*', 'medicines.name')
                            ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')
                            ->where(function($query) use ($search){
                                    $query->where('barcode','=',$search);
                            })->get();
        }
        else{
           $stock = [];
        }
        return $stock;
    }

    public function byAppointment($id = null)
    {
        $year = '20'.substr($id, 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $record = $table->select("$tablename.medicines")
                        ->where('appointment_code', $id)
                        ->first();
        $medicines = json_decode($record['medicines']);
        //return $medicines;
        foreach ($medicines as $key => $medicine) {
            $stocks = Stocks::where('medicine_id', $medicine->id)->get();
            foreach ($stocks as $stock) {
                $return[$medicine->id][] = ['value' => $stock->id,
                                            'text' => 'Batch No - '.$stock->batch_no.', Exp Date - '.date('Y-m-d', strtotime($stock->exp_date)).', Stock - '.$stock->stock];
            }
        }
        return $return;
    }

    public function getFullSelectList()
    {
        $stocks = Stocks::select('stocks.*', 'medicines.name')
                        ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')->where('stocks.location_id', Auth::user()->location_id)->get();
        $result = [];
        foreach($stocks as $stock):
            $result[] = ['value' => $stock->id, 'text' => $stock->name.' - '.$stock->batch_no.' - '.date('d-m-Y', strtotime($stock->exp_date)).' - '.$stock->stock.' - '.number_format($stock->selling_cost, 2)];
        endforeach;
        return $result;
    }

    public function getListByMedicine($id = null)
    {
        $stocks = Stocks::where('medicine_id', $id)->where('stocks.location_id', Auth::user()->location_id)->orderBy('exp_date', 'asc')->get();
        $result = [];
        foreach($stocks as $stock):
            if($stock->stock >= 1):
                $result[] = ['value' => $stock->id, 'text' =>$stock->batch_no.' - '.date('d-m-Y', strtotime($stock->exp_date)).' - '.$stock->stock];
            endif;
        endforeach;
        return $result;
    }

    public function getMedicineStocks($id = null)
    {
        $stocks = Stocks::where('medicine_id', $id)->with('medicine')->where('stocks.stock', '>=', 1)->where('stocks.location_id', Auth::user()->location_id)->orderBy('exp_date', 'desc')->get();
        if($stocks):
            return ['data' => $stocks, 'status' => 'success'];
        else:
            return ['data' => 'Out of Stock', 'status' => 'failed'];
        endif;
    }

    public function getExpiringByMedicine($id = null)
    {
        $stock = Stocks::select('stocks.*', 'medicines.name', 'medicines.insurance_price', 'medicines.cash_price')
                            ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')
                            ->where('stocks.location_id', Auth::user()->location_id)
                            ->where('stocks.medicine_id', $id)->where('stocks.stock', '>=', 1)->orderBy('stocks.exp_date', 'asc')->get()->first();
        return $stock;
    }

    public function getInsuredExpiringByMedicine(Request $request)
    {
        $returnMdes = []; $counter = []; $ckey = 0;
        if(count($request['medicines']) >= 1):
            foreach ($request['medicines'] as $key => $medicine):
                $stock = Stocks::select('stocks.*', 'medicines.name', 'medicines.insurance_price', 'medicines.cash_price')
                            ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')
                            ->join('insurance_medicine_maps', 'medicines.id', '=', 'insurance_medicine_maps.medicine_id')
                            ->where('stocks.medicine_id', $medicine['id'])
                            ->where('stocks.location_id', Auth::user()->location_id)
                            ->where('insurance_medicine_maps.insurance_id', $request['insurance_id'])
                            ->where('stocks.stock', '>=', 1)
                            ->orderBy('stocks.exp_date', 'asc')
                            ->get()->first();
                if(($stock) && ($stock['stock'])):
                    array_push($counter, $ckey);
                    if($medicine['qty'] < $stock->stock):
                        $returnMdes['qty'][$ckey] = $medicine['qty'];
                        $returnMdes['stock_check'][$ckey] = 0;
                    else:
                        $returnMdes['qty'][$ckey] = $stock->stock;
                        $returnMdes['stock_check'][$ckey] = 1;
                    endif;
                    $returnMdes['batch_no'][$ckey] = $stock->batch_no.' ('.date('d.m.Y', strtotime($stock->exp_date)).')';
                    $returnMdes['stock_id'][$ckey] = $stock->id;
                    $returnMdes['cost'][$ckey] = $stock->insurance_price;
                    $returnMdes['stock'][$ckey] = $stock->stock;
                    $returnMdes['name'][$ckey] = $medicine['name'];
                    $returnMdes['id'][$ckey] = $medicine['id'];
                    $returnMdes['total'][$ckey] =  $stock->insurance_price*$returnMdes['qty'][$ckey];
                    $ckey = $ckey + 1;
                endif;
            endforeach;
            return ['counter' => $counter, 'medicines' => $returnMdes];
        endif;
    }

    public function getPurchaseStocks($pid = null)
    {
        return Stocks::select('stocks.*', 'medicines.name', 'medicines.barcode')
                ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')
                ->where('stocks.purchase_id', $pid)
                ->get();
    }

    public function getStockLedger(Request $request)
    {
        $stocks = []; $medicine = '';
        $medicine = Medicines::select('medicines.*', 'categories.name as category_name', 'categories.unit')
                            ->join('categories', 'categories.id', '=', 'medicines.category_id')
                            ->where('medicines.status_id', '!=', '7')
                            ->where('medicines.id', $request->barcode)->get()->first();
        if($medicine):
            $stocks = Stocks::select('stocks.*', 'purchases.invoice_number as purchase_invoice')
                            ->join('purchases', 'purchases.id', '=', 'stocks.purchase_id')
                            ->orderBy('purchases.created_at', 'desc')
                            ->where('stocks.medicine_id', $medicine->id)
                            ->where('stocks.location_id', Auth::user()->location_id)->get();
        endif;
        return ['stocks' =>  $stocks, 'medicine' => $medicine];
    }

    public function getSaleStockLedger($id = null)
    {
        $sales = []; $adjustments = [];
        $stock = Stocks::where('id', $id)->get()->first();
        if($stock):
            $adjustments = json_decode($stock->reason);
            $sales = SaleMedicines::select('sale_medicines.*', 'sales.invoice_number', 'sales.revised', 'sales.created_at', 'user_profiles.first_name', 'user_profiles.last_name', 'admins.name')
                                ->join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                                ->join('users', 'sales.customer_id', '=', 'users.id')
                                ->join('admins', 'sales.added_by', '=', 'admins.id')
                                ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                                ->where('stock_id', $id)
                                ->orderBy('sales.created_at', 'desc')->get();
        endif;
        return ['sales' =>  $sales, 'adjustments' => $adjustments];
    }

    public function getStockStatus($days = null)
    {
        $currentDate = date('Y-m-d', time());
        if($days == 30):
            $startDate = $currentDate;
            $endDate = date('Y-m-d', strtotime($currentDate.' + 30 days'));
        elseif($days == 60):
            $startDate = date('Y-m-d', strtotime($currentDate.' + 30 days'));
            $endDate = date('Y-m-d', strtotime($currentDate.' + 60 days'));
        else:
            $startDate = date('Y-m-d', strtotime($currentDate.' + 60 days'));
            $endDate = date('Y-m-d', strtotime($currentDate.' + 90 days'));
        endif;
        return Stocks::select('stocks.batch_no', 'stocks.exp_date', 'stocks.received_stock', 'stocks.stock', 'stocks.created_at', 'medicines.name')
                        ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')
                        ->orderBy('stocks.exp_date', 'asc')
                        ->where('stocks.location_id', Auth::user()->location_id)
                        ->whereBetween('stocks.exp_date', [$startDate, $endDate])
                        ->get();
    }
}

