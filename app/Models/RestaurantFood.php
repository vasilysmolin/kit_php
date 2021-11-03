<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantFood extends Model
{
    use HasFactory;

    protected $table = 'restaurant_foods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'alias',
        'active',
        'restaurant_id',
        'description',
        'price',
        'salePrice',
        'quantity',
        'popular',
        'sale',
        'weight',
        'novetly',
    ];
    protected $casts = [
      'price' => 'int',
      'weight' => 'int',
      'salePrice' => 'int',
      'active' => 'bool',
      'popular' => 'bool',
      'sale' => 'bool',
      'novetly' => 'bool',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    public function categoriesRestaurantFood()
    {
        return $this->belongsToMany(CategoryFood::class, 'restaurant_food_has_categories', 'restaurant_food_id', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
