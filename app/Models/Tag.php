<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public function journal()
    {
        return $this->belongsToMany(Journal::class, 'journal_tags', 'journal_id', 'id');
    }
}
