<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {        //comprobar si esta identificado
        $token=$request->header('Authorization');
        $jwdtAuth= new \JwdtAuth(); 
        $checktoken=$jwdtAuth->checkToken($token);

        if($checktoken){
            return $next($request);
        }else{
            $data=array(
                'code'=>400,
                'status'=>'error',
                'message'=>'error de middleware'
            );
            return response()->json($data,$data['code']);
        }
    }
}
