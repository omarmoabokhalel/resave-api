<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
    'order_id',
    'item_id',
    'estimated_quantity',
    'actual_quantity',
    'price',
    'points_earned',
    'image'
];



    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
