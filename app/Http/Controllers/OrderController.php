<?php

namespace App\Http\Controllers;

use Facade\FlareClient\Http\Response as HttpResponse;
use Illuminate\Http\Request;
use Response;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Transformers\OrderDetailTransformer;
use App\Models\Product;
use App\Transformers\ProductTransformer;
use App\Validators\OrderValidator;
use App\Transformers\OrderTransformer;
use App\Helpers\DataHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\Random;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function __construct(Order $orderModel, OrderTransformer $orderTransformer, OrderValidator $orderValidator, OrderDetail $orderDetailModel, Product $productModel, OrderDetailTransformer $orderDetailTransformer, ProductTransformer $productTransformer)
    {
        $this->orderModel = $orderModel;
        $this->orderTransformer = $orderTransformer;
        $this->orderValidator = $orderValidator;
        $this->orderDetailModel = $orderDetailModel;
        $this->productModel = $productModel;
        $this->orderDetailTransformer = $orderDetailTransformer;
        $this->productTransformer = $productTransformer;
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
        
        $query = $this->orderModel->filter($this->orderModel::query(), $params)->orderBy('order_id', 'desc' ?? null);

        $data = DataHelper::getList($query, $this->orderTransformer, $perPage, 'ListAllOrder');
        
        return ResponseHelper::success($response, $data);
    }

    public function delete(Request $request, Response $response)
    {
        $param = $request->all();
        $orderId = $param['orderId'] ?? null;

        if (!$this->orderValidator->setRequest($request)->delete()) {
            $errors = $this->orderValidator->getErrors();
            return ResponseHelper::errors($response, $errors);
        }

        if($this->orderModel->where('order_id', $orderId)->update(['order_deleted' => 1])) {
            $order = $this->orderModel->where('order_id', $orderId)->first();
            $order = $this->orderTransformer->transformItem($order);
            return ResponseHelper::success($response, compact('order'), 'Success Delete order success');
        }
        return ResponseHelper::requestFailed($response);
    }


    public function exportCsv(Request $request)
    {
       //return Excel::download(new OrderExport, 'Orders.xlsx');
        $params = $request->all();
        $dateStart=$params['dateStart'] ?? null;
        $dateEnd=$params['dateEnd'] ?? null;
        $type=$params['type'] ?? 0;
        
        $fileName = 'orders.csv';
        if($dateStart && $dateEnd ){
            $query = $this->orderModel::query()->where([['order_date','>=',$dateStart],['order_date','<=',$dateEnd]]);   
        }
        else{
            $query = $this->orderModel::query();    
        }

        switch($type){
            case 0:
                $orders=$query->get();
                break;
            case 1:
                $orders=$query->where('order_type', 'Nhập')->get();
                break;
            case 2: 
                $orders=$query->where('order_type', 'Xuất')->get();
                break;
        }
        
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Content-Encoding"    => "UTF-8",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('STT', 'Tên người mua', 'Tổng tiền', 'Ngày mua', 'Loại');
        
        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $index = 0;
            foreach ($orders as $order) {
                $index++;
                $row['STT']             = $index;
                $row['Tên người mua']   = $order->order_name ?? '(Trống)';
                $row['Tổng tiền']       = $order->order_total;
                $row['Ngày mua']        = $order->order_date;
                $row['Loại']            = $order->order_type;

                fputcsv($file, array($row['STT'], $row['Tên người mua'], $row['Tổng tiền'], $row['Ngày mua'], $row['Loại']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function orderDetail(Request $request, Response $response)
    {
        $params = $request->all();
        if (!$this->orderValidator->setRequest($request)->delete()) {
            $errors = $this->orderValidator->getErrors();
            return ResponseHelper::errors($response, $errors);
        }
        $id = $params['orderId'] ?? 0;
        $perPage = $params['perPage'] ?? 0;
        $with = $params['with'] ?? [];
        

        $query = $this->orderDetailModel->where('order_id', $id);

        $query = $this->orderDetailModel->includes($query, $with);

        $data = DataHelper::getList($query, $this->orderDetailTransformer, $perPage, 'ListOrderDetail');
        
        return ResponseHelper::success($response, $data);
    }
    public function ReportProduct(Request $request, Response $response)
    {
        $params = $request->all();
        $dateStart = $params['dateStart'] ?? null;
        $dateEnd = $params['dateEnd'] ?? null;
        $type = $params['type'] ?? null;
        $status = $params['status'] ?? 1;
        $typePro = $params['typePro'] ?? null;

        if($type === 1){
            $sortType = 'asc';
        }else{
            $sortType = 'desc';
        }
        $sortBy = 'amountSell';
        $listOrderDetail=[];
        $arr=[];
        $test=[];
        $pro=[];

        if(!$dateStart || !$dateEnd)
        {
            $orders = $this->orderModel->where('order_type','Xuất')->get();
        }else{
            $orders = $this->orderModel->where([
                ['order_date','>=', $dateStart],
                ['order_date','<=', $dateEnd],
                ['order_type','=', 'Xuất']
            ])->get();
        }
        

        foreach($orders as $value){
            $orderDetails = [];
            $orderDetails = $this->orderDetailModel->where('order_id', $value->order_id)->get()->toArray();
            $listOrderDetail = array_merge($orderDetails, $listOrderDetail);
        }
        $dem=0;
        for ($i=0; $i < count($listOrderDetail) ; $i++) { 
            if(!in_array($listOrderDetail[$i]['pro_id'],$arr)){
                array_push($arr, $listOrderDetail[$i]['pro_id']);
                array_push($pro, $listOrderDetail[$i]);
            }else{
                for($j=0 ; $j<count($pro); $j++){ 
                   if($pro[$j]['pro_id']===$listOrderDetail[$i]['pro_id']) {
                       $pro[$j]['detail_amount'] += $listOrderDetail[$i]['detail_amount'];
                   }
                }
                $dem++;
            }
        }


        if($status == 1){
            $query = $this->productModel->whereIn('pro_id',$arr)->with('productType');
            if($typePro){
               $query = $query->where('pro_type',$typePro); 
            }
            $listProduct = $query->get();
            
            $listProduct = $this->productTransformer->transformCollection($listProduct);

            for($i=0; $i < count($listProduct); $i++){
                for($j=0; $j < count($pro); $j++){
                    if($listProduct[$i]['id'] === $pro[$j]['pro_id']){
                       $listProduct[$i]['amountSell'] = $pro[$j]['detail_amount'];
                    }
                }
            }  
        }else{
            $query = $this->productModel->whereNotIn('pro_id',$arr)->with('productType');
            if($typePro){
               $query = $query->where('pro_type',$typePro); 
            }
            $listProduct = $query->get();
            
            $listProduct = $this->productTransformer->transformCollection($listProduct);

            for($i=0; $i < count($listProduct); $i++){
                $listProduct[$i]['amountSell'] = 0;
            } 
        }
        
        $listProduct = DataHelper::sortData($listProduct, $sortBy, $sortType);
        
        return ResponseHelper::success($response, compact('listProduct')); 
    }
        
}
