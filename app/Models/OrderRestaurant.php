<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRestaurant extends Model
{
    use HasFactory;

    public function orderFood()
    {
        return $this->hasMany(OrderFood::class, 'restaurant_food_id', 'id');
    }
}
