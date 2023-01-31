<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Journal extends Model
{
    use HasFactory;
    use Searchable;

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'id');
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
        return $this->belongsToMany(Tag::class, 'journal_tags', 'tag_id', 'tag_id');
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->load(['category', 'tags'])->only(['name','description','category', 'tags']);
        return $array;
    }

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'journal';
    }
}
