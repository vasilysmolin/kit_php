<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitedUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'profile_id',
        'user_id',
    ];

    public function invitedUser(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class,'id', 'profile_id');
    }
}
