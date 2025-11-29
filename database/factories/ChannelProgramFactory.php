<?php

namespace Database\Factories;

use App\Models\LiveTvChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChannelProgram>
 */
class ChannelProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('now', '+7 days');
        $duration = $this->faker->numberBetween(30, 180);
        $endTime = (clone $startTime)->modify("+{$duration} minutes");

        return [
            'channel_id' => LiveTvChannel::factory(),
            'title_ar' => 'Program ' . $this->faker->words(3, true),
            'title_en' => $this->faker->sentence(4),
            'description_ar' => 'Description: ' . $this->faker->paragraph(),
            'description_en' => $this->faker->paragraph(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_minutes' => $duration,
            'genre' => $this->faker->randomElement([
                'news',
                'sports',
                'entertainment',
                'documentary',
                'movies',
                'series',
                'kids',
                'music',
                'educational',
                'others',
            ]),
            'is_live' => $this->faker->boolean(10),
            'is_repeat' => $this->faker->boolean(20),
            'poster_url' => $this->faker->optional(0.5)->imageUrl(640, 360, 'tv'),
        ];
    }

    /**
     * Indicate that the program is currently airing.
     */
    public function airing(): static
    {
        return $this->state(function (array $attributes) {
            $startTime = $this->faker->dateTimeBetween('-1 hour', 'now');
            $duration = $this->faker->numberBetween(60, 180);
            $endTime = (clone $startTime)->modify("+{$duration} minutes");

            return [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration_minutes' => $duration,
                'is_live' => true,
            ];
        });
    }

    /**
     * Indicate that the program aired in the past.
     */
    public function past(): static
    {
        return $this->state(function (array $attributes) {
            $startTime = $this->faker->dateTimeBetween('-7 days', '-1 hour');
            $duration = $this->faker->numberBetween(30, 180);
            $endTime = (clone $startTime)->modify("+{$duration} minutes");

            return [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration_minutes' => $duration,
                'is_live' => false,
            ];
        });
    }

    /**
     * Indicate that the program will air in the future.
     */
    public function upcoming(): static
    {
        return $this->state(function (array $attributes) {
            $startTime = $this->faker->dateTimeBetween('+1 hour', '+7 days');
            $duration = $this->faker->numberBetween(30, 180);
            $endTime = (clone $startTime)->modify("+{$duration} minutes");

            return [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration_minutes' => $duration,
            ];
        });
    }

    /**
     * Indicate that the program is very old.
     */
    public function old(): static
    {
        return $this->state(function (array $attributes) {
            $startTime = $this->faker->dateTimeBetween('-30 days', '-8 days');
            $duration = $this->faker->numberBetween(30, 180);
            $endTime = (clone $startTime)->modify("+{$duration} minutes");

            return [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration_minutes' => $duration,
            ];
        });
    }

    /**
     * Indicate a specific genre.
     */
    public function genre(string $genre): static
    {
        return $this->state(fn (array $attributes) => [
            'genre' => $genre,
        ]);
    }
}
