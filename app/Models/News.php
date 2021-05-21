<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';

    protected $fillable = ['id_post', 'date', 'content', 'title', 'image', 'slug', 'categories'];

    protected $dates = ['date'];

    public function getCategoriesAttribute($value)
    {
        return json_decode($value);
    }
}
