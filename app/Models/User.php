<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use SoftDeletes;
    use SortableTrait;

    private const ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'state',
        'password',
        'city_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
//        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'bool',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'profile_id' => $this->profile->getKey(),
            'id' => $this->getKey(),
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function orders()
    {
        return $this->hasMany(FoodOrder::class, 'user_id', 'id');
    }

    public function bindingAccounts()
    {
        return $this->hasMany(InvitedUser::class, 'user_id', 'id')->with('profile.user', 'profile.person');
    }

    public function checkProfile(int $profileID): bool
    {
        $invitedProfileID = $this->bindingAccounts->pluck('profile_id');
        $invitedProfileID->push((int) $this->profile->getKey());
        return array_search($profileID, $invitedProfileID->toArray(), true) !== false;
    }

    public function isAdmin()
    {
        $role = $this->roles->first();
        return $role && $role->name === self::ADMIN;
    }
}
