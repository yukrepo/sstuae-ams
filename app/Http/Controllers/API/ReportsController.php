<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reports\FullController;
use App\Http\Controllers\Reports\InvoiceController;
use App\Http\Controllers\Reports\PharmacyController;
use App\Http\Controllers\Reports\ConsultReportsController;
use App\Models\Appointments;
use App\Models\Insurances;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoices;
use App\Models\Stocks;
use App\Models\SaleMedicines;
use App\Models\Medicines;
use App\Models\Treatments;
use App\Models\Reports;
use DB;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $rtype = Reports::where('id', $request->report_type)->where('status', 1)->first();
        if($rtype):
            switch ($rtype->id) 
            {
                case 2: 
                    $fullReport = new FullController(); 
                    $response = $fullReport->stocks(); 
                    break;
                case 3: 
                    $fullReport = new FullController(); 
                    $response = $fullReport->medicines(); 
                    break;
                case 4: 
                    $fullReport = new FullController(); 
                    $response = $fullReport->customers($request->date[0], $request->date[1]);
                    break;
                case 5: 
                    $fullReport = new FullController(); 
                    $response = $fullReport->treatments(); 
                    break;
                case 6: 
                    $fullReport = new FullController(); 
                    $response = $fullReport->insurances(); 
                    break;
                case 7: 
                    $fullReport = new FullController(); 
                    $response = $fullReport->insuranceDetail($request->other_data); 
                    break;
                case 8: 
                    $fullReport = new InvoiceController(); 
                    $response = $fullReport->report($request->date[0], $request->date[1]); 
                    break;
                case 9: 
                    $fullReport = new InvoiceController(); 
                    $response = $fullReport->completeReport($request->date[0], $request->date[1]); 
                    break;
                case 10: 
                    $fullReport = new InvoiceController(); 
                    $response = $fullReport->cumulativeReport($request->date[0], $request->date[1], $request->other_data);
                    break;
                case 11: 
                    $fullReport = new PharmacyController(); 
                    $response = $fullReport->medicineStockReport($request->date[0], $request->date[1]);
                    break;
                case 12: 
                    $fullReport = new PharmacyController(); 
                    $response = $fullReport->medicineSalesReport($request->date[0], $request->date[1]);
                    break;
                case 13: 
                    $fullReport = new PharmacyController(); 
                    $response = $fullReport->drbasedSalesReport($request->date[0], $request->date[1]);
                    break;
                case 14: 
                    $fullReport = new PharmacyController(); 
                    $response = $fullReport->directSalesReport($request->date[0], $request->date[1]);
                    break;
                case 15: 
                    $report = new ConsultReportsController(); 
                    $response = $report->OverallReport($request->date[0], $request->date[1]);
                    break;
                case 16: 
                    $report = new ConsultReportsController(); 
                    $response = $report->PrescriptionReport($request->date[0], $request->date[1]); 
                    break;
                case 17: 
                    $report = new ConsultReportsController(); 
                    $response = $report->DrbasedReport($request->date[0], $request->date[1]); 
                    break;
                case 18: 
                    $report = new ConsultReportsController(); 
                    $response = $report->InsbasedReport($request->date[0], $request->date[1]); 
                    break;
                case 19: 
                    $report = new ConsultReportsController(); 
                    $response = $report->InsDetailReport($request->date[0], $request->date[1], $request->other_data); 
                    break;
                case 20: 
                    $report = new ConsultReportsController(); 
                    $response = $report->overallTreatmentReport($request->date[0], $request->date[1]); 
                    break;
                case 21: 
                    $report = new ConsultReportsController(); 
                    $response = $report->drbasedTreatmentReport($request->date[0], $request->date[1]); 
                    break;
                case 22: 
                    $report = new ConsultReportsController(); 
                    $response = $report->DrbasedReport($request->date[0], $request->date[1]); 
                    break;
                case 23: 
                    $report = new ConsultReportsController(); 
                    $response = $report->InsbasedReport($request->date[0], $request->date[1]); 
                    break;
                case 24: 
                    $fullReport = new InvoiceController(); 
                    $response = $fullReport->creditReport($request->date[0], $request->date[1]);
                    break;
                default: 
                    $response = ['records' => [], 'json_fields' => []];
            }

            $records = $response['records'];
            $json_fields = $response['json_fields'];
            $filename = str_replace(' ', '-', strtoupper($rtype->type)).'-'.str_replace(' ', '-', strtoupper($rtype->name)).'-FROM-'.substr($request->date[0], 0, 10).'-TO-'.substr($request->date[1], 0, 10).'-'.time().'.xls';
        else:
            $records = [];
            $json_fields = [];
            $filename = 'unknown-report-'.time().'.xls';
        endif;
        return ['records' => $records, 'json_fields' => $json_fields, 'filename' => $filename];
    }
}
