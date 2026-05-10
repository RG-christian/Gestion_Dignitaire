<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pays extends Model
{
    use HasFactory;

    protected $table = 'pays';
    public $timestamps = false;

    protected $fillable = [
        'nom',
        'code_iso',
        'indicatif',
        'continent',
        'region_id',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function villes(): HasMany
    {
        return $this->hasMany(Ville::class);
    }
}
