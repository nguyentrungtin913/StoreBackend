<?php


namespace App\Helpers;


use Firebase\JWT\JWT;

class JwtHelper
{
    /**
     * @param $payload
     * @return string
     */
    static function generate($payload)
    {
        return JWT::encode($payload,'MyProject');
    }

    /**
     * @param $jwt
     * @return object
     */
    static function decode($jwt)
    {
        return JWT::decode($jwt, getenv('JWT_SECRET'), ['HS256']);
    }

    /**
     * @return string|null
     */
    static function getBearerToken()
    {
        if ($authorizationHeader = @getallheaders()['authorization']){
            return (explode(' ', $authorizationHeader)[1] ?? null);
        }elseif ($authorizationHeader = @getallheaders()['Authorization']){
            return (explode(' ', $authorizationHeader)[1] ?? null);
        }else{
            return null;
        }
    }
}