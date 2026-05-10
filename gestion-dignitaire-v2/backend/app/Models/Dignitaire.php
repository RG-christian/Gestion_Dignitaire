<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Dignitaire extends Model
{
    use HasFactory;

    protected $table = 'dignitaire'; // Utiliser le nom de table existant
    public $timestamps = false; // Désactiver les timestamps car la table n'a pas created_at/updated_at

    protected $fillable = [
        'nip',
        'matricule',
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'genre',
        'etat_civil',
        'photo',
        'adresse',
        'telephone',
        'casier_judiciaire',
        'certificats_medicaux',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    protected $appends = ['nom_complet'];

    public function getNomCompletAttribute(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }

    // Relations
    public function lieuNaissance(): BelongsTo
    {
        return $this->belongsTo(Ville::class, 'lieu_naissance');
    }

    public function diplomes(): HasMany
    {
        return $this->hasMany(Diplome::class);
    }

    public function enfants(): HasMany
    {
        return $this->hasMany(Enfant::class);
    }

    public function languesParlees(): HasMany
    {
        return $this->hasMany(LangueParlee::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    public function postes(): HasMany
    {
        return $this->hasMany(Poste::class);
    }

    public function nominations(): HasMany
    {
        return $this->hasMany(Nomination::class);
    }

    public function decorations(): BelongsToMany
    {
        return $this->belongsToMany(Decoration::class, 'decoration_dignitaire')
            ->withPivot('date_attribution')
            ->withTimestamps();
    }

    // Scopes
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
              ->orWhere('prenom', 'like', "%{$search}%")
              ->orWhere('matricule', 'like', "%{$search}%")
              ->orWhere('nip', 'like', "%{$search}%");
        });
    }

    public function scopeByGenre($query, $genre)
    {
        return $query->where('genre', $genre);
    }

    public function scopeByVille($query, $villeId)
    {
        return $query->where('lieu_naissance', $villeId);
    }
}
