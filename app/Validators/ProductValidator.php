<?php


namespace App\Validators;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductType;
class ProductValidator extends BaseValidator
{
    public function __construct(Product $product, ProductType $productType)
    {
        $this->product = $product;
        $this->productType = $productType;
    }
    public function requireData()
    {
        if(     
            !$this->requireParam('name', 'Vui lòng nhập tên sản phẩm !') || 
            !$this->requireParam('priceImport', 'Vui lòng thêm giá nhập !') || 
            !$this->requireParam('priceExport', 'Vui lòng thêm giá bán !') || 
            !$this->requireParam('amount', 'Vui lòng nhập số lượng sản phẩm !') || 
            !$this->requireParam('type', 'Vui lòng chọn loại sản phẩm !') )
        {
            return false;
        }else{
            return true;
        }
    }
    public function checkNumericData()
    {
        if(
            !$this->checkNumeric('priceImport', 'Vui lòng thêm giá nhập là một số !') ||
            !$this->checkNumeric('priceExport', 'Vui lòng thêm giá xuất là một số !')
        ){
            return false;
        }else{
            return true;
        }
    }

    public function checkNumericAmount()
   {
        if (!is_numeric($this->request->get('amount')) || $this->request->get('amount') < 0) {
            $this->setError(400, 'invalid_param', "Vui lòng thêm số lượng sản phẩm là một số !");
            return false;
        }
       return true;
   }

    public function checkProductTypeExist()
    {
        $productTypeId = $this->request->get('type') ?? null;
        $productType = $this->productType->where("type_id" , $productTypeId)->first();
        if(!$productType){
            $this->setError(400, 'Error', "Product type not exist", 'Vui lòng chọn lại loại sản phẩm !');
            return false;
        }else{
            return true;
        }
    }

    public function checkProductExist()
    {
        $id = $this->request->get('id') ?? null;
        $product = $this->product->where("pro_id" , $id)->first();
        if(!$product){
            $this->setError(400, 'Error', "Product not exist", 'Sản phẩm không tồn tại');
            return false;
        }else{
            return true;
        }
    }
    public function save()
    {
        if (!$this->requireData() || !$this->checkNumericData() || !$this->checkProductTypeExist() || !$this->checkNumericAmount()) {
            return false;
        } else {
            return true;
        }
    }

}
?>
