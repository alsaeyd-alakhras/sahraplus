<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LiveTvCategory>
 */
class LiveTvCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nameEn = $this->faker->unique()->words(2, true);
        $nameAr = 'Category ' . $this->faker->unique()->word();
        
        return [
            'name_ar' => $nameAr,
            'name_en' => ucfirst($nameEn),
            'slug' => \Illuminate\Support\Str::slug($nameEn),
            'description_ar' => 'Description: ' . $this->faker->sentence(10),
            'description_en' => $this->faker->sentence(10),
            'icon_url' => null,
            'cover_image_url' => null,
            'sort_order' => $this->faker->numberBetween(0, 100),
            'is_featured' => $this->faker->boolean(10),
            'is_active' => $this->faker->boolean(90),
        ];
    }

    /**
     * Indicate that the category is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
