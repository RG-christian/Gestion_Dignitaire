<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Experience extends Model
{
    use HasFactory;

    protected $table = 'experiences'; // Table au pluriel
    public $timestamps = false;

    protected $fillable = [
        'dignitaire_id',
        'intitule',
        'date_debut',
        'date_fin',
        'structure_id',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    protected $appends = ['duree_annees'];

    public function getDureeAnneesAttribute(): ?int
    {
        if (!$this->date_debut) return null;
        
        $dateFin = $this->date_fin ?? now();
        return $this->date_debut->diffInYears($dateFin);
    }

    public function dignitaire(): BelongsTo
    {
        return $this->belongsTo(Dignitaire::class);
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class);
    }

    public function scopeEnCours($query)
    {
        return $query->whereNull('date_fin');
    }
}
