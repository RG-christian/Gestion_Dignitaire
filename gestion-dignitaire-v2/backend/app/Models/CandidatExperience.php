<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Modèle CandidatExperience - Expériences professionnelles déclarées par un candidat avant validation
 */
class CandidatExperience extends Model
{
    use HasFactory;

    protected $table = 'candidat_experiences';

    protected $fillable = [
        'candidat_id',
        'intitule',
        'structure_id',
        'date_debut',
        'date_fin',
        'justificatif_path',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(Candidat::class);
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($experience) {
            if ($experience->justificatif_path && Storage::disk('public')->exists($experience->justificatif_path)) {
                Storage::disk('public')->delete($experience->justificatif_path);
            }
        });
    }
}
