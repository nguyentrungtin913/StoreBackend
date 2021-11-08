<?php


namespace App\Validators;
use Illuminate\Http\Request;
use App\Models\CartDetail;

class CartDetailValidator extends BaseValidator
{

    public function __construct(CartDetail $cartDetail)
    {
        $this->cartDetail= $cartDetail;
    }
    public function checkCartDetailExist()
    {
        $id = $this->request->get('cartDetailId') ?? null;
        $cartDetail = $this->cartDetail->where('cartDetail_id' , $id)->first();
        if($cartDetail){
            return true;
        }else{
            $this->setError(400, 'error', 'Cart detail not exist', 'Đơn hàng chi tiết không tìm thấy!');
            return false;
        }
    }

    public function detail()
    {
        if (!$this->checkCartDetailExist()) {
            return false;
        } else {
            return true;
        }
    }

}
?>
