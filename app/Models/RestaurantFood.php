<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantFood extends Model
{
    use HasFactory;

    protected $table = 'restaurant_foods';

    protected $casts = [
      'price' => 'int',
      'salePrice' => 'int',
      'active' => 'bool',
      'popular' => 'bool',
      'sale' => 'bool',
      'novetly' => 'bool',
    ];

    public function categoryFood()
    {
        return $this->belongsTo(CategoryFood::class, 'category_id', 'id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
