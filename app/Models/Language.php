<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['language'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'pivot'];

    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }
}
