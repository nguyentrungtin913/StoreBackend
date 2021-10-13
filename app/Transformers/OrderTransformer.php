<?php


namespace App\Transformers;


use App\Models\Order;
use League\Fractal\Manager;

class OrderTransformer extends BaseTransformer
{
    public function __construct(Manager $fractal, Order $order)
    {
        parent::__construct($fractal, $order);
    }
    
    public function transform($data)
    {
        $transform = parent::transform($data);
           
        return $transform;
    }
}