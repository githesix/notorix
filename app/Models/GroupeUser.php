<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupeUser extends Model
{
    use HasFactory;
    protected $table = 'groupe_user'; // By default, Eloquent would look for groupe_users
}
