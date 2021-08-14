<?php

namespace Database\Factories;

use App\Models\Classe;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClasseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Classe::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $years = [1,2,3,4,5,6];
        $letters = ["A", "B", "C", "D", "E", "F", "G", "H", "J", "K"];
        foreach ($years as $year) {
            foreach ($letters as $letter) {
                $ref[] = "1 D1 {$year}C =\"{$letter}\"";
                $libelle[] = $year.$letter;
                $titulaire[] = $year;
            }
        }
        $rid = $this->faker->unique()->numberBetween(0,59);
        return [
            'ref' => $ref[$rid],
            'libelle' => $libelle[$rid],
            'titulaire' => $titulaire[$rid],
        ];
    }
}
