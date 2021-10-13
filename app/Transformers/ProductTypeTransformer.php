<?php


namespace App\Transformers;


use App\Models\ProductType;
use League\Fractal\Manager;

class ProductTypeTransformer extends BaseTransformer
{
    // protected ProductTransformer $productTransformer;
    public function __construct(Manager $fractal, ProductType $productType/*, ProductTransformer $productTransformer*/)
    {
        parent::__construct($fractal, $productType);
        //$this->productTransformer = $productTransformer;
    }
    public function transform($data)
    {
        $transform = parent::transform($data);
        
        // if($products = $data->getRelations()['products'] ?? null) {
           
        //     $products = $this->productTransformer->transformCollection($products);
            
        //     $transform += compact('products');
        // }
        return $transform;
    }
}