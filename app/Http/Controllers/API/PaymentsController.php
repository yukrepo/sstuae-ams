<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payments;

class PaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        return Payments::select('payments.*', 'purchases.grand_total')
        ->join('purchases', 'purchases.id', '=', 'payments.purchase_id')
        ->where('payments.id', $id)
        ->get();
    }

    public function update(Request $request, $id)
    {
        $payment = Payments::findOrFail($id);
        $this->validate($request, [
            'secondpay_date' => 'required',
            'secondpay_mode' => 'required',
            'secondpay_desc' => 'required'
        ]);
        $payment->update([
            'secondpay_date' => $request['secondpay_date'],
            'secondpay_mode' => $request['secondpay_mode'],
            'secondpay_desc' => $request['secondpay_desc'],
            'pay_status' => 1
        ]);
        return ['message' => 'data updated.'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
