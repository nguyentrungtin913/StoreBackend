<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\hasMany;

class ImageProduct extends BaseModel
{
    public $timestamps = false;
    protected $table = 'image_product';
    protected $primaryKey = 'ipro_id';
    const ALIAS = [
        'ipro_id'         => 'id',
        'ipro_image'      => 'image'
    ];

    protected $fillable = [
        'ipro_id',
        'ipro_image'
    ];
}
