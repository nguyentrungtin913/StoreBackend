<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartDetail extends BaseModel
{
    public $timestamps = false;
    protected $table = 'cart_detail';
    protected $primaryKey = 'detail_id';
    const ALIAS = [
        'detail_id'         => 'detailId',
        'pro_id'            => 'productId',
        'cart_id'           => 'cartId',
        'detail_amount'     => 'detailAmount',
    ];

    protected $fillable = [
        'pro_id',
        'cart_id',
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

    public function cart()
    {
        return $this->belongsTo(cart::class,'cart_id','cart_id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'pro_id', 'pro_id');
    }
}
