<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Etablissement extends Model
{
    use HasFactory;

    protected $table = 'etablissement'; // Table au singulier
    public $timestamps = false;

    protected $fillable = [
        'nom',
        'type',
        'ville_id',
    ];

    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class);
    }
}
