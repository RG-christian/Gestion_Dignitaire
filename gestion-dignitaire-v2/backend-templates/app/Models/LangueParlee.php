<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LangueParlee extends Model
{
    use HasFactory;

    protected $table = 'langues_parlees';

    protected $fillable = [
        'dignitaire_id',
        'langue_id',
        'niveau',
    ];

    public function dignitaire(): BelongsTo
    {
        return $this->belongsTo(Dignitaire::class);
    }

    public function langue(): BelongsTo
    {
        return $this->belongsTo(Langue::class);
    }
}
