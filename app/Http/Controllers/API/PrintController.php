<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Models\Appointments;
use App\Models\Invoices;
use App\Models\Sales;
use App\Models\SaleMedicines;

class PrintController extends Controller
{
    public function prescription($id = null)
    {
        $encoded_code = explode('-', base64_decode($id));
        $year = '20'.substr($encoded_code[0], 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $appointment = $table->select("$tablename.*", 'statuses.status as status', 'statuses.css as status_css', 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type',  'locations.clinic_name as location', 'user_profiles.first_name', 'user_profiles.last_name', 'user_profiles.address as address', 'user_profiles.city as city', 'users.mobile as mobile', 'user_profiles.address', 'user_profiles.gender', 'users.username', 'user_profiles.married', 'user_profiles.date_of_birth', 'doctors.name as doctor', 'treatments.treatment as reason', 'user_documents.insurance_copy', 'user_documents.policy_number', 'user_documents.insurance_verified', 'insurances.name as insurancer', 'admins.name as admin', 'user_documents.insurance_id')
                        ->join('statuses', 'statuses.id', '=', "$tablename.status_id")
                        ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->join('users', 'users.id', '=', "$tablename.user_id")
                        ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('locations', 'locations.id', '=', "$tablename.location_id")
                        ->join('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                        ->join('user_documents', 'user_documents.user_id', '=', 'users.id')
                        ->join('insurances', 'insurances.id', '=', 'user_documents.insurance_id')
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where('appointment_code', $encoded_code[0])
                        ->where("$tablename.user_id", $encoded_code[1])
                        ->get()->first();
        return $appointment;
    }

    public function uctinvoice($id = null)
    {
        $encoded_code = explode('-', base64_decode($id));
        $year = '20'.substr($encoded_code[1], 0, 2);
        $code = $encoded_code[0].'-'.$encoded_code[1].'-'.$encoded_code[2];
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();
        $invoice = $table->select("$tablename.*", 'admins.name as admin')
                        ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                        ->where('invoice_number', $code)
                        ->get()->first();

        $year = '20'.substr($invoice->appointment_id, 1, 2);
        $table =  new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $appointment = $table->select("$tablename.*", 'visit_types.visit_type as visit_type', 'appointment_types.appointment_type as appointment_type', 'doctors.name as doctor', 'treatments.treatment as reason', 'admins.name as admin')
                        ->join('visit_types', 'visit_types.id', '=', "$tablename.visit_type_id")
                        ->join('appointment_types', 'appointment_types.id', '=', "$tablename.appointment_type_id")
                        ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                        ->join('doctors', 'doctors.id', '=', "$tablename.doctor_id")
                        ->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                        ->where('appointment_code', $invoice->appointment_id)
                        ->where("$tablename.user_id", $encoded_code[3])
                        ->get()->first();
        $customer = Users::with('user_profile')->where('id', $encoded_code[3])->get()->first();

        return ['invoice' => $invoice, 'appointment' => $appointment, 'customer' => $customer];
    }

    public function dpinvoice($id = null)
    {
        $encoded_code = explode('-', base64_decode($id));
        $year = '20'.substr($encoded_code[1], 0, 2);
        $code = $encoded_code[0].'-'.$encoded_code[1].'-'.$encoded_code[2];
        $table =  new Invoices;
        $table = $table->setTable('invoice'.$year.'s');
        $tablename = $table->getTable();
        $invoice = $table->select("$tablename.*", 'admins.name as admin')
                        ->join('admins', 'admins.id', '=', "$tablename.admin_id")
                        ->where('invoice_number', $code)
                        ->get()->first();
        $sale = Sales::where('invoice_number', $code)->get()->first();
        $sale_medicines =  SaleMedicines::where('sale_id', $sale->id)->get();
        $return = [];
        foreach ($sale_medicines as $key => $sale) {
            $return[$key]['spid'] = $sale['id'];
            $return[$key]['medicine_id'] = $sale['medicine_id'];
            $return[$key]['stock_id'] = $sale['stock_id'];
            $return[$key]['price'] = $sale['price'];
            $return[$key]['qty'] = $sale['qty'];
            $return[$key]['total'] = $sale['qty']*$sale['price'];
            $return[$key]['rowdata'] = json_decode($sale['rowdata']);
        }
        $customer = Users::with('user_profile')->where('id', $encoded_code[3])->get()->first();

        return ['invoice' => $invoice, 'sale' => $sale, 'sale_medicines' => $return, 'customer' => $customer];
    }
}
