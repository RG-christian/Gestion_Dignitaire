<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nomination extends Model
{
    use HasFactory;

    protected $fillable = [
        'dignitaire_id',
        'entite_id',
        'poste_id',
        'pv_id',
        'date_debut',
        'date_fin',
        'fonction',
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

    public function poste(): BelongsTo
    {
        return $this->belongsTo(Poste::class);
    }

    public function pv(): BelongsTo
    {
        return $this->belongsTo(Pv::class);
    }

    public function scopeActives($query)
    {
        return $query->whereNull('date_fin')->orWhere('date_fin', '>=', now());
    }
}
