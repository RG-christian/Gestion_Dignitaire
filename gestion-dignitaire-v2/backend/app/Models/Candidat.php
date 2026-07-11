<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modèle Candidat - Préinscription avant validation
 *
 * Ce modèle étend Authenticatable pour permettre aux candidats de se connecter
 * et consulter le statut de leur candidature.
 */
class Candidat extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'candidats';

    protected $fillable = [
        'statut',
        'motif_refus',
        'nom',
        'prenom',
        'date_naissance',
        'genre',
        'nip',
        'matricule',
        'pays_naissance_id',
        'lieu_naissance_id',
        'ville_naissance_custom',
        'etat_civil',
        'photo',
        'cv_path',
        'lettre_motivation_path',
        'email',
        'telephone',
        'adresse',
        'ville_residence_id',
        'password',
        'valide_par',
        'date_validation',
        'dignitaire_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_candidature' => 'datetime',
        'date_validation' => 'datetime',
    ];

    protected $appends = ['nom_complet', 'age', 'status_badge'];

    /**
     * Accesseur pour nom complet
     */
    public function getNomCompletAttribute(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }

    /**
     * Accesseur pour l'âge
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_naissance ? now()->diffInYears($this->date_naissance) : null;
    }

    /**
     * Accesseur pour le badge de statut (UI)
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->statut) {
            'en_attente' => ['text' => 'En attente', 'color' => 'yellow'],
            'valide' => ['text' => 'Validé', 'color' => 'green'],
            'refuse' => ['text' => 'Refusé', 'color' => 'red'],
            default => ['text' => 'Inconnu', 'color' => 'gray'],
        };
    }

    /**
     * Relations
     */
    public function paysNaissance(): BelongsTo
    {
        return $this->belongsTo(Pays::class, 'pays_naissance_id');
    }

    public function lieuNaissance(): BelongsTo
    {
        return $this->belongsTo(Ville::class, 'lieu_naissance_id');
    }

    public function villeResidence(): BelongsTo
    {
        return $this->belongsTo(Ville::class, 'ville_residence_id');
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function dignitaire(): BelongsTo
    {
        return $this->belongsTo(Dignitaire::class, 'dignitaire_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CandidatDocument::class);
    }

    public function diplomes(): HasMany
    {
        return $this->hasMany(CandidatDiplome::class);
    }

    public function langues(): HasMany
    {
        return $this->hasMany(CandidatLangue::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(CandidatExperience::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CandidatMessage::class)->latest();
    }

    /**
     * Scopes pour filtres
     */
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeValide($query)
    {
        return $query->where('statut', 'valide');
    }

    public function scopeRefuse($query)
    {
        return $query->where('statut', 'refuse');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
              ->orWhere('prenom', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('matricule', 'like', "%{$search}%")
              ->orWhere('nip', 'like', "%{$search}%");
        });
    }

    public function scopeByGenre($query, $genre)
    {
        return $query->where('genre', $genre);
    }

    /**
     * Méthodes métier
     */

    /**
     * Vérifier si le candidat peut se connecter
     */
    public function peutSeConnecter(): bool
    {
        return in_array($this->statut, ['en_attente', 'valide']);
    }

    /**
     * Vérifier si la candidature est modifiable
     */
    public function estModifiable(): bool
    {
        return $this->statut === 'en_attente';
    }

    /**
     * Marquer comme validé
     */
    public function valider(int $adminId, int $dignitaireId): void
    {
        $this->update([
            'statut' => 'valide',
            'valide_par' => $adminId,
            'date_validation' => now(),
            'dignitaire_id' => $dignitaireId,
        ]);
    }

    /**
     * Marquer comme refusé
     */
    public function refuser(int $adminId, string $motif): void
    {
        $this->update([
            'statut' => 'refuse',
            'valide_par' => $adminId,
            'date_validation' => now(),
            'motif_refus' => $motif,
        ]);
    }
}
