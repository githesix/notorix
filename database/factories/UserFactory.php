<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

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
        return [
            'name' => "$prenom $nom",
            'email' => $email,
            'email_verified_at' => now(),
            'username' => $email,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'prenom' => $prenom,
            'nom' => $nom,
            'tel1' => $this->faker->unique()->e164PhoneNumber(),
            'sexe' => $sexe,
            'uid' => $this->faker->uuid(),
            'secu' => '1234',
            'role' => 1,
            'statut' => 1,
            'solde' => 0,
        ];
    }
}
