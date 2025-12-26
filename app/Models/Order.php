<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'rider_id',
        'status',
        'total_quantity',
        'total_points',
        'scheduled_at'
    ];
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }
}
