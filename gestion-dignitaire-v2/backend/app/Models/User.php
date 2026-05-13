<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false; // Pas de updated_at dans votre table

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'nom_complet',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relations à charger automatiquement
     *
     * @var array
     */
    protected $with = ['fonctions', 'sousfonctions', 'role'];

    /**
     * Ajouter role_name comme attribut accessible
     *
     * @var array
     */
    protected $appends = ['role_name'];

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'username'; // Utiliser username au lieu de email
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Relation avec les fonctions de l'utilisateur
     */
    public function fonctions()
    {
        return $this->belongsToMany(
            \App\Models\Fonction::class,
            'user_fonctions',
            'user_id',
            'fonction_id'
        )->orderBy('fonctions.fonction_name');
    }

    /**
     * Relation avec les sous-fonctions de l'utilisateur
     */
    public function sousfonctions()
    {
        return $this->belongsToMany(
            \App\Models\Sousfonction::class,
            'user_sousfonctions',
            'user_id',
            'sousfonction_id'
        )->orderBy('sousfonctions.sousfonction_name');
    }

    /**
     * Relation avec le rôle
     */
    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id');
    }

    /**
     * Accesseur pour role_name
     */
    public function getRoleNameAttribute()
    {
        return $this->role ? $this->role->role_name : null;
    }
}
