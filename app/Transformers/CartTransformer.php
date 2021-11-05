<?php


namespace App\Transformers;


use App\Models\Cart;
use League\Fractal\Manager;

class CartTransformer extends BaseTransformer
{
    protected CartDetailTransformer $cartDetailTransformer;
    public function __construct(Manager $fractal, Cart $cart, CartDetailTransformer $cartDetailTransformer)
    {
        parent::__construct($fractal, $cart);
        $this->cartDetailTransformer = $cartDetailTransformer;
    }
    
    public function transform($data)
    {
        $transform = parent::transform($data);
        if($cartDetails = $data->getRelations()['cartDetail'] ?? null) {
           
            $cartDetails = $this->cartDetailTransformer->transformCollection($cartDetails);
            
            $transform += compact('cartDetails');
        }
        return $transform;
    }
}