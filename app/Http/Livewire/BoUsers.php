<?php

namespace App\Http\Livewire;

use App\Http\Controllers\ExcelExportController;
use App\Models\Groupe;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Maatwebsite\Excel\Facades\Excel;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Illuminate\Support\Str;

class BoUsers extends LivewireDatatable
{
    protected $listeners = ['resetPassword', 'fieldEdited', 'forcerefreshLivewireDatatable', 'resendemailverif', 'refreshGroups'];
    public $filters = [
        'Unverified' => [false, 'email_verified_at', '=', null],
        'Invited' => [false, 'role', '=', 0],
        'Student' => [false, 'role', '&', 2],
        'Parent' => [false, 'role', '&', 4],
        'Teacher' => [false, 'role', '&', 16],
        'Educator' => [false, 'role', '&', 32],
        'Administrator' => [false, 'role', '&', 128],
    ];
    public $withTrashed = false;
    public $groupes = [], $groupFilters = [];

    public function init()
    {
        $this->groupes = Groupe::orderBy('nom', 'asc')->withCount('users')->get(['id', 'nom', 'description']);
    }

    /* public $model = User::class; */

    public function builder()
    {
        $query = User::query()->with('groupes');
        foreach ($this->filters as $filter => $where) {
            if ($where[0]) {
                $query = $query->where($where[1], $where[2], $where[3]);
            }
        }
        if ($this->withTrashed) {
            /* $query = $query->withTrashed() */
            $query = $query->onlyTrashed() // swap with line above to get both trashed AND not trashed
            ->where(function($binquery) {
                $binquery->whereNull('deleted_at')
                ->orWhereDate('deleted_at', '>', Carbon::today()->subMonths(18)->toDateTimeString());
            });
        }
        foreach ($this->groupFilters as $key => $value) {
            if ($value) {
                $query = $query->whereHas('groupes', function(Builder $query) use ($key) {$query->where('groupes.id',$key);});
            }
        }
        return $query;
    }

    public function columns()
    {
        return [
            Column::name('users.id')
            ->label('ID')
            ->excludeFromExport()
            ->hide(),

            Column::name('users.prenom')
            ->filterable()
            ->searchable()
            ->editable()
            ->label(__('First name')),
            
            Column::name('users.nom')
            ->filterable()
            ->searchable()
            ->editable()
            ->label(__('Last name')),
            
            /* Column::callback(['email', 'memo'], function ($privatemail, $memo) {
                $memo = json_decode($memo);
                if (isset($memo) && isset($memo->exim) && !empty($memo->exim->type)) {
                    $email = $memo->exim->username.'@'.$memo->exim->domain;
                } else {
                    $email = $privatemail;
                }
                return $email;
            })
            ->label('Email')
            ->filterable()
            ->searchable(), */

            Column::name('users.email')
            ->filterable()
            ->searchable()
            ->editable()
            ->label(__('E-Mail')),

            Column::callback(['id', 'role', 'statut', 'deleted_at'], function ($id, $role, $statut, $trashed) {
                $user = User::withTrashed()->find($id);
                $roles = $user->roles;
                return view('partials.boedituserrole', ['id' => $user->id, 'r' => $user->role, 'roles' => $roles, 'statut' => $statut, 'trashed' => $trashed]);
            })
            ->label(__('Role & status')),

            Column::callback(['id', 'name', 'email', 'email_verified_at', 'deleted_at'], function($id, $name, $email, $isVerified, $trashed) {
                return view('partials.boedituseractions', ['id' => $id, 'name' => $name, 'email' => $email, 'value' => $id, 'isVerified' => $isVerified, 'trashed' => $trashed]);
            })
            ->label(__('Actions')),

        ];
    }

    public function softDelete($id) {
        $user = User::find($id);
        $user->delete();
        $this->init();
    }

    public function resetPassword($datas) {
        $email = $datas['email'];
        $response = Password::broker()->sendResetLink(['email'=>$email]);
        if ($response == Password::RESET_LINK_SENT) {
            info(__(':admin sent password reset link to :user', ["admin" => Auth::user()->name, "user" => $email]));
            return true;
        } else {
            info('ERROR: ' . __(':admin had a problem sending reset password link to :user', ["admin" => Auth::user()->name, "user" => $email]));
            return false;
        }
    }

    public function fieldEdited($id)
    {
        $user = User::find($id);
        $user->name = $user->prenom . " " . $user->nom;
        $user->username = $user->email;
        $user->save();
        $this->init();
    }

    public function forcerefreshLivewireDatatable()
    {
        $this->mount();
        $this->render();
        $this->init();
    }

    public function resendemailverif($datas)
    {
        $user = User::find($datas['id']);
        $user->sendEmailVerificationNotification();
        $this->emit('validationlinksent');
    }

    public function deleteGroupe($groupeId)
    {
        $groupe = Groupe::find($groupeId);
        $groupe->delete();
        $this->init();
    }

    public function refreshGroups()
    {
        $this->init();
        foreach ($this->groupes as $groupe) {
            $this->groupFilters[$groupe->id] = false;
        }
    }

    public function activate($id)
    {
        $this->init();
        $user = User::find($id);
        $user->setRole(1,1);
        $user->save();
    }

    /**
     * UNUSED
     * Just for info
     */
    public function getExports()
    {
        // skipped columns with no label or hidden
        $columns = collect($this->columns)->reject(function ($value, $key) {
            return $value['label'] == null || $value['hidden'] == true;
        });
        
        // build array of column names
        $column_names = $columns->map(function ($value, $key) {
            return $value['name'];
        })->all();
        
        $mapped = $this->mapCallbacks(
            $this->getQuery()->get()
        );
        
        // filter column name data results
        $results = $mapped->map(function ($item, $key) use ($column_names) {
            return collect($item)->only($column_names)->all();
        });
        
        $data['columns'] = $columns;
        $data['results'] = $results;
        
        return $data;
    }

    public function customSearch($search = null, $query = null)
    {
        $search = $search ?? $this->search;
        $query = $query ?? $this->builder();
        $query = $query->with(['classes', 'kids']);
        $search = explode(' ', $search);
        foreach ($search as $s) {
            $query = $query->where(function($q) use ($s){
                $q->where('prenom', 'like', $this->like($s))
                ->orWhere('nom', 'like', $this->like($s))
                ->orWhere('email', 'like', $this->like($s))
                ->orWhereHas('kids', function (Builder $kids) use($s) {
                    $kids->where('prenom', 'like', $this->like($s))
                    ->orwhere('nom', 'like', $this->like($s));
                })
                ->orWhereHas('classes', function (Builder $classes) use($s) {
                    $classes->where('libelle', 'like', $this->like($s));
                });
            });
        }
        return $query;
    }

    public function addGlobalSearch()
    {
        if (! $this->search) {
            return $this;
        }

        $this->query = $this->customSearch(null, $this->query);
        return $this;
    }

    public function prepareExport()
    {
        $query = $this->customSearch();
        $filters = $this->activeTextFilters;
        $colfilters = [1 => 'prenom', 2 => 'nom', 3 => 'email'];
        foreach ($filters as $key => $value) {
            foreach ($value as $s) {
                $query = $query->where($colfilters[$key], 'like', $this->like($s));
            }
        }
        $res = $query->get();
        return $res;
    }

    public function like($s)
    {
        return '%' . strtolower($s) . '%';
    }

    public function export()
    {
        $this->forgetComputed();
        $results = $this->prepareExport();
        $exportix = collect();
        foreach ($results as $r) {
            $x = [
                'id' => $r['id'],
                __('First name') => $r['prenom'],
                __('Last name') => $r['nom'],
                __('Email') => $r['email'],
                __('Phone 1') => $r['tel1'],
                __('Phone 2') => $r['tel2'],
                __('Groups') => $r['groupes']->implode('nom', '|'),
                __('Classes') => $r['classes']->implode('libelle', '|'),
                /* 'kids' => implode('|', array_map(function($i) {return $i['prenom'].' '.$i['nom'];}, $r['kids']->toArray())), */
                __('Kids') => implode('|', $r['kids']->map(function ($i, $k) {return $i['prenom'].' '.$i['nom'];})->toArray()),
            ];
            $exportix->push($x);
        }
        return $exportix->downloadExcel('exportix.xlsx', null, true);
    }

}