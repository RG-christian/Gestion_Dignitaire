<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Decoration extends Model
{
    use HasFactory;

    protected $table = 'decoration';
    protected $primaryKey = 'deco_id';
    public $timestamps = false;

    protected $fillable = [
        'deco_nom',
        'deco_type',
        'deco_niveau',
        'deco_grade',
        'deco_date_obtention',
        'deco_autorite',
        'deco_motif',
        'deco_description',
        'deco_fichierAttestation',
    ];

    protected $casts = [
        'deco_date_obtention' => 'date',
    ];

    // Forcer l'inclusion des accesseurs dans la sérialisation JSON
    protected $appends = [
        'id',
        'nom',
        'type',
        'niveau',
        'grade',
        'date_obtention',
        'autorite',
        'motif',
        'description',
        'fichier_attestation'
    ];

    // Accesseurs pour compatibilité avec l'API
    public function getIdAttribute()
    {
        return $this->deco_id;
    }
    public function getNomAttribute()
    {
        return $this->deco_nom;
    }

    public function getTypeAttribute()
    {
        return $this->deco_type;
    }

    public function getNiveauAttribute()
    {
        return $this->deco_niveau;
    }

    public function getGradeAttribute()
    {
        return $this->deco_grade;
    }

    public function getDateObtentionAttribute()
    {
        return $this->deco_date_obtention;
    }

    public function getAutoriteAttribute()
    {
        return $this->deco_autorite;
    }

    public function getMotifAttribute()
    {
        return $this->deco_motif;
    }

    public function getDescriptionAttribute()
    {
        return $this->deco_description;
    }

    public function getFichierAttestationAttribute()
    {
        return $this->deco_fichierAttestation;
    }

    public function dignitaires(): BelongsToMany
    {
        return $this->belongsToMany(Dignitaire::class, 'decoration_dignitaire', 'decoration_id', 'dignitaire_id')
            ->withPivot('date_attribution');
    }
}
