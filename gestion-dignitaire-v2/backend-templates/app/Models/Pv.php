<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pv extends Model
{
    use HasFactory;

    protected $table = 'pvs';

    protected $fillable = [
        'numero',
        'date',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function nominations(): HasMany
    {
        return $this->hasMany(Nomination::class);
    }
}
