<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Modèle CandidatDocument - Documents joints aux candidatures
 */
class CandidatDocument extends Model
{
    use HasFactory;

    protected $table = 'candidat_documents';

    protected $fillable = [
        'candidat_id',
        'type_document',
        'nom_fichier',
        'chemin_fichier',
        'taille_fichier',
        'extension',
        'description',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'taille_fichier' => 'integer',
    ];

    protected $appends = ['taille_lisible', 'url_complete', 'icone_type'];

    /**
     * Accesseur pour taille lisible (Ko, Mo, etc.)
     */
    public function getTailleLisibleAttribute(): string
    {
        if (!$this->taille_fichier) {
            return 'N/A';
        }

        $units = ['o', 'Ko', 'Mo', 'Go'];
        $taille = $this->taille_fichier;
        $unitIndex = 0;

        while ($taille >= 1024 && $unitIndex < count($units) - 1) {
            $taille /= 1024;
            $unitIndex++;
        }

        return round($taille, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Accesseur pour l'URL complète du fichier
     */
    public function getUrlCompleteAttribute(): string
    {
        return Storage::url($this->chemin_fichier);
    }

    /**
     * Accesseur pour l'icône selon le type de document
     */
    public function getIconeTypeAttribute(): array
    {
        return match($this->type_document) {
            'diplome' => ['icon' => '🎓', 'color' => 'blue'],
            'cv' => ['icon' => '📄', 'color' => 'gray'],
            'lettre' => ['icon' => '✉️', 'color' => 'green'],
            'attestation' => ['icon' => '📜', 'color' => 'yellow'],
            'casier' => ['icon' => '🔒', 'color' => 'red'],
            'medical' => ['icon' => '⚕️', 'color' => 'purple'],
            'passeport' => ['icon' => '🛂', 'color' => 'indigo'],
            default => ['icon' => '📎', 'color' => 'gray'],
        };
    }

    /**
     * Relations
     */
    public function candidat(): BelongsTo
    {
        return $this->belongsTo(Candidat::class);
    }

    /**
     * Scopes
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type_document', $type);
    }

    public function scopePDF($query)
    {
        return $query->where('extension', 'pdf');
    }

    public function scopeImages($query)
    {
        return $query->whereIn('extension', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Méthodes métier
     */

    /**
     * Vérifier si le fichier est une image
     */
    public function estImage(): bool
    {
        return in_array(strtolower($this->extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Vérifier si le fichier est un PDF
     */
    public function estPDF(): bool
    {
        return strtolower($this->extension) === 'pdf';
    }

    /**
     * Supprimer le fichier physique lors de la suppression du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($document) {
            if (Storage::exists($document->chemin_fichier)) {
                Storage::delete($document->chemin_fichier);
            }
        });
    }
}
