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
        'date_prise_fonction',
        'lieu_naissance',
        'nationalite',
        'nationalite_id',
        'genre',
        'etat_civil',
        'photo',
        'adresse',
        'telephone',
        'casier_judiciaire',
        'certificats_medicaux',
        'statut',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_prise_fonction' => 'date',
    ];

    protected $appends = ['nom_complet', 'anciennete_annees', 'date_reference_anciennete'];

    public function getNomCompletAttribute(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }

    /**
     * Date de référence pour l'ancienneté : date_prise_fonction si renseignée,
     * sinon la date de début du plus ancien poste occupé (demande du CR de
     * réunion : ancienneté basée sur la date d'acceptation ou d'affectation).
     */
    public function getDateReferenceAncienneteAttribute(): ?\Illuminate\Support\Carbon
    {
        if ($this->date_prise_fonction) {
            return $this->date_prise_fonction;
        }

        $premierPoste = $this->relationLoaded('postes')
            ? $this->postes->sortBy('date_debut')->first()
            : $this->postes()->orderBy('date_debut')->first();

        return $premierPoste?->date_debut;
    }

    /**
     * Ancienneté en années entières, ou null si aucune date de référence.
     */
    public function getAncienneteAnneesAttribute(): ?int
    {
        $reference = $this->date_reference_anciennete;

        return $reference ? $reference->diffInYears(now()) : null;
    }

    // Relations
    public function lieuNaissance(): BelongsTo
    {
        return $this->belongsTo(Ville::class, 'lieu_naissance');
    }

    /**
     * Nationalité d'origine (pays), distincte des affectations qui suivent
     * les séjours du dignitaire à l'étranger.
     */
    public function nationaliteRef(): BelongsTo
    {
        return $this->belongsTo(Pays::class, 'nationalite_id');
    }

    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class);
    }

    public function diplomes(): HasMany
    {
        return $this->hasMany(Diplome::class);
    }

    public function enfants(): HasMany
    {
        return $this->hasMany(Enfant::class);
    }

    public function conjoints(): HasMany
    {
        return $this->hasMany(Conjoint::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DignitaireDocument::class);
    }

    public function conjointActif(): HasMany
    {
        return $this->hasMany(Conjoint::class)->where('statut', 'actif');
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
        return $this->belongsToMany(Decoration::class, 'decoration_dignitaire', 'dignitaire_id', 'decoration_id')
            ->withPivot('date_attribution');
    }

    public function telephones(): HasMany
    {
        return $this->hasMany(DignitaireTelephone::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(DignitaireEmail::class);
    }

    /**
     * Email à utiliser pour les notifications : celui marqué "principal",
     * sinon le premier disponible, sinon null (le dignitaire n'a aucune
     * adresse enregistrée — les envois doivent alors être ignorés en silence).
     */
    public function emailNotification(): ?string
    {
        $email = $this->emails()->where('principal', true)->first()
            ?? $this->emails()->first();

        return $email?->email;
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

    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }
}
