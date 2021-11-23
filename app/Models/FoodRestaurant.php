<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodRestaurant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'alias',
        'active',
        'isDelivery',
        'isPickup',
        'active',
        'latitude',
        'longitude',
        'delivery_time',
        'work_time',
        'profile_id',
        'title',
        'description',
        'min_delivery_price',
        'address',
        'house',
        'coords',
        'phone',
        'email',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $hidden = [
        'title',
//        'alias',
        'active',
        'user_id',
//        'category_id',
//        'title',
//        'description',
//        'min_delivery_price',
//        'street',
        'house',
        'coords',
//        'coords',
//        'phone',
//        'email',
        'updated_at',
        'created_at',
    ];
    protected $casts = [
        'active' => 'bool',
        'work_time' => 'array',
        'delivery_time' => 'array',
        'min_delivery_price' => 'int',
        'isDelivery' => 'bool',
        'isPickup' => 'bool',
    ];

    public function categories()
    {
        return $this->belongsToMany(FoodCategoryRestaurant::class, 'food_restaurant_has_categories', 'restaurant_id', 'category_id');
    }

    public function dishes()
    {
        return $this->hasMany(FoodRestaurantDishes::class, 'restaurant_id', 'id');
    }

    public function foodRestaurantDish()
    {
        return $this->hasMany(FoodRestaurantDishes::class, 'restaurant_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function latestImage()
    {
        return $this->morphOne(Image::class, 'imageable')->latestOfMany();
    }

    public function oldestImage()
    {
        return $this->morphOne(Image::class, 'imageable')->oldestOfMany();
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'id');
    }
}
