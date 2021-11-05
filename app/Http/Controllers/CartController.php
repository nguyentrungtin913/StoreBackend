<?php

namespace App\Http\Controllers;

use Facade\FlareClient\Http\Response as HttpResponse;
use Illuminate\Http\Request;
use Response;
use App\Models\Cart;
use App\Transformers\CartTransformer;
use App\Helpers\DataHelper;
use App\Helpers\ResponseHelper;

class CartController extends Controller
{
    public function __construct(Cart $cartModel, CartTransformer $cartTransformer)
    {
        $this->cartModel = $cartModel;
        $this->cartTransformer = $cartTransformer;
    }

    public function index(Request $request, Response $response)
    {
        $params = $request->all();

        $perPage = $params['perPage'] ?? 0;
        $with = $params['with'] ?? [];

        $orderBy = $this->cartModel->orderBy($params['sortBy'] ?? null, $params['sortType'] ?? null);

        $query = $this->cartModel->filter($this->cartModel::query(), $params)->orderBy($orderBy['sortBy'], $orderBy['sortType']);

        $query = $this->cartModel->includes($query, $with);

        $data = DataHelper::getList($query, $this->cartTransformer, $perPage, 'ListAllCart');
        
        return ResponseHelper::success($response, $data);
    }
    public function updateStatus(Request $request, Response $response)
    {
        $params = $request->all();
        $status = $params['cartStatus'] ?? 0;
        $id = $params['cartId'] ?? 0;
        $cart = $this->cartModel->where('cart_id',$id)->first();
        if($cart){
            $cart->update([
                'cart_status' => $status,
            ]);
            $cart = $this->cartTransformer->transformItem($cart);
            return ResponseHelper::success($response, compact('cart'));
        }
        return ResponseHelper::requestFailed($response);
    }
    public function findCartById(Request $request, Response $response)
    {
        $params = $request->all();
        $id = $params['cartId'] ?? 0;
        $cart = $this->cartModel->with('cartDetail')->where('cart_id',$id)->first();
        if($cart){
            $cart = $this->cartTransformer->transformItem($cart);
            return ResponseHelper::success($response, compact('cart'));
        }
        return ResponseHelper::requestFailed($response);
    }
}   
