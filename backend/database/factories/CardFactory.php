<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\Deck;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition(): array
    {
        return [
            'deck_id' => Deck::factory(),
            'question' => $this->faker->sentence(),
            'answer' => $this->faker->paragraph(),
            'tags' => ['example','sample'],
            'box_level' => 1,
            'repetitions' => 0,
            'easiness_factor' => 2.5,
            'interval_days' => 0,
            'next_review_at' => null,
            'last_reviewed_at' => null,
        ];
    }
}
