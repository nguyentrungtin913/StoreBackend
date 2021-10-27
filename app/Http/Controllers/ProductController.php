<?php

namespace App\Http\Controllers;

use Facade\FlareClient\Http\Response as HttpResponse;
use Illuminate\Http\Request;
use Response;
use App\Models\Product;
use App\Validators\ProductValidator;
use App\Transformers\ProductTransformer;
use App\Helpers\DataHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\Random;
class ProductController extends Controller
{
    public function __construct(Product $productModel, ProductTransformer $productTransformer, ProductValidator $productValidator)
    {
        $this->productModel = $productModel;
        $this->productTransformer = $productTransformer;
        $this->productValidator = $productValidator;
    }

    public function index(Request $request, Response $response)
    {
        $params = $request->all();

        $perPage = $params['perPage'] ?? 0;
        $with = $params['with'] ?? [];

        $orderBy = $this->productModel->orderBy($params['sortBy'] ?? null, $params['sortType'] ?? null);

        $query = $this->productModel->filter($this->productModel::query(), $params)->orderBy($orderBy['sortBy'], $orderBy['sortType']);
        //$query->where([['pro_amount',">",0]]);
        $query = $this->productModel->includes($query, $with);

        $data = DataHelper::getList($query, $this->productTransformer, $perPage, 'ListAllProduct');
        
        return ResponseHelper::success($response, $data);
    }

    public function find(Request $request, Response $response)
    {
      $params = $request->all();

        if (!$this->productValidator->setRequest($request)->checkProductExist()) {
            $errors = $this->productValidator->getErrors();
            return ResponseHelper::errors($response, $errors);
        }

        $id = $params['id'] ?? null;
        $product = $this->productModel->where('pro_id', $id)->first();
        $product = $this->productTransformer->transformItem($product);
        return ResponseHelper::success($response, compact('product'));
    }

    public function save(Request $request, Response $response)
    {
        $params=$request->all();
          
        if (!$this->productValidator->setRequest($request)->save()) {
            $errors = $this->productValidator->getErrors();
            return ResponseHelper::errors($response, $errors);
        }
        $name           = $params['name'];
        $priceImport    = $params['priceImport'];
        $priceExport    = $params['priceExport'];
        $amount         = $params['amount'];
        $amountSell     = $params['amountSell'] ?? 0;
        $note           = $params['note'] ?? null;
        $proType        = $params['type'];

        
        $image =request('image');
        $path = "";
        $clientOriginalExtension = 'jpg';
        if($image){
            $clientOriginalExtension = explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
        }

        $newImage =  Random::character(18).'.'.$clientOriginalExtension;

        $folder = 'storage/app/public/products/';

        if(!is_dir($folder)){
            mkdir($folder, 0777, true);
        }
        if($image){
            \Image::make($request->get('image'))->save(public_path($folder).$newImage);
            $path =$folder.$newImage;
        }

        
        // if (!is_dir("storage/app/public/products")) {
        //     mkdir("storage/app/public/products");
        // }
        
        // $nameImage = Random::character(18).'.jpg';

        // $image = 'storage/app/public/products/' . $nameImage;
        

        $product = $this->productModel->create([
            'pro_name'          => $name,
            'pro_image'         => $path, //$image,
            'pro_im_price'      => $priceImport,
            'pro_ex_price'      => $priceExport,
            'pro_amount'        => $amount,
            'pro_amount_sell'   => $amountSell,
            'pro_note'          => $note,
            'pro_type'          => $proType
        ]);
        if($product){
            //copy($path, $image);
            $id = $product->pro_id;
            $product =  $this->productModel->where('pro_id', $id)->with('productType')->first();
            $product= $this->productTransformer->transformItem($product);
            return ResponseHelper::success($response, compact('product'));    
        }
        return ResponseHelper::requestFailed($response); 
    }

    public function update(Request $request, Response $response)
    {
        $params = $request->all();
        // if (!$this->productTypeValidator->setRequest($request)->update()) {
        //     $errors = $this->productTypeValidator->getErrors();
        //     return ResponseHelper::errors($response, $errors);
        // }
        $product = $this->productModel->where('pro_id', $params['id'])->first();

        $name           = $params['name'] ?? $product->pro_name;
        $image          = $params['image'] ?? $product->pro_image;
        $priceImport    = $params['priceImport'] ?? $product->pro_im_price;
        $priceExport    = $params['priceExport'] ?? $product->pro_ex_price;
        $amount         = $params['amount'] ?? $product->pro_amount;
        $amountSell     = $params['amountSell'] ?? $product->pro_amount_sell;
        $note           = $params['note'] ?? $product->pro_note;
        $proType        = $params['type'] ?? $product->pro_type;

        $file = $params['image'] ?? null;
        if($file){
            $image =request('image');
   
            $clientOriginalExtension = explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];

            $newImage =  Random::character(18).'.'.$clientOriginalExtension;

            $folder = 'storage/app/public/products/';

            if(!is_dir($folder)){
                mkdir($folder, 0777, true);
            }

            \Image::make($request->get('image'))->save(public_path($folder).$newImage);

            $image =$folder.$newImage;
        }
        if($product->update([
                'pro_name' => $name,
                'pro_image' => $image,
                'pro_im_price' => $priceImport,
                'pro_ex_price' => $priceExport,
                'pro_amount' => $amount,
                'pro_amount_sell' => $amountSell,
                'pro_note' => $note,
                'pro_type' => $proType,
            ])) {
            $product =  $this->productModel->where('pro_id', $params['id'])->with('productType')->first();
            $product = $this->productTransformer->transformItem($product);
            return ResponseHelper::success($response, compact('product'));
        }else
        {
            return ResponseHelper::requestFailed($response);
        }
    }

    public function delete(Request $request, Response $response)
    {
        $param = $request->all();
        if (!$this->productValidator->setRequest($request)->checkProductExist()) {
            $errors = $this->productValidator->getErrors();
            return ResponseHelper::errors($response, $errors);
        }
        $id = $param['id'] ?? null;
        if($this->productModel->where('pro_id', $id)->update(['pro_deleted' => 1])) {
            $product = $this->productModel->where('pro_id', $id)->first();
            $product = $this->productTransformer->transformItem($product);
            return ResponseHelper::success($response, compact('product'), 'Success Delete  product success');
        }

        return ResponseHelper::requestFailed($response);

    }
}
