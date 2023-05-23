<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

use App\Models\FinancialYears;

class FinancialYearsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return FinancialYears::latest()->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'year' => 'required|numeric|max:2025|min:2014',
            'remark' => 'nullable'
        ]);
        if(($this->__CreateAppointmentTable($request['year']) == true) && ($this->__CreateAvailablityTable($request['year']) == true) && ($this->__CreateInvoiceTable($request['year']) == true)):
            return FinancialYears::create([
                'year' => $request['year'],
                'remark' => 'Appointments, Availabilities, Courses and Invoices are open for this year.'
            ]);
        else:
            return ['message' => 'Not created.'];
        endif;
    }

    public function show($id)
    {
        $record = FinancialYears::findOrFail($id);
        return $record;
    }

    public function update(Request $request, $id)
    {
        $record = FinancialYears::findOrFail($id);
        $this->validate($request, [
            'year' => 'required|numeric|max:2025|min:2014|unique:financial_years,year,'.$record->id,
            'remark' => 'nullable'
        ]);
        $record->update($request->all());
        return ['message' => 'Record updated successfully.'];
    }

    public function destroy($id)
    {
        $record = FinancialYears::findOrFail($id);
        $record->delete();
        return ['message' => 'record deleted'];
    }

    public function getList()
    {
        return FinancialYears::latest();
    }

    public function getAllList()
    {
        $return = [];
        $years = FinancialYears::latest()->get();
        foreach($years as $year):
            $return[] = $year['year'];
        endforeach;
        return $return;
    }

    public function getPrevList()
    {
        $return = [];
        $years = FinancialYears::latest()->where('year', '<=', date('Y', time()))->get();
        foreach($years as $year):
            $return[] = $year['year'];
        endforeach;
        return $return;
    }

    public function getNxtList()
    {
        $return = [];
        $years = FinancialYears::where('year', '>=', date('Y', time()))->get();
        foreach($years as $year):
            $return[] = $year['year'];
        endforeach;
        return $return;
    }

    public function getLastYear()
    {
        return FinancialYears::orderBy('id', 'desc')->first();
    }

    private function __CreateAppointmentTable($year = null)
    {
        if($year):
            Schema::create('appointment'.$year.'s', function($table) {
                $table->increments('id');
                $table->unsignedInteger('location_id');
                $table->string('followup_appointment', 100)->nullable();
                $table->string('course_id', 50)->nullable();
                $table->integer('admin_id');
                $table->integer('user_id');
                $table->string('appointment_code')->nullable();
                $table->integer('appointment_type_id');
                $table->integer('treatment_id');
                $table->integer('room_id')->nullable();
                $table->integer('doctor_id');
                $table->integer('second_doctor_id')->nullable();
                $table->integer('start_timeslot')->nullable();
                $table->integer('end_timeslot')->nullable();
                $table->text('user_remark')->nullable();
                $table->text('dr_remark')->nullable();
                $table->date('date');
                $table->integer('status_id');
                $table->integer('visit_Type_id');
                $table->text('tests')->nullable();
                $table->text('symptoms')->nullable();
                $table->text('medicines')->nullable();
                $table->text('therapies')->nullable();
                $table->text('diseases')->nullable();
                $table->text('diagnosis')->nullable();
                $table->text('oe_categories')->nullable();
                $table->text('drug_history')->nullable();
                $table->date('reminder_date')->nullable();
                $table->integer('reminder_days')->nullable();
                $table->string('pinvoice', 30)->nullable();
                $table->string('ainvoice', 30)->nullable();
                $table->text('cancel_reason')->nullable();
                $table->integer('inv_status', 5)->nullable();
                $table->timestamps();
            });
            return true;
        endif;
    }

    private function __CreateAvailablityTable($year = null)
    {
        if($year):
            Schema::create('availability'.$year.'s', function($table) {
                $table->increments('id');
                $table->unsignedInteger('location_id');
                $table->integer('admin_id')->nullable();
                $table->integer('doctor_id');
                $table->tinyInteger('type');
                $table->text('shift_timings')->nullable();
                $table->text('block_time_slots')->nullable();
                $table->text('remark')->nullable();
                $table->text('note')->nullable();
                $table->date('date');
                $table->string('hours', 100)->nullable();
                $table->integer('status_id');
                $table->timestamps();
            });
            return true;
        endif;
    }

    private function __CreateInvoiceTable($year = null)
    {
        if($year):
            Schema::create('invoice'.$year.'s', function($table) {
                $table->increments('id');
                $table->unsignedInteger('location_id');
                $table->integer('invoice_type');
                $table->integer('user_id')->nullable();
                $table->integer('admin_id');
                $table->string('invoice_number', 50)->nullable();
                $table->string('appointment_id', 50)->nullable();
                $table->double('amount', 8, 3)->nullable();
                $table->double('payable_amount', 8, 3)->nullable();
                $table->string('payment_mode', 99)->nullable();
                $table->text('payment_history')->nullable();
                $table->integer('payment_status', 10)->nullable();
                $table->tinyInteger('ins_clearance')->nullable();
                $table->string('bliss_discount', 200)->nullable();
                $table->double('bliss_discount_value', 8, 3)->nullable();
                $table->integer('insurance_id')->nullable();
                $table->string('inc_disc', 200)->nullable();
                $table->double('inc_disc_value', 8, 3)->nullable();
                $table->string('inc_deduction', 200)->nullable();
                $table->double('inc_deduction_value', 8, 3)->nullable();
                $table->string('coinsuarnce', 200)->nullable();
                $table->double('coinsuarnce_value', 8, 3)->nullable();
                $table->text('revise_history')->nullable();
                $table->text('rawdata')->nullable();
                $table->double('visa_amount', 8, 3)->nullable();
                $table->double('cash_amount', 8, 3)->nullable();
                $table->double('received', 8, 3)->nullable();
                $table->double('returable', 8, 3)->nullable();
                $table->tinyInteger('rtype', 5)->nullable();
                $table->integer('rcount')->nullable();
                $table->text('reason')->nullable();
                $table->text('partialpay')->nullable();
                $table->text('cancel')->nullable();
                $table->timestamps();
            });
            return true;
        endif;
    }

    private function __CreateCoursesTable($year = null)
    {
        if($year):
            Schema::create('course'.$year.'s', function($table) {
                $table->increments('id');
                $table->unsignedInteger('location_id');
                $table->string('course_code');
                $table->integer('admin_id');
                $table->integer('doctor_id');
                $table->integer('user_id');
                $table->date('start_date');
                $table->date('end_date');
                $table->text('remark')->nullable();
                $table->integer('status_id');
                $table->timestamps();
            });
            return true;
        endif;
    }
}
