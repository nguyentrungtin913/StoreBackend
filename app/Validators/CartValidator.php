<?php


namespace App\Validators;
use Illuminate\Http\Request;
use App\Models\Cart;

class CartValidator extends BaseValidator
{

    public function __construct(Cart $cart)
    {
        $this->cart= $cart;
    }
    public function checkCartExist()
    {
        $id = $this->request->get('cartId') ?? null;
        $cart = $this->cart->where('cart_id' , $id)->first();
        if($cart){
            return true;
        }else{
            $this->setError(400, 'error', 'Cart not exist', 'Đơn hàng không tìm thấy!');
            return false;
        }
    }

    public function detail()
    {
        if (!$this->checkCartExist()) {
            return false;
        } else {
            return true;
        }
    }

    public function delete()
    {
        if (!$this->checkCartExist()) {
            return false;
        } else {
            return true;
        }
    }
}
?>
