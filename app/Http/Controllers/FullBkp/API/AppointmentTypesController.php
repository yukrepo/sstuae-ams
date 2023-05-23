<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AppointmentTypes;

class AppointmentTypesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return AppointmentTypes::latest()->paginate(15);
    }

    public function destroy($id)
    {
        $record = AppointmentTypes::findOrFail($id);
        $record->delete();
        return ['message' => 'record deleted'];
    }

    public function filter()
    {
        if($search = \Request::get('q')) {
            $records = AppointmentTypes::where(function($query) use ($search){
                $query->where('value','LIKE',"%$search%");
            })->latest()->paginate(15);
        }
        else{
           $records = '';
        }
        return $records;
    }

    public function search()
    {
        if($search = \Request::get('q')) {
            $records = AppointmentTypes::where(function($query) use ($search){
                $query->where('value','LIKE',"%$search%");
            })->get();
        }
        else{
           $records = [];
        }
        $result = [];
        foreach($records as $record):
            $result[$record->id] = $record->value;
        endforeach;
        return $result;
    }

    public function getList()
    {
        return AppointmentTypes::get();
    }
}
