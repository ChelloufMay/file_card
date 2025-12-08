<?php

namespace Database\Factories;

use App\Models\VerificationCode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class VerificationCodeFactory extends Factory
{
    protected $model = VerificationCode::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'contact' => $this->faker->word(),
            'code' => $this->faker->word(),
            'method' => $this->faker->word(),
            'purpose' => $this->faker->word(),
            'token' => Str::random(10),
            'expires_at' => Carbon::now(),
            'used' => $this->faker->boolean(),
        ];
    }
}
