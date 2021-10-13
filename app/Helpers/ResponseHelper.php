<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Response;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
class ResponseHelper
{
    /**
     * @param int $code
     * @param string|null $msg
     * @param string|null $clientMsg
     * @return array
     */
    static function errorFormat($code, $msg = null, $clientMsg = null)
    {
        return compact('code', 'msg', 'clientMsg');
    }

    /**
     * @param Response $response
     * @param string $msg
     * @param string $clientMsg
     * @return MessageInterface|ResponseInterface|Response
     */
    static function requestFailed(Response $response, string $msg = 'Request failed', string $clientMsg = 'Request failed')
    {
        return static::response($response, [static::errorFormat('request_failed', $msg, $clientMsg)] ,500);
    }

    /**
     * @param Response $response
     * @param null $data
     * @param string|null $clientMsg
     * @return MessageInterface|ResponseInterface|Response
     */
    static function success(Response $response, $data = null, string $clientMsg = null)
    {
        $code = 'success';
        $msg = 'Request successful';
        return static::response($response, compact('code', 'msg', 'clientMsg', 'data'),200);
    }

    /**
     * @param Response $response
     * @param $errors
     * @return MessageInterface|ResponseInterface|Response
     */
    static function errors(Response $response, $errors)
    {
        return static::response($response, $errors['errors'], $errors['status']);
    }

    /**
     * @param Response $response
     * @param $data
     * @param $status
     * @return MessageInterface|ResponseInterface|Response
     */
    static function response(Response $response, $data, $status)
    {
        return $response::json($data, $status)/*
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')*/;
    }
}