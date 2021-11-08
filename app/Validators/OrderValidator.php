<?php


namespace App\Validators;
use Illuminate\Http\Request;
use App\Models\Order;
class OrderValidator extends BaseValidator
{

    public function __construct(Order $order)
    {
        $this->order= $order;
    }
    public function checkOrderExist()
    {
        $id = $this->request->get('orderId') ?? null;
        $order = $this->order->where('order_id' , $id)->first();
        if($order){
            return true;
        }else{
            $this->setError(400, 'error', 'Order not exist', 'Đơn hàng không tìm thấy!');
            return false;
        }
    }

    public function detail()
    {
        if (!$this->checkOrderExist()) {
            return false;
        } else {
            return true;
        }
    }

    public function delete()
    {
        if (!$this->checkOrderExist()) {
            return false;
        } else {
            return true;
        }
    }
}
?>
