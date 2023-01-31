<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalCategory extends Model
{
    use HasFactory;

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'category_id', 'id');
    }
}
