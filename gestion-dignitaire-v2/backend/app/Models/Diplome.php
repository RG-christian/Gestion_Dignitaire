<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

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
