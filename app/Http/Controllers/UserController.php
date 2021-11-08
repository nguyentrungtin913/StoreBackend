<?php

namespace App\Http\Controllers;

use Facade\FlareClient\Http\Response as HttpResponse;
use Illuminate\Http\Request;
use Response;
use App\Models\User;
use App\Models\Token;

use App\Validators\UserValidator;

use App\Transformers\TokenTransformer;

use App\Helpers\JwtHelper;
use App\Helpers\DataHelper;
use App\Helpers\ResponseHelper;

use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function __construct(User $userModel, Token $tokenModel , UserValidator $userValidator, TokenTransformer $tokenTransformer)
    {
        $this->userModel = $userModel;
        $this->userValidator = $userValidator;      
        $this->tokenModel = $tokenModel;  
        $this->tokenTransformer = $tokenTransformer;  
    }

    public function login(Request $request, Response $response)
    {
        if (!$this->userValidator->setRequest($request)->login()) {
            $errors = $this->userValidator->getErrors();
            return ResponseHelper::errors($response, $errors);
        }
        $params = $request->all();
        $email = $params['email'];
        $time = time() + (60 * 60 * 24 );
        $key = $email.$time;
        $accessToken = JwtHelper::generate($key);
        $userLogin = $this->tokenModel->where('token_email', $email)->first();
        if($userLogin){
            $userLogin->delete();
        }
        $result = $this->tokenModel->create([
            'token_value'   => $accessToken,
            'token_email'   => $email,
            'token_expired' => $time,
        ]);
        if($result){
            return ResponseHelper::success($response, compact('accessToken'), 'Login successful');    
        }else
        {
            return ResponseHelper::requestFailed($response);
        }
    }
    public function logout(Request $request, Response $response)
    {
        $authorization = $request->headers->get('authorization');
        $token = $authorization ?? "abc";
        $result = $this->tokenModel->where('token_value', $token)->first();
        if($result){
            if($result->delete()){
                return ResponseHelper::success($response, null, 'Logout successful');  
            }
        }
        return ResponseHelper::requestFailed($response);
    }
    public function sentMail()
    {
        $data = [
          'title'=>'Hellow Avijit',
          'content'=>'This is a testing of mailing in Laravel using mailgun'
        ];

        \Mail::send('nguyentrungtin913@gmail.com', $data, function($message) {
            $message->to('nttin_18th1@student.agu.edu.vn', 'Avijit Biswas')
                ->subject('This is Subject');
        });
    }
}
