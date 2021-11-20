<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodOrderDishes extends Model
{
    use HasFactory;

    public function dishes(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FoodRestaurantDishes::class, 'id', 'restaurant_food_id');
    }
}
