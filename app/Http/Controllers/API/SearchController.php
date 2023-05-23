<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Treatments;
use App\Models\Medicines;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function menuSearch(Request $request)
    {
        $search = $request['search'];
        if($request['type'] == 'treatments') {
            $records = Treatments::select('treatments.*', 'appointment_types.appointment_type as appointment_type', 'statuses.status as status', 'statuses.css as css')
                                ->join('appointment_types', 'appointment_types.id', '=', 'treatments.appointment_type_id')
                                ->join('statuses', 'statuses.id', '=', 'treatments.status_id')
                                ->where('treatments.status_id', '!=', 7)
                                ->where(function($query) use ($search){
                                    $query->where('treatment','LIKE',"%$search%");
                                })->orderBy('treatments.created_at', 'desc')->get();
        } elseif($request['type'] == 'medicines') {
            $records = Medicines::select('medicines.*', 'categories.name as category_name', 'categories.unit')
                                ->join('categories', 'categories.id', '=', 'medicines.category_id')
                                ->orderBy('medicines.name', 'asc')
                                ->where('medicines.status_id', '!=', '7')
                                ->where(function($query) use ($search){
                                        $query->where('medicines.name','LIKE',"%$search%");
                            })->get();
        } else{
           $records = [];
        }
        return $records;
    }
}
