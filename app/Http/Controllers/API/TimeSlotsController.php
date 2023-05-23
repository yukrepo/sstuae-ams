<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TimeSlots;

class TimeSlotsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getList()
    {
        $slots = TimeSlots::where('season', 1)->get();
        foreach($slots as $slot) {
            $active_slots[$slot->id] = $slot->time;
        }
        return $active_slots;
    }

    public function getBothList()
    {
        $slots = TimeSlots::get();
        foreach($slots as $slot) {
            $starts[$slot->id] = $slot->time;
            $ends[$slot->id] = $slot->closing;
        }
        return ['starts' => $starts, 'ends' => $ends];
    }

    public function getOffTimeSlots()
    {
        return TimeSlots::where('season', 0)->get();
    }

    public function getOnTimeSlots()
    {
        return TimeSlots::where('season', 1)->get();
    }

    public function modifySlots(Request $request)
    {
        if($request->season == 1): $season = 0;
        else: $season = 1; endif;
        $slot = TimeSlots::findOrFail($request->id);
        $slot->update(['season' => $season]);
        return ['slot updated'];
    }
}
