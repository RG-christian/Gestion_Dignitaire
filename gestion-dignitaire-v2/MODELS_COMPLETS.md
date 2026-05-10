# Models Laravel - À créer dans app/Models/

Voici tous les models à créer. Chaque fichier doit être placé dans `backend/app/Models/`

## Nomination.php
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nomination extends Model
{
    protected $fillable = [
        'dignitaire_id', 'entite_id', 'poste_id', 'pv_id',
        'date_debut', 'date_fin', 'fonction'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function dignitaire() {
        return $this->belongsTo(Dignitaire::class);
    }

    public function entite() {
        return $this->belongsTo(Entite::class);
    }

    public function poste() {
        return $this->belongsTo(Poste::class);
    }

    public function pv() {
        return $this->belongsTo(Pv::class);
    }
}
```

## Decoration.php
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Decoration extends Model
{
    protected $fillable = [
        'nom', 'type', 'niveau', 'grade', 'date_obtention',
        'autorite', 'motif', 'description', 'fichier_attestation'
    ];

    protected $casts = ['date_obtention' => 'date'];

    public function dignitaires() {
        return $this->belongsToMany(Dignitaire::class, 'decoration_dignitaire')
            ->withPivot('date_attribution')
            ->withTimestamps();
    }
}
```

## Ville.php, Pays.php, Entite.php, Poste.php, etc.
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ville extends Model {
    protected $fillable = ['nom', 'pays_id'];
    public function pays() { return $this->belongsTo(Pays::class); }
}

class Pays extends Model {
    protected $fillable = ['nom', 'code_iso', 'indicatif', 'continent', 'region_id'];
    public function region() { return $this->belongsTo(Region::class); }
    public function villes() { return $this->hasMany(Ville::class); }
}

class Entite extends Model {
    protected $fillable = ['nom', 'type', 'id_sup', 'description'];
    public function parent() { return $this->belongsTo(Entite::class, 'id_sup'); }
    public function enfants() { return $this->hasMany(Entite::class, 'id_sup'); }
}

class Poste extends Model {
    protected $fillable = ['dignitaire_id', 'intitule', 'date_debut', 'date_fin', 'entite_id', 'ville_id'];
    protected $casts = ['date_debut' => 'date', 'date_fin' => 'date'];
    public function dignitaire() { return $this->belongsTo(Dignitaire::class); }
    public function entite() { return $this->belongsTo(Entite::class); }
    public function ville() { return $this->belongsTo(Ville::class); }
}

class Diplome extends Model {
    protected $fillable = ['dignitaire_id', 'intitule', 'etablissement_id', 'annee', 'ville_id', 'domaine_id', 'code', 'type'];
    public function dignitaire() { return $this->belongsTo(Dignitaire::class); }
}

class Enfant extends Model {
    protected $fillable = ['dignitaire_id', 'nom', 'prenom', 'date_naissance', 'lieu_naissance', 'genre'];
    protected $casts = ['date_naissance' => 'date'];
    public function dignitaire() { return $this->belongsTo(Dignitaire::class); }
}

class Experience extends Model {
    protected $fillable = ['dignitaire_id', 'intitule', 'date_debut', 'date_fin', 'structure_id'];
    protected $casts = ['date_debut' => 'date', 'date_fin' => 'date'];
    public function dignitaire() { return $this->belongsTo(Dignitaire::class); }
}

class LangueParlee extends Model {
    protected $table = 'langues_parlees';
    protected $fillable = ['dignitaire_id', 'langue_id', 'niveau'];
    public function dignitaire() { return $this->belongsTo(Dignitaire::class); }
    public function langue() { return $this->belongsTo(Langue::class); }
}
```
