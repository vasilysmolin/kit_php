<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodOrder extends Model
{
    use HasFactory;

    public function orderRestaurant()
    {
        return $this->hasMany(FoodOrderRestaurant::class, 'order_id', 'id');
    }
}
