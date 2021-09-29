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
        return $this->belongsTo(CategoryFood::class,'category_id','id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class,'restaurant_id','id');
    }
}
