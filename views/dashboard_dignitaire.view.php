<?php
require_once __DIR__ . '/../config/database.php';
require 'layout.php';

$pdo = getDatabaseConnection();
if (!isset($dignitaires)) $dignitaires = [];
$search = $search ?? '';
$annee_min = $annee_min ?? '';
$annee_max = $annee_max ?? '';
$ville_id = $ville_id ?? '';
$entite_id = $entite_id ?? '';
$genre = $genre ?? '';

      // Récupère villes et entités
        $villes = $pdo->query("SELECT id, nom FROM ville ORDER BY nom")->fetchAll(\PDO::FETCH_ASSOC);
        $entites = $pdo->query("SELECT id, nom FROM entite ORDER BY nom")->fetchAll(\PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Dignitaires</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">


<?php
$display_mode = isset($_GET['mode']) && $_GET['mode'] === 'liste' ? 'liste' : 'grille';

?>


<!-- Contenu principal décalé -->
<main class="flex-1 ml-64 bg-gray-100 min-h-screen">

    <section class="max-w-7xl mx-auto mt-10 mb-12 px-4">
    <!-- DASHBOARD -->
<div class="mt-8 mb-8 px-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow border p-5 flex items-center">
            <div class="flex-1">
                <div class="text-gray-500 mb-1">Nombre de dignitaires</div>
                <div class="text-2xl font-bold"><?= $totalDignitaires ?? 0 ?></div>
            </div>
            <div class="ml-3">
                <span class="inline-flex bg-green-200 p-3 rounded-full">
                    <!-- Icone utilisateur -->
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                         d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5m6-6a4 4 0 10-8 0 4 4 0 008 0z"/></svg>
                </span>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow border p-5 flex items-center">
            <div class="flex-1">
                <div class="text-gray-500 mb-1">Nombres de postes</div>
                <div class="text-2xl font-bold"><?= $totalPostes ?? 0 ?></div>
            </div>
            <div class="ml-3">
                <span class="inline-flex bg-yellow-100 p-3 rounded-full">
                    <!-- Icone valise -->
                    <svg class="w-7 h-7 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                         d="M6 7V6a2 2 0 012-2h8a2 2 0 012 2v1M6 7h12M6 7v12a2 2 0 002 2h8a2 2 0 002-2V7" /></svg>
                </span>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow border p-5 flex items-center">
            <div class="flex-1">
                <div class="text-gray-500 mb-1">Décorations données</div>
                <div class="text-2xl font-bold"><?= $totalDecorations ?? 0 ?></div>
            </div>
            <div class="ml-3">
                <span class="inline-flex bg-blue-100 p-3 rounded-full">
                    <!-- Icone médaille -->
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                         d="M12 8v8m0 0a4 4 0 110-8 4 4 0 010 8z" /></svg>
                </span>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow border p-5 flex items-center">
            <div class="flex-1">
                <div class="text-gray-500 mb-1">Villes d'affectation</div>
                <div class="text-2xl font-bold"><?= $totalVilles ?? 0 ?></div>
            </div>
            <div class="ml-3">
                <span class="inline-flex bg-red-100 p-3 rounded-full">
                    <!-- Icone ville -->
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                         d="M3 21V3h18v18M3 21v-6h18v6" /></svg>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="flex items-center justify-end mb-4">
    <a href="?mode=grille" class="mr-2 px-3 py-1 rounded <?php echo $display_mode == 'grille' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">
        <!-- Icône grille SVG --> Grille
    </a>
    <a href="?mode=liste" class="px-3 py-1 rounded <?php echo $display_mode == 'liste' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'; ?>">
        <!-- Icône liste SVG --> Liste
    </a>
</div>
        <h2 class="mb-6 text-3xl font-bold">Gestion des Dignitaires</h2>

        <!-- Bouton d'ajout -->
        <button class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded mb-6"
                onclick="openModal('modal-ajout')">
            Ajouter un dignitaire
        </button>
        <?php if ($display_mode == 'grille'): ?>
            <!-- Ici ta vue "cartes dignitaires" avec photos, actions flottantes, etc. (dashboard stylisé) -->
            <!-- Grille dignitaires -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 w-full">
                <?php foreach ($dignitaires as $d): ?>
                    <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center">
                        <div class="relative group w-full flex flex-col items-center">
                            <img src="uploads/photos/<?= $d->getPhoto() ?>" alt="Photo de <?= $d->getPrenom() ?>"
                                 class="w-24 h-24 rounded-full object-cover border-4 border-green-200 shadow mb-2">
                            <h4 class="text-base font-semibold mb-0.5"><?= $d->getPrenom() . ' ' . $d->getNom() ?></h4>
                            <?php if (!empty($d->postes)): ?>
                                <?php foreach ($d->postes as $poste): ?>
                                    <div class="mt-1 mb-1 text-center">
                                        <div class="font-semibold text-green-700 text-sm flex items-center gap-1 justify-center">
                                            <!-- Icon -->
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 7v4m0 0v4m0-4h4m-4 0H8m12 0a2 2 0 002-2V7a2 2 0 00-2-2h-2.586A2 2 0 0015 3.586L13.414 2H10.586L9 3.586A2 2 0 007.586 5H5a2 2 0 00-2 2v2a2 2 0 002 2h16z"/></svg>
                                            <?= htmlspecialchars($poste['intitule']) ?>
                                        </div>
                                        <div class="text-gray-400 text-xs flex items-center gap-1 justify-center">
                                            <!-- Calendar Icon -->
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10m-4 4h4m-4 4h4m1 1a2 2 0 002-2V7a2 2 0 00-2-2h-1.5"/></svg>
                                            <?php
                                            $anneeDebut = isset($poste['date_debut']) ? date('Y', strtotime($poste['date_debut'])) : '—';
                                            $anneeFin = (isset($poste['date_fin']) && $poste['date_fin']) ? date('Y', strtotime($poste['date_fin'])) : "à ce jour";
                                            echo $anneeDebut . " - " . $anneeFin;
                                            ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>


                            <!-- ... (img, nom, poste, etc.) ... -->

                            <!-- Actions flottantes au hover -->
                            <div class="absolute top-3 right-3 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition">
                                <a href="index.php?controller=dignitaire&action=details&id=<?= $d->getId() ?>"
                                   class="bg-sky-100 hover:bg-sky-200 text-sky-800 p-1.5 rounded-full shadow"
                                   title="Voir">
                                    <!-- Eye Icon -->
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-9 0a9 9 0 1118 0 9 9 0 01-18 0z"/></svg>
                                </a>
                                <button onclick="openModal('modal-modif-<?= $d->getId() ?>')"
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-800 p-1.5 rounded-full shadow"
                                        title="Modifier">
                                    <!-- Pencil Icon -->
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536M9 13l6 6M9 13l-3-3a2 2 0 112.828-2.828l3 3z"/></svg>
                                </button>
                                <a href="index.php?controller=dignitaire&action=supprimer&id=<?= $d->getId() ?>"
                                   onclick="return confirm('Supprimer ce dignitaire ?')"
                                   class="bg-red-100 hover:bg-red-200 text-red-700 p-1.5 rounded-full shadow"
                                   title="Supprimer">
                                    <!-- Trash Icon -->
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                </a>
                            </div>
                        </div>

                    </div>

                <?php endforeach; ?>
            </div>
               <footer class="bg-blue-600 text-white p-4 text-center">
                <p>© 2025 Gestion des Dignitaires - République Gabonaise</p>
            </footer>

        <?php else: ?>
            <!-- Ici ta vue filtrée/liste (celle que tu viens d’envoyer, avec formulaire de recherche etc.) -->
            <!-- Reprends ton code, à placer dans ce else -->


            <?php if ($display_mode == 'liste'): ?>
<header class="bg-green-600 text-white p-4 shadow-md rounded-t-lg mb-4">
    <div class="max-w-5xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
        <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">
            <svg class="w-7 h-7 text-white mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                <path d="M8 12l2 2 4-4" stroke="white" stroke-width="2" fill="none"/>
            </svg>
            Gestion des Dignitaires
        </h1>
        <!-- Formulaire de recherche stylisé -->
        <form method="GET" class="flex items-center w-full sm:w-auto gap-0.5">
            <input type="hidden" name="mode" value="liste">
            <input
                type="text"
                name="search"
                value="<?php echo htmlspecialchars($search); ?>"
                placeholder="Rechercher dignitaire..."
                class="border-none rounded-l-lg px-4 py-2 text-gray-700 focus:ring-2 focus:ring-yellow-400 focus:outline-none w-52 sm:w-64"
            >
            <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-green-900 px-4 py-2 rounded-r-lg font-semibold shadow">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                <span class="sr-only">Rechercher</span>
            </button>
        </form>
    </div>
</header>
<?php endif; ?>

            <main class="container mx-auto p-6">
                       <form method="GET" class="bg-white p-4 mb-6 rounded-lg shadow-md">
            <input type="hidden" name="mode" value="liste"> <!-- IMPORTANT : pour rester en liste -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ville</label>
                    <select name="ville_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2">
                        <option value="">Toutes les villes</option>
                        <?php foreach ($villes as $ville): ?>
                            <option value="<?= $ville['id']; ?>" <?= $ville_id == $ville['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($ville['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Entité</label>
                    <select name="entite_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2">
                        <option value="">Toutes les entités</option>
                        <?php foreach ($entites as $entite): ?>
                            <option value="<?= $entite['id']; ?>" <?= $entite_id == $entite['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($entite['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filtrer</button>
                </div>
            </div>
        </form>

                <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <?php if (empty($dignitaires)): ?>
                        <div class="col-span-full text-center py-6 text-gray-500">Aucun dignitaire trouvé.</div>
                    <?php else: ?>
                       <?php foreach ($dignitaires as $dignitaire): ?>
    <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition-all duration-300 relative group">
        <h3 class="text-lg font-bold text-green-800 mb-1">
            <?= htmlspecialchars($dignitaire['prenom'] . ' ' . $dignitaire['nom']); ?>
        </h3>
        <p class="text-sm text-gray-600 mb-1">
            <span class="font-medium">Lieu de naissance :</span>
            <?= htmlspecialchars($dignitaire['lieu_naissance'] ?? 'N/A'); ?>
        </p>
        <p class="text-sm text-gray-600 mb-1">
            <span class="font-medium">Ville d'affectation :</span>
            <?= htmlspecialchars($dignitaire['ville_poste'] ?? 'Aucune'); ?>
        </p>
        <p class="text-sm text-gray-600 mb-1">
            <span class="font-medium">Poste actuel :</span>
            <?= htmlspecialchars($dignitaire['poste_actuel'] ?? 'Aucun poste actuel'); ?>
        </p>
        <p class="text-sm text-gray-600 mb-1">
            <span class="font-medium">Entité :</span>
            <?= htmlspecialchars($dignitaire['nom_entite'] ?? 'Aucune entité actuelle'); ?>
        </p>

        <!-- Actions flottantes sur hover, même principe que la grille -->
        <div class="absolute top-3 right-3 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition">
            <a href="index.php?controller=dignitaire&action=details&id=<?= $dignitaire['id'] ?>"
               class="bg-sky-100 hover:bg-sky-200 text-sky-800 p-1.5 rounded-full shadow"
               title="Voir">
                <!-- Eye Icon -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-9 0a9 9 0 1118 0 9 9 0 01-18 0z"/>
                </svg>
            </a>
            <a href="index.php?controller=dignitaire&action=formulaireModification&id=<?= $dignitaire['id'] ?>"
               class="bg-blue-100 hover:bg-blue-200 text-blue-800 p-1.5 rounded-full shadow"
               title="Modifier">
                <!-- Pencil Icon -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15.232 5.232l3.536 3.536M9 13l6 6M9 13l-3-3a2 2 0 112.828-2.828l3 3z"/>
                </svg>
            </a>
            <a href="index.php?controller=dignitaire&action=supprimer&id=<?= $dignitaire['id'] ?>"
               onclick="return confirm('Supprimer ce dignitaire ?')"
               class="bg-red-100 hover:bg-red-200 text-red-700 p-1.5 rounded-full shadow"
               title="Supprimer">
                <!-- Trash Icon -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                </svg>
            </a>
        </div>
    </div>
<?php endforeach; ?>

                    <?php endif; ?>

                </div>
            </main>

            <footer class="bg-blue-600 text-white p-4 text-center">
                <p>© 2025 Gestion des Dignitaires - République Gabonaise</p>
            </footer>
            </body>
            </html>

        <?php endif; ?>



<!-- Modal Ajout -->
<div class="fixed z-50 inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden" id="modal-ajout">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
        <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 text-2xl" onclick="closeModal('modal-ajout')">&times;</button>
        <h4 class="text-lg font-bold mb-4">Ajouter un dignitaire</h4>
        <form method="post" action="index.php?controller=dignitaire&action=ajouter" class="flex flex-col gap-2">
            <input class="border rounded px-2 py-1" name="nip" placeholder="NIP" required>
            <input class="border rounded px-2 py-1" name="matricule" placeholder="Matricule" required>
            <input class="border rounded px-2 py-1" name="nom" placeholder="Nom" required>
            <input class="border rounded px-2 py-1" name="prenom" placeholder="Prénom" required>
            <input class="border rounded px-2 py-1" name="date_naissance" type="date" required>
            <input class="border rounded px-2 py-1" name="lieu_naissance" placeholder="ID ville naissance" required>
            <input class="border rounded px-2 py-1" name="genre" placeholder="Genre" required>
            <input class="border rounded px-2 py-1" name="etat_civil" placeholder="Etat civil" required>
            <input class="border rounded px-2 py-1" name="photo" placeholder="Nom du fichier photo">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">Enregistrer</button>
        </form>
    </div>
</div>

<!-- Modals de modification -->
<?php foreach ($dignitaires as $d): ?>
    <div class="fixed z-50 inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden" id="modal-modif-<?= $d->getId() ?>">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
            <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 text-2xl" onclick="closeModal('modal-modif-<?= $d->getId() ?>')">&times;</button>
            <h4 class="text-lg font-bold mb-4">Modifier le dignitaire</h4>
            <form method="post" action="index.php?controller=dignitaire&action=modifier&id=<?= $d->getId() ?>" class="flex flex-col gap-2">
                <input class="border rounded px-2 py-1" name="nip" value="<?= htmlspecialchars($d->getNip()) ?>" required>
                <input class="border rounded px-2 py-1" name="matricule" value="<?= htmlspecialchars($d->getMatricule()) ?>" required>
                <input class="border rounded px-2 py-1" name="nom" value="<?= htmlspecialchars($d->getNom()) ?>" required>
                <input class="border rounded px-2 py-1" name="prenom" value="<?= htmlspecialchars($d->getPrenom()) ?>" required>
                <input class="border rounded px-2 py-1" name="date_naissance" type="date" value="<?= htmlspecialchars($d->getDateNaissance()) ?>" required>
                <input class="border rounded px-2 py-1" name="lieu_naissance" value="<?= htmlspecialchars($d->getLieuNaissance()) ?>" required>
                <input class="border rounded px-2 py-1" name="genre" value="<?= htmlspecialchars($d->getGenre()) ?>" required>
                <input class="border rounded px-2 py-1" name="etat_civil" value="<?= htmlspecialchars($d->getEtatCivil()) ?>" required>
                <input class="border rounded px-2 py-1" name="photo" value="<?= htmlspecialchars($d->getPhoto()) ?>">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-2">Modifier</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
    function toggleCollapse(id) {
        const el = document.getElementById(id);
        el.classList.toggle('hidden');
    }
    window.onclick = function(event) {
        document.querySelectorAll('.fixed.z-50').forEach(function(modal) {
            if (event.target === modal) modal.classList.add('hidden');
        });
    }
</script>

