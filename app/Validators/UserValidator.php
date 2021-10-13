<?php


namespace App\Validators;
use Illuminate\Http\Request;
use App\Models\User;
class UserValidator extends BaseValidator
{
    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }
    public function requireData()
    {
        if(     
            !$this->requireParam('email', 'Vui lòng nhập email !') || 
            !$this->requireParam('password', 'Vui lòng mật khẩu !') 
          )
        {
            return false;
        }else{
            return true;
        }
    }
    public function checkLogin()
    {
        $email = $this->request->get('email') ?? null;
        $password = $this->request->get('password') ?? null;
        $user = $this->userModel->where('user_email', $email)->first();
        if($user){
            if($user->user_password === md5($password)){
                return true;
            }else{
                $this->setError(400, 'Error', "Incorrect password", 'Mật khẩu không chính xác !');
                return false;
            }
        }else{
            $this->setError(400, 'Error', "Email not exist", 'Email không tồn tại !');
            return false;
        }
    }

    public function login()
    {
        if (!$this->requireData() || !$this->checkLogin()) {
            return false;
        } else {
            return true;
        }
    }

}
?>
