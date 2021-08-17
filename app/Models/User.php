<?php

namespace App\Models;

use App\Events\UserDeleted;
use App\Http\Traits\BitwiseTrait;
use App\Mail\InvitationPreinscription;
use App\Maison\UUID;
// use Githesix\NotorixExim\Http\Traits\EximUserTrait; // Uncomment this line for NotorixExim package to work
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
//use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
    use BitwiseTrait;
    // use EximUserTrait; // Uncomment this line for NotorixExim package to work

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sexe', 'nom', 'prenom', 'name', 'tel1', 'tel2', 'email', 'email_verified_at', 'username', 'password', 'secu', 'statut', 'ou', 'uid', 'solde', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'secu',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'memo' => 'array',
        'email_verified_at' => 'datetime',
        'admin' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        /* 'exim', */
        'admin',
        'profile_photo_url',
    ];

    /**
     * Status 
     * | status    | 1          |        2 | 4            | 8        |            16 | 32 |     64 | 128 | 256 |
     * |-----------+------------+----------+--------------+----------+---------------+----+--------+-----+-----|
     * | users     | active     |          | unsubscribed |          | email Notorix |    |        |     |     |
     */
    
    /**
     * Roles
    |    0 |     1 |     2 |      4 |          8 |   16 |   32 | 64 |   128 | 256 |
    | User | Guest | Élève | Parent | Resp (dyn) | Prof | Educ |    | Admin |     |
    */
    protected $bitroles = [/* 1 => 'Active', */ 2 => 'Student', 4 => 'Parent', 16 => 'Teacher', 32 => 'Educator', 128 => 'Administrator'];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'deleted' => UserDeleted::class, // e-mail scrambling for further registration
    ];

    /**
     * Custom constructor
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        //
    }

    public static function findByEmail($email)
    {
        return self::withTrashed()->where('email', $email)->first();
    }

    /**
     * whereRaw for a «case sensitive» search
     * @param type $uid
     * @return User
     */
    public static function findByUid($uid) {
        return self::whereRaw("BINARY `uid`= ?", [$uid])->first();
    }

    public function tabRoles()
    {
        return $this->bitroles;
    }
    
    public function hasRole($flag)
    {
        return (($this->role & $flag) == $flag);
    }
    
    public function hasNotRole($flag)
    {
        return (($this->role & $flag) != $flag);
    }
    
    public function setRole($flag, $value)
    {
        if ($value) {
            $this->role |= $flag;
        } else {
            $this->role &= ~$flag;
        }
    }
    
    public function getRolesAttribute()
    {
        $roles = [];
        foreach ($this->bitroles as $bit => $role) {
            if ($this->hasRole($bit)) {
                $roles[] = $role;
            }
        }
        return $roles;
    }

    public function getAdminAttribute()
    {
        return $this->hasRole(128);
    }

    public function setAdminAttribute($value)
    {
        $this->setRole(128, $value);
    }

    /**
     * UpdateUserProfileInformation updates username instead
     * of email, to prevent conflicts with notorix-exim plugin.
     * This mutator keeps email field in sync.
     */
    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = $value;
        $this->attributes['email'] = $value;
    }

    // GROUPES

    public function groupes()
    {
        return $this->belongsToMany(Groupe::class);
    }
    
    /**
     * isMemberOf
     * Returns true is user is member of the group
     *
     * @param  mixed $groupeid = int(id) OR string(nom)
     * @return bool
     */
    public function isMemberOf($groupeid)
    {
        if (!is_int($groupeid)) {
            $groupe = Groupe::where('nom', '=', $groupeid)->first();
            if (!$groupe) {return false;}
            $groupeid = $groupe->id;
        }
        return GroupeUser::where([['user_id', '=', $this->id], ['groupe_id', '=', $groupeid]])->exists();
    }

    /**
     * French alias for backward compatibility
     */
    public function estMembreDe($groupeid)
    {
        return $this->isMemberOf($groupeid);
    }

    // CLASSES

    /**
    * Returns the classes linked to this user (teacher/educator)
    * or sync classes with optional array
    * @param type $checkedClassesIds array of classes.id
    * @return Classe
    */
    public function classes($checkedClassesIds = null)
    {
        if (isset($checkedClassesIds)) {
            $this->belongsToMany('App\Models\Classe')->sync($checkedClassesIds);
            $this->refresh();
        }
        return $this->belongsToMany('App\Models\Classe');
    }
    
    /**
    * In which classes are the kids of this parent user?
    * @return array de classe_id
    */
    public function kidsClasses()
    {
        return $this->kids->pluck('classe_id')->unique()->values()->toArray();
    }
    // French backward compatibility
    public function leursClasses()
    {
        return $this->kidsClasses();
    }
    
    /**
    * Who are the kids of this parent user?
    * @return Eleve
    */
    public function kids()
    {
        return $this->belongsToMany('App\Models\Eleve')->withTrashed();
    }
    // French backward compatibility
    public function enfants()
    {
        return $this->kids();
    }

    /**
    * This user is a student (Eleve)
    * @return Eleve
    */
    public function elu()
    {
        return $this->belongsTo('App\Models\Eleve', 'elu');
    }

    /**
    * Who are the students of this teacher user?
    * @return array eleves.id
    */
    public function students()
    {
        if ($this->role & 128) {
            return Eleve::get(['id'])->pluck('id')->toArray();
        }
        $sql = "SELECT eleves.id FROM eleves JOIN classe_user ON classe_user.classe_id = eleves.classe_id WHERE eleves.deleted_at IS NULL AND classe_user.user_id = " . $this->id;
        $res = DB::select($sql);
        foreach ($res as $objet) {
            $ids[] = $objet->id;
        }
        return isset($ids) ? $ids : null;
    }
    // French backward compatibility
    public function eleves()
    {
        return $this->students();
    }

    /**
    * Eleve parenting with this user
    * @param int $eleveid
    * @param int $lien "resp"
    */
    public function isParentOf($eleveid, $lien = 'resp')
    {
        $now = date('Y-m-d H:i:s');
        $this->kids()->detach($eleveid); // Prevents doubles
        $this->kids()->attach($eleveid, ['lien' => $lien, 'created_at' => $now, 'updated_at' => $now]);
        $this->refresh();
        if (!$this->hasRole(4)) {
            $this->setRole(4, 1);
            $this->save();
        }
    }
    // French backward compatibility
    public function estResponsableDe($eleveid, $lien = 'resp')
    {
        return $this->isParentOf($eleveid, $lien);
    }
    
    /**
    * Removes parenting between this user and eleve.id
    * @param int $eleveid
    */
    public function isNotParentOf($eleveid)
    {
        $this->kids()->detach($eleveid);
        $this->refresh();
        if ($this->kids->count() == 0) {
            $this->setRole(4, 0);
            $this->save();
        }
    }
    // French backward compatibility
    public function nEstPlusResponsableDe($eleveid)
    {
        return $this->isNotParentOf($eleveid);
    }
    
    /**
     * memoMerge
     * 
     * merge (add or replace) any array to memo
     * 
     * @param  array $value
     * @return void
     */
    public function memoMerge(array $value)
    {
        $memo = $this->memo;
        foreach ($value as $key => $v) {
            if (is_array($v)) {
                $base = $memo[$key] ?? [];
                $mergedval = array_unique(array_merge($base, $v));
            } else {
                $mergedval = $v;
            }
            $memo[$key] = $mergedval;
            $this->memo = $memo;
        }
        return $this->memo;
    }
    
    public static function preRegister($preuser)
    {
        extract($preuser, EXTR_SKIP);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["error" => __('validation.email', ['attribute' => $email])];
        }
        $groupeid = isset($groupe) ? Groupe::where('nom', $groupe)->first()->id : null;
        $memomerge = $memo ?? null;
        $existuser = User::where('email', $email)->first();
        if ($existuser) {
            if (isset($memomerge)) {
                $existuser->memoMerge($memomerge);
                $existuser->save();
            }
            if (!$existuser->email_verified_at) {
                Mail::to($existuser)->send(new InvitationPreinscription($existuser));
            }
            if(isset($groupeid) && !$existuser->estMembreDe($groupeid)) {
                $existuser->groupes()->attach($groupeid);
            }
            return ["success" => __("User :name updated", ['name' => $existuser->name])];
        } else {
            // Pre-user creation
            $user = new User();
            $uid = 'u'.config('perso.fase_ecole').UUID::uid8();
            $user->uid = $uid;
            $user->prenom = $prenom;
            $user->nom = $nom;
            $user->name = "$prenom $nom";
            $user->sexe = $sexe;
            $user->email = $email;
            $user->username = $email;
            $user->statut = null;
            $user->role = $role ?? 0;
            $user->solde = 0;
            $user->ou = env('OU', 'thesix');
            $user->password = Hash::make('change_me');
            $user->memo = $memomerge;
            $user->save();
            Mail::to($user)->send(new InvitationPreinscription($user));
            $user->groupes()->attach($groupeid);
            return ["success" => __("User account preregistered and invited")];
        }
    }
}
