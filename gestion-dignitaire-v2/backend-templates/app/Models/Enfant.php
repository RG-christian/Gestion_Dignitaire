<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enfant extends Model
{
    use HasFactory;

    protected $table = 'enfants';

    protected $fillable = [
        'dignitaire_id',
        'nom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'genre',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    protected $appends = ['nom_complet', 'age'];

    public function getNomCompletAttribute(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_naissance ? now()->diffInYears($this->date_naissance) : null;
    }

    public function dignitaire(): BelongsTo
    {
        return $this->belongsTo(Dignitaire::class);
    }

    public function lieuNaissance(): BelongsTo
    {
        return $this->belongsTo(Ville::class, 'lieu_naissance');
    }
}
