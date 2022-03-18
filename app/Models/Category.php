<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    // We set the relationship between a category and a post, a category has many posts
    // In the routes file we use this ($category->posts) to get all the posts within a certain given category
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
