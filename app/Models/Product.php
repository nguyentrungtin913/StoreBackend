<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
class Product extends BaseModel
{
    public $timestamps = false;
    protected $table = 'product';
    protected $primaryKey = 'pro_id';
    const ALIAS = [
        'pro_id'            => 'id',
        'pro_name'          => 'name',
        'pro_image'         => 'image',
        'pro_im_price'      => 'priceImport',
        'pro_ex_price'      => 'priceExport',
        'pro_amount'        => 'amount',
        'pro_amount_sell'   => 'amountSell',
        'pro_note'          => 'note',
        'pro_type'          => 'type'    
    ];

    protected $fillable = [
        'pro_name',
        'pro_image',
        'pro_im_price',
        'pro_ex_price',
        'pro_amount',
        'pro_amount_sell',
        'pro_note',
        'pro_type',
        'pro_deleted'
    ];
    static function query()
    {
        $query = parent::query();
        $query->notDeleted();
        return $query;
    }

    function scopeNotDeleted($query)
    {
        return $query->where('pro_deleted', 0);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class, 'pro_type', 'type_id');
    }
}
