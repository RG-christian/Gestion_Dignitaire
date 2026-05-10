<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entite extends Model
{
    use HasFactory;

    protected $table = 'entite'; // Table au singulier
    public $timestamps = false;

    protected $fillable = [
        'nom',
        'type',
        'id_sup',
        'description',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Entite::class, 'id_sup');
    }

    public function enfants(): HasMany
    {
        return $this->hasMany(Entite::class, 'id_sup');
    }

    public function postes(): HasMany
    {
        return $this->hasMany(Poste::class);
    }

    public function nominations(): HasMany
    {
        return $this->hasMany(Nomination::class);
    }
}
