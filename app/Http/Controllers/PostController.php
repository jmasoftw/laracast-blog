<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

// Give me a listing of all the posts in our database including the category and the author (eager loading) to which the post is bind
// and give as well a list of all the categories in the database table to fill the categories drop-down component in the blade views.
// We set a key: currentCategory to store the current category being showed (taken from the route address) and pass it to the view to
// highlight that category in the categories dropdown as it's the one currently being showed.
// If there's a search term in the route path, pass it through to the Post model scopeFilter and perform the query there accordingly.
// If there's a category term in the route path, pass it through to the Post model scopeFilter and perform the query there accordingly.
class PostController extends Controller
{
    public function index()
    {
        return view('posts', [
            'posts' => Post::latest('published_at')
                ->filter(request(['search', 'category']))
                ->with('category', 'author')
                ->get(),
            'categories' => Category::all(),
            'currentCategory' => Category::where(
                'slug',
                request('category')
            )->first()
        ]);
    }

    // We find a post in the database matching the slug provided by the user.
    public function show(Post $post)
    {
        return view('post', [
            'post' => $post
        ]);
    }
}
