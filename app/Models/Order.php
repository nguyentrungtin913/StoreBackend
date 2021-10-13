<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class Order extends BaseModel
{
    public $timestamps = false;
    protected $table = 'order';
    protected $primaryKey = 'order_id';
    const ALIAS = [
        'order_id'         => 'orderId',
        'order_name'       => 'orderName',
        'order_total'      => 'orderTotal',
        'order_date'       => 'orderDate'
    ];

    protected $fillable = [
        'order_id',
        'order_name',
        'order_total',
        'order_date',
        'order_deleted'
    ];

    static function query()
    {
        $query = parent::query();
        $query->notDeleted();
        return $query;
    }

    function scopeNotDeleted($query)
    {
        return $query->where('order_deleted', 0);
    }

    public function order()
    {
        return $this->hasMany(OrderDetail::class,'order_id','order_id');
    }
}
