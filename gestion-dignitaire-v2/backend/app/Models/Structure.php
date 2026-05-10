<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Structure extends Model
{
    use HasFactory;

    protected $table = 'structure'; // Table au singulier
    public $timestamps = false;

    protected $fillable = [
        'nom',
        'type',
        'adresse',
        'ville_id',
    ];

    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class);
    }
}
