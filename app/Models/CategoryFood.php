<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryFood extends Model
{
    use HasFactory;

    public function restaurantFood()
    {
        return $this->hasMany(RestaurantFood::class,'category_id','id');
    }
}
