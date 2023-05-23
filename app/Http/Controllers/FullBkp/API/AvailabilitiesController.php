<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TimeSlots;
use App\Models\Availabilities;
use App\Models\FinancialYears;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use DB;

class AvailabilitiesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function makeAbsent(Request $request)
    {
        $this->validate($request, [
            'location_id' => 'nullable',
            'doctor_id' => 'nullable',
            'admin_id' => 'required',
            'remark' => 'nullable',
            'date' => 'required|string|max:190',
            'status_id' => 'required'
        ]);

        $table =  new Availabilities;
        $table = $table->setTable('availability'.date('Y', strtotime($request['date'])).'s');
        $tablename = $table->getTable();

        //return $request;
        for($i = 0 ; $i < $request['days']; $i++) {
            $str = strtotime('+'.$i.' days', strtotime($request['date']));
            $date = date('Y-m-d', $str);
            if(date('N', strtotime($date)) != 5):
                $response = DB::table($tablename)->insert([
                            'date' => $date,
                            'admin_id' => Auth::user()->id,
                            'doctor_id' => Auth::user()->relative_id,
                            'location_id' => Auth::user()->location_id,
                            'remark' => $request['remark'],
                            'block_time_slots' => 0,
                            'status_id' => 2,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                            ]);
            endif;
        }
        return ['response' => 'adding complete'];
    }

    public function makeAbsentByAdmin(Request $request)
    {
        $this->validate($request, [
            'location_id' => 'nullable',
            'doctor_id' => 'nullable',
            'admin_id' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'days' => 'required',
            'type' => 'required|numeric',
        ]);

        $table =  new Availabilities;
        $table = $table->setTable('availability'.date('Y', strtotime($request['start_date'])).'s');
        $tablename = $table->getTable();

        $startTime = strtotime($request['start_date']);
        $endTime = strtotime($request['end_date']);
        //return [$startTime, $endTime];
        for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
            $day = date('N', $i);
            if($request->type == 1):
                if(in_array(8, $request['days'])):
                    $check = DB::table($tablename)
                                ->where('doctor_id', $request['doctor_id'])
                                ->where('location_id', Auth::user()->location_id)
                                ->whereDate('date', date('Y-m-d', $i))
                                ->get()->first();
                    if($check) {
                        DB::table($tablename)->where('id', $check->id)
                            ->update([
                            'admin_id' => Auth::user()->id,
                            'remark' => $request['remark'],
                            'type' => $request['type'],
                            'shift_timings' => implode(',', $request['slots']),
                            'status_id' => 2,
                            'updated_at' => Carbon::now()
                        ]);
                    } else {
                        $response = DB::table($tablename)->insert([
                            'date' => date('Y-m-d', $i),
                            'admin_id' => Auth::user()->id,
                            'doctor_id' => $request['doctor_id'],
                            'type' => $request['type'],
                            'location_id' => Auth::user()->location_id,
                            'remark' => $request['remark'],
                            'shift_timings' => implode(',', $request['slots']),
                            'status_id' => 2,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                elseif(in_array($day, $request['days'])):
                    $check = DB::table($tablename)
                                ->where('doctor_id', $request['doctor_id'])
                                ->where('location_id', Auth::user()->location_id)
                                ->whereDate('date', date('Y-m-d', $i))
                                ->get()->first();
                    if($check) {
                        DB::table($tablename)->where('id', $check->id)
                            ->update([
                            'admin_id' => Auth::user()->id,
                            'remark' => $request['remark'],
                            'type' => $request['type'],
                            'shift_timings' => implode(',', $request['slots']),
                            'status_id' => 2,
                            'updated_at' => Carbon::now()
                        ]);
                    } else {
                        $response = DB::table($tablename)->insert([
                            'date' => date('Y-m-d', $i),
                            'admin_id' => Auth::user()->id,
                            'doctor_id' => $request['doctor_id'],
                            'location_id' => Auth::user()->location_id,
                            'remark' => $request['remark'],
                            'type' => $request['type'],
                            'shift_timings' => implode(',', $request['slots']),
                            'status_id' => 2,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                else:
                    // nothing
                endif;
            else:
                if(in_array(8, $request['days'])):
                    $check = DB::table($tablename)
                                ->where('doctor_id', $request['doctor_id'])
                                ->where('location_id', Auth::user()->location_id)
                                ->whereDate('date', date('Y-m-d', $i))
                                ->get()->first();
                    if($check) {
                        DB::table($tablename)->where('id', $check->id)
                            ->update([
                            'admin_id' => Auth::user()->id,
                            'remark' => $request['remark'],
                            'type' => $request['type'],
                            'block_time_slots' => implode(',', $request['slots']),
                            'shift_timings' => '',
                            'status_id' => 2,
                            'updated_at' => Carbon::now()
                        ]);
                    } else {
                        $response = DB::table($tablename)->insert([
                            'date' => date('Y-m-d', $i),
                            'admin_id' => Auth::user()->id,
                            'doctor_id' => $request['doctor_id'],
                            'type' => $request['type'],
                            'location_id' => Auth::user()->location_id,
                            'remark' => $request['remark'],
                            'block_time_slots' => implode(',', $request['slots']),
                            'status_id' => 2,
                            'shift_timings' => '',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                elseif(in_array($day, $request['days'])):
                    $check = DB::table($tablename)
                                ->where('doctor_id', $request['doctor_id'])
                                ->where('location_id', Auth::user()->location_id)
                                ->whereDate('date', date('Y-m-d', $i))
                                ->get()->first();
                    if($check) {
                        DB::table($tablename)->where('id', $check->id)
                            ->update([
                            'admin_id' => Auth::user()->id,
                            'type' => $request['type'],
                            'remark' => $request['remark'],
                            'block_time_slots' =>  implode(',', $request['slots']),
                            'shift_timings' => '',
                            'status_id' => 2,
                            'updated_at' => Carbon::now()
                        ]);
                    } else {
                        $response = DB::table($tablename)->insert([
                            'date' => date('Y-m-d', $i),
                            'admin_id' => Auth::user()->id,
                            'doctor_id' => $request['doctor_id'],
                            'location_id' => Auth::user()->location_id,
                            'remark' => $request['remark'],
                            'shift_timings' => '',
                            'type' => $request['type'],
                            'block_time_slots' => implode(',', $request['slots']),
                            'status_id' => 2,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                endif;
            endif;
        }

        return ['response' => 'adding complete'];
    }

    public function getMonthlyAvailabilities($date = null, $did = null)
    {
            $table =  new Availabilities;
            $allreturn = [];
            $subdate = explode('-', $date);
            $table = $table->setTable('availability'.$subdate[0].'s');
            $tablename = $table->getTable();

            if(Auth::user()->admin_type_id == 2):
                $records = $table->select("$tablename.*")
                        ->whereMonth('date', $subdate[1])
                        ->whereYear('date', $subdate[0])
                        ->where("$tablename.doctor_id", Auth::user()->relative_id)
                        ->orderBy('date', 'asc')->get();
            else:
                $records = $table->select("$tablename.*")
                        ->whereMonth('date', $subdate[1])
                        ->whereYear('date', $subdate[0])
                        ->where("$tablename.doctor_id", $did)
                        ->orderBy('date', 'asc')->get();
            endif;

            foreach($records as $record):
                $allreturn[$record->date] = $record;
            endforeach;
        return $allreturn;
    }

    public function getDayShifts(Request $request)
    {
        $table =  new Availabilities;
        $subdate = explode('-', $request->date);
        $table = $table->setTable('availability'.$subdate[0].'s');
        $tablename = $table->getTable();
        $shift = $table->where('location_id', Auth::user()->location_id)
                        ->where('doctor_id', $request->doctor_id)
                        ->whereDate('date', '=', date('Y-m-d', strtotime($request->date)))
                        ->get()->first();
        if($shift->type == 1):
            $shift->slots = array_filter(explode(',', $shift->shift_timings));
            $shift->bslots = array_filter(explode(',', $shift->shift_timings));
        else:
            $shift->slots = array_filter(explode(',', $shift->block_time_slots));
            $shift->bslots = array_filter(explode(',', $shift->block_time_slots));
        endif;
        return $shift;
    }

    public function modifySlots(Request $request)
    {
        $this->validate($request, [
            'doctor_id' => 'nullable',
            'type' => 'required',
            'remark' => 'required',
            //'slots' => 'required'
        ]);
        $table =  new Availabilities;
        $subdate = explode('-', $request->date);
        $table = $table->setTable('availability'.$subdate[0].'s');
        $tablename = $table->getTable();
        $shift = $table->where('id', $request['id'])
                        ->get()->first();
        if($request->type == 1):
            $slots = array_merge($request['slots'], $request['bslots']);
            DB::table($tablename)->where('id', $request->id)
                            ->update([
                            'admin_id' => Auth::user()->id,
                            'remark' => $request['remark'],
                            'type' => $request['type'],
                            'shift_timings' => implode(',', $slots),
                            'status_id' => 2,
                            'hours' => $request->hours,
                            'updated_at' => Carbon::now()
                        ]);
        else:
            DB::table($tablename)->where('id', $request->id)
                                ->update([
                                'admin_id' => Auth::user()->id,
                                'remark' => $request['remark'],
                                'type' => $request['type'],
                                'block_time_slots' => implode(',', $bslots),
                                'shift_timings' => '',
                                'status_id' => 2,
                                'hours' => $request->hours,
                                'updated_at' => Carbon::now()
                            ]);
        endif;
        return ['Slots updated'];
    }

    public function createSlots(Request $request)
    {
        $this->validate($request, [
            'doctor_id' => 'nullable',
            'type' => 'required',
            'hours' => 'required',
            'slots' => 'required'
        ]);
        $table =  new Availabilities;
        $subdate = explode('-', $request->date);
        $table = $table->setTable('availability'.$subdate[0].'s');
        $tablename = $table->getTable();
        if($request->type == 1):
            DB::table($tablename)->insert([
                'date' => date('Y-m-d', strtotime($request->date)),
                'admin_id' => Auth::user()->id,
                'doctor_id' => $request['doctor_id'],
                'type' => $request['type'],
                'location_id' => Auth::user()->location_id,
                'remark' => $request['remark'],
                'shift_timings' => implode(',', $request['slots']),
                'status_id' => 2,
                'hours' => $request->hours,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        else:
            DB::table($tablename)->insert([
                'date' => date('Y-m-d', strtotime($request->date)),
                'admin_id' => Auth::user()->id,
                'doctor_id' => $request['doctor_id'],
                'type' => $request['type'],
                'location_id' => Auth::user()->location_id,
                'remark' => $request['remark'],
                'block_time_slots' => implode(',', $request['slots']),
                'status_id' => 2,
                'hours' => $request->hours,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

        endif;
        return ['Slots updated'];
    }
}

