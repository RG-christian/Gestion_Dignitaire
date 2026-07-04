<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle CandidatLangue - Langues parlées déclarées par un candidat avant validation
 */
class CandidatLangue extends Model
{
    use HasFactory;

    protected $table = 'candidat_langues';

    protected $fillable = [
        'candidat_id',
        'langue_id',
        'niveau',
    ];

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(Candidat::class);
    }

    public function langue(): BelongsTo
    {
        return $this->belongsTo(Langue::class);
    }
}
