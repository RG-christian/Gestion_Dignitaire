<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle Conjoint - Gestion des conjoints des dignitaires
 * 
 * Recommandation Marcel : Inclure le lien avec le statut militaire/dignitaire
 */
class Conjoint extends Model
{
    use HasFactory;

    protected $table = 'conjoints';

    protected $fillable = [
        'dignitaire_id',
        'nom',
        'prenom',
        'date_naissance',
        'genre',
        'lieu_naissance_id',
        'nationalite_id',
        'profession',
        'employeur',
        'date_mariage',
        'lieu_mariage',
        'statut',
        'date_fin_union',
        'est_militaire',
        'est_dignitaire',
        'grade_militaire',
        'fonction_dignitaire',
        'telephone',
        'email',
        'adresse',
        'photo',
        'acte_mariage_path',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_mariage' => 'date',
        'date_fin_union' => 'date',
        'est_militaire' => 'boolean',
        'est_dignitaire' => 'boolean',
    ];

    protected $appends = ['nom_complet', 'age', 'duree_mariage', 'status_badge'];

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
     * Accesseur pour la durée du mariage
     */
    public function getDureeMariageAttribute(): ?string
    {
        if (!$this->date_mariage) {
            return null;
        }

        $dateFin = $this->statut === 'actif' ? now() : ($this->date_fin_union ?? now());
        $annees = $this->date_mariage->diffInYears($dateFin);
        $mois = $this->date_mariage->copy()->addYears($annees)->diffInMonths($dateFin);

        if ($annees === 0) {
            return "{$mois} mois";
        }

        return $mois > 0 ? "{$annees} ans et {$mois} mois" : "{$annees} ans";
    }

    /**
     * Accesseur pour le badge de statut (UI)
     */
    public function getStatusBadgeAttribute(): array
    {
        return match($this->statut) {
            'actif' => ['text' => 'Actif', 'color' => 'green'],
            'divorce' => ['text' => 'Divorcé', 'color' => 'orange'],
            'veuf' => ['text' => 'Veuf', 'color' => 'gray'],
            'separe' => ['text' => 'Séparé', 'color' => 'yellow'],
            default => ['text' => 'Inconnu', 'color' => 'gray'],
        };
    }

    /**
     * Relations
     */
    public function dignitaire(): BelongsTo
    {
        return $this->belongsTo(Dignitaire::class);
    }

    public function lieuNaissance(): BelongsTo
    {
        return $this->belongsTo(Ville::class, 'lieu_naissance_id');
    }

    public function nationalite(): BelongsTo
    {
        return $this->belongsTo(Pays::class, 'nationalite_id');
    }

    /**
     * Scopes
     */
    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeMilitaires($query)
    {
        return $query->where('est_militaire', true);
    }

    public function scopeDignitaires($query)
    {
        return $query->where('est_dignitaire', true);
    }

    public function scopeByStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }

    public function scopeByGenre($query, string $genre)
    {
        return $query->where('genre', $genre);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nom', 'like', "%{$search}%")
              ->orWhere('prenom', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('telephone', 'like', "%{$search}%");
        });
    }

    /**
     * Méthodes métier
     */

    /**
     * Vérifier si le conjoint est toujours marié
     */
    public function estMarie(): bool
    {
        return $this->statut === 'actif';
    }

    /**
     * Vérifier si le conjoint a un statut spécial
     */
    public function aStatutSpecial(): bool
    {
        return $this->est_militaire || $this->est_dignitaire;
    }

    /**
     * Obtenir le statut spécial (militaire ou dignitaire)
     */
    public function getStatutSpecial(): ?string
    {
        if ($this->est_militaire && $this->est_dignitaire) {
            return "Militaire & Dignitaire";
        }
        if ($this->est_militaire) {
            return "Militaire" . ($this->grade_militaire ? " ({$this->grade_militaire})" : "");
        }
        if ($this->est_dignitaire) {
            return "Dignitaire" . ($this->fonction_dignitaire ? " ({$this->fonction_dignitaire})" : "");
        }
        return null;
    }

    /**
     * Marquer le mariage comme terminé
     */
    public function terminerUnion(string $nouveauStatut, ?\DateTime $dateFin = null): void
    {
        $this->update([
            'statut' => $nouveauStatut,
            'date_fin_union' => $dateFin ?? now(),
        ]);
    }
}
