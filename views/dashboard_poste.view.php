<?php
$pdo = getDatabaseConnection();
require 'layout.php';

if (!isset($postes)) $postes = [];
//$search = isset($search) ? $search : '';
$ville_id = isset($ville_id) ? $ville_id : '';
$entite_id = isset($entite_id) ? $entite_id : '';
$dignitaire_id = isset($dignitaire_id) ? $dignitaire_id : '';

// Récupère villes, entités, postes et dignitaires
$villes = $pdo->query("SELECT id, nom FROM ville ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$entites = $pdo->query("SELECT id, nom FROM entite ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$dignitaires = $pdo->query("SELECT id, prenom, nom FROM dignitaire ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM postes WHERE intitule LIKE :search ORDER BY intitule");
    $stmt->execute(['search' => "%$search%"]);
    $postes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $postes = $pdo->query("SELECT * FROM postes ORDER BY intitule")->fetchAll(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Dignitaires</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="dark:bg-gray-800 dark:text-gray-100 bg-gray-100 min-h-screen">
<!-- DASHBOARD -->
<div class="my-4 ml-64">
    <!-- Liste postes -->
    <header class="p-2">
        <div class="  flex flex-col sm:flex-row justify-between gap-4">
            <h1 class="text-2xl font-bold tracking-tight flex items-center gap-2">Gestion des Postes</h1>
            <form method="GET" action="" class="flex items-center w-full sm:w-auto gap-0.5">
                <input
                        type="text"
                        name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Rechercher poste..."
                        class="border-none rounded-l-lg px-4 py-2 text-gray-700 focus:ring-2 focus:ring-blue-600 focus:outline-none w-52 sm:w-64"
                >
                <button type="submit" class="bg-white hover:bg-gray-200  px-4 py-2 rounded-r-lg font-semibold shadow">
                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    <span class="sr-only">Rechercher</span>
                </button>
                <!-- Bouton d'ajout -->
                <button class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 ml-2  rounded-lg font-semibold shadow"
                        onclick="openModal('modal-ajout')">
                    Ajouter un poste
                </button>
            </form>
        </div>
    </header>

    <main class="container mx-auto p-6">


        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intitulé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dignitaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ville</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($postes)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun poste trouvé.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($postes as $poste): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($poste['intitule']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php
                                $dignitaire = $pdo->query("SELECT nom, prenom FROM dignitaire WHERE id = " . $poste['dignitaire_id'])->fetch(PDO::FETCH_ASSOC);
                                echo htmlspecialchars($dignitaire ? $dignitaire['prenom'] . ' ' . $dignitaire['nom'] : 'N/A');
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php
                                $ville_nom = 'Aucune';
                                foreach ($villes as $ville) {
                                    if ($ville['id'] == $poste['ville_id']) {
                                        $ville_nom = $ville['nom'];
                                        break;
                                    }
                                }
                                echo htmlspecialchars($ville_nom);
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php
                                $entite_nom = 'Aucune';
                                foreach ($entites as $entite) {
                                    if ($entite['id'] == $poste['entite_id']) {
                                        $entite_nom = $entite['nom'];
                                        break;
                                    }
                                }
                                echo htmlspecialchars($entite_nom);
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php
                                $date_debut = $poste['date_debut'] ? date('d/m/Y', strtotime($poste['date_debut'])) : 'N/A';
                                $date_fin = $poste['date_fin'] ? date('d/m/Y', strtotime($poste['date_fin'])) : 'À ce jour';
                                echo htmlspecialchars($date_debut . ' - ' . $date_fin);
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
<!--                                <a href="index.php?controller=poste&action=details&id=--><?php //echo $poste['id']; ?><!--" class="text-sky-600 hover:text-sky-800" title="Voir">-->
<!--                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">-->
<!--                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-9 0a9 9 0 1118 0 9 9 0 01-18 0z"/>-->
<!--                                    </svg>-->
<!--                                </a>-->
                                <a href="#" onclick="openModal('modal-modif-<?php echo $poste['id']; ?>')" class="text-blue-600 hover:text-blue-800 ml-4" title="Modifier">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M15.232 5.232l3.536 3.536M9 13l6 6M9 13l-3-3a2 2 0 112.828-2.828l3 3z"/>
                                    </svg>
                                </a>
                                <a href="index.php?controller=poste&action=supprimer&id=<?php echo $poste['id']; ?>" class="text-red-600 hover:text-red-800 ml-4" onclick="return confirm('Supprimer ce poste ?')" title="Supprimer">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</div>
<!-- Modal Ajout -->
<div class="fixed z-50 inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden" id="modal-ajout">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
        <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 text-2xl" onclick="closeModal('modal-ajout')">×</button>
        <h4 class="text-lg font-bold mb-4">Ajouter un poste</h4>
        <form method="post" action="index.php?controller=poste&action=ajouter" class="flex flex-col gap-2">
            <select name="dignitaire_id" class="border rounded px-2 py-1" required>
                <option value="">Sélectionner un dignitaire</option>
                <?php foreach ($dignitaires as $dignitaire): ?>
                    <option value="<?php echo $dignitaire['id']; ?>">
                        <?php echo htmlspecialchars($dignitaire['prenom'] . ' ' . $dignitaire['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input class="border rounded px-2 py-1" name="intitule" placeholder="Intitulé du poste" required>
            <select name="ville_id" class="border rounded px-2 py-1">
                <option value="">Sélectionner une ville</option>
                <?php foreach ($villes as $ville): ?>
                    <option value="<?php echo $ville['id']; ?>"><?php echo htmlspecialchars($ville['nom']); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="entite_id" class="border rounded px-2 py-1">
                <option value="">Sélectionner une entité</option>
                <?php foreach ($entites as $entite): ?>
                    <option value="<?php echo $entite['id']; ?>"><?php echo htmlspecialchars($entite['nom']); ?></option>
                <?php endforeach; ?>
            </select>
            <input class="border rounded px-2 py-1" name="date_debut" type="date" placeholder="Date de début">
            <input class="border rounded px-2 py-1" name="date_fin" type="date" placeholder="Date de fin">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">Enregistrer</button>
        </form>
    </div>
</div>

<!-- Modals de modification -->
<?php foreach ($postes as $poste): ?>
    <div class="fixed z-50 inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden" id="modal-modif-<?php echo $poste['id']; ?>">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
            <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 text-2xl" onclick="closeModal('modal-modif-<?php echo $poste['id']; ?>')">×</button>
            <h4 class="text-lg font-bold mb-4">Modifier le poste</h4>
            <form method="post" action="index.php?controller=poste&action=modifier&id=<?php echo $poste['id']; ?>" class="flex flex-col gap-2">
                <select name="dignitaire_id" class="border rounded px-2 py-1" required>
                    <option value="">Sélectionner un dignitaire</option>
                    <?php foreach ($dignitaires as $dignitaire): ?>
                        <option value="<?php echo $dignitaire['id']; ?>" <?php echo $poste['dignitaire_id'] == $dignitaire['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dignitaire['prenom'] . ' ' . $dignitaire['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input class="border rounded px-2 py-1" name="intitule" value="<?php echo htmlspecialchars($poste['intitule']); ?>" required>
                <select name="ville_id" class="border rounded px-2 py-1">
                    <option value="">Sélectionner une ville</option>
                    <?php foreach ($villes as $ville): ?>
                        <option value="<?php echo $ville['id']; ?>" <?php echo $poste['ville_id'] == $ville['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($ville['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="entite_id" class="border rounded px-2 py-1">
                    <option value="">Sélectionner une entité</option>
                    <?php foreach ($entites as $entite): ?>
                        <option value="<?php echo $entite['id']; ?>" <?php echo $poste['entite_id'] == $entite['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($entite['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input class="border rounded px-2 py-1" name="date_debut" type="date" value="<?php echo htmlspecialchars($poste['date_debut'] ? $poste['date_debut'] : ''); ?>">
                <input class="border rounded px-2 py-1" name="date_fin" type="date" value="<?php echo htmlspecialchars($poste['date_fin'] ? $poste['date_fin'] : ''); ?>">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-2">Modifier</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>
</body>
</html>
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
    window.onclick = function(event) {
        document.querySelectorAll('.fixed.z-50').forEach(function(modal) {
            if (event.target === modal) modal.classList.add('hidden');
        });
    }
</script>