<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use App\Models\Token;
use Illuminate\Auth\AuthenticationException;

class Authenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next)
    {
        $authorization = $request->headers->get('authorization');
        $token = $authorization ?? "abc";
        $time = time();
        $tokenModel = Token::query()->where('token_value', $token)->first();
        if($tokenModel){
            if($tokenModel->token_expired > $time ){
                return $next($request);
            }else{
                return response()->json([[
                                            'code'=> 'missing',
                                            'msg'=> 'Token has expired',
                                            'clientMsg'=> 'Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại!'
                                        ]], 400);
            }
        }else{
            return response()->json([[
                                            'code'=> 'missing',
                                            'msg'=> 'Token not found',
                                            'clientMsg'=> 'Vui lòng đăng nhập',
                                    ]], 400);
        }  
    }
}
