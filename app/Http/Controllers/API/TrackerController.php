<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Models\AppInstallations;

header('Access-Control-Allow-Origin:*');

class TrackerController extends Controller
{

    function index(Request $request)
    {
        $check = AppInstallations::where('campaign', $request->cid)->where('device', $request->did)->get()->count();
        if($check >= 1): 
            AppInstallations::where('campaign', $request->cid)->where('device', $request->did)->increment('counter');
        else:
            $record = AppInstallations::create(['campaign' => $request->cid,
                                                    'device' => $request->did,
                                                        'counter' => 1]);
        endif;
        return [ 'status' => 'success', 'message' => 'You are being redirected to relevant page.'];
    }


}
