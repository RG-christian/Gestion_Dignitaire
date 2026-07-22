<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Séjour d'un dignitaire dans un pays (ambassade, mission, consulat...),
 * distinct du poste occupé (fonction/entité) et de sa nationalité
 * d'origine (dignitaire.nationalite_id).
 */
class Affectation extends Model
{
    protected $fillable = [
        'dignitaire_id',
        'poste_id',
        'pays_id',
        'ville_id',
        'date_debut',
        'date_fin',
        'type_affectation',
        'nature',
        'statut',
    ];

    public function poste(): BelongsTo
    {
        return $this->belongsTo(Poste::class);
    }

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function dignitaire(): BelongsTo
    {
        return $this->belongsTo(Dignitaire::class);
    }

    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class);
    }

    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class);
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', 'en_cours');
    }
}
