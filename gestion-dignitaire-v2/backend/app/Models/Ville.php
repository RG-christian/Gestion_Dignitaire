<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ville extends Model
{
    use HasFactory;

    protected $table = 'ville'; // Table au singulier
    public $timestamps = false;

    protected $fillable = [
        'nom',
        'pays_id',
    ];

    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class);
    }
}
