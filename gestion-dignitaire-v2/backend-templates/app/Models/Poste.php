<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Poste extends Model
{
    use HasFactory;

    protected $fillable = [
        'dignitaire_id',
        'intitule',
        'date_debut',
        'date_fin',
        'entite_id',
        'ville_id',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function dignitaire(): BelongsTo
    {
        return $this->belongsTo(Dignitaire::class);
    }

    public function entite(): BelongsTo
    {
        return $this->belongsTo(Entite::class);
    }

    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class);
    }

    public function scopeActuels($query)
    {
        return $query->whereNull('date_fin')->orWhere('date_fin', '>=', now());
    }
}
