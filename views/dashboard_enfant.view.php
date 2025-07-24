<?php
require_once __DIR__ . '/../config/database.php';
require 'layout.php';

$pdo = getDatabaseConnection();
if (!isset($dignitaires)) $dignitaires = [];
if (!isset($enfants)) $enfants = [];

$entite_id = $entite_id ?? '';
$dignitaire_id = $dignitaire_id ?? '';

$dignitaires = $pdo->query("SELECT id, nom, prenom FROM dignitaire ORDER BY nom")->fetchAll(\PDO::FETCH_ASSOC);

$enfants = $pdo->query("
    SELECT e.id, e.nom, e.prenom, e.date_naissance, e.genre, e.dignitaire_id,
           d.nom AS dignitaire_nom, d.prenom AS dignitaire_prenom
    FROM enfants e
    LEFT JOIN dignitaire d ON e.dignitaire_id = d.id
    ORDER BY e.nom
")->fetchAll(\PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion des Enfants</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .main-content { margin-left: 250px; } /* espace pour sidebar */
        .btn {
            @apply px-4 py-2 rounded font-semibold transition duration-300 shadow;
        }
        .btn-ajout { @apply bg-green-600 hover:bg-green-700 text-white; }
        .btn-modif { @apply bg-yellow-500 hover:bg-yellow-600 text-white; }
        .btn-suppr { @apply bg-red-600 hover:bg-red-700 text-white; }
        .btn-detail { @apply bg-blue-600 hover:bg-blue-700 text-white; }

        .animate-scale-in {
            animation: scaleIn 0.3s ease forwards;
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease forwards;
        }
        @keyframes scaleIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->


<!-- Main Content -->
<main class=" p-4 bg-white shadow rounded-xl main-content">
  <div class="flex justify-between mb-3">
      <h2 class="text-2xl font-semibold mb-4 text-gray-700">Liste des enfants</h2>
      <button onclick="openModal('modal-ajout')" class="btn btn-ajout bg-green-600 p-2 rounded-xl text-white ">+ Ajouter un enfant</button>

  </div>

    <div class="overflow-x-auto rounded">
        <?php if (!empty($enfants)) : ?>
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-100 border-b">
            <tr>
                <th class="py-3 px-4 text-left">Nom</th>
                <th class="py-3 px-4 text-left">Prénom</th>
                <th class="py-3 px-4 text-left">Genre</th>
                <th class="py-3 px-4 text-left">Naissance</th>
                <th class="py-3 px-4 text-left">Dignitaire</th>
                <th class="py-3 px-4 text-center">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            <?php foreach ($enfants as $enfant) : ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-2 px-4"><?= htmlspecialchars($enfant['nom']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($enfant['prenom']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($enfant['genre']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($enfant['date_naissance']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($enfant['dignitaire_nom'] . ' ' . $enfant['dignitaire_prenom']) ?></td>
                    <td class="py-2 px-4 flex justify-center space-x-2">
                        <button onclick="openDetailModal('<?= $enfant['id'] ?>', '<?= htmlspecialchars($enfant['dignitaire_nom'] . ' ' . $enfant['dignitaire_prenom']) ?>')" class="btn btn-detail">Détails</button>
                        <button onclick="openModal('modal-modif')" class="btn btn-modif">Modifier</button>
                        <form method="POST" action="index.php?controller=enfant&action=supprimer&id=<?= $enfant['id'] ?>" onsubmit="return confirm('Supprimer cet enfant ?');">
                            <button type="submit" class="btn btn-suppr">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else : ?>
            <p>Aucun enfant enregistré.</p>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Ajout -->
<div id="modal-ajout" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative animate-scale-in">
        <h2 class="text-2xl font-bold text-green-700 mb-4">Ajouter un enfant</h2>
        <form method="POST" action="index.php?controller=enfant&action=ajouter" class="space-y-4">
            <input type="text" name="nom" placeholder="Nom" required class="w-full border rounded px-3 py-2" />
            <input type="text" name="prenom" placeholder="Prénom" required class="w-full border rounded px-3 py-2" />
            <input type="date" name="date_naissance" required class="w-full border rounded px-3 py-2" />
            <input type="text" name="lieu_naissance" placeholder="Lieu de naissance" required class="w-full border rounded px-3 py-2" />
            <select name="genre" required class="w-full border rounded px-3 py-2">
                <option value="" disabled selected>Genre</option>
                <option value="M">Masculin</option>
                <option value="F">Féminin</option>
            </select>
            <select name="dignitaire_id" class="w-full border rounded px-3 py-2" required>
                <option value="">Choisir un dignitaire</option>
                <?php foreach ($dignitaires as $d): ?>
                    <option value="<?= $d['id']; ?>"><?= htmlspecialchars($d['nom'] . ' ' . $d['prenom']); ?></option>
                <?php endforeach; ?>
            </select>

            <div class="flex justify-end gap-3 mt-4">
                <button type="button" onclick="closeModal('modal-ajout')" class="btn bg-gray-300 hover:bg-gray-400">Annuler</button>
                <button type="submit" class="btn btn-ajout">Ajouter</button>
            </div>
        </form>
        <button onclick="closeModal('modal-ajout')" class="absolute top-3 right-4 text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
    </div>
</div>

<!-- Modal Détail -->
<div id="modal-detail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-xl p-6 relative animate-fade-in">
        <h2 class="text-2xl font-bold text-blue-700 mb-4">Détails de l'enfant</h2>
        <div id="detail-content" class="space-y-3">
            <!-- Contenu dynamique -->
        </div>
        <button onclick="closeModal('modal-detail')" class="absolute top-3 right-4 text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
    </div>
</div>

<!-- Modal Modification -->
<div id="modal-modif" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6 relative animate-scale-in">
        <h2 class="text-2xl font-bold text-yellow-600 mb-4">Modifier un enfant</h2>
        <form id="form-modif" method="POST" action="" class="space-y-4">
            <!-- Champs remplis dynamiquement -->
        </form>
        <button onclick="closeModal('modal-modif')" class="absolute top-3 right-4 text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
    function openDetailModal(enfantId, dignitaireNom) {
        document.getElementById('detail-content').innerHTML = `
            <p><strong>ID:</strong> ${enfantId}</p>
            <p><strong>Dignitaire :</strong> ${dignitaireNom}</p>
        `;
        openModal('modal-detail');
    }
</script>

</body>
</html>