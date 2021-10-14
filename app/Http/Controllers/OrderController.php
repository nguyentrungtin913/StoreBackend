<?php

namespace App\Http\Controllers;

use Facade\FlareClient\Http\Response as HttpResponse;
use Illuminate\Http\Request;
use Response;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Transformers\OrderDetailTransformer;
use App\Models\Product;
use App\Validators\OrderValidator;
use App\Transformers\OrderTransformer;
use App\Helpers\DataHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\Random;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function __construct(Order $orderModel, OrderTransformer $orderTransformer, OrderValidator $orderValidator, OrderDetail $orderDetailModel, Product $productModel, OrderDetailTransformer $orderDetailTransformer)
    {
        $this->orderModel = $orderModel;
        $this->orderTransformer = $orderTransformer;
        $this->orderValidator = $orderValidator;
        $this->orderDetailModel = $orderDetailModel;
        $this->productModel = $productModel;
        $this->orderDetailTransformer = $orderDetailTransformer;
    }

    public function sell(Request $request, Response $response)
    {
        $params = $request->all();
        $name = $params['name'] ?? null;
        $products = $params['order'];
        $keys = array();
        $values = array();
        $total = 0;
        $date = date("Y-m-d");
        foreach ($products as $value) {
            array_push($keys, $value['id']);
            array_push($values, $value['amountSell']);
            $total += ($value['amountSell'] * $value['priceExport']);
        }

        $order = $this->orderModel->create([
            'order_total'   => $total,
            'order_name'    => $name,
            'order_type'    => 'Xuất',
            'order_date'    => $date
        ]);
        if($order){
            $details = array_combine($keys, $values); 
            foreach($details as $key => $value){
                $detail = $this->orderDetailModel->create([
                    'order_id'      => $order->order_id,
                    'pro_id'        => $key,
                    'detail_amount' => $value
                ]);
                if($detail){
                    $product = $this->productModel->where('pro_id',$key)->first();
                    if($product)
                    {
                        $product->update([
                            'pro_amount'        => ($product->pro_amount - $value),
                            'pro_amount_sell'   => ($product->pro_amount_sell + $value),
                        ]); 
                    }
                }
            }
        }
        
        return $products;
    }

    public function buy(Request $request, Response $response)
    {
        $params = $request->all();
        $name = $params['name'] ?? null;
        $products = $params['order'];
        $keys = array();
        $values = array();
        $total = 0;
        $date = date("Y-m-d");
        foreach ($products as $value) {
            array_push($keys, $value['id']);
            array_push($values, $value['amountSell']);
            $total += ($value['amountSell'] * $value['priceExport']);
        }

        $order = $this->orderModel->create([
            'order_total'   => $total,
            'order_name'    => $name,
            'order_type'    => 'Nhập',
            'order_date'    => $date
        ]);
        if($order){
            $details = array_combine($keys, $values); 
            foreach($details as $key => $value){
                $detail = $this->orderDetailModel->create([
                    'order_id'      => $order->order_id,
                    'pro_id'        => $key,
                    'detail_amount' => $value
                ]);
                if($detail){
                    $product = $this->productModel->where('pro_id',$key)->first();
                    if($product)
                    {
                        $product->update([
                            'pro_amount'        => ($product->pro_amount + $value),
                        ]); 
                    }
                }
            }
        }
        
        return $products;
    }

    public function index(Request $request, Response $response)
    {
        $params = $request->all();

        $perPage = $params['perPage'] ?? 0;
        
        $orderBy = $this->orderModel->orderBy($params['sortBy'] ?? null, $params['sortType'] ?? null);

        $query = $this->orderModel->filter($this->orderModel::query(), $params)->orderBy($orderBy['sortBy'], $orderBy['sortType']);


        $data = DataHelper::getList($query, $this->orderTransformer, $perPage, 'ListAllOrder');
        
        return ResponseHelper::success($response, $data);
    }

    public function exportCsv(Request $request)
    {
       //return Excel::download(new OrderExport, 'Orders.xlsx');
        $fileName = 'orders.csv';
        $orders = $this->orderModel::all();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Content-Encoding"    => "UTF-8",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Id', 'Name', 'Total', 'Date');

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                $row['Id']      = $order->order_id;
                $row['Name']    = $order->order_name;
                $row['Total']   = $order->order_total;
                $row['Date']    = $order->order_date;

                 fputcsv($file, array($row['Id'], $row['Name'], $row['Total'], $row['Date']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function orderDetail(Request $request, Response $response)
    {
        $params = $request->all();

        $id = $params['orderId'] ?? 0;
        $perPage = $params['perPage'] ?? 0;
        $with = $params['with'] ?? [];
        $query = $this->orderDetailModel->where('order_id', $id);
        $query = $this->orderDetailModel->includes($query, $with);

        $data = DataHelper::getList($query, $this->orderDetailTransformer, $perPage, 'ListOrderDetail');
        
        return ResponseHelper::success($response, $data);
    }
}
