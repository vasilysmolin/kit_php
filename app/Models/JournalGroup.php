<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalGroup extends Model
{
    use HasFactory;

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'group_id', 'id');
    }
}
