<?php


namespace App\Transformers;


use App\Models\Product;
use League\Fractal\Manager;

class ProductTransformer extends BaseTransformer
{
    protected ProductTypeTransformer $productTypeTransformer;
    public function __construct(Manager $fractal, Product $product, ProductTypeTransformer $productTypeTransformer)
    {
        parent::__construct($fractal, $product);
        $this->productTypeTransformer = $productTypeTransformer;
    }
    public function transform($data)
    {
        $transform = parent::transform($data);
        if($productType = $data->getRelations()['productType'] ?? null) {
           
            $productType = $this->productTypeTransformer->transformItem($productType);
            
            $transform += compact('productType');
        }    
        return $transform;
    }
}