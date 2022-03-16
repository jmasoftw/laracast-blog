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

Route::get('posts/{post}', function ($id) {
    // We find a post in the database matching the id provided by the user.
    // We filter the user input coming from the post route in the {post} parameter using regex
    // in order to sanitize it by only allowing alphanumeric characters and the characters _ and - as well.

    return view('post', [
        'post' => Post::findOrFail($id)
    ]);
})->where('post', '[a-zA-Z0-9_\-]+');
