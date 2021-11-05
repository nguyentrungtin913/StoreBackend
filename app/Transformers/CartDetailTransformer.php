<?php


namespace App\Transformers;


use App\Models\CartDetail;
use League\Fractal\Manager;

class CartDetailTransformer extends BaseTransformer
{
    protected ProductTransformer $productTransformer;
    public function __construct(Manager $fractal, CartDetail $cartDetail, ProductTransformer $productTransformer)
    {
        parent::__construct($fractal, $cartDetail);
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