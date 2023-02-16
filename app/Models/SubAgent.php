<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'phone',
        'email',
        'itemable_id',
        'profile_id',
        'itemable_type',
    ];

    public function houses()
    {
        return $this->hasMany(House::class, 'agent_id', 'id');
    }
}
