<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class OrderDetail extends BaseModel
{
    public $timestamps = false;
    protected $table = 'order_detail';
    protected $primaryKey = 'detail_id';
    const ALIAS = [
        'detail_id'         => 'detailId',
        'pro_id'            => 'productId',
        'order_id'          => 'orderId',
        'detail_amount'     => 'detailAmount',
    ];

    protected $fillable = [
        'pro_id',
        'order_id',
        'detail_amount',
        'detail_deleted'
    ];

    static function query()
    {
        $query = parent::query();
        $query->notDeleted();
        return $query;
    }

    function scopeNotDeleted($query)
    {
        return $query->where('detail_deleted', 0);
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id','order_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'pro_id', 'pro_id');
    }
}
