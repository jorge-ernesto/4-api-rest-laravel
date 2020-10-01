<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\JwtAuth;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /* Comprobamos si el usuario esta identificado */
        $token      = $request->header("Authorization");                    
        $token      = str_replace('"', '', $token);
        $jwtAuth    = new JwtAuth();              
        $checkToken = $jwtAuth->checkToken($token);

        if($checkToken){
            return $next($request);
        }else{
            $data = array(
                "status"  => "error",
                "code"    => "404",
                "message" => "EL usuario no esta identificado"
            ); 

            return response()->json($data, $data['code']);
        }        
    }
}
