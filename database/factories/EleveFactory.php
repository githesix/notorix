<?php

namespace Database\Factories;

use App\Models\Eleve;
use Illuminate\Database\Eloquent\Factories\Factory;

class EleveFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Eleve::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sexe = $this->faker->randomElement(['m', 'f']);
        $sex = ['m'=>'male', 'f'=>'female'];
        $email = $this->faker->unique()->safeEmail();
        $prenom = $this->faker->firstName($sex[$sexe]);
        $nom = $this->faker->lastName();
        $matricule = $this->faker->unique()->randomNumber(5, true);
        $date_nais = $this->faker->date();
        $date_inscript = "2021-09-01";
        return [
            'sexe' => $sexe,
            'email' => $email,
            'prenom' => $prenom,
            'nom' => $nom,
            'matricule' => $matricule,
            'date_nais' => $date_nais,
            'date_inscript' => $date_inscript,
            'type_responsable_1' => "pere",
            'prenom_resp_1' => $this->faker->firstName('male'),
            'nom_resp_1' => $nom,
            'email_r1' => $email,
        ];
    }
}
