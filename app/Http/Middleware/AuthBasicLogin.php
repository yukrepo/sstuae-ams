<?php

namespace App\Http\Middleware;

use Closure;

class AuthBasicLogin
{
    /**
     * Handle an incoming request.
     *-
00     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->login_token;
        $accepted_origins = ['https://srisritattvapanchakarma.ae/', 'https://srisritattvapanchakarma.ae', 'http://srisritattvapanchakarma.ae', 'http://srisritattvapanchakarma.ae/', 'http://localhost:8888'];
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
