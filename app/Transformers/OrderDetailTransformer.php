<?php


namespace App\Transformers;


use App\Models\OrderDetail;
use League\Fractal\Manager;

class OrderDetailTransformer extends BaseTransformer
{
    protected ProductTransformer $productTransformer;
    public function __construct(Manager $fractal, OrderDetail $orderDetail, ProductTransformer $productTransformer)
    {
        parent::__construct($fractal, $orderDetail);
        $this->productTransformer = $productTransformer;
    }
    
    public function transform($data)
    {
        $transform = parent::transform($data);
        if($product = $data->getRelations()['product'] ?? null) {
           
            $product = $this->productTransformer->transformItem($product);
            
            $transform += compact('product');
        }   
        return $transform;
    }
}