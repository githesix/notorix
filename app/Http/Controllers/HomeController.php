<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Groupe;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tuilesplug = [];
        foreach(config('plugins') ?? [] as $plugin) {
            $plugconf = config($plugin) ?? false;
            if ($plugconf && isset($plugconf['plugin']) && isset($plugconf['plugin']['hometuiles'])) {
                foreach($plugconf['plugin']['hometuiles'] as $tuile) {
                    $tuilesplug[] = $tuile;
                }
            }
        }
        return view('home', ['u' => auth()->user(), 'plugins' => $tuilesplug]);
    }

    public function BOUsers()
    {
        $groupFilters = [];
        $groupes = Groupe::orderBy('nom', 'asc')->withCount('users')->get(['id', 'nom', 'description']);
        foreach ($groupes as $groupe) {
            $groupFilters[$groupe->id] = false;
        }
        return view('bousers', ['groupes' => $groupes, "groupFilters" => $groupFilters]);
    }

    public function eleve(Request $request, $eleve_uid = null)
    {
        if ($eleve_uid == null) {
            return redirect('/')->with(['status' => __('Unknown code')]);
        }
        $eleve = new Eleve();
        $eleve = $eleve->findByUid($eleve_uid);
        // Bad UID
        if (!isset($eleve->id)) {
            abort(403, __('This code does not match our records.'));
        }
        $u = Auth()->user();
        if (isset($u->elu) && $u->elu->id == $eleve->id) {
            // The student logs back in with the invitation's QR code
            return redirect()->route('dashboard')->with('status', __('Hi :Prenom, bookmark this page for easy access.', ['prenom' => $eleve->prenom]));
        }
        if ($u->role & 4 && $eleve->parents->contains($u->id)) {
            // The parent logs back in with student's qr code
            return redirect()->route('dashboard')->with('status', __('Hi :Prenom, bookmark this page for easy access.', ['prenom' => $u->prenom]));
        }
        $data = [
            'eleve' => $eleve,
            'user' => $u,
        ];
        $sim = similar_text(strtolower($eleve->prenom . ' ' . $eleve->nom), strtolower($u->name), $perc);
        $data['simelu'] = $perc;
        return view('eleve', $data);
    }

    public function imtheone(Request $request)
    {
        $u = auth()->user();
        $eleveuid = $request->input('eleveuid');
        $eleve = new Eleve();
        $eleve = $eleve->findByUid($eleveuid);
        if (!isset($eleve->id)) {
            info(__(":user pretends being inexistant eleve with uid :uid", ['user' => $u->name, 'uid' => $eleveuid]));
            return response()->json(['error' => __("There's no student with code :uid", ["uid" => $eleveuid])]);
        }
        if (isset($eleve->elu)) {
            $uel = $eleve->elu->name;
            info(__(":user tries to pretend to be :eleve instead of :uel", ['user' => $u->name, 'eleve' => $eleve->prenom . ' ' . $eleve->nom, 'uel' => $uel]));
            return response()->json(['error' => __("This student is already linked to another user.")]);
        }
        if ($eleve->trashed()) {
            info(__(":user tries to link to deleted student :eleve", ['user' => $u->name, 'eleve' => $eleve->prenom . ' ' . $eleve->nom]));
            return response()->json(['error' => __("This student is not registered in our school anymore")]);
        }
        if ($u->role > 1) {
            info(__("User :user with role :role pretends to be student :eleve", ['user' => $u->name, 'role' => implode(', ', $u->roles), 'eleve' => $eleve->prenom . ' ' . $eleve->nom]));
            return response()->json(['error' => __('This action is unauthorized.')]);
        }
        $u->role = 3;
        $u->elu = $eleve->id;
        $u->save();
        info(__(':user is the student :eleve', ['user' => $u->name, 'eleve' => $eleve->prenom . ' ' . $eleve->nom]));
        $brol = $eleve->brol;
        // Backup existing brol['notorix']
        if (isset($brol['notorix'])) {
            $ajd = date('YmdHis');
            $brol[$ajd . '_notorix'] = $brol['notorix'];
        }
        $brol['notorix'] = [
            'created_at' => date('Y-m-d H:i:s'),
            'user_id' => $u->id,
        ];
        $eleve->brol = $brol;
        $eleve->setFlag(4,true);
        $eleve->save();
    }

    public function imyourfather(Request $request)
    {
        $u = auth()->user();
        $eleveuid = $request->input('eleveuid');
        $eleve = new Eleve();
        $eleve = $eleve->findByUid($eleveuid);
        if (!isset($eleve->id)) {
            info(__(":user pretends to be parent of non-existent eleve uid: :uid", ['user' => $u->name, 'uid' => $eleveuid]));
            return response()->json(['error' => __("There's no student with code :uid", ["uid" => $eleveuid])]);
        }
        if ($u->role & 2) {
            info(__("Student-user :user pretends to be parent of :eleve", ['user' => $u->name, 'eleve' => $eleve->prenom . " " . $eleve->nom]));
            return response()->json(['error' => __('This action is unauthorized.')]);
        }
        if ($eleve->trashed()) {
            info(__(":user tries to parent deleted student :eleve", ['user' => $u->name, 'eleve' => $eleve->prenom . " " . $eleve->nom]));
            return response()->json(['error' => _("This student is not registered in our school anymore")]);
        }
        $u->isParentOf($eleve->id);
        info(__(":user is parent of :eleve", ['user' => $u->name, 'eleve' => $eleve->prenom . " " . $eleve->nom]));
    }

    public function prof(Request $request, $code = null)
    {
        if ($code == null) {
            return redirect('/')->with(['status' => __('Unknown code')]);
        }
        $u = auth()->user();
        if ($code == config('perso.profcode')) {
            $u->setRole(16, 1);
            $u->save();
            info(__(":user subscribes to teacher's role", ['user' => $u->name]));
        }
        if ($code == config('perso.educcode')) {
            $u->setRole(32, 1);
            $u->save();
            info(__(":user subscribes to educator's role", ['user' => $u->name]));
        }
        return redirect('/')->with(['status' => __('Updated role')]);
    }

    public function BOEleves(Request $request)
    {
        $classes = \App\Models\Classe::allVue(true);
        $refclasses = \App\Models\Classe::withTrashed()->get(['id', 'ref'])->pluck('id', 'ref');
        $stats = [
            'elevescount' => Eleve::count(),
            'eluscount' => User::where('role', '&', 2)->count(),
            'elnuscount' => Eleve::whereRaw('(!(statut & 4) OR (statut IS NULL)) AND deleted_at IS NULL')->count(),
            'orphelins' => \App\Models\Eleve::doesntHave('users')->count(),
        ];
        $data = [
            /* 'eleves' => $eleves, */
            'classes' => $classes,
            'refclasses' => $refclasses,
            'stats' => $stats,
        ];
        return view('boeleves', $data);
    }

    /**
     * @TODO clean up and translate
     * Called from boeleves blade
     */
    public function downloadExcel(Request $request, $action)
    {
        if ($action == 'elus') {
            $elus = User::where('role', '&', 2)->get(['uid', 'prenom', 'nom', 'email']);
            $elus->each->setAppends([]);
            return $elus->downloadExcel('utilisateurs_eleves.xlsx', null, true);
        } else
        if ($action == 'elnus') {
            $dbelnus = Eleve::whereRaw('(!(statut & 4) OR (statut IS NULL)) AND deleted_at IS NULL')
            ->with('classe:id,libelle')
            ->get(['uid', 'classe_id', 'prenom', 'nom']);
            $url = route('eleve')."/";
            $elnus = collect();
            foreach ($dbelnus as $e) {
                $elnus->push([
                    'uid' => $e->uid,
                    'prenom' => $e->prenom,
                    'nom' => $e->nom,
                    'classe' => $e->classe->libelle,
                    'url' => $url.$e->uid
                ]);
            }
            return $elnus->downloadExcel('eleves_a_confirmer.xlsx', null, true);
        } else
        if ($action == 'orphelins') {
            $dborphelins = \App\Models\Eleve::doesntHave('users')
            ->with('classe:id,libelle')
            ->get(['uid', 'classe_id', 'prenom', 'nom',
            'type_responsable_1', 'prenom_resp_1', 'nom_resp_1', 'email_r1',
            'type_responsable_2', 'prenom_resp_2', 'nom_resp_2', 'email_r2',
            ]);
            $url = route('eleve')."/";
            $orphelins = collect();
            foreach ($dborphelins as $e) {
                $orphelins->push([
                    'uid' => $e->uid,
                    'prenom' => $e->prenom,
                    'nom' => $e->nom,
                    'classe' => $e->classe->libelle,
                    'url' => $url.$e->uid,
                    'type_responsable_1' => $e->type_responsable_1,
                    'prenom_resp_1' => $e->prenom_resp_1,
                    'nom_resp_1' => $e->nom_resp_1,
                    'email_r1' => $e->email_r1,
                    'type_responsable_2' => $e->type_responsable_2,
                    'prenom_resp_2' => $e->prenom_resp_2,
                    'nom_resp_2' => $e->nom_resp_2,
                    'email_r2' => $e->email_r2,
                ]);
            }
            return $orphelins->downloadExcel('eleves_orphelins.xlsx', null, true);
        }
    }

}
