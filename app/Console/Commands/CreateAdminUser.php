<?php

namespace App\Console\Commands;

use App\Maison\Calculs;
use App\Maison\UUID;
use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:adminuser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user with admin privileges (generally the first user)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $first_name = $this->ask(__('First name'));
        $last_name = $this->ask(__('Last name'));
        $sex = $this->choice(__('Gender'), ['m', 'f']);
        $email = $this->ask(__('Email'));
        $password = $this->secret(__('Password'));
        $calculs = new Calculs();
        User::create([
            'prenom' => $first_name,
            'nom' => $last_name,
            'name' => $first_name . ' ' . $last_name,
            'email' => $email,
            'username' => $email,
            'password' => bcrypt($password),
            'secu' => $calculs->tokenize($password),
            'email_verified_at' => date('Y-m-d H:i:s'),
            'sexe' => $sex,
            'uid' => UUID::uid8(),
            'statut' => 1,
            'role' => 128
        ]);
        $this->info("Admin user $first_name $last_name was created");
    }
}
