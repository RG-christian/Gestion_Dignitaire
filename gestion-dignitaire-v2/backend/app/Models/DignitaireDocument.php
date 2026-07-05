<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DignitaireDocument extends Model
{
    use HasFactory;

    protected $table = 'dignitaire_documents';

    protected $fillable = [
        'dignitaire_id',
        'type_document',
        'nom_document',
        'numero_document',
        'date_emission',
        'date_expiration',
        'organisme_emetteur',
        'description',
        'nom_fichier',
        'chemin_fichier',
        'taille_fichier',
        'extension',
    ];

    protected $casts = [
        'date_emission' => 'date',
        'date_expiration' => 'date',
        'taille_fichier' => 'integer',
    ];

    protected $appends = ['taille_lisible', 'url_complete', 'icone_type', 'statut', 'expire_bientot'];

    public function getTailleLisibleAttribute(): string
    {
        if (!$this->taille_fichier) {
            return 'N/A';
        }

        $units = ['o', 'Ko', 'Mo', 'Go'];
        $taille = $this->taille_fichier;
        $i = 0;
        while ($taille >= 1024 && $i < count($units) - 1) {
            $taille /= 1024;
            $i++;
        }

        return round($taille, 2) . ' ' . $units[$i];
    }

    public function getUrlCompleteAttribute(): string
    {
        return Storage::url($this->chemin_fichier);
    }

    public function getIconeTypeAttribute(): array
    {
        return match ($this->type_document) {
            'diplome' => ['icon' => '🎓', 'color' => 'blue'],
            'passeport' => ['icon' => '🛂', 'color' => 'indigo'],
            'casier' => ['icon' => '🔒', 'color' => 'red'],
            'medical' => ['icon' => '⚕️', 'color' => 'purple'],
            'attestation' => ['icon' => '📜', 'color' => 'yellow'],
            default => ['icon' => '📎', 'color' => 'gray'],
        };
    }

    /**
     * 'expire' si la date d'expiration est passée, 'valide' sinon
     * (un document sans date d'expiration, comme un diplôme, est toujours valide).
     */
    public function getStatutAttribute(): string
    {
        if (!$this->date_expiration) {
            return 'valide';
        }

        return $this->date_expiration->isPast() ? 'expire' : 'valide';
    }

    /**
     * Alerte "expire bientôt" : dans les 30 prochains jours, pas encore expiré.
     */
    public function getExpireBientotAttribute(): bool
    {
        if (!$this->date_expiration || $this->date_expiration->isPast()) {
            return false;
        }

        return $this->date_expiration->lte(now()->addDays(30));
    }

    public function dignitaire(): BelongsTo
    {
        return $this->belongsTo(Dignitaire::class);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type_document', $type);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (DignitaireDocument $document) {
            if (Storage::disk('public')->exists($document->chemin_fichier)) {
                Storage::disk('public')->delete($document->chemin_fichier);
            }
        });
    }
}
