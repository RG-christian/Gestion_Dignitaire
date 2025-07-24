<?php
require 'layout.php';

if (!isset($diplomes) && !isset($diplome)) die('Accès direct interdit'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Diplômes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .modal { display:none; position:fixed; z-index:1000; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3);}        .modal-content { background: white;padding: 2rem; border-radius: 0.5rem; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto;box-shadow: 0 5px 15px rgba(0,0,0,0.3);position: relative;outline: none;}
        .close {position: absolute;top: 10px;right: 16px;font-size: 1.5rem;font-weight: bold;cursor: pointer;}
        .diplome-block {background-color: #f3f4f6;padding: 1rem; border: 1px solid #d1d5db; border-radius: 0.375rem; margin-bottom: 1rem;}
        input {display: block;width: 100%;padding: 0.5rem;margin-bottom: 0.5rem;border: 1px solid #d1d5db;border-radius: 0.375rem;font-size: 0.875rem;}
    </style>

</head>
<body>
<section class="max-w-5xl mx-auto mt-10 mb-12 px-4">
    <h2 class="mb-6 text-3xl font-bold">Gestion des Diplômes</h2>

    <form method="get" action="index.php" class="mb-6 flex gap-2">
        <input type="hidden" name="controller" value="diplome">
        <input type="hidden" name="action" value="rechercher">
        <input type="text" name="q" placeholder="Rechercher un diplôme (intitulé, établissement, année)..."
               value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>"
               class="flex-grow border rounded px-3 py-2">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Rechercher
        </button>
    </form>

    <?php if (isset($diplome)): ?>
        <!-- Bloc détail diplôme -->
        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xl font-semibold mb-4">Détail du Diplôme</h3>
            <p><strong>Intitulé :</strong> <?= htmlspecialchars($diplome->getIntitule()) ?></p>
            <p><strong>Établissement :</strong> <?= htmlspecialchars($diplome->getEtablissement()) ?></p>
            <p><strong>Année :</strong> <?= htmlspecialchars($diplome->getAnnee()) ?></p>
            <p><strong>Code :</strong> <?= htmlspecialchars($diplome->getCode()) ?></p>
            <p><strong>Type :</strong> <?= htmlspecialchars($diplome->getType()) ?></p>
            <p><strong>ID Dignitaire :</strong> <?= htmlspecialchars($diplome->getDignitaireId()) ?></p>
            <p><strong>ID Ville :</strong> <?= htmlspecialchars($diplome->getVilleId()) ?></p>
            <p><strong>ID Domaine :</strong> <?= htmlspecialchars($diplome->getDomaineId()) ?></p>
            <a href="index.php?controller=diplome&action=afficherListe"
               class="inline-block mt-4 text-white bg-gray-700 hover:bg-gray-800 px-4 py-2 rounded">
                Retour à la liste
            </a>
        </div>
    <?php endif; ?>

    <?php if (isset($diplomes_par_dignitaire)): ?>
        <div class="bg-white shadow rounded p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4">Dignitaires et nombre de diplômes</h2>
            <table class="min-w-full table-auto border">
                <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 border">ID Dignitaire</th>
                    <th class="px-4 py-2 border">Nombre de diplômes</th>
                    <th class="px-4 py-2 border">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($diplomes_par_dignitaire as $ligne): ?>
                    <tr class="border-t">
                        <td class="px-4 py-2 border"><?= htmlspecialchars($ligne['dignitaire_id']) ?></td>
                        <td class="px-4 py-2 border"><?= htmlspecialchars($ligne['total_diplomes']) ?></td>
                        <td class="px-4 py-2 border">
                            <a href="index.php?controller=diplome&action=afficherDetailParDignitaire&id=<?= $ligne['dignitaire_id'] ?>"
                               class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                Détails
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if (isset($diplomes)): ?>
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
                    <th class="px-4 py-2">Année</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                <?php foreach ($diplomes as $dip): ?>
                    <tr>
                        <td class="px-4 py-2"><?= $dip->getId() ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($dip->getDignitaireId()) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($dip->getIntitule()) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($dip->getAnnee()) ?></td>
                        <td class="px-4 py-2 flex flex-col gap-2 sm:flex-row">
                            <a href="index.php?controller=diplome&action=afficherDetail&id=<?= $dip->getId() ?>"
                               class="bg-sky-500 hover:bg-sky-600 text-white rounded px-2 py-1 text-xs">
                                Détail
                            </a>
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

                    <!-- Modal Modification -->
                    <div class="modal" id="modal-modif-<?= $dip->getId() ?>">
                        <div class="modal-content">
                            <span class="close" onclick="closeModal('modal-modif-<?= $dip->getId() ?>')">&times;</span>
                            <h4 class="text-lg font-bold mb-4">Modifier le diplôme</h4>
                            <form method="post" action="index.php?controller=diplome&action=modifier&id=<?= $dip->getId() ?>" class="flex flex-col gap-4">

                                <label for="dignitaire_id_<?= $dip->getId() ?>">ID Dignitaire</label>
                                <input id="dignitaire_id_<?= $dip->getId() ?>" class="border rounded px-2 py-1 bg-gray-200 cursor-not-allowed"
                                       name="dignitaire_id"
                                       value="<?= htmlspecialchars($dip->getDignitaireId()) ?>"
                                       readonly>

                                <label for="intitule_<?= $dip->getId() ?>">Intitulé</label>
                                <input id="intitule_<?= $dip->getId() ?>" class="border rounded px-2 py-1"
                                       name="intitule"
                                       value="<?= htmlspecialchars($dip->getIntitule()) ?>"
                                       required>

                                <label for="etablissement_<?= $dip->getId() ?>">Établissement</label>
                                <input id="etablissement_<?= $dip->getId() ?>" class="border rounded px-2 py-1"
                                       name="etablissement"
                                       value="<?= htmlspecialchars($dip->getEtablissement()) ?>"
                                       required>

                                <label for="annee_<?= $dip->getId() ?>">Année</label>
                                <input id="annee_<?= $dip->getId() ?>" class="border rounded px-2 py-1"
                                       name="annee"
                                       value="<?= htmlspecialchars($dip->getAnnee()) ?>"
                                       required>

                                <label for="ville_id_<?= $dip->getId() ?>">ID Ville</label>
                                <input id="ville_id_<?= $dip->getId() ?>" class="border rounded px-2 py-1"
                                       name="ville_id"
                                       value="<?= htmlspecialchars($dip->getVilleId()) ?>"
                                       required>

                                <label for="domaine_id_<?= $dip->getId() ?>">ID Domaine</label>
                                <input id="domaine_id_<?= $dip->getId() ?>" class="border rounded px-2 py-1"
                                       name="domaine_id"
                                       value="<?= htmlspecialchars($dip->getDomaineId()) ?>"
                                       required>

                                <label for="code_<?= $dip->getId() ?>">Code</label>
                                <input id="code_<?= $dip->getId() ?>" class="border rounded px-2 py-1"
                                       name="code"
                                       value="<?= htmlspecialchars($dip->getCode()) ?>"
                                       required>

                                <label for="type_<?= $dip->getId() ?>">Type</label>
                                <input id="type_<?= $dip->getId() ?>" class="border rounded px-2 py-1"
                                       name="type"
                                       value="<?= htmlspecialchars($dip->getType()) ?>"
                                       required>

                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-4">
                                    Modifier
                                </button>
                            </form>
                        </div>
                    </div>


                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<!-- Modal Ajout -->
<div class="modal" id="modal-ajout">
    <div class="modal-content" tabindex="0">
        <span class="close" onclick="closeModal('modal-ajout')">&times;</span>
        <h4 class="text-lg font-bold mb-4">Ajouter un ou plusieurs diplômes</h4>
        <form method="post" action="index.php?controller=diplome&action=ajouter" class="flex flex-col gap-2" id="form-ajout">

            <!-- Un seul dignitaire pour tous les diplômes -->
            <input class="border rounded px-2 py-1 mb-3" name="dignitaire_id" placeholder="ID Dignitaire" required>

            <div id="diplome-container">
                <div class="diplome-block border rounded p-3 mb-3 bg-gray-100">
                    <input class="border rounded px-2 py-1 mb-1 w-full" name="intitule[]" placeholder="Intitulé" required>
                    <input class="border rounded px-2 py-1 mb-1 w-full" name="etablissement[]" placeholder="Établissement" required>
                    <input class="border rounded px-2 py-1 mb-1 w-full" name="annee[]" placeholder="Année" required>
                    <input class="border rounded px-2 py-1 mb-1 w-full" name="ville_id[]" placeholder="ID Ville" required>
                    <input class="border rounded px-2 py-1 mb-1 w-full" name="domaine_id[]" placeholder="ID Domaine" required>
                    <input class="border rounded px-2 py-1 mb-1 w-full" name="code[]" placeholder="Code" required>
                    <input class="border rounded px-2 py-1 mb-1 w-full" name="type[]" placeholder="Type" required>
                </div>
            </div>

            <button type="button" onclick="ajouterBlocDiplome()"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded mb-2 text-sm">
                + Ajouter un autre diplôme
            </button>

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

    function ajouterBlocDiplome() {
        const container = document.getElementById('diplome-container');
        const bloc = document.createElement('div');
        bloc.classList.add('diplome-block', 'border', 'rounded', 'p-3', 'mb-3', 'bg-gray-100');
        bloc.innerHTML = `
        <input class="border rounded px-2 py-1 mb-1 w-full" name="intitule[]" placeholder="Intitulé" required>
        <input class="border rounded px-2 py-1 mb-1 w-full" name="etablissement[]" placeholder="Établissement" required>
        <input class="border rounded px-2 py-1 mb-1 w-full" name="annee[]" placeholder="Année" required>
        <input class="border rounded px-2 py-1 mb-1 w-full" name="ville_id[]" placeholder="ID Ville" required>
        <input class="border rounded px-2 py-1 mb-1 w-full" name="domaine_id[]" placeholder="ID Domaine" required>
        <input class="border rounded px-2 py-1 mb-1 w-full" name="code[]" placeholder="Code" required>
        <input class="border rounded px-2 py-1 mb-1 w-full" name="type[]" placeholder="Type" required>
    `;
        container.appendChild(bloc);
    }
</script>
</body>
</html>
