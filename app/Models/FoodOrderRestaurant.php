<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodOrderRestaurant extends Model
{
    use HasFactory;

    public function orderDishes()
    {
        return $this->hasMany(FoodOrderDishes::class, 'order_restaurant_id', 'id');
    }

    public function restaurant(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FoodRestaurant::class, 'id', 'restaurant_id');
    }
}
