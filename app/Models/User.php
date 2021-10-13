<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends BaseModel //Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'user';
    public $timestamps = false;
    protected $primaryKey = 'user_email';

    protected $fillable = [
        'user_email',
        'user_password',
        'user_name',
        'user_delete',
    ];
    
    static function query()
    {
        $query = parent::query();
        $query->notDeleted();
        return $query;
    }

    function scopeNotDeleted($query)
    {
        return $query->where('user_deleted', 0);
    }

    const ALIAS = [
        'user_email'            => 'email',
        'user_name'             => 'name',
    ];

}
