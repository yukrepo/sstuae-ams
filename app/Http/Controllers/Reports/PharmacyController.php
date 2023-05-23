<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointments;
use App\Models\Insurances;
use App\Models\Invoices;
use App\Models\Stocks;
use App\Models\SaleMedicines;
use App\Models\Medicines;
use App\Models\Treatments;
use App\Models\Doctors;
use DB;

class PharmacyController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function fullStockReport()
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $columns = array('S.No.', 'Medicine', 'Batch No', 'Exp Date', 'Cost', 'Total Stock', 'Available Stock');
        fputcsv($output, $columns);

        $data = Stocks::select('stocks.*', 'medicines.name', 'medicines.cash_price')
                        ->join('medicines', 'medicines.id', '=', 'stocks.medicine_id')->get();
         foreach($data as $key => $index)
         {
            fputcsv($output, array(++$key, $index->name, $index->batch_no, date('d-m-Y', strtotime($index->exp_date)), $index->cash_price, $index->received_stock, $index->stock ));
         }
         rewind($output);
         header('Content-type: application/octet-stream');
         header('Content-Disposition: attachment; filename="full-stock-report.csv"');
         echo stream_get_contents($output);
         fclose($output);
         die;
    }

    public function medicineStockReport($sdate = null, $edate = null)
    {
        $output = [];
        $columns = ['S.No.' => 'sno', 
                    'Medicine' => 'medicine', 
                    'Opening Stock' => 'openingstock', 
                    'Purchase Stock' => 'purchasing', 
                    'Sold Qty' => 'sales', 
                    'Current Stock' => 'currentstock'];

        $data = Medicines::leftJoin(DB::raw('(SELECT medicine_id, SUM(stock) as stock_sum FROM stocks GROUP BY medicine_id) AS stocks'), function ($join) { $join->on('stocks.medicine_id', '=', 'medicines.id'); })->orderBy('name', 'asc')->get();

        foreach($data as $key => $index)
        {
            $out = SaleMedicines::join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                                ->whereDate('sale_medicines.created_at','<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate('sale_medicines.created_at','>=',date('Y-m-d', strtotime($sdate)))
                                ->where('sale_medicines.medicine_id', $index->id)
                                ->where('sales.status', 1)->sum('sale_medicines.qty');
            $in = Stocks::join('purchases', 'purchases.id', '=', 'stocks.purchase_id')
                                ->whereDate('purchases.created_at', '<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate('purchases.created_at', '>=',date('Y-m-d', strtotime($sdate)))
                                ->where('stocks.medicine_id', $index->id)
                                ->where('purchases.location_id', Auth::user()->location_id)
                                ->sum('stocks.received_stock');
            $op = $index->stock_sum - $in + $out;

            $newdata = ['sno' => ++$key,
                        'medicine' => ucwords($index->name),
                        'openingstock' => $op,
                        'purchasing' => $in,
                        'sales' => $out,
                        'currentstock' => number_format($index->stock_sum)];
            array_push($output, $newdata);
        }
        return ['json_fields' => $columns, 'records' => $output];
        die;
    }

    public function medicineSalesReport($sdate = null, $edate = null)
    {
        $output = [];
        
        $columns = ['S.No.' => 'sno',
                    'Medicine' => 'medicine',
                    'Qty' => 'qty',
                    'Rate' => 'rate',
                    'Total' => 'total'];
        
        $data = Medicines::leftJoin(DB::raw('(SELECT medicine_id, SUM(stock) as stock_sum FROM stocks GROUP BY medicine_id) AS stocks'), function ($join) { $join->on('stocks.medicine_id', '=', 'medicines.id'); })->orderBy('name', 'asc')->get();
        $total = 0; $qty = 0; $counter = 0;
        
        foreach($data as $key => $index)
        {
            $cout = SaleMedicines::join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                            ->whereDate('sale_medicines.created_at','<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate('sale_medicines.created_at','>=',date('Y-m-d', strtotime($sdate)))
                            ->where('sale_medicines.medicine_id', $index->id)
                            ->where('sales.status', 1)->sum('sale_medicines.qty');
            if($cout >= 1) {
                $newdata = ['sno' => ++$counter,
                    'medicine' => ucwords($index->name),
                    'qty' => $cout,
                    'rate' => number_format($index->cash_price, 3),
                    'total' => number_format($cout*$index->cash_price, 3)];
                    
                $total = $total+ ($cout*$index->cash_price);
                $qty = $qty + $cout;

                array_push($output, $newdata);
            }
        }
        $data = ['sno' => '-',
                    'medicine' => 'TOTAL',
                    'qty' => $qty,
                    'rate' => '0.000',
                    'total' => number_format($total, 3)];
        array_push($output, $data);
        return ['json_fields' => $columns, 'records' => $output];
        die;
    }

    public function detailedStockReport($sdate = null, $edate = null)
    {
        $memoryToUse = 50*1024*1024*1024*1024;
        $output = fopen('php://temp/maxmemory:'.$memoryToUse, 'r+');
        $header = array('-', 'Detailed Stock Report ');
        fputcsv($output, $header);
        $sep = array(' ', ' ', ' ', ' ');
        fputcsv($output, $sep);
        $columns = array('S.No.', 'Medicine', 'Batch No', 'Exp Date', 'FOC', 'Received Stock', 'Available');
        fputcsv($output, $columns);
        $data = Stocks::with('medicine')->where('stocks.location_id', Auth::user()->location_id)
                            ->orderBy('medicine_id', 'asc')
                            ->get();
        foreach($data as $key => $index)
        {
            fputcsv($output, array(++$key, ucwords($index->medicine->name), $index->batch_no, date('d-M-Y', strtotime($index->exp_date)), number_format($index->foc), number_format($index->received_stock), number_format($index->stock) ));
        }
        rewind($output);
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="medicine-custom-stock-report'.'.csv"');
        echo stream_get_contents($output);
        fclose($output);
        die;
    }

    public function directSalesReport($sdate = null, $edate = null)
    {
        $output = [];
        $columns = ['S.No.' => 'sno',
                    'Medicine' => 'medicine',
                    'Invoice Date' => 'date',
                    'Invoice No' => 'invoice_no',
                    'Client' => 'client',
                    'Qty' => 'qty',
                    'Price' => 'price',
                    'Total Amount' => 'total'];
        
        $data = Medicines::orderBy('name', 'asc')->get();
        $count = 1;
        foreach($data as $key => $index)
        {
            $out = SaleMedicines::select('sale_medicines.*', 'sales.invoice_number', 'user_profiles.first_name', 'user_profiles.last_name')->join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                        ->join('users', 'sales.customer_id', '=', 'users.id')
                        ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                        ->whereDate('sale_medicines.created_at','<=', date('Y-m-d', strtotime($edate)))
                        ->whereDate('sale_medicines.created_at','>=',date('Y-m-d', strtotime($sdate)))
                        ->where('sale_medicines.medicine_id', $index->id)
                        ->where('sales.status', 1)->get();

            foreach ($out as $key2 => $value2)
            {
                $name = $value2->first_name.' '.($value2->last_name?$value2->last_name:'');
                
                $newdata = ['sno' => ++$count,
                            'medicine' =>  ucwords($index->name),
                            'date' => date('d-M-Y', strtotime($value2->created_at)),
                            'invoice_no' => $value2->invoice_number,
                            'client' => $name,
                            'qty' => $value2->qty,
                            'price' => $index->cash_price,
                            'total' => number_format(($index->cash_price*$value2->qty), 3)];

                array_push($output, $newdata);
                $count++;
            }
        }
        return ['json_fields' => $columns, 'records' => $output];
        die;
    }

    public function drbasedSalesReport($sdate = null, $edate = null)
    {
        $output = [];
        $columns = ['S.No.' => 'sno',
                    'Medicine' => 'medicine',
                    'Qty' => 'qty',
                    'Price' => 'price',
                    'Cash' => 'cash',
                    'Insurance' => 'insurance',
                    'Total' => 'total'];
        
        $doctors = Doctors::where('location_id', Auth::user()->location_id)->where('designation_type_id', 1)->where('status_id', 2)->get();

        foreach ($doctors as $key => $doctor) 
        {
            $newdata = ['sno' => '-',
                        'medicine' => ucwords($doctor->name),
                        'qty' => '-',
                        'price' => '-',
                        'cash' => '-',
                        'insurance' => '-',
                        'total' => '-'];
            array_push($output, $newdata);
            $data = Medicines::orderBy('name', 'asc')->get();
            
            $itable =  new Appointments;
            $table = $itable->setTable('appointment'.date('Y', strtotime($sdate)).'s');
            $tablename = $itable->getTable();

            $invoices = $table->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                    ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                    ->where("$tablename.status_id", 5)
                                    ->where("$tablename.location_id", Auth::user()->location_id)
                                    ->where("$tablename.doctor_id", $doctor->id)
                                    ->where("$tablename.is_dummy", 0)
                                    ->orderBy("$tablename.date", 'asc')
                                    ->get()->pluck('pinvoice')->toArray();
            
            $invoices = array_filter($invoices);

            $invoices = implode(',', $invoices);
            $invoices = explode(',', $invoices);
            $cashinvoices = array_filter($invoices,
                function ($element) {
                  return strpos($element, 'CM') === false;
                }
            );
            $cashinvoices = array_values($cashinvoices);
            $insinvoices = array_filter($invoices,
                function ($element) {
                  return strpos($element, 'IM') === false;
                }
            );
            $insinvoices = array_values($insinvoices);
            
            $total = 0; $qty = 0; $counter = 0; $tc = 0; $ti = 0;
            
            foreach($data as $key => $index)
            {
                $cq = SaleMedicines::join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                                    ->where('sale_medicines.medicine_id', $index->id)
                                    ->where('sales.status', 1)
                                    ->whereIn('sales.invoice_number', $cashinvoices)->sum('sale_medicines.qty');
                $iq = SaleMedicines::join('sales', 'sales.id', '=', 'sale_medicines.sale_id')
                                    ->where('sale_medicines.medicine_id', $index->id)
                                    ->where('sales.status', 1)
                                    ->whereIn('sales.invoice_number', $insinvoices)->sum('sale_medicines.qty');
                $tq = $cq+$iq;            
                if($tq >= 1) {

                    $newdata = ['sno' => ++$counter,
                                'medicine' => ucwords($index->name),
                                'qty' => $cq+$iq,
                                'price' => number_format($index->cash_price, 3),
                                'cash' => number_format($cq*$index->cash_price, 3),
                                'insurance' => number_format($iq*$index->cash_price, 3),
                                'total' => number_format($tq*$index->cash_price, 3)];
                    array_push($output, $newdata);
                    
                    $total = $total+ ($tq*$index->cash_price);
                    $qty = $qty + $tq;
                    $tc = $tc + ($cq*$index->cash_price);
                    $ti = $ti + ($iq*$index->cash_price);
                }
            }
            $newdata = ['sno' => '-',
                        'medicine' => 'TOTAL',
                        'qty' => $qty,
                        'price' => '-',
                        'cash' => number_format($tc, 3),
                        'insurance' => number_format($ti, 3),
                        'total' => number_format($total, 3)];
            array_push($output, $newdata);
            $newdata = ['sno' => '-',
                        'medicine' => '-',
                        'qty' => '-',
                        'price' => '-',
                        'cash' => '-',
                        'insurance' => '-',
                        'total' => '-'];
            array_push($output, $newdata);
        }
        return ['json_fields' => $columns, 'records' => $output];
        die;
    }
}
