<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function restaurant()
    {
        return $this->hasMany(FoodRestaurant::class, 'profile_id', 'id');
    }

    public function vacancy()
    {
        return $this->hasMany(JobsVacancy::class, 'profile_id', 'id');
    }

    public function resume()
    {
        return $this->hasMany(JobsResume::class, 'profile_id', 'id');
    }

    public function person()
    {
        return $this->hasOne(Person::class, 'profile_id', 'id');
    }

    public function foodRestaurant()
    {
        return $this->hasMany(FoodRestaurant::class, 'profile_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(FoodOrder::class, 'profile_id', 'id');
    }

}
