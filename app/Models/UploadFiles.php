<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadFiles extends Model
{
    use HasFactory;

    /**
     * Get the parent imageable model.
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}
