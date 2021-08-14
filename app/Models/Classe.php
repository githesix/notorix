<?php

namespace App\Models;

use App\Http\Traits\BitwiseTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classe extends Model
{
    use HasFactory;
    use BitwiseTrait;
    use SoftDeletes;

    public function vue() {
        return $this->only('id', 'libelle', 'titulaire', 'deleted_at');
    }
    
    public static function allVue($withTrashed = false) {
        $vue=[];
        $classes = $withTrashed ? self::withTrashed()->orderBy('ref', 'asc')->get() : self::orderBy('ref', 'asc')->get();
        foreach ($classes as $c) {
            $vue[$c->id] = $c->vue();
        }
        return $vue;
    }
    public function findByRef($ref) {
        return $this->where('ref', $ref)->first();
    }
    
    public function eleves() {
        $eleves = $this->hasMany('App\Models\Eleve');
        return $eleves;
    }
    
    /**
     * Who's working with this classroom?
     * @return User
     */
    public function users() {
        return $this->belongsToMany('App\Models\User');
    }

    /**
     * French backward compatibility
     */
    public function profs()
    {
        return $this->users();
    }
    
    /**
     * Returns an array of parents (users.id) for this classroom.
     * This method is a longer sql workaround of hasManyThrough that can not
     * crawl up through two joints.
     * @return array users.id
     */
    public function parents() {
        $sql = "SELECT DISTINCT users.id FROM users JOIN eleve_user ON users.id=eleve_user.user_id JOIN eleves ON eleves.id = eleve_user.eleve_id JOIN classes ON eleves.classe_id = ".$this->id;
        $userids = DB::select($sql);
        foreach ($userids as $objet) {
            $ids[]=$objet->id;
        }
        return isset($ids) ? $ids : null;
    }
    
}
