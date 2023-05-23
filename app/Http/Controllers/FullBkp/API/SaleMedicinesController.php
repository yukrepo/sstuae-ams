<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Medicines;
use App\Models\Sales;
use App\Models\SaleMedicines;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use DB;


class SaleMedicinesController extends Controller
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
        $sales =  SaleMedicines::where('sale_id', $id)->get();
        foreach ($sales as $key => $sale) {
            $return[$key]['spid'] = $sale['id'];
            $return[$key]['medicine_id'] = $sale['medicine_id'];
            $return[$key]['stock_id'] = $sale['stock_id'];
            $return[$key]['price'] = $sale['price'];
            $return[$key]['qty'] = $sale['qty'];
            $return[$key]['total'] = $sale['qty']*$sale['price'];
            $return[$key]['rowdata'] = json_decode($sale['rowdata']);
        }
        return $return;
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
