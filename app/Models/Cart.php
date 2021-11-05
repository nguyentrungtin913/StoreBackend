<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends BaseModel
{
    public $timestamps = false;
    protected $table = 'cart';
    protected $primaryKey = 'cart_id';
    const ALIAS = [
        'cart_id'         => 'cartId',
        'cart_name'       => 'cartName',
        'cart_phone'      => 'cartPhone',
        'cart_total'      => 'cartTotal',
        'cart_status'     => 'cartStatus',
    ];

    protected $fillable = [
        'cart_id',
        'cart_name',
        'cart_phone',
        'cart_total',
        'cart_status',
        'cart_deleted',
    ];

    static function query()
    {
        $query = parent::query();
        $query->notDeleted();
        return $query;
    }

    function scopeNotDeleted($query)
    {
        return $query->where('cart_deleted', 0);
    }

    public function cartDetail()
    {
        return $this->hasMany(cartDetail::class,'cart_id','cart_id');
    }
}
