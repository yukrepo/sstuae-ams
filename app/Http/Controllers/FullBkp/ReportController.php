<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Insurances;
use Illuminate\Support\Facades\Auth;
use DB;

class ReportController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function fullReport()
    {
        $insurances = Insurances::where('status_id', 2)->where('id', '!=', 1)->orderBy('name', 'asc')->get();
        return view('reports.full')->with(compact('insurances'));
    }

    public function invoices()
    {
        $insurances = Insurances::where('status_id', 2)->where('id', '!=', 1)->orderBy('name', 'asc')->get();
        return view('reports.invoices')->with(compact('insurances'));
    }

    public function consultation()
    {
        $insurances = Insurances::where('status_id', 2)->where('id', '!=', 1)->orderBy('name', 'asc')->get();
        return view('reports.consultation')->with(compact('insurances'));
    }

    public function treatments()
    {
        $insurances = Insurances::where('status_id', 2)->where('id', '!=', 1)->orderBy('name', 'asc')->get();
        return view('reports.treatments')->with(compact('insurances'));
    }

    public function pharmacy()
    {
        $insurances = Insurances::where('status_id', 2)->where('id', '!=', 1)->orderBy('name', 'asc')->get();
        return view('reports.pharmacy')->with(compact('insurances'));
    }
}
