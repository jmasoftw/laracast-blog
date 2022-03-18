<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'excerpt', 'body', 'published_at'];

    /*    protected $with = ['category', 'author'];   // An alternative way to set eager loading for this properties in every post*/

    public function category()
    {
        //        Switch to this one if a post in this app can only have one distinctive category
        return $this->belongsTo(Category::class);

        /*        Switch to this one if a post in this app is allowed to belong to multiple categories
         return $this->hasMany(Category::class);*/
    }

    public function author()
    {
        // We set an alias to change user() with author() in the method name and keep the key (user_id) to
        //which this method makes reference. Its more legible and understandable to say that a post has an author

        //        Switch to this one if a post in this app can only have one distinctive author
        return $this->belongsTo(User::class, 'user_id');

        /*        Switch to this one if a post in this app is allowed to have to multiple authors
         return $this->hasMany(User::class, 'user_id');*/
    }
}
