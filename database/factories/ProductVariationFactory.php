<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariation>
 */
class ProductVariationFactory extends Factory
{
    protected $model = ProductVariation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(2, true);

        return [
            'product_id' => Product::factory(),
            'title' => [
                'en' => $title,
                'km' => $title . ' (km)',
            ],
            'status' => fake()->randomElement(['ACTIVE', 'INACTIVE']),
            'price' => fake()->randomFloat(2, 1, 500),
            'size' => fake()->randomElement(['XS', 'S', 'M', 'L', 'XL']),
            'description' => [
                'en' => fake()->sentence(10),
                'km' => fake()->sentence(10),
            ],
            'note' => [
                'en' => fake()->sentence(6),
                'km' => fake()->sentence(6),
            ],
            'is_available' => fake()->boolean(70),
            'image' => fake()->optional(0.6)->lexify('variation-????.jpg'),
            'user_id' => User::factory(),
        ];
    }

    // \App\Models\ProductVariation::factory()->count(20)->create();
}
