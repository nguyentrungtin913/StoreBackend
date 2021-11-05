<?php

namespace App\Http\Controllers;

use Facade\FlareClient\Http\Response as HttpResponse;
use Illuminate\Http\Request;
use Response;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Transformers\CartTransformer;
use App\Helpers\DataHelper;
use App\Helpers\ResponseHelper;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Transformers\OrderDetailTransformer;
use App\Transformers\OrderTransformer;

class CartController extends Controller
{
    public function __construct(
        Cart $cartModel, 
        CartDetail $cartDetailModel,
        CartTransformer $cartTransformer,
        Order $orderModel,
        OrderTransformer $orderTransformer,
        OrderDetail $orderDetailModel,
        OrderDetailTransformer $orderDetailTransformer,
        Product $productModel
    )
    {
        $this->cartModel = $cartModel;
        $this->cartTransformer = $cartTransformer;
        $this->cartDetailModel = $cartDetailModel;
        $this->orderModel = $orderModel;
        $this->orderTransformer = $orderTransformer;
        $this->orderDetailModel = $orderDetailModel;
        $this->orderDetailTransformer = $orderDetailTransformer;
        $this->productModel = $productModel;
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
            
            if($status === 1)
            {
                $products = $this->cartDetailModel->with('product')->where('cart_id', $cart->cart_id)->get();    
                $name = $cart->cart_name;
                $this->addOrder($products, $name);
            }
            $cart = $this->cartTransformer->transformItem($cart);
            return ResponseHelper::success($response, compact('cart'));
        }
        return ResponseHelper::requestFailed($response);
    }
    
    public function addOrder($products, $name )
    {
        $total = 0;
        $date = date("Y-m-d");
        
        foreach ($products as $value) {
            $total += ($value->detail_amount * $value->product->pro_ex_price);
        }

        $order = $this->orderModel->create([
            'order_total'   => $total,
            'order_name'    => $name,
            'order_type'    => 'Xuất',
            'order_date'    => $date
        ]);
        if($order){
            foreach($products as $value){
                $detail = $this->orderDetailModel->create([
                    'order_id'      => $order->order_id,
                    'pro_id'        => $value->pro_id,
                    'detail_amount' => $value->detail_amount
                ]);
                if($detail){
                    $product = $this->productModel->where('pro_id', $value->pro_id)->first();
                    if($product)
                    {
                        $product->update([
                            'pro_amount'        => ($product->pro_amount - $value->detail_amount),
                            'pro_amount_sell'   => ($product->pro_amount_sell + $value->detail_amount),
                        ]); 
                    }
                }
            }
        }

    }
}   
