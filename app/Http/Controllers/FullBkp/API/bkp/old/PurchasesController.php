<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Purchases;

class PurchasesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return Purchases::select('purchases.*', 'suppliers.name as supplier_name', 'payments.id as pay_id', 'payments.pay_status')
                        ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
                        ->join('payments', 'payments.purchase_id', '=', 'purchases.id')
                        ->withCount('stocks')
                        ->latest()->paginate(15);
    }

    public function show($id)
    {
        return Purchases::select('purchases.*', 'suppliers.name as supplier_name', 'payments.id as pay_id', 'payments.firstpay_mode')
                            ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
                            ->join('payments', 'payments.purchase_id', '=', 'purchases.id')
                            ->withCount('stocks')
                            ->where('purchases.id', $id)->withCount('stocks')->get()->first();
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function search(Request $request)
    {
        $conditions = [];
        if($request['supplier_id']): $conditions["purchases.supplier_id"] = $request['supplier_id']; endif;
        if($request['date']): $conditions["purchases.purchase_date"] = date('Y-m-d', strtotime($request['date'])); endif;

        return Purchases::select('purchases.*', 'suppliers.name as supplier_name', 'payments.id as pay_id', 'payments.pay_status')
        ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
        ->join('payments', 'payments.purchase_id', '=', 'purchases.id')
        ->where($conditions)
        ->withCount('stocks')
        ->latest()->paginate(15);
    }
}
