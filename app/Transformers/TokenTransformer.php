<?php


namespace App\Transformers;


use App\Models\Token;
use League\Fractal\Manager;

class TokenTransformer extends BaseTransformer
{
    public function __construct(Manager $fractal, Token $token)
    {
        parent::__construct($fractal, $token);
    }
    
    public function transform($data)
    {
        $transform = parent::transform($data);
           
        return $transform;
    }
}