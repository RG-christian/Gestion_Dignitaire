<?php if (!isset($diplomes) && !isset($diplome)) die('Accès direct interdit'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Diplômes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .modal { display:none; position:fixed; z-index:1000; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3);}
        .modal-content { background:#fff; margin:8% auto; padding:25px; border-radius:8px; width:480px; position:relative;}
        .close { position:absolute; top:10px; right:18px; font-size:24px; cursor:pointer; }
    </style>
</head>
<body>
<section class="max-w-5xl mx-auto mt-10 mb-12 px-4">
    <h2 class="mb-6 text-3xl font-bold">Gestion des Diplômes</h2>

    <!-- Bouton Ajout -->
    <button class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded mb-4"
            onclick="openModal('modal-ajout')">
        Ajouter un diplôme
    </button>

    <!-- Table des diplômes -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Dignitaire</th>
                <th class="px-4 py-2">Intitulé</th>
                <th class="px-4 py-2">Établissement</th>
                <th class="px-4 py-2">Année</th>
                <th class="px-4 py-2">Ville</th>
                <th class="px-4 py-2">Domaine</th>
                <th class="px-4 py-2">Code</th>
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            <?php if(isset($diplomes)): foreach ($diplomes as $dip): ?>
                <tr>
                    <td class="px-4 py-2"><?= $dip->getId() ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($dip->getDignitaireId()) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($dip->getIntitule()) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($dip->getEtablissement()) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($dip->getAnnee()) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($dip->getVilleId()) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($dip->getDomaineId()) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($dip->getCode()) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($dip->getType()) ?></td>
                    <td class="px-4 py-2 flex flex-col gap-2 sm:flex-row">
                        <a href="index.php?controller=diplome&action=afficherDetail&id=<?= $dip->getId() ?>"
                           class="bg-sky-500 hover:bg-sky-600 text-white rounded px-2 py-1 text-xs">Détail</a>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white rounded px-2 py-1 text-xs"
                                type="button"
                                onclick="openModal('modal-modif-<?= $dip->getId() ?>')">
                            Modifier
                        </button>
                        <a href="index.php?controller=diplome&action=supprimer&id=<?= $dip->getId() ?>"
                           class="bg-red-600 hover:bg-red-700 text-white rounded px-2 py-1 text-xs"
                           onclick="return confirm('Supprimer ce diplôme ?')">
                            Supprimer
                        </a>
                    </td>
                </tr>

                <!-- Modal Modification pour chaque diplôme -->
                <div class="modal" id="modal-modif-<?= $dip->getId() ?>">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal('modal-modif-<?= $dip->getId() ?>')">&times;</span>
                        <h4 class="text-lg font-bold mb-4">Modifier le diplôme</h4>
                        <form method="post" action="index.php?controller=diplome&action=modifier&id=<?= $dip->getId() ?>" class="flex flex-col gap-2">
                            <input class="border rounded px-2 py-1" name="dignitaire_id" value="<?= htmlspecialchars($dip->getDignitaireId()) ?>" required>
                            <input class="border rounded px-2 py-1" name="intitule" value="<?= htmlspecialchars($dip->getIntitule()) ?>" required>
                            <input class="border rounded px-2 py-1" name="etablissement" value="<?= htmlspecialchars($dip->getEtablissement()) ?>" required>
                            <input class="border rounded px-2 py-1" name="annee" value="<?= htmlspecialchars($dip->getAnnee()) ?>" required>
                            <input class="border rounded px-2 py-1" name="ville_id" value="<?= htmlspecialchars($dip->getVilleId()) ?>" required>
                            <input class="border rounded px-2 py-1" name="domaine_id" value="<?= htmlspecialchars($dip->getDomaineId()) ?>" required>
                            <input class="border rounded px-2 py-1" name="code" value="<?= htmlspecialchars($dip->getCode()) ?>" required>
                            <input class="border rounded px-2 py-1" name="type" value="<?= htmlspecialchars($dip->getType()) ?>" required>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-2">Modifier</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Modal Ajout -->
<div class="modal" id="modal-ajout">
    <div class="modal-content">
        <span class="close" onclick="closeModal('modal-ajout')">&times;</span>
        <h4 class="text-lg font-bold mb-4">Ajouter un diplôme</h4>
        <form method="post" action="index.php?controller=diplome&action=ajouter" class="flex flex-col gap-2">
            <input class="border rounded px-2 py-1" name="dignitaire_id" placeholder="ID Dignitaire" required>
            <input class="border rounded px-2 py-1" name="intitule" placeholder="Intitulé" required>
            <input class="border rounded px-2 py-1" name="etablissement" placeholder="Établissement" required>
            <input class="border rounded px-2 py-1" name="annee" placeholder="Année" required>
            <input class="border rounded px-2 py-1" name="ville_id" placeholder="ID Ville" required>
            <input class="border rounded px-2 py-1" name="domaine_id" placeholder="ID Domaine" required>
            <input class="border rounded px-2 py-1" name="code" placeholder="Code" required>
            <input class="border rounded px-2 py-1" name="type" placeholder="Type" required>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">Enregistrer</button>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = "block";
    }
    function closeModal(id) {
        document.getElementById(id).style.display = "none";
    }
    window.onclick = function(event) {
        document.querySelectorAll('.modal').forEach(function(modal) {
            if (event.target === modal) modal.style.display = "none";
        });
    }
</script>
</body>
</html>
