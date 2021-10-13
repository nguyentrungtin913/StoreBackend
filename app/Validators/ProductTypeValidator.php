<?php


namespace App\Validators;
use Illuminate\Http\Request;
use App\Models\ProductType;
class ProductTypeValidator extends BaseValidator
{

    public function __construct(ProductType $productType)
    {
        $this->productType= $productType;
    }

     public function requireName()
    {
        return $this->requireParam('typeName', 'Vui lòng nhập tên loại sản phẩm');
    }

    public function requireId()
    {
        return $this->requireParam('typeId', 'Vui lòng nhập Id');
    }

    public function checkNameExist()
    {
        $productTypeName = $this->request->get('typeName') ?? null;
        $productType = $this->productType->query()->where("type_name" , $productTypeName)->first();
        if($productType){
            $this->setError(400, 'error', "Type name exist", 'Tên loại sản phẩm đã tồn tại!');
            return false;
        }else{
            return true;
        }
    }

    public function checkNameExistUpdate()
    {
        $productTypeName = $this->request->get('typeName') ?? null;
        $productTypeId = $this->request->get('typeId') ?? null;

        $productType = $this->productType->where([["type_name" , $productTypeName], ["type_id", "!=", $productTypeId]])->first();
        if($productType){
            $this->setError(400, 'Error', "Type name exist", 'Tên loại sản phẩm đã tồn tại!');
            return false;
        }else{
            return true;
        }
    }

    public function checkProductTypeExist()
    {
        $productTypeId = $this->request->get('typeId') ?? null;
        $productType = $this->productType->where("type_id" , $productTypeId)->first();
        if(!$productType){
            $this->setError(400, 'Error', "Product type not exist", 'Loại sản phẩm không tồn tại!');
            return false;
        }else{
            return true;
        }
    }

    public function store(){
        if (!$this->requireName() || !$this->checkNameExist()) {
            return false;
        } else {
            return true;
        }
    }

    public function update(){
        if (!$this->requireName() || !$this->requireId() || !$this->checkProductTypeExist() || !$this->checkNameExistUpdate()) {
            return false;
        } else {
            return true;
        }
    }
}

?>
