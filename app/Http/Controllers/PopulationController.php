<?php

namespace App\Http\Controllers;

use App\Jobs\ImporteElevesSiel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Storage;

/**
 * PopulationController
 * @TODO Clean this mess up and translate
 */
class PopulationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }
    
    /**
     * Charge la page population
     * @param Request $request
     * @return view
     */
    public function index(Request $request) {
        return view('population.index', []);
    }
    
    /**
     * Réceptionne l'upload du csv issu de Siel et retourne le tableau brut,
     * ainsi que la liste des classes nouvelles/anciennes/stables
     * @param Request $request
     * @return array ['tableau' => $siel->importe($csv), 'classes' => $siel->concordancesclasses($refs)]
     */
    public function postcsv(Request $request) {
        $path = $request->file('sielpopulation')->store('sielcsv');
        $uname = auth()->user()->name;
        info("$uname démarre l'importation d'un fichier Siel.");
        //$this->journalise('%1$s démarre l\'importation d\'un csv Siel');
        $siel = new \App\Maison\Siel();
        $csv = $siel->get($path);
        //info("siel->get(path)");
        $tableau = $siel->importe($csv);
        //info("tableau = siel->importe(csv)");
        $refs = $siel->classesuniques($tableau);
        //info("refs = siel->classesuniques(tableau)");
        $classes = $siel->concordanceclasses($refs);
        //info("classes = siel->concordanceclasses(refs)");
        // checkboxing transféré côté client (javascript forEach)
        //$classes["nouvelles"] = $siel->checkboxing($classes["nouvelles"]);
        $domcol = count($classes['anciennes']) > 0 ? $this->dommagesCollaterauxSupClas($classes['anciennes']) : [];
        $eleves = $siel->parcourt($tableau);
        //info("eleves = siel->parcourt(tableau)");
        $dbclasses = \App\Models\Classe::withTrashed()->get();
        return response()->json(['tableau' => $tableau, 'eleves' => $eleves, 'classes' => $classes, 'domcol' => $domcol, 'dbclasses' => $dbclasses]);
    }
    
    
    public function postclasses(Request $request) {
        $classes = json_decode($request->input('classes'));
        $tableau = new \Illuminate\Support\Collection(json_decode($request->input('tableau'), true));
        if ($nouvelles = $classes->nouvelles) {
            $this->ajouteNouvellesClasses($nouvelles);
        }
        if ($anciennes = $classes->anciennes) {
            $this->supprimeAnciennesClasses($anciennes);
        }
        if ($stables = $classes->stables) {
            $this->corrigeClassesStables($stables);
        }
        $siel = new \App\Maison\Siel();
        $refs = $siel->classesuniques($tableau);
        $classes = $siel->concordanceclasses($refs);
        $domcol = count($classes['anciennes']) > 0 ? $this->dommagesCollaterauxSupClas($classes['anciennes']) : [];
        $eleves = $siel->parcourt($tableau);
        $dbclasses = \App\Models\Classe::all();
        return response()->json(['tableau' => $tableau, 'eleves' => $eleves, 'classes' => $classes, 'domcol' => $domcol, 'dbclasses' => $dbclasses]);
    }
    
    private function dommagesCollaterauxSupClas($anciennes) {
        foreach ($anciennes as $classe) {
            $domcol[]=['classe'=>$classe, 'evenements'=>$classe->evenements, 'profs'=>$classe->profs];
        }
        return $domcol;
    }
    
    private function ajouteNouvellesClasses($nouvelles) {
        $ids = [];
        foreach ($nouvelles as $c) {
            if (isset($c->checked) && $c->checked) {
                $classe = new \App\Models\Classe();
                $classe->ref = $c->ref;
                $classe->libelle = isset($c->libelle) ? $c->libelle : '';
                $classe->titulaire = isset($c->titulaire) ? $c->titulaire : '';
                $classe->save();
                $ids[] = $classe->id;
            }
        }
        //$this->journalise('%1$s ajoute %2$d nouvelles classes', count($ids), ['classes_nouvelles' => $ids]);
        $uname = auth()->user()->name;
        $nids = count($ids);
        info("$uname ajoute $nids nouvelles classes");
    }
    
    private function supprimeAnciennesClasses($anciennes) {
        $ids = [];
        foreach ($anciennes as $c) {
            if (isset($c->checked)) {
                $classe = new \App\Models\Classe();
                $classe = $classe->findByRef($c->ref);
                $classe->delete();
                $ids[] = $classe->id;
            }
        }
        //$this->journalise('%1$s supprime %2$d anciennes classes inutilisées', count($ids), ['classes_anciennes' => $ids]);
        $uname = auth()->user()->name;
        $nids = count($ids);
        info("$uname supprime $nids anciennes classes inutilisées");
    }
    
    private function corrigeClassesStables($stables) {
        $ids = [];
        foreach ($stables as $c) {
            if (isset($c->checked)) {
                $classe = new \App\Models\Classe();
                $classe = $classe->findByRef($c->ref);
                $classe->libelle = isset($c->libelle) ? $c->libelle : '';
                $classe->titulaire = isset($c->titulaire) ? $c->titulaire : '';
                $classe->save();
                $ids[] = $classe->id;
            }
        }
        //$this->journalise('%1$s corrige %2$d classes', count($ids), ['classes_modifiees' => $ids]);
        $uname = auth()->user()->name;
        $nids = count($ids);
        info("$uname modifie $nids classes");
    }
    
    private function journalise($action, $n=null, $memo=null) {
        \App\Models\Journal::create([
            'categorie' => 'population',
            'public' => 128,
            'action' => sprintf($action, auth()->user()->name, $n),
            'memo' => $memo,
            'user_id' => auth()->user()->id,
        ]);
    }
    
    public function posteleves(Request $request) {
        $eleves = json_decode($request->input('eleves'), true);
        $tableau = new \Illuminate\Support\Collection(json_decode($request->input('tableau'), true));
        ImporteElevesSiel::dispatch($eleves, $tableau, auth()->user());
        //\App\Jobs\ImporteElevesSiel::dispatch($eleves, $tableau, auth()->user());
        return 1;
    }
    
    public function listes() {
        return view('population.listes', []);
    }
    
    public function liste_identifiants() {
        $dbeleves = \App\Models\Eleve::with('classe:id,libelle')->get(['prenom','nom','classe_id','matricule']);
        $c = new \App\Maison\Calculs();
        foreach ($dbeleves as $e) {
            $eleves[]=['matricule'=>$e->matricule,
                'prenom'=>$e->prenom,
                'nom'=>$e->nom,
                'classe'=>$e->classe->libelle,
                'identifiant'=>$c->prenomme($e->prenom, $e->nom)];
        }
        return response()->json($eleves);
    }
    
    /**
     * Charge (AJAX) la table élèves pour la rapprocher avec la base d'utilisateurs Moodle
     */
    public function listedeseleves() {
        $dbeleves = \App\Models\Eleve::with('classe:id,libelle,titulaire')->get(['id', 'prenom','nom','classe_id','brol']);
        foreach ($dbeleves as $e) {
            $eleves[]=['sid'=>$e->id,
                'mid'=>$e->brol->moodleid ?? null,
                'prenom'=>$e->prenom,
                'nom'=>$e->nom,
                'classe'=>$e->classe->libelle,
                'titulaire'=>$e->classe->titulaire];
        }
        return response()->json($eleves);
    }
    
}
