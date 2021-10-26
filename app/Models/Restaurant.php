<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
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
        'delivery_time',
        'work_time',
        'user_id',
        'category_id',
        'title',
        'description',
        'min_delivery_price',
        'street',
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
//        'name',
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
    ];

    public function categoryRestaurant()
    {
        return $this->belongsTo(CategoryRestaurant::class, 'category_id', 'id');
    }

    public function restaurantFood()
    {
        return $this->hasMany(RestaurantFood::class, 'restaurant_id', 'id');
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
}
