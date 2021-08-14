<?php

namespace App\Models;

use App\Http\Traits\BitwiseTrait;
use App\Maison\Siel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Eleve extends Model
{
    use SoftDeletes;
    use BitwiseTrait;
    use HasFactory;
    
    // protected $table = 'eleves'; // Eloquent guesses the table name already
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    protected $dates = ['deleted_at', 'date_nais', 'date_inscript'];
    
    protected $casts = [
        'brol' => 'array',
    ];
    
    public function vue() {
        $vue=$this->only('id', 'uid', 'statut', 'prenom', 'nom', 'solde');
        $vue['date_nais']=$this->date_nais->format('Y-m-d H:i:s');
        $vue['classe']=$this->classe()->withTrashed()->first()->vue();
        return $vue;
    }
    
    public function classe() {
        $classe = $this->belongsTo('App\Models\Classe');
        return $classe;
    }
    
    /**
     * Who are the parents of this student?
     * @return User
     */
    public function users() {
        return $this->belongsToMany('App\Models\User');
    }
    public function parents() {
        return $this->users();
    }

    /**
     * Which user corresponds to this student?
     *
     * @return User
     */
    public function elu()
    {
        return $this->hasOne('App\Models\User', 'elu');
    }
    
    /**
     * Counts the parents for this student
     * @param array $filtreclasses
     * @return array[eleveid=>nparents]
     */
    public static function nparents($filtreclasses = null) {
        $whereclasses = $filtreclasses ? " AND eleves.classe_id IN (".implode($filtreclasses, ',').") " : "";
        $sql = "SELECT eleves.id AS elid, COUNT(users.id) AS nparents FROM eleves
            JOIN eleve_user ON eleve_user.eleve_id = eleves.id
            JOIN users ON users.id = eleve_user.user_id
            WHERE eleves.deleted_at IS NULL AND users.deleted_at IS NULL
            $whereclasses
            GROUP BY eleves.id
            ORDER BY eleves.id";
        $res = DB::select($sql);
        $nparents = [];
        foreach ($res as $objet) {
            $nparents[$objet->elid] = $objet->nparents;
        }
        return $nparents;
    }
    
    public function findByMatricule($matricule) {
        return $this->where('matricule', $matricule)->first();
    }
    
    /**
     * whereRaw for a case sensitive search
     * @param type $uid
     * @return Eleve
     */
    public function findByUid($uid) {
        return $this->whereRaw("BINARY `uid`= ?", [$uid])->first();
    }
    
    /**
     * Fills Eleve with $brut array, and verifies if every field corresponds to
     * schema. Can be used for instance to clean «checked» values from input forms.
     * Serializes brol, even if $brut was already flatten, and let Carbon check dates.
     * @param array $brut
     * @return Eleve
     */
    public function dbilise($brut) {
        //$e = new \App\Models\Eleve();
        $siel = new Siel();
        $schema = $siel->schema;
        $brol = [];
        foreach ($schema as $champ => $v) {
            if ($v['save']==1) {
                if ($v['brol']==1) {
                    if (isset($brut[$champ])) { // brol aplati dans brut
                        $brol[$champ]=$brut[$champ];
                    }
                    if (isset($brut['brol'][$champ])) { // brol déjà brolisé dans brut
                        $brol[$champ] = $brut['brol'][$champ];
                    }
                } else {
                    if (isset($brut[$champ])) {
                        if ($v['type'] == 'date d/m/Y') {
                            // ATTENTION DateTime attend une date américaine si elle contient des slashes (m/d/Y)
                            // Je ne sais pas comment j'ai importé d'autres dates avant sans avoir d'erreurs
                            //$this->$champ = \Carbon\Carbon::parse($brut[$champ]);
                            // Il faut spécifier le format à Carbon (qui appelle DateTime::__construct)
                            $this->$champ = \Carbon\Carbon::createFromFormat('d/m/Y', $brut[$champ]);
                        } else if ($v['type'] == 'date Y/m/d') {
                            $this->$champ = \Carbon\Carbon::createFromFormat('Y/m/d', $brut[$champ]);
                        } else {
                            $this->$champ=$brut[$champ];
                        }
                    }
                }
            }
        }
        //$this->brol = serialize($brol);
        $this->brol = $brol;
        return $this;
    }
    
}
