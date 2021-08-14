<?php

namespace App\Jobs;

use App\Maison\Siel;
use App\Models\Classe;
use App\Models\Eleve;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

/**
 * ImporteElevesSiel
 * @TODO clean this mess up and translate
 */
class ImporteElevesSiel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $eleves, $tableau, $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($eleves, $tableau, User $user)
    {
        $this->eleves = $eleves;
        info("Job importe eleves siel ".count($eleves["nouveaux"])." nouveaux élèves");
        $this->tableau = $tableau;
        $this->user = $user;
    }
    
    /**
     * Write a line into the log
     * format «population» with two variables
     * @param string sprintf $action
     * @param int $n = nombre
     * @param array $memo
     */
    private function journalise($action, $n=null, $memo=null) {
        \App\Models\Journal::create([
            'categorie' => 'population',
            'public' => 128,
            'action' => sprintf($action, $this->user->name, $n),
            'memo' => $memo,
            'user_id' => $this->user->id,
        ]);
    }
    
    private function ajouteNouveauxEleves($nouveaux) {
        $ids = [];
        $classe = new \App\Models\Classe();
        $classesref = \App\Models\Classe::withTrashed()->get()->pluck('id', 'ref');
        foreach ($nouveaux as $e) {
            $eleve = new Eleve();
            $eleve->dbilise($e);
            $eleve->classe_id = $classesref[$e['classe_ref']];
            //$eleve->classe_id = $classe->findByRef($e['classe_ref'])->id;
            // Le champ 'classe_ref' est fabriqué temporairement par Siel->importe
            //$eleve->uid = 'e'.env('FASE_ECOLE'). \App\Maison\UUID::uid8();
            $eleve->uid = \App\Maison\UUID::uid8();
            $eleve->save();
            $ids[] = $eleve->id;
        }
        //$this->journalise('%1$s importe %2$d nouveaux élèves', count($ids), ['ids_nouveaux' => $ids]);
        $uname = $this->user->name;
        $n = count($ids);
        info("$uname importe $n nouveaux élèves");
        return $ids;
    }
    
    private function parentsNouveaux($ids_nouveaux) {
        $siel = new Siel();
        return $siel->parentsNouveaux($ids_nouveaux);
    }
    
    private function modifieElevesModifs($modifs) {
        $ids = [];
        $dbidsparmatricule = Eleve::all()->pluck('id', 'matricule');
        $modifs = collect($modifs);
        $matriculesatraiter = $modifs->pluck('matricule');
        $classesref = Classe::withTrashed()->get()->pluck('id', 'ref');
        foreach ($matriculesatraiter as $matricule) {
            $eleve = Eleve::find($dbidsparmatricule[$matricule]);
            $e = $modifs->where('matricule', $matricule)->first();
            $eleve->dbilise($e);
            $eleve->classe_id = Classe::find($classesref[$e['classe_ref']])->id;
            $eleve->save();
            $ids[] = $eleve->id;
        }
        $uname = $this->user->name;
        $n = count($ids);
        info("$uname importe $n élèves à modifier");
        return $ids;
        // Ancienne version plus lente et plus consommatrice de mémoire
        $ids = [];
        $classe = new Classe();
        $eleve = new Eleve();
        foreach ($modifs as $e) {
            $eleve = $eleve->findByMatricule($e['matricule']);
            $eleve->dbilise($e);
            $eleve->classe_id = $classe->findByRef($e['classe_ref'])->id;
            // Le champ 'classe_ref' est fabriqué temporairement par Siel->importe
            $eleve->save();
            $ids[] = $eleve->id;
        }
        //$this->journalise('%1$s importe %2$d élèves à modifier', count($ids), ['ids_modifs' => $ids]);
        return $ids;
    }
    
    private function archiveElevesDeletes($deletes) {
        $ids = [];
        foreach ($deletes as $d) {
            $e = Eleve::find($d['id']);
            $e->setFlag(8,1);
            $e->save();
            $ids[] = $e->id;
            $e->delete();
        }
        /* 
        $eleve = new \App\Models\Eleve();
        foreach ($deletes as $e) {
            $eleve = $eleve->findByMatricule($e['matricule']);
            $eleve->setFlag(8, 1);
            $eleve->save();
            $ids[] = $eleve->id;
            $eleve->delete();
        } */
        //$this->journalise('%1$s déclare %2$d élève(s) à archiver', count($ids), ['ids_deletes' => $ids]);
        $uname = $this->user->name;
        $n = count($ids);
        info("$uname déclare $n élèves à archiver");
        return $ids;
    }
    
    private function fouilleEmails() {
        $siel = new Siel();
        $emails = $siel->fouilleEmailResp();
        Cache::forget('emails_responsables');
        Cache::forever('emails_responsables', $emails);
        //$this->journalise('%2$d adresse(s) e-mail de parents trouvée(s)', count($emails));
        $n = count($emails);
        info("$n adresses e-mail trouvées lors de la fouille import");
    }
    
    private function cloture() {
        info("Import csv Siel terminé");
        //$this->journalise('Import csv Siel terminé');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Siel $siel)
    {
        // funnel (disponible uniquement avec redis) restreint ce job à 1 à la fois
        //\Illuminate\Support\Facades\Redis::funnel('Cle_ImporteElevesSiel')->limit(1)->then(function () {
        Redis::funnel('Cle_ImporteElevesSiel')->limit(1)->then(function () {
            if ($nouveaux = $this->eleves['nouveaux']) {
                $ids_nouveaux = $this->ajouteNouveauxEleves($nouveaux);
            }
            if ($modifs = $this->eleves['modifs']) {
                $ids_modifs = $this->modifieElevesModifs($modifs);
            }
            if ($deletes = $this->eleves['deletes']) {
                $ids_deletes = $this->archiveElevesDeletes($deletes);
            }
            $this->fouilleEmails();
            $this->cloture();
            //$eleves = $siel->parcourt($this->tableau);
            //return response()->json(['eleves' => $eleves]);
        }, function () {
            // Could not obtain lock...
            return $this->release(10);
        });
    }
    
}
