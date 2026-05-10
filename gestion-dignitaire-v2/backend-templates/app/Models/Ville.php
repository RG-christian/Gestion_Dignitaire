<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ville extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'pays_id',
    ];

    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class);
    }
}
