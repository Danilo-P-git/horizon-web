<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'code'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at', 'pivot'];

    public function languages()
    {
        return $this->belongsToMany(Language::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }


    public static function transformData($data)
    {
        return [
            'name' => $data['name'],
            'code' => $data['code'],
            'languages' => $data['languages']->pluck('language'),
            'categories' => $data['categories']->pluck('name'),
        ];
    }
}
