<?php

namespace App\Http\Controllers;

use Facade\FlareClient\Http\Response as HttpResponse;
use Illuminate\Http\Request;
use Response;
use App\Models\OrderDetail;
use App\Validators\OrderDetailValidator;
use App\Transformers\OrderDetailTransformer;
use App\Helpers\DataHelper;
use App\Helpers\ResponseHelper;

class OrderDetailController extends Controller
{
    public function __construct(OrderDetail $orderDetailModel, OrderDetailTransformer $orderDetailTransformer, OrderDetailValidator $orderDetailValidator)
    {
        $this->orderDetailModel = $orderDetailModel;
        $this->orderDetailTransformer = $orderDetailTransformer;
        $this->orderDetailValidator = $orderDetailValidator;
    }

    public function index(Request $request, Response $response)
    {
        $params = $request->all();

        $perPage = $params['perPage'] ?? 0;
        $with = $params['with'] ?? [];

        $orderBy = $this->productTypeModel->orderBy($params['sortBy'] ?? null, $params['sortType'] ?? null);

        $query = $this->productTypeModel->filter($this->productTypeModel::query(), $params)->orderBy($orderBy['sortBy'], $orderBy['sortType']);

        $query = $this->productTypeModel->includes($query, $with);

        $data = DataHelper::getList($query, $this->productTypeTransformer, $perPage, 'ListOrderDetail');
        
        return ResponseHelper::success($response, $data);
    }

    public function find(Request $request, Response $response)
    {
        

    }

    public function save(Request $request, Response $response)
    {
        
        
    }

    public function update(Request $request, Response $response)
    {
       
    }

    public function delete(Request $request, Response $response)
    {
        
    }
}
