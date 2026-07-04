<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Modèle CandidatDiplome - Diplômes déclarés par un candidat avant validation
 */
class CandidatDiplome extends Model
{
    use HasFactory;

    protected $table = 'candidat_diplomes';

    protected $fillable = [
        'candidat_id',
        'intitule',
        'etablissement_id',
        'ville_id',
        'domaine_id',
        'annee',
        'justificatif_path',
    ];

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(Candidat::class);
    }

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class);
    }

    public function domaine(): BelongsTo
    {
        return $this->belongsTo(Domaine::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($diplome) {
            if ($diplome->justificatif_path && Storage::disk('public')->exists($diplome->justificatif_path)) {
                Storage::disk('public')->delete($diplome->justificatif_path);
            }
        });
    }
}
