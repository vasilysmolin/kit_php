<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRestaurant extends Model
{
    use HasFactory;

    public function orderFood()
    {
        return $this->hasMany(OrderFood::class, 'order_restaurant_id', 'id');
    }

    public function restaurant(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Restaurant::class, 'id', 'restaurant_id');
    }
}
