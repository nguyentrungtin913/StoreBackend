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
}
?>
