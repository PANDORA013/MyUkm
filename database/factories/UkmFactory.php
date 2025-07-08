<?php

namespace Database\Factories;

use App\Models\Ukm;
use Illuminate\Database\Eloquent\Factories\Factory;

class UkmFactory extends Factory
{
    protected $model = Ukm::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'code' => $this->faker->unique()->numerify('####'), // Generate 4 digit number
            'description' => $this->faker->paragraph,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
