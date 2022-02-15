<?php

namespace App\Models;

use App\Http\Traits\BitwiseTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    use HasFactory;
    use BitwiseTrait;

    protected $fillable = [
        'nom', 'description',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
