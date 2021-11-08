<?php

namespace App\Http\Controllers;

use Facade\FlareClient\Http\Response as HttpResponse;
use Illuminate\Http\Request;
use Response;
use App\Models\CartDetail;

use App\Transformers\CartDetailTransformer;

use App\Helpers\DataHelper;
use App\Helpers\ResponseHelper;

use App\Validators\CartValidator;

class CartDetailController extends Controller
{
    public function __construct(CartDetail $cartDetailModel, CartDetailTransformer $cartDetailTransformer, CartValidator $cartValidator)
    {
        $this->cartDetailModel = $cartDetailModel;
        $this->cartDetailTransformer = $cartDetailTransformer;
        $this->cartValidator = $cartValidator;
    }
    public function findCartDetailByCartId(Request $request, Response $response)
    {
        $params = $request->all();

        if (!$this->cartValidator->setRequest($request)->detail()) {
            $errors = $this->cartValidator->getErrors();
            return ResponseHelper::errors($response, $errors);
        }

        $id = $params['cartId'] ?? 0;
        $cartDetails = $this->cartDetailModel->with('product')->where('cart_id',$id)->get();
        if($cartDetails){
            $cartDetails = $this->cartDetailTransformer->transformCollection($cartDetails);
            return ResponseHelper::success($response, compact('cartDetails'));
        }
        return ResponseHelper::requestFailed($response);
    }
}   
