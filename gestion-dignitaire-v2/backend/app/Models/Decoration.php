<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Decoration extends Model
{
    use HasFactory;

    protected $table = 'decoration';
    protected $primaryKey = 'deco_id'; // Clé primaire personnalisée
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

    public function dignitaires(): BelongsToMany
    {
        return $this->belongsToMany(Dignitaire::class, 'decoration_dignitaire', 'decoration_id', 'dignitaire_id')
            ->withPivot('date_attribution');
    }
}
