<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Users;

class AuthBasic
{

    public function handle($request, Closure $next)
    {
        $token = $request->api_token;
        if($token == null)
        {
            return response()->json(['message' => 'Api Token not found', 'status' => 'unauthorized'], 401);
        }
        if(Users::where('api_token', $token)->where('status_id', 2)->get()->count())
        {
            return $next($request);
        }
        else
        {
            return response()->json(['message' => 'Authentication failed or Invalid token'], 401);
        }

        $token = $request->login_token;
        $accepted_origins = ['https://srisritattva.me/panchakarma/', 'https://srisritattva.me/panchakarma', 'http://srisritattva.me/panchakarma', 'http://srisritattva.me/panchakarma/', 'https://srisritattva.me', 'http://srisritattva.me', 'http://localhost:8888'];
        if($token == '22813c84d0bc10b75d424c2a4fa12d889beeedcdc4945a25abde0e64ccbd7ac7'):
            return $next($request);
        elseif($token == '22813c84d0bc10b75d424c2a4fa12d889beeedcdc4945a25abde0e64ccbd7ac720'):
            if(in_array($request->origin, $accepted_origins)) { return $next($request); }
            else { return response()->json(['message' => 'Unauthorized Access.', 'origin' =>  $request->origin], 401); }
        else:
            return response()->json(['message' => 'token mismatch.'], 401);
        endif;

    }
}
