# Mapping exact des tables et colonnes

## Tables au SINGULIER (pas de 's')
- `dignitaire` (pas dignitaires)
- `decoration` (pas decorations) - **ATTENTION: clé primaire = `deco_id`**
- `ville` (pas villes)
- `pays` (singulier et pluriel identiques)
- `region` (pas regions)
- `entite` (pas entites)
- `diplome` (pas diplomes)
- `enfants` (PLURIEL dans la base!)
- `experiences` (PLURIEL dans la base!)
- `nominations` (PLURIEL dans la base!)
- `langue` (pas langues) - table de référence
- `langues` (PLURIEL dans la base!) - table pivot dignitaire_langue
- `structure` (pas structures)
- `etablissement` (pas etablissements)
- `domaine` (pas domaines)
- `postes` (PLURIEL dans la base!)

## Colonnes spéciales

### Table `decoration`
- Clé primaire: `deco_id` (pas `id`)
- Colonnes préfixées: `deco_nom`, `deco_type`, `deco_niveau`, etc.

### Table `dignitaire`
- `casierJud` (camelCase, pas snake_case)
- `certificatsMed` (camelCase, pas snake_case)

### Table `langues` (pivot)
- `dignitaire_id`
- `langue_id`
- `niveau`

## Relations importantes

### Dignitaire
- `lieu_naissance` → `ville.id`
- `postes` → hasMany Poste
- `diplomes` → hasMany Diplome (table `diplome`)
- `enfants` → hasMany Enfant (table `enfants`)
- `experiences` → hasMany Experience (table `experiences`)
- `nominations` → hasMany Nomination (table `nominations`)
- `languesParlees` → hasMany LangueParlee (table `langues`)
- `decorations` → belongsToMany Decoration via `decoration_dignitaire`

### Poste
- `dignitaire_id` → Dignitaire
- `entite_id` → Entite
- `ville_id` → Ville

### Decoration (ATTENTION!)
- Clé primaire: `deco_id`
- Pivot: `decoration_dignitaire` avec `decoration_id` → `decoration.deco_id`

## Timestamps
**AUCUNE table n'a de timestamps** (`created_at`, `updated_at`)
→ Tous les models doivent avoir `public $timestamps = false;`
