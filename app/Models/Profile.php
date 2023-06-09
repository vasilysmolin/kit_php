<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ParagonIE\Sodium\Core\Curve25519\Fe;

class Profile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'isPerson',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

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

    public function sellerHouse()
    {
        return $this->hasOne(SellerHouse::class, 'profile_id', 'id');
    }

    public function restaurant()
    {
        return $this->hasMany(FoodRestaurant::class, 'profile_id', 'id');
    }

    public function vacancy()
    {
        return $this->hasMany(JobsVacancy::class, 'profile_id', 'id');
    }

    public function vacancies()
    {
        return $this->hasMany(JobsVacancy::class, 'profile_id', 'id');
    }

    public function ads()
    {
        return $this->hasMany(CatalogAd::class, 'profile_id', 'id');
    }

    public function realties()
    {
        return $this->hasMany(Realty::class, 'profile_id', 'id');
    }

    public function journals()
    {
        return $this->hasMany(Journal::class, 'profile_id', 'id');
    }

    public function feeds()
    {
        return $this->hasMany(Feed::class, 'profile_id', 'id');
    }

    public function service()
    {
        return $this->hasMany(Service::class, 'profile_id', 'id');
    }

    public function houses()
    {
        return $this->hasMany(House::class, 'profile_id', 'id');
    }

    public function resume()
    {
        return $this->hasMany(JobsResume::class, 'profile_id', 'id');
    }

    public function person()
    {
        return $this->hasOne(Person::class, 'profile_id', 'id');
    }

    public function invitedAccounts()
    {
        return $this->hasMany(InvitedUser::class, 'profile_id', 'id');
    }

    public function isPerson()
    {
        return $this->isPerson === true;
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
