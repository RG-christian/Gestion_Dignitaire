<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DignitaireEmail extends Model
{
    use HasFactory;

    protected $table = 'dignitaire_emails';

    protected $fillable = [
        'dignitaire_id',
        'email',
        'type',
        'principal',
    ];

    protected $casts = [
        'principal' => 'boolean',
    ];

    public function dignitaire(): BelongsTo
    {
        return $this->belongsTo(Dignitaire::class);
    }
}
