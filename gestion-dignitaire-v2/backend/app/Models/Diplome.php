<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Diplome extends Model
{
    use HasFactory;

    protected $table = 'diplome'; // Table au singulier
    public $timestamps = false;

    protected $fillable = [
        'dignitaire_id',
        'intitule',
        'etablissement_id',
        'annee',
        'ville_id',
        'domaine_id',
        'code',
        'type',
        'justificatif_path',
    ];

    protected $appends = ['justificatif_url'];

    public function getJustificatifUrlAttribute(): ?string
    {
        return $this->justificatif_path ? Storage::url($this->justificatif_path) : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Diplome $diplome) {
            if ($diplome->justificatif_path && Storage::disk('public')->exists($diplome->justificatif_path)) {
                Storage::disk('public')->delete($diplome->justificatif_path);
            }
        });
    }

    public function dignitaire(): BelongsTo
    {
        return $this->belongsTo(Dignitaire::class);
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
}
