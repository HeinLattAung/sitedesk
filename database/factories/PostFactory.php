<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => rtrim(fake()->sentence(6), '.'),
            'excerpt' => fake()->sentence(18),
            'body' => collect(fake()->paragraphs(5))->implode("\n\n"),
            'published_at' => fake()->dateTimeBetween('-6 months', '-1 day'),
        ];
    }

    public function draft(): static
    {
        return $this->state(['published_at' => null]);
    }
}
