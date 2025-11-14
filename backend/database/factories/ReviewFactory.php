<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'card_id' => Card::factory(),
            'user_id' => User::factory(),
            'result' => $this->faker->randomElement(['easy','ok','hard']),
            'comment' => $this->faker->optional()->sentence(),
        ];
    }
}
