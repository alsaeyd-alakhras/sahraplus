<?php

namespace Database\Factories;

use App\Models\LiveTvCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LiveTvChannel>
 */
class LiveTvChannelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nameEn = $this->faker->unique()->company() . ' TV';
        $nameAr = 'Channel ' . $this->faker->unique()->company();

        return [
            'category_id' => LiveTvCategory::factory(),
            'name_ar' => $nameAr,
            'name_en' => $nameEn,
            'slug' => \Illuminate\Support\Str::slug($nameEn),
            'description_ar' => 'Description: ' . $this->faker->sentence(15),
            'description_en' => $this->faker->sentence(15),
            'logo_url' => 'https://placehold.co/300x300?text=' . urlencode($nameEn),
            'poster_url' => null,
            'stream_url' => \Illuminate\Support\Str::slug($nameEn),
            'stream_type' => $this->faker->randomElement(['hls', 'dash', 'rtmp']),
            'viewer_count' => 0,
            'language' => $this->faker->randomElement(['ar', 'en', 'fr']),
            'country' => $this->faker->countryCode(),
            'is_active' => $this->faker->boolean(80),
            'is_featured' => $this->faker->boolean(20),
            'sort_order' => $this->faker->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the channel is active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the channel is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
