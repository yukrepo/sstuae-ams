<?php

namespace App\Http\Controllers\Reports;;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Appointments;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoices;
use App\Models\Treatments;
use App\Models\Doctors;
use App\Models\Insurances;
use App\Models\UserDocuments;
use DB;

class ConsultReportsController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function OverallReport($sdate = null, $edate = null)
    {
        $output = [];
        $columns = ['S.No.' => 'sno', 
                    'Consultation' => 'consultation', 
                    'Insurance Qty' => 'ins_qty',
                    'Insurance Amount' => 'ins_amount',
                    'Insurance Deductable CashQty' => 'ins_cash',
                    'Insurance Deductable Visa' => 'ins_visa',
                    'Cash Qty' => 'cash_qty',
                    'Cash Amount' => 'cash_amount',
                    'Visa Qty' => 'visa_qty',
                    'Visa Amount' => 'visa_amount',
                    'E-Payment Qty' => 'epay_qty',
                    'E-Payment Amount' => 'epay_amount',
                    'Credit Qty' => 'credit_qty',
                    'Credit Amount' => 'credit_amount',
                    'Total Qty' => 'total_qty',
                    'Total Amount' => 'total_amount',
                    'Insurance Discount' => 'ins_discount',
                    'Cash Discount' => 'cash_discount', 
                    'Receiving' => 'receiving' ];
                  
        $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

        $year =  date('Y', strtotime($sdate));
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        $itable =  new Invoices;
        $itable = $itable->setTable('invoice'.$year.'s');
        $itablename = $itable->getTable();

        foreach($data as $key => $index)
        {
            $record = $table->where('appointment_type_id', 1)
                            ->whereDate('date','<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate('date','>=',date('Y-m-d', strtotime($sdate)))
                            ->where('treatment_id', $index->id)->whereIn('status_id', [5,8,10])->get()->count();
            $tcost = Treatments::where('id', $index->id)->get()->first();

            $invoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->where("$tablename.is_dummy", 0)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->where("$itablename.insurance_id", '>', 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");

            $idinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.inc_deduction_value");

            $cdinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.bliss_discount_value");


            $cinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->where("$itablename.payment_mode", 'cash')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $vainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $vinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->where("$itablename.payment_mode", 'visa')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $eainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->where("$itablename.payment_mode", 'epay')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $einvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->where("$itablename.payment_mode", 'epay')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $crainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->where("$itablename.payment_mode", 'credit')
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.payable_amount");
            $crinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.payment_mode", 'credit')
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->where("$itablename.insurance_id", 1)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();

            $cvinvoice = 0;

            $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.insurance_id", '>', 1)
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->sum("$itablename.inc_deduction_value");

            $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                            ->where("$tablename.appointment_type_id", 1)
                            ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                            ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                            ->where("$tablename.treatment_id", $index->id)
                            ->where("$itablename.insurance_id", '>', 1)
                            ->where("$tablename.location_id", Auth::user()->location_id)
                            ->whereIn("$tablename.status_id", [5,8,10])
                            ->get()->count();
                            
            $newdata = [ 'sno' => ++$key, 
                        'consultation' => ucwords($index->treatment),
                        'ins_qty' => $ciinvoice,
                        'ins_amount' => number_format($iinvoice+$invoice2+$vinvoice2, 3),
                        'ins_cash' => $invoice2,
                        'ins_visa' => $vinvoice2,
                        'cash_qty' => $cinvoice,
                        'cash_amount' => number_format($invoice, 3),
                        'visa_qty' => $vinvoice,
                        'visa_amount' => number_format($vainvoice, 3),
                        'epay_qty' => $einvoice,
                        'epay_amount' => number_format($eainvoice, 3),
                        'credit_qty' => $crinvoice,
                        'credit_amount' => number_format($crainvoice, 3),
                        'total_qty' => $record,
                        'total_amount' => number_format($record*$tcost->cost, 3),
                        'ins_discount' => number_format($idinvoice, 3),
                        'cash_discount' => number_format($cdinvoice, 3),
                        'receiving'  => number_format(($iinvoice+$invoice+$vainvoice+$eainvoice+$crainvoice), 3)
                    ];
            array_push($output, $newdata);
        }

        return ['json_fields' => $columns, 'records' => $output];
        die;
    }

    public function PrescriptionReport($sdate = null, $edate = null)
    {
        $output = [];
        
        $columns = ['S.No.' => 'sno',
                        'Appointment ID' => 'appointment_id',
                        'Doctor' => 'doctor',
                        'Customer ID' => 'customerID',
                        'Customer Name' => 'CustomerName',
                        'Customer Mobile No' => 'CustomerMobileNo',
                        'Customer Nationality' => 'CustomerNationality',
                        'Consultation' => 'Consultation',
                        'Symptoms' => 'Symptoms',
                        'General Examination' => 'GeneralExamination',
                        'Lab Test' => 'LabTest',
                        'Diagnosis' => 'Diagnosis',
                        'Therapies' => 'Therapies',
                        'Medicines' => 'Medicines',
                        'Appointment Invoice' => 'AppointmentInvoice',
                        'Medicine Invoice' => 'MedicineInvoice',
                        'Course ID' => 'CourseID'];

        $year =  date('Y', strtotime($sdate));
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        $record = $table->where('appointment_type_id', 1)
                        ->where('location_id', Auth::user()->location_id)
                        ->whereDate('date','<=', date('Y-m-d', strtotime($edate)))
                        ->where("$tablename.is_dummy", 0)
                        ->whereDate('date','>=',date('Y-m-d', strtotime($sdate)))
                        ->whereIn('status_id', [5,8,10])
                        ->orderBy('doctor_id', 'asc')->get();

        foreach($record as $key => $index)
        {
            $medicines = [];
            if($index->medicines):
                $decode = json_decode($index->medicines);
                foreach($decode as $value): $medicines[] ='('.$value->name.' - '.$value->qty.')'; endforeach;
                $medicines = implode(', ', $medicines);
            else: $medicines = '-';  endif;

            $therapies = [];
            if($index->therapies):
                $decode = json_decode($index->therapies);
                foreach($decode as $value): $therapies[] ='('.$value->name.' - '.$value->days.')'; endforeach;
                $therapies = implode(', ', $therapies);
            else: $therapies = '-';  endif;

            $symptoms = [];
            if($index->symptoms):
                $decode = json_decode($index->symptoms);
                foreach($decode as $value): $symptoms[] ='('.$value->name.')'; endforeach;
                $symptoms = implode(', ', $symptoms);
            else: $symptoms = '-';  endif;

            $oe_categories = [];
            if($index->oe_categories):
                $decode = json_decode($index->oe_categories);
                foreach($decode as $value): $oe_categories[] ='('.$value->name.' - '.$value->result.')'; endforeach;
                $ge = implode(', ', $oe_categories);
            else: $ge = '-';  endif;

            $tests = [];
            if($index->tests):
                $decode = json_decode($index->tests);
                foreach($decode as $value): $tests[] ='('.$value->name.')'; endforeach;
                $lt = implode(', ', $tests);
            else: $lt = '-';  endif;


            $diagnosis = [];
            if($index->diagnosis):
                $decode = json_decode($index->diagnosis);
                foreach($decode as $value): $diagnosis[] ='('.$value->name.' - '.$value->code.')'; endforeach;
                $diagnosis = implode(', ', $diagnosis);
            else: $diagnosis = '-';  endif;
            
            $newdata = ['sno' => $key+1,
                        'appointment_id' => $index->appointment_code,
                        'doctor' => $index->doctor->name,
                        'customerID' => $index->user->username,
                        'CustomerName' => $index->user_profile->first_name,
                        'CustomerMobileNo' => $index->user->mobile,
                        'CustomerNationality' => $index->user_profile->nationality->nationality,
                        'Consultation' => $index->treatment->treatment,
                        'Symptoms' => $symptoms,
                        'GeneralExamination' => $ge,
                        'LabTest' => $lt,
                        'Diagnosis' => $diagnosis,
                        'Therapies' => $therapies,
                        'Medicines' => $medicines,
                        'AppointmentInvoice' => $index->ainvoice,
                        'MedicineInvoice' => $index->pinvoice,
                        'CourseID' => $index->course_id];
            
            array_push($output, $newdata);
        }

        return ['json_fields' => $columns, 'records' => $output];
        die;
    }

    public function DrbasedReport($sdate = null, $edate = null)
    {
        $output = [];
        $columns = ['S.No.' => 'sno', 
                    'Consultation' => 'consultation', 
                    'Insurance Qty' => 'ins_qty',
                    'Insurance Amount' => 'ins_amount',
                    'Insurance Deductable CashQty' => 'ins_cash',
                    'Insurance Deductable Visa' => 'ins_visa',
                    'Cash Qty' => 'cash_qty',
                    'Cash Amount' => 'cash_amount',
                    'Visa Qty' => 'visa_qty',
                    'Visa Amount' => 'visa_amount',
                    'E-Payment Qty' => 'epay_qty',
                    'E-Payment Amount' => 'epay_amount',
                    'Credit Qty' => 'credit_qty',
                    'Credit Amount' => 'credit_amount',
                    'Total Qty' => 'total_qty',
                    'Total Amount' => 'total_amount',
                    'Insurance Discount' => 'ins_discount',
                    'Cash Discount' => 'cash_discount', 
                    'Receiving' => 'receiving' ];
        $sep = [ 'sno' => '-', 
                    'consultation' => '-',
                    'ins_qty' => '-',
                    'ins_amount' => '-',
                    'ins_cash' => '-',
                    'ins_visa' => '-',
                    'cash_qty' => '-',
                    'cash_amount' => '-',
                    'visa_qty' => '-',
                    'visa_amount' => '-',
                    'epay_qty' => '-',
                    'epay_amount' => '-',
                    'credit_qty' => '-',
                    'credit_amount' => '-',
                    'total_qty' => '-',
                    'total_amount' => '-',
                    'ins_discount' => '-',
                    'cash_discount' => '-',
                    'receiving'  => '-' ];

        if(Auth::user()->admin_type_id == 2):
            $doctors = Doctors::where('designation_type_id', 1)->where('location_id', Auth::user()->location_id)->where('id', Auth::user()->relative_id)->where('status_id', 2)->get();
        else:
            $doctors = Doctors::where('designation_type_id', 1)->where('location_id', Auth::user()->location_id)->where('status_id', 2)->get();
        endif;

        foreach($doctors as $doctor)
        {
            $ndata = [ 'sno' => '-', 
                        'consultation' => $doctor->name,
                        'ins_qty' => '-',
                        'ins_amount' => '-',
                        'ins_cash' => '-',
                        'ins_visa' => '-',
                        'cash_qty' => '-',
                        'cash_amount' => '-',
                        'visa_qty' => '-',
                        'visa_amount' => '-',
                        'epay_qty' => '-',
                        'epay_amount' => '-',
                        'credit_qty' => '-',
                        'credit_amount' => '-',
                        'total_qty' => '-',
                        'total_amount' => '-',
                        'ins_discount' => '-',
                        'cash_discount' => '-',
                        'receiving'  => '-' ];

            array_push($output, $ndata);

            $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

            $year =  date('Y', strtotime($sdate));
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();

            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.$year.'s');
            $itablename = $itable->getTable();

            foreach($data as $key => $index)
            {
                $record = $table->where('appointment_type_id', 1)
                                ->whereDate('date','<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate('date','>=',date('Y-m-d', strtotime($sdate)))
                                ->where("doctor_id", $doctor->id)
                                ->where("$tablename.is_dummy", 0)
                                ->where('treatment_id', $index->id)->whereIn('status_id', [5,8,10])->get()->count();
                $tcost = Treatments::where('id', $index->id)->get()->first();

                $invoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$itablename.insurance_id", 1)
                                ->where("$tablename.is_dummy", 0)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.is_dummy", 0)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.is_dummy", 0)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $idinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.is_dummy", 0)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $cdinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.is_dummy", 0)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.bliss_discount_value");


                $cinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.is_dummy", 0)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $vainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.is_dummy", 0)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");
                $vinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.is_dummy", 0)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $eainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.is_dummy", 0)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'epay')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");
                $einvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.is_dummy", 0)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'epay')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $crainvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.is_dummy", 0)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'credit')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");
                $crinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.is_dummy", 0)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'credit')
                                ->where("$itablename.insurance_id", 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $cvinvoice = 0;

                $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->where("$tablename.is_dummy", 0)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.is_dummy", 0)
                                ->where("$tablename.doctor_id", $doctor->id)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.insurance_id", '>', 1)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();
                
                $newdata = [ 'sno' => ++$key, 
                                'consultation' => ucwords($index->treatment),
                                'ins_qty' => $ciinvoice,
                                'ins_amount' => number_format($iinvoice+$invoice2+$vinvoice2, 3),
                                'ins_cash' => $invoice2,
                                'ins_visa' => $vinvoice2,
                                'cash_qty' => $cinvoice,
                                'cash_amount' => number_format($invoice, 3),
                                'visa_qty' => $vinvoice,
                                'visa_amount' => number_format($vainvoice, 3),
                                'epay_qty' => $einvoice,
                                'epay_amount' => number_format($eainvoice, 3),
                                'credit_qty' => $crinvoice,
                                'credit_amount' => number_format($crainvoice, 3),
                                'total_qty' => $record,
                                'total_amount' => number_format($record*$tcost->cost, 3),
                                'ins_discount' => number_format($idinvoice, 3),
                                'cash_discount' => number_format($cdinvoice, 3),
                                'receiving'  => number_format(($iinvoice+$invoice+$vainvoice+$eainvoice+$crainvoice), 3)
                            ];
                array_push($output, $newdata);
            }

            array_push($output, $sep);

            $data = Treatments::orderBy('treatment', 'asc')->where('is_it_dual', 2)->where('status_id', 2)->get();

            foreach($data as $key2 => $index)
            {
                $trecords = $table->where('appointment_type_id', 2)
                                ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.is_dummy", 0)
                                ->where('second_doctor_id', $doctor->id)
                                ->where('treatment_id', $index->id)
                                ->whereIn('status_id', [2,4,5,9])
                                ->get()->count();

                $newdata = [ 'sno' => ++$key2, 
                                'consultation' => ucwords($index->treatment),
                                'ins_qty' => $trecords,
                                'ins_amount' => '-',
                                'ins_cash' => '-',
                                'ins_visa' => '-',
                                'cash_qty' => '-',
                                'cash_amount' => '-',
                                'visa_qty' => '-',
                                'visa_amount' => '-',
                                'epay_qty' => '-',
                                'epay_amount' => '-',
                                'credit_qty' => '-',
                                'credit_amount' => '-',
                                'total_qty' => '-',
                                'total_amount' => '-',
                                'ins_discount' => '-',
                                'cash_discount' => '-',
                                'receiving'  => '-' ];
                array_push($output, $newdata);
            }

            
            array_push($output, $sep);
        }

        return ['json_fields' => $columns, 'records' => $output];
        die;
    }

    public function InsbasedReport($sdate = null, $edate = null)
    {
        $output = [];
        $columns = ['S.No' => 'sno',
                    'Consulatation' => 'consultation',
                    'Insurance Qty' => 'qty',
                    'Insurance Amount' => 'amt',
                    'Insurance Deductable Cash' => 'ded_cash',
                    'Insurance Deductable Visa' => 'ded_visa'];

        $sep = ['sno' => '-',
                'consultation' => '-',
                'qty' => '-',
                'amt' => '-',
                'ded_cash' => '-',
                'ded_visa' => '-'];

        $insurances = Insurances::where('status_id', 2)->get();

        foreach($insurances as $insurance)
        {
            $ndata = ['sno' => '',
                    'consultation' => $insurance->name,
                    'qty' => '-',
                    'amt' => '-',
                    'ded_cash' => '-',
                    'ded_visa' => '-'];

            array_push($output, $ndata);

            $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

            $year =  date('Y', strtotime($sdate));
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();

            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.$year.'s');
            $itablename = $itable->getTable();

            foreach($data as $key => $index)
            {
                $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->whereDate("$itablename.updated_at",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$itablename.updated_at",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$tablename.is_dummy", 0)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$tablename.location_id", Auth::user()->location_id)
                                ->where("$itablename.insurance_id", $insurance->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->whereDate("$itablename.updated_at",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$itablename.updated_at",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$tablename.location_id", Auth::user()->location_id)
                                ->where("$itablename.insurance_id", $insurance->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->whereDate("$itablename.updated_at",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$itablename.updated_at",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$tablename.location_id", Auth::user()->location_id)
                                ->where("$itablename.insurance_id", $insurance->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->whereDate("$itablename.updated_at",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$itablename.updated_at",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$tablename.location_id", Auth::user()->location_id)
                                ->where("$itablename.insurance_id", $insurance->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $newdata = ['sno' => ++$key,
                    'consultation' => ucwords($index->treatment),
                    'qty' => $ciinvoice,
                    'amt' => number_format($iinvoice+$invoice2+$vinvoice2, 3),
                    'ded_cash' => number_format($invoice2, 3),
                    'ded_visa' => number_format($vinvoice2, 3)];

                array_push($output, $newdata);    
            }

            array_push($output, $sep);
            
        }

        return ['json_fields' => $columns, 'records' => $output];
        die;
    }

    public function InsDetailReport($iid = null, $sdate = null, $edate = null)
    {
        $output = [];
        $columns = ['S.No' => 'sno',
                    'Consulatation' => 'consultation',
                    'Insurance Qty' => 'qty',
                    'Insurance Amount' => 'amt',
                    'Insurance Deductable Cash' => 'ded_cash',
                    'Insurance Deductable Visa' => 'ded_visa'];

        $sep = ['sno' => '-',
                'consultation' => '-',
                'qty' => '-',
                'amt' => '-',
                'ded_cash' => '-',
                'ded_visa' => '-'];

        $insurances = Insurances::where('status_id', 2)->where('id', $iid)->get();

        foreach($insurances as $insurance)
        {
            $ndata = ['sno' => '',
                    'consultation' => $insurance->name,
                    'qty' => '-',
                    'amt' => '-',
                    'ded_cash' => '-',
                    'ded_visa' => '-'];

            array_push($output, $ndata);

            $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 1)->where('status_id', 2)->get();

            $year =  date('Y', strtotime($sdate));
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();

            $itable =  new Invoices;
            $itable = $itable->setTable('invoice'.$year.'s');
            $itablename = $itable->getTable();

            foreach($data as $key => $index)
            {
                $invoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.is_dummy", 0)
                                ->whereDate("$itablename.updated_at",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$itablename.updated_at",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'cash')
                                ->where("$tablename.location_id", Auth::user()->location_id)
                                ->where("$itablename.insurance_id", $insurance->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $vinvoice2 = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.is_dummy", 0)
                                ->whereDate("$itablename.updated_at",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$itablename.updated_at",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$itablename.payment_mode", 'visa')
                                ->where("$tablename.location_id", Auth::user()->location_id)
                                ->where("$itablename.insurance_id", $insurance->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.payable_amount");

                $iinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.is_dummy", 0)
                                ->whereDate("$itablename.updated_at",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$itablename.updated_at",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$tablename.location_id", Auth::user()->location_id)
                                ->where("$itablename.insurance_id", $insurance->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->sum("$itablename.inc_deduction_value");

                $ciinvoice = $table->join("$itablename", "$itablename.invoice_number", '=', "$tablename.ainvoice")
                                ->where("$tablename.appointment_type_id", 1)
                                ->where("$tablename.is_dummy", 0)
                                ->whereDate("$itablename.updated_at",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("$itablename.updated_at",'>=',date('Y-m-d', strtotime($sdate)))
                                ->where("$tablename.treatment_id", $index->id)
                                ->where("$tablename.location_id", Auth::user()->location_id)
                                ->where("$itablename.insurance_id", $insurance->id)
                                ->whereIn("$tablename.status_id", [5,8,10])
                                ->get()->count();

                $newdata = ['sno' => ++$key,
                    'consultation' => ucwords($index->treatment),
                    'qty' => $ciinvoice,
                    'amt' => number_format($iinvoice+$invoice2+$vinvoice2, 3),
                    'ded_cash' => number_format($invoice2, 3),
                    'ded_visa' => number_format($vinvoice2, 3)];

                array_push($output, $newdata);    
            }

            array_push($output, $sep);
            
        }

        return ['json_fields' => $columns, 'records' => $output];
        die;
    }

    public function overallTreatmentReport($sdate = null, $edate)
    {
        $output = [];
        $columns = ['S.No.' => 'sno',
                    'Therapist' => 'therapist',
                    'Date' => 'date',
                    'Appointment ID' => 'appointmentID',
                    'Client ID' => 'clientID',
                    'Client Name' => 'clientName',
                    'Time' => 'time',
                    'Type' => 'type',
                    'Treatment' => 'treatment',
                    'Status' => 'status'];

        $sep = ['sno' => '-',
                'therapist' => '-',
                'date' => '-',
                'appointmentID' => '-',
                'clientID' => '-',
                'clientName' => '-',
                'time' => '-',
                'type' => '-',
                'treatment' => '-',
                'status' => '-'];

        $doctors = Doctors::where('designation_type_id', 2)->where("location_id", Auth::user()->location_id)->where('status_id', 2)->get();

        foreach($doctors as $doctor)
        {
            $year =  date('Y', strtotime($sdate));
            $table =  new Appointments;
            $table = $table->setTable('appointment'.$year.'s');
            $tablename = $table->getTable();

            $data = $table->select("$tablename.*", 'statuses.status as status', 'user_profiles.first_name', 'user_profiles.last_name', 'users.username', 'treatments.treatment as reason')
                        ->with('start_time')->with('end_time')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where('doctor_id', $doctor->id)
                        ->where("$tablename.location_id", Auth::user()->location_id)
                        ->where("$tablename.is_dummy", 0)
                        ->whereDate("$tablename.date",'<=', date('Y-m-d', strtotime($edate)))
                        ->whereDate("$tablename.date",'>=',date('Y-m-d', strtotime($sdate)))
                        ->whereIn("$tablename.status_id", [2,4,5,9])
                        ->get();

            foreach($data as $key => $index)
            {
                $newdata = ['sno' => ++$key,
                        'therapist' => ucwords($doctor->name),
                        'date' => date('d-M-Y', strtotime($index->date)),
                        'appointmentID' => $index->appointment_code,
                        'clientID' => $index->username,
                        'clientName' => ucwords($index->first_name.' '.$index->last_name),
                        'time' => $index->start_time->time.' - '.$index->end_time->closing,
                        'type' => 'Course',
                        'treatment' => $index->reason,
                        'status' => $index->status];
                array_push($output, $newdata);
            }

            array_push($output, $sep);
        }

        return ['json_fields' => $columns, 'records' => $output];
        die;
    }

    public function drbasedTreatmentReport($sdate = null, $edate = null)
    {
        $output = [];
        $columns = [];

        $doctors = Doctors::where('designation_type_id', 2)->where("location_id", Auth::user()->location_id)->where('status_id', 2)->orderBy('gender', 'asc')->get();

        $row = [];
        $count = $doctors->count();
        $count = ($count+1)*2;
        for ($i=1; $i <= $count; $i++) { 
            $columns['COL '.$i] = 'col_'.$i;
            $row['col_ '.$i] = '-';
        }
        array_push($output, $row);

        $row1 = ['col_1' => 'S.No.', 'col_2' => 'Treatment'];
        $row2 = ['col_1' => '-', 'col_2' => '-'];
        foreach($doctors as $dk => $doctor)
        {
            $i = ($dk+1)*2;
            $row1['col_'.($i+1)] = $doctor->name;
            $row1['col_'.($i+2)] = '-';
            $row2['col_'.($i+1)] = 'T.C.P.';
            $row2['col_'.($i+2)] = 'T.C.S.';
        }
        array_push($output, $row1);
        array_push($output, $row2);

        $data = Treatments::orderBy('treatment', 'asc')->where('appointment_type_id', 2)->where('status_id', 2)->get();

        $year =  date('Y', strtotime($sdate));
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();

        foreach($data as $key => $index)
        {
            $newrow = ['col_1' => ++$key, 'col_2' => ucwords($index->treatment)];
            

            foreach($doctors as $dk => $doctor)
            {
                $record = $table->where('appointment_type_id', 2)
                                ->where('treatment_id', $index->id)
                                ->where('doctor_id', $doctor->id)
                                ->whereIn('status_id', [2,4,5,9])
                                ->where("$tablename.is_dummy", 0)
                                ->whereDate("date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->get()->count();

                $record2 = $table->where('appointment_type_id', 2)
                                ->where('treatment_id', $index->id)
                                ->where('second_doctor_id', $doctor->id)
                                ->whereIn('status_id', [2,4,5,9])
                                ->where("$tablename.is_dummy", 0)
                                ->whereDate("date",'<=', date('Y-m-d', strtotime($edate)))
                                ->whereDate("date",'>=',date('Y-m-d', strtotime($sdate)))
                                ->get()->count();
                
                $i = ($dk+1)*2;
                $newrow['col_'.($i+1)] = $record;
                $newrow['col_'.($i+2)] = $record2;
            }

            array_push($output, $newrow);
        }      

        return ['json_fields' => $columns, 'records' => $output];
        die;
    }

}
