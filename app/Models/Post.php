<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'excerpt', 'body', 'published_at'];

    /*    protected $with = ['category', 'author'];   // An alternative way to set eager loading for this properties in every post*/

    //  We use query scopes here, defining a filter to be used in the routes file in order to allow for the search functionality.
    //  We allow passing different filters for different purposes through the filters array using a unique name.This name ('search') is
    //  passed to the route in the input tag for the search field in the _posts-header.blade.php, the route point it here where we check for it.
    //  When we have such search term (not mandatory hereby the false condition), we use it to filter the results according to the where clauses.
    public function scopeFilter($query, array $filters)
    {
        //        If we have a 'search' key, use its value ($search) to perform the sub query
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query
                ->where('title', 'like', '%' . $search . '%')
                ->orWhere('body', 'like', '%' . $search . '%');
        });
    }

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
