<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category()
    {
        return $this->hasOne(JournalCategory::class, 'id', 'category_id');
    }

    public function group()
    {
        return $this->hasOne(JournalGroup::class, 'id', 'group_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'journal_tags', 'tag_id', 'id');
    }
}
