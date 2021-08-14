<?php

namespace Database\Factories;

use App\Models\Groupe;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Groupe::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nom' => $this->faker->unique()->jobTitle(),
            'description' => $this->faker->catchPhrase(),
        ];
    }
}
