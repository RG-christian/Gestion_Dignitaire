<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidatMessage extends Model
{
    protected $fillable = [
        'candidat_id',
        'user_id',
        'user_label',
        'type',
        'contenu',
        'lu',
        'lu_le',
    ];

    protected $casts = [
        'lu' => 'boolean',
        'lu_le' => 'datetime',
    ];

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(Candidat::class);
    }

    public function scopeNonLus($query)
    {
        return $query->where('lu', false);
    }

    public function marquerCommeLu(): void
    {
        if (!$this->lu) {
            $this->update(['lu' => true, 'lu_le' => now()]);
        }
    }
}
