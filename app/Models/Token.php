<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Token extends BaseModel
{
    use HasFactory, Notifiable;
    protected $table = 'token';
    public $timestamps = false;
    protected $primaryKey = 'token_value';

    protected $fillable = [
        'token_value',
        'token_expired',
        'token_email',
    ];
    static function query()
    {
        $query = parent::query();
        return $query;
    }

    const ALIAS = [
        'token_value'   => 'Token',
    ];

}
