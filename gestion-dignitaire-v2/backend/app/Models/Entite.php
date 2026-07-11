<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Entite extends Model
{
    use HasFactory;

    protected $table = 'entite'; // Table au singulier (comme dans la BDD)
    public $timestamps = false;

    protected $fillable = [
        'nom',
        'type',
        'id_sup',
        'entite_rattachement_id',
        'description',
        'logo',
        'telephone',
        'email',
        'site_web',
        'adresse',
    ];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? Storage::url($this->logo) : null;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Entite::class, 'id_sup');
    }

    public function enfants(): HasMany
    {
        return $this->hasMany(Entite::class, 'id_sup');
    }

    /**
     * Entité de rattachement administratif, distincte de l'entité parente
     * (id_sup) qui représente la hiérarchie organique.
     */
    public function rattachement(): BelongsTo
    {
        return $this->belongsTo(Entite::class, 'entite_rattachement_id');
    }

    public function entitesRattachees(): HasMany
    {
        return $this->hasMany(Entite::class, 'entite_rattachement_id');
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
