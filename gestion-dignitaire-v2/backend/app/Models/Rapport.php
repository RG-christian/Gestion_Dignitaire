<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rapport extends Model
{
    protected $fillable = [
        'type',
        'periode_debut',
        'periode_fin',
        'nom_fichier',
        'chemin_fichier',
        'taille_octets',
        'genere_le',
    ];

    protected $casts = [
        'periode_debut' => 'date',
        'periode_fin' => 'date',
        'genere_le' => 'datetime',
    ];
}
