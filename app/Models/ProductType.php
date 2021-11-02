<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\hasMany;

class ProductType extends BaseModel
{
    public $timestamps = false;
    protected $table = 'product_type';
    protected $primaryKey = 'type_id';
    const ALIAS = [
        'type_id'        => 'id',
        'type_name'      => 'name',
        'type_vote'      => 'vote',
    ];

    protected $fillable = [
        'type_name',
        'type_vote',
        'type_delete'
    ];
    static function query()
    {
        $query = parent::query();
        $query->notDeleted();
        return $query;
    }

    function scopeNotDeleted($query)
    {
        return $query->where('type_deleted', 0);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'pro_type', 'type_id');
    }
}
