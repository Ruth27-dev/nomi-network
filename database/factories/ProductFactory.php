<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->words(3, true);

        return [
            'branch_id' => fake()->numberBetween(1, 5),
            'code' => fake()->unique()->bothify('PRD-#####'),
            'title' => [
                'en' => $title,
                'km' => $title . ' (km)',
            ],
            'is_sellable' => fake()->boolean(),
            'is_consumable' => fake()->boolean(70),
            'is_vat' => fake()->boolean(30),
            'is_popular' => fake()->boolean(20),
            'status' => fake()->randomElement(['ACTIVE', 'INACTIVE']),
            'type' => fake()->randomElement(['CUSTOMER', 'POS']),
            'description' => [
                'en' => fake()->sentence(10),
                'km' => fake()->sentence(10),
            ],
            'image' => fake()->optional(0.6)->lexify('product-????.jpg'),
            'user_id' => User::factory(),
        ];
    }

    // Product::factory()
    // ->count(5)
    // ->has(ProductVariation::factory()->count(3), 'productVariations')
    // ->create();
}
