<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // We seed our database with 13 new posts creating at the same time
        // 13 categories and 13 users as well because every post is related to a
        //  user and a category that have to pre-exist beforehand (see the Post model)
        Post::factory(13)->create();
    }
}
