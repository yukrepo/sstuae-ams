<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointments;
use App\Models\Treatments;
use App\Models\Doctors;
use App\Models\FinancialYears;
use App\Models\Users;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getAdminDashboardSynops()
    {
        $appointments = [];
        $years = FinancialYears::get();

        foreach ($years as $key => $year) {

            $table = new Appointments;
            $table = $table->setTable('appointment'.$year->year.'s');

            $appointments[$key] = ['total' => $table->where('location_id', Auth::user()->location_id)->get()->count(),
                                    'active' => $table->where('location_id', Auth::user()->location_id)->whereIn('status_id', [2,4,9,10,8])->get()->count(),
                                    'completed' => $table->where('location_id', Auth::user()->location_id)->where('status_id', 5)->get()->count(),
                                    'deleted' => $table->where('location_id', Auth::user()->location_id)->where('status_id', 7)->get()->count(),
                                    'cancelled' => $table->where('location_id', Auth::user()->location_id)->where('status_id', 11)->get()->count(),
                                    'year' => $year->year
                                ];
        }

        $users = ['total' => Users::get()->count(),
                    'byapp' => Users::where('source', 'app')->get()->count(),
                    'clinic' => Users::where('source', 'clinic')->get()->count(),
                    'clinic1' => Users::where('source', 'Muscat Clinic')->get()->count(),
                    'clinic2' => Users::where('source', 'Kims Clinic')->get()->count(),
                    'byweb' => Users::where('source', 'web')->get()->count() ];
        return ['appointments' => $appointments, 'users' => $users ];
    }

    public function getDashboardScore()
    {
        return ['dayscore' => $this->getDayScore(Auth::user()->relative_id),
                'monthscore' => $this->getMonthScore(Auth::user()->relative_id),
                'todayscore' => $this->getTodayScore(Auth::user()->relative_id),
                'todaycount' =>  $this->getTodayCount(Auth::user()->relative_id)
        ];
    }

    public function getDrDashboardScore()
    {
        return ['dayscore' => $this->getDayCount(Auth::user()->relative_id),
                'monthscore' => $this->getMonthCount(Auth::user()->relative_id),
                'curmonthcount' => $this->getCurMonthCount(Auth::user()->relative_id),
                'todaycount' =>  $this->getTodayCount(Auth::user()->relative_id)
        ];
    }

    public function getAdminDashboardScore()
    {
        $scores = [];
        $doctors = Doctors::where('status_id', 2)->where('designation_type_id', 1)->where('location_id', Auth::user()->location_id)->get();

        $therapists = Doctors::where('status_id', 2)->where('designation_type_id', 2)->where('location_id', Auth::user()->location_id)->orderBy('gender', 'asc')->get();

        foreach ($doctors as $doctor) {
            $doctor_score[] = ['name' => ucwords($doctor->name),
                                'dayscore' => $this->getDayScore($doctor->id),
                                'monthscore' => $this->getMonthScore($doctor->id),
                                'todayscore' => $this->getTodayScore($doctor->id),
                                'todaycount' =>  $this->getTodayCount($doctor->id)
                            ];
        }

        foreach ($therapists as $doctor) {
            $therapist_score[] = ['name' => ucwords($doctor->name),
                                'dayscore' => $this->getDayScore($doctor->id),
                                'monthscore' => $this->getMonthScore($doctor->id),
                                'todayscore' => $this->getTodayScore($doctor->id),
                                'todaycount' =>  $this->getTodayCount($doctor->id)
                            ];
        }

        return ['doctors' => $doctor_score, 'therapists' => $therapist_score];

    }

    public function getDayScore($id = null)
    {
        $date = date('Y-m-d', strtotime('-1 days'));
        $year = date('Y', strtotime($date));
        $table = new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $p = $table->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereIn("$tablename.status_id", [2,4,5,9])
                    ->where('date', $date)
                    ->where('location_id', Auth::user()->location_id)
                    ->where('doctor_id', $id)
                    ->sum('treatments.points');
        $sp = $table->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereIn("$tablename.status_id", [2,4,5,9])
                    ->where('date', $date)
                    ->where('location_id', Auth::user()->location_id)
                    ->where('second_doctor_id', $id)
                    ->sum('treatments.spoints');
        return $p+$sp;
    }

    public function getDayCount($id = null)
    {
        $date = date('Y-m-d', strtotime('-1 days'));
        $year = date('Y', strtotime($date));
        $table = new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $p = $table->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereIn("$tablename.status_id", [2,4,5,9,8])
                    ->where('date', $date)
                    ->where('location_id', Auth::user()->location_id)
                    ->where('doctor_id', $id)
                    ->get()->count();
        $sp = $table->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereIn("$tablename.status_id", [2,4,5,9,10])
                    ->where('date', $date)
                    ->where('location_id', Auth::user()->location_id)
                    ->where('second_doctor_id', $id)
                    ->get()->count();
        return $p+$sp;
    }

    public function getTodayScore($id = null)
    {
        $date = date('Y-m-d', time());
        $year = date('Y', strtotime($date));
        $table = new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $p = $table->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereIn("$tablename.status_id", [2,4,5,9])
                    ->where('date', $date)
                    ->where('location_id', Auth::user()->location_id)
                    ->where('doctor_id', $id)
                    ->sum('treatments.points');
        $sp = $table->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereIn("$tablename.status_id", [2,4,5,9])
                    ->where('date', $date)
                    ->where('location_id', Auth::user()->location_id)
                    ->where('second_doctor_id', $id)
                    ->sum('treatments.spoints');
        return $p+$sp;
    }

    public function getTodayCount($id = null)
    {
        $date = date('Y-m-d', time());
        $year = date('Y', strtotime($date));
        $table = new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $p = $table->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereIn("$tablename.status_id", [2,4,5,9])
                    ->where('date', $date)
                    ->where('location_id', Auth::user()->location_id)
                    ->where('doctor_id', $id)
                    ->get()->count();
        $sp = $table->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereIn("$tablename.status_id", [2,4,5,9])
                    ->where('date', $date)
                    ->where('location_id', Auth::user()->location_id)
                    ->where('second_doctor_id', $id)
                    ->get()->count();
        return $p+$sp;
    }

    public function getMonthScore($id = null)
    {
        $date = date('Y-m-d', strtotime('-1 days'));
        $year = date('Y', strtotime($date));
        $table = new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $p = $table->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereIn("$tablename.status_id", [5])
                    ->whereMonth('date', date('m', strtotime("first day of previous month")))
                    ->whereYear('date', $year)
                    ->where('location_id', Auth::user()->location_id)
                    ->where('doctor_id', $id)
                    ->sum('treatments.points');
        $sp = $table->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereIn("$tablename.status_id", [5])
                    ->whereMonth('date', date('m', strtotime("first day of previous month")))
                    ->whereYear('date', $year)
                    ->where('location_id', Auth::user()->location_id)
                    ->where('second_doctor_id', $id)
                    ->sum('treatments.spoints');
        return $p+$sp;
    }

    public function getMonthCount($id = null)
    {
        $date = date('Y-m-d', strtotime('-1 days'));
        $year = date('Y', strtotime($date));
        $table = new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $p = $table->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereIn("$tablename.status_id", [5])
                    ->whereMonth('date', date('m', strtotime("first day of previous month")))
                    ->whereYear('date', $year)
                    ->where('location_id', Auth::user()->location_id)
                    ->where('doctor_id', $id)
                    ->get()->count();
        $sp = $table->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereIn("$tablename.status_id", [5])
                    ->whereMonth('date', date('m', strtotime("first day of previous month")))
                    ->whereYear('date', $year)
                    ->where('location_id', Auth::user()->location_id)
                    ->where('second_doctor_id', $id)
                    ->get()->count();
        return $p+$sp;
    }

    public function getCurMonthCount($id = null)
    {
        $date = date('Y-m-d', time());
        $year = date('Y', strtotime($date));
        $table = new Appointments;
        $table = $table->setTable('appointment'.$year.'s');
        $tablename = $table->getTable();
        $p = $table->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereIn("$tablename.status_id", [5])
                    ->whereMonth('date', date('m', time()))
                    ->whereYear('date', $year)
                    ->where('location_id', Auth::user()->location_id)
                    ->where('doctor_id', $id)
                    ->get()->count();
        $sp = $table->join('treatments', 'treatments.id', '=', "$tablename.treatment_id")
                    ->whereIn("$tablename.status_id", [5])
                    ->whereMonth('date', date('m', time()))
                    ->whereYear('date', $year)
                    ->where('location_id', Auth::user()->location_id)
                    ->where('second_doctor_id', $id)
                    ->get()->count();
        return $p+$sp;
    }
}
