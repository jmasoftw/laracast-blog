<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     *
     * For the user_id and category_id we need to create first instances of
     *  a user and a category model objects to which this post will relate
     * and that's why we build them calling to their respective factories.
     * By doing it so, everytime we create a post, we are also creating a user
     * and a category in the database at the same time we insert a post.
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->slug,
            'excerpt' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'published_at' => $this->faker->dateTime
        ];
    }
}
