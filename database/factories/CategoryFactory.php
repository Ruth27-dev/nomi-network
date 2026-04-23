<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(2, true);

        return [
            'title' => [
                'en' => $title,
                'km' => $title . ' (km)',
            ],
            'description' => [
                'en' => fake()->sentence(8),
                'km' => fake()->sentence(8),
            ],
            'sequence' => fake()->numberBetween(1, 50),
            'status' => fake()->randomElement(['ACTIVE', 'INACTIVE']),
            'image' => fake()->optional(0.6)->lexify('category-????.jpg'),
            'slug' => Str::slug($title),
            'user_id' => User::factory(),
        ];
    }
    
}
