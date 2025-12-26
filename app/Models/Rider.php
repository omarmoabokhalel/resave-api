<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Rider extends Authenticatable
{
    use HasApiTokens,Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_available'
    ];

    protected $hidden = [
        'password'
    ];

    // العلاقة مع الطلبات
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
