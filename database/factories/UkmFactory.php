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
            'description' => $this->faker->paragraph,
            'pembina' => $this->faker->name,
            'logo' => 'default-logo.png',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
