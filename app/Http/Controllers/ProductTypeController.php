<?php

namespace App\Http\Controllers;

use Facade\FlareClient\Http\Response as HttpResponse;
use Illuminate\Http\Request;
use Response;
use App\Models\ProductType;
use App\Validators\ProductTypeValidator;
use App\Transformers\ProductTypeTransformer;
use App\Helpers\DataHelper;
use App\Helpers\ResponseHelper;

class ProductTypeController extends Controller
{
    public function __construct(ProductType $productTypeModel, ProductTypeTransformer $productTypeTransformer, ProductTypeValidator $productTypeValidator)
    {
        $this->productTypeModel = $productTypeModel;
        $this->productTypeTransformer = $productTypeTransformer;
        $this->productTypeValidator = $productTypeValidator;
    }
    public function test(Request $request)
    {
        $params = $request->all();

        $design=$params['image'] ?? null;
        return $design;
    }
    public function index(Request $request, Response $response)
    {
        $params = $request->all();

        $perPage = $params['perPage'] ?? 0;
        $with = $params['with'] ?? [];

        $orderBy = $this->productTypeModel->orderBy($params['sortBy'] ?? null, $params['sortType'] ?? null);

        $query = $this->productTypeModel->filter($this->productTypeModel::query(), $params)->orderBy($orderBy['sortBy'], $orderBy['sortType']);

        $query = $this->productTypeModel->includes($query, $with);

        $data = DataHelper::getList($query, $this->productTypeTransformer, $perPage, 'ListAllProductType');
        
        return ResponseHelper::success($response, $data);
    }

    public function find(Request $request, Response $response)
    {
        $param = $request->all();

        if (!$this->productTypeValidator->setRequest($request)->checkProductTypeExist()) {
            $errors = $this->productTypeValidator->getErrors();
            return ResponseHelper::errors($response, $errors);
        }
        
        $typeId = $param['typeId'] ?? null;
        $productType = $this->productTypeModel->where('type_id', $typeId)->first();
        $productType = $this->productTypeTransformer->transformItem($productType);
        return ResponseHelper::success($response, compact('productType'));

    }

    public function rating(Request $request, Response $response)
    {
        $param = $request->all();

        if (!$this->productTypeValidator->setRequest($request)->checkProductTypeExist()) {
            $errors = $this->productTypeValidator->getErrors();
            return ResponseHelper::errors($response, $errors);
        }
        
        $typeId = $param['typeId'] ?? null;
        $typeVote = $param['typeVote'] ?? 0;
        $productType = $this->productTypeModel->where('type_id', $typeId)->first();
        $productType->update([
            'type_vote' => $typeVote
        ]);
        $productType = $this->productTypeTransformer->transformItem($productType);
        return ResponseHelper::success($response, compact('productType'));

    }
    public function getProTypeByVote(Request $request, Response $response)
    {
        $params = $request->all();

        $perPage = $params['perPage'] ?? 0;
        $with = $params['with'] ?? [];
        $sortBy = 'vote';
        $sortType = 'desc';

        $orderBy = $this->productTypeModel->orderBy($sortBy, $sortType);

        $query = $this->productTypeModel->filter($this->productTypeModel::query(), $params)->orderBy($orderBy['sortBy'], $orderBy['sortType']);

        $query = $this->productTypeModel->includes($query, $with);

        $data = DataHelper::getList($query, $this->productTypeTransformer, $perPage, 'ListAllProductType');
        
        return ResponseHelper::success($response, $data);
    }
    public function save(Request $request, Response $response)
    {
        $params = $request->all();
        if (!$this->productTypeValidator->setRequest($request)->store()) {
            $errors = $this->productTypeValidator->getErrors();
            return ResponseHelper::errors($response, $errors);
        }
        $productType = $this->productTypeModel->create([
            'type_name' => $params['typeName']
        ]);
        if($productType){
            $productType = $this->productTypeTransformer->transformItem($productType);
            return ResponseHelper::success($response, compact('productType'));    
        }
        return ResponseHelper::requestFailed($response);
        
    }

    public function update(Request $request, Response $response)
    {
        $param = $request->all();
        if (!$this->productTypeValidator->setRequest($request)->update()) {
            $errors = $this->productTypeValidator->getErrors();
            return ResponseHelper::errors($response, $errors);
        }

        $productType = $this->productTypeModel->where('type_id', $param['typeId'])->first();
        if($productType->update(['type_name' => $param['typeName']])) {
            $productType = $this->productTypeTransformer->transformItem($productType);
            return ResponseHelper::success($response, compact('productType'));
        }else
        {
            return ResponseHelper::requestFailed($response);
        }
    }

    public function delete(Request $request, Response $response)
    {
        $param = $request->all();
        $typeId = $param['typeId'] ?? null;

        if (!$this->productTypeValidator->setRequest($request)->checkProductTypeExist()) {
            $errors = $this->productTypeValidator->getErrors();
            return ResponseHelper::errors($response, $errors);
        }

        if($this->productTypeModel->where('type_id', $typeId)->update(['type_deleted' => 1])) {
            $productType = $this->productTypeModel->where('type_id', $typeId)->first();
            $productType = $this->productTypeTransformer->transformItem($productType);
            return ResponseHelper::success($response, compact('productType'), 'Success Delete type product success');
        }

        return ResponseHelper::requestFailed($response);
    }
    
}
