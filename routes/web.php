<?php

use Illuminate\Support\Facades\Route;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Give me a listing of all the posts in our database including the category and the author (eager loading) to which the post is bind
Route::get('/', function () {
    return view('posts', [
        'posts' => Post::latest('published_at')
            ->with('category', 'author')
            ->get()
    ]);
});

// We find a post in the database matching the id provided by the user.
// We filter the user input coming from the post route in the {post} parameter using regex
// in order to sanitize it by only allowing alphanumeric characters and the characters _ and - as well.
// We use Route Model Binding (see notes)
Route::get('posts/{post}', function (Post $post) {
    return view('post', [
        'post' => $post
    ]);
})->where('post', '[a-zA-Z0-9_\-]+');

//  Give me all the posts regarding a certain given category
//  We're explicit about using slug and not the default (id) as key to get the results
//  because it's prettier showing a slugged category appended at the end of the route name instead of an id number.
// We use the load() method to eager load every post's author and category in one call avoiding unnecesary calls to the server.
// We could get rid off the load method if we activate the eager loading as an overall for all the posts in the Post class (see $with in Post.php file)
Route::get('categories/{category:slug}', function (Category $category) {
    return view('posts', [
        'posts' => $category->posts->load(['category', 'author']) // We use the category <-> post relationship set in the category model to fetch the results
    ]);
});

//  Give me all the posts written by a certain given author
//  We're explicit about using slug and not the default (id) as key to get the results
//  because it's prettier showing a slugged name appended at the end of the route name instead of an id number.
// We use the load() method to eager load every post's author and category in one call avoiding unnecesary calls to the server.
// We could get rid off the load method if we activate the eager loading as an overall for all the posts in the Post class (see $with in Post.php file)
Route::get('authors/{author:slug}', function (User $author) {
    return view('posts', [
        'posts' => $author->posts->load(['category', 'author']) // We use the author <-> post relationship set in the Post model to fetch the results
    ]);
});
