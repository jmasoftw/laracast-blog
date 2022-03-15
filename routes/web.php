<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Models\Post;
use Spatie\YamlFrontMatter\YamlFrontMatter;

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

Route::get('/', function () {
    return view('posts', [
        'posts' => Post::all()
    ]);
});

Route::get('posts/{post}', function ($slug) {
    // We find a post by its slug which matches the name of the post html file stored in the filesystem.
    // FOR THIS METHOD TO WORK, THE POSTS HAVE TO BE STORED BY THE NAME OF THE POST'S TITLE SLUGGED
    // We filter the user input coming from the post route in the {post} parameter using regex
    // in order to sanitize it by only allowing alphanumeric characters and the characters _ and - as well.

    return view('post', [
        'post' => Post::findOrFail($slug)
    ]);
})->where('post', '[a-zA-Z0-9_\-]+');
