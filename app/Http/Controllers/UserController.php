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
        $to      = "abc@example.com";
        $subject = "Tiêu đề email";
        $message = "<h1>Đây là Email có chứa HTML</h1>
                    <p>Đoạn văn trong Email</p>";       //MỚI
        $header  =  "From:myemail@exmaple.com \r\n";
        $header .=  "Cc:other@exmaple.com \r\n";
        
        $header .= "MIME-Version: 1.0\r\n";             //MỚI
        $header .= "Content-type: text/html\r\n";       //MỚI

        $success = mail ($to,$subject,$message,$header);

        if( $success == true )
        {
            echo "Đã gửi mail thành công...";
        }
        else
        {
              echo "Không gửi đi được...";
        }
    }
}
