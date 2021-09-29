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
        'user_id',
    ];

    public function restaurantFood()
    {
        return $this->hasMany(RestaurantFood::class,'restaurant_id','id');
    }
}
