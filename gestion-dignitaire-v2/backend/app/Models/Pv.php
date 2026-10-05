<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pv extends Model
{
    use HasFactory;

    protected $table = 'pv';

    public $timestamps = false;

    protected $fillable = [
        'numero',
        'date',
        'description',
        'fichier_path',
        'statut',
        'archive_le',
    ];

    protected $casts = [
        'date' => 'date',
        'archive_le' => 'datetime',
    ];

    public function nominations(): HasMany
    {
        return $this->hasMany(Nomination::class);
    }
}
