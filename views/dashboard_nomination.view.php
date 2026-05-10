<?php
require_once __DIR__ . '/../config/database.php';
require 'layout.php';

$pdo = getDatabaseConnection();
if (!isset($nominations)) $nominations = [];

// Récupérer les données pour les selects
$dignitaires = $pdo->query("SELECT id, nom, prenom FROM dignitaire ORDER BY nom")->fetchAll(\PDO::FETCH_ASSOC);
$entites = $pdo->query("SELECT id, nom FROM entite ORDER BY nom")->fetchAll(\PDO::FETCH_ASSOC);
$postes = $pdo->query("SELECT id, intitule FROM postes ORDER BY intitule")->fetchAll(\PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Nominations</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Contenu principal décalé -->
<main class="flex-1 ml-64 bg-gray-100 min-h-screen">
    <section class="max-w-7xl mx-auto mt-10 mb-12 px-4">
        <h2 class="mb-6 text-3xl font-bold">Gestion des Nominations</h2>

        <!-- Bouton d'ajout -->
        <button class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded mb-6"
                onclick="openModal('modal-ajout')">
            Ajouter une nomination
        </button>

        <!-- Tableau des nominations -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dignitaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Poste</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date début</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date fin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fonction</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($nominations)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">Aucune nomination trouvée.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($nominations as $n): ?>
                            <?php
                            // Récupérer les noms pour l'affichage
                            $dig = $pdo->query("SELECT nom, prenom FROM dignitaire WHERE id = " . $n->getDignitaireId())->fetch();
                            $ent = $n->getEntiteId() ? $pdo->query("SELECT nom FROM entite WHERE id = " . $n->getEntiteId())->fetch() : null;
                            $pos = $n->getPosteId() ? $pdo->query("SELECT intitule FROM postes WHERE id = " . $n->getPosteId())->fetch() : null;
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $n->getId() ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $dig ? htmlspecialchars($dig['prenom'] . ' ' . $dig['nom']) : 'N/A' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $pos ? htmlspecialchars($pos['intitule']) : 'N/A' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $ent ? htmlspecialchars($ent['nom']) : 'N/A' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($n->getDateDebut() ?? 'N/A') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($n->getDateFin() ?? 'En cours') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($n->getFonction() ?? 'N/A') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openModal('modal-modif-<?= $n->getId() ?>')" 
                                            class="text-blue-600 hover:text-blue-900 mr-3">Modifier</button>
                                    <a href="index.php?controller=nomination&action=supprimer&id=<?= $n->getId() ?>" 
                                       onclick="return confirm('Supprimer cette nomination ?')"
                                       class="text-red-600 hover:text-red-900">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<!-- Modal Ajout -->
<div class="fixed z-50 inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden" id="modal-ajout">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative max-h-screen overflow-y-auto">
        <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 text-2xl" onclick="closeModal('modal-ajout')">&times;</button>
        <h4 class="text-lg font-bold mb-4">Ajouter une nomination</h4>
        <form method="post" action="index.php?controller=nomination&action=ajouter" class="flex flex-col gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dignitaire *</label>
                <select name="dignitaire_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">Sélectionner un dignitaire</option>
                    <?php foreach ($dignitaires as $dig): ?>
                        <option value="<?= $dig['id'] ?>"><?= htmlspecialchars($dig['prenom'] . ' ' . $dig['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Entité</label>
                <select name="entite_id" class="w-full border rounded px-3 py-2">
                    <option value="">Aucune</option>
                    <?php foreach ($entites as $ent): ?>
                        <option value="<?= $ent['id'] ?>"><?= htmlspecialchars($ent['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Poste</label>
                <select name="poste_id" class="w-full border rounded px-3 py-2">
                    <option value="">Aucun</option>
                    <?php foreach ($postes as $pos): ?>
                        <option value="<?= $pos['id'] ?>"><?= htmlspecialchars($pos['intitule']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">PV ID</label>
                <input type="number" name="pv_id" class="w-full border rounded px-3 py-2" placeholder="ID du PV">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date début *</label>
                <input type="date" name="date_debut" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input type="date" name="date_fin" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fonction</label>
                <input type="text" name="fonction" class="w-full border rounded px-3 py-2" placeholder="Fonction">
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">Enregistrer</button>
        </form>
    </div>
</div>

<!-- Modals de modification -->
<?php foreach ($nominations as $n): ?>
    <div class="fixed z-50 inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden" id="modal-modif-<?= $n->getId() ?>">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative max-h-screen overflow-y-auto">
            <button class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 text-2xl" onclick="closeModal('modal-modif-<?= $n->getId() ?>')">&times;</button>
            <h4 class="text-lg font-bold mb-4">Modifier la nomination</h4>
            <form method="post" action="index.php?controller=nomination&action=modifier&id=<?= $n->getId() ?>" class="flex flex-col gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dignitaire *</label>
                    <select name="dignitaire_id" class="w-full border rounded px-3 py-2" required>
                        <?php foreach ($dignitaires as $dig): ?>
                            <option value="<?= $dig['id'] ?>" <?= $n->getDignitaireId() == $dig['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dig['prenom'] . ' ' . $dig['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Entité</label>
                    <select name="entite_id" class="w-full border rounded px-3 py-2">
                        <option value="">Aucune</option>
                        <?php foreach ($entites as $ent): ?>
                            <option value="<?= $ent['id'] ?>" <?= $n->getEntiteId() == $ent['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ent['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poste</label>
                    <select name="poste_id" class="w-full border rounded px-3 py-2">
                        <option value="">Aucun</option>
                        <?php foreach ($postes as $pos): ?>
                            <option value="<?= $pos['id'] ?>" <?= $n->getPosteId() == $pos['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pos['intitule']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">PV ID</label>
                    <input type="number" name="pv_id" value="<?= htmlspecialchars($n->getPvId() ?? '') ?>" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date début *</label>
                    <input type="date" name="date_debut" value="<?= htmlspecialchars($n->getDateDebut() ?? '') ?>" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                    <input type="date" name="date_fin" value="<?= htmlspecialchars($n->getDateFin() ?? '') ?>" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fonction</label>
                    <input type="text" name="fonction" value="<?= htmlspecialchars($n->getFonction() ?? '') ?>" class="w-full border rounded px-3 py-2">
                </div>
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
    window.onclick = function(event) {
        document.querySelectorAll('.fixed.z-50').forEach(function(modal) {
            if (event.target === modal) modal.classList.add('hidden');
        });
    }
</script>

</body>
</html>
