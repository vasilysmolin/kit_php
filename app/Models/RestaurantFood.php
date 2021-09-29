<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantFood extends Model
{
    use HasFactory;
    protected $table = 'restaurant_foods';

    public function categoryFood()
    {
        return $this->hasOne(CategoryFood::class,'category_id','id');
    }
}
