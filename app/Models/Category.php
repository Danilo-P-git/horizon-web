<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'pivot'];

    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }
}
