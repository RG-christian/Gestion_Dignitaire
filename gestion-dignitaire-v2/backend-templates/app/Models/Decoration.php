<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Decoration extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'type',
        'niveau',
        'grade',
        'date_obtention',
        'autorite',
        'motif',
        'description',
        'fichier_attestation',
    ];

    protected $casts = [
        'date_obtention' => 'date',
    ];

    public function dignitaires(): BelongsToMany
    {
        return $this->belongsToMany(Dignitaire::class, 'decoration_dignitaire')
            ->withPivot('date_attribution')
            ->withTimestamps();
    }
}
