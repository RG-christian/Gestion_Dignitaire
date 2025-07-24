<?php
require_once __DIR__ . '/../config/database.php';
require 'layout.php';

$pdo = getDatabaseConnection();
if (!isset($list_pays)) $list_pays = [];

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des villes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .modal { display:none; position:fixed; z-index:1000; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3);}
        .modal-content { background:#fff; margin:8% auto; padding:25px; border-radius:8px; width:480px; position:relative;}
        .close { position:absolute; top:10px; right:18px; font-size:24px; cursor:pointer; }
    </style>
</head>
<body>
<section class="max-w-5xl mx-auto mt-10 mb-12 px-4">
    <h2 class="mb-6 text-3xl font-bold">Liste des villes</h2>

    <!-- Bouton Ajout -->
    <button class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded mb-4"
            onclick="openModal('modal-ajout')">
        Ajouter une ville
    </button>

    <!-- Champ de recherche -->
    <div class="mb-4">
        <input type="text" id="searchInput" placeholder="Rechercher une ville ou un pays..."
               class="border px-3 py-2 rounded w-full sm:w-64">
    </div>

    <!-- Table des villes -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table id="tableVilles" class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(0)">ID</th>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(1)">Ville</th>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(2)">Nom du Pays</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            <?php if (!empty($villes)): ?>
                <?php foreach ($villes as $v): ?>
                    <tr>
                        <td class="px-4 py-2"><?= $v->getId() ?></td>
                        <td class="px-4 py-2"><?= ($v->getNom()) ?></td>
                        <td class="px-4 py-2"><?= ($v->getPaysId()) ?></td>
                        <td class="px-4 py-2 flex flex-col gap-2 sm:flex-row">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white rounded px-2 py-1 text-xs"
                                    type="button"
                                    onclick="openModal('modal-modif-<?= $v->getId() ?>')">
                                Modifier
                            </button>
                            <a href="index.php?controller=ville&action=supprimer&id=<?= $v->getId() ?>"
                               class="bg-red-600 hover:bg-red-700 text-white rounded px-2 py-1 text-xs"
                               onclick="return confirm('Supprimer <?= ($v->getNom()) ?> ?')">
                                Supprimer
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Modification -->
                    <div class="modal" id="modal-modif-<?= $v->getId() ?>">
                        <div class="modal-content">
                            <span class="close" onclick="closeModal('modal-modif-<?= $v->getId() ?>')">&times;</span>
                            <h4 class="text-lg font-bold mb-4">Modifier la ville : <?= htmlspecialchars($v->getNom()) ?></h4>
                            <form method="post" action="index.php?controller=ville&action=modifier&id=<?= $v->getId() ?>" class="flex flex-col gap-2">
                                <input class="border rounded px-2 py-1" name="nom" value="<?= htmlspecialchars($v->getNom()) ?>" required>
                                <select name="pays_id" id="" class="border rounded px-2 py-1" required>
                                    <option value="0">Sélectionner un pays</option>
                                    <?php foreach ($list_pays as $lp): ?>
                                        <option value="<?= $lp['id'] ?>"><?= htmlspecialchars($lp['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <!--<input class="border rounded px-2 py-1" name="pays_id" value="<//?= htmlspecialchars($v->getPaysId()) ?>" required>-->
                                <input type="hidden" name="id" value="<?= $v->getId() ?>">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-2">Modifier</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center py-4">Aucune ville trouvée.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- Info + Pagination -->
        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between text-sm text-gray-700">
            <div id="infoRange"></div>
            <div id="pagination" class="flex flex-wrap gap-1 mt-2 sm:mt-0"></div>
        </div>
    </div>
</section>

<!-- Modal Ajout -->
<div class="modal" id="modal-ajout">
    <div class="modal-content">
        <span class="close" onclick="closeModal('modal-ajout')">&times;</span>
        <h4 class="text-lg font-bold mb-4">Ajouter une ville</h4>
        <form method="post" action="index.php?controller=ville&action=ajouter" class="flex flex-col gap-2">
            <input class="border rounded px-2 py-1" name="nom" placeholder="Nom de la ville" required>
            <select name="pays_id" id="" class="border rounded px-2 py-1" required>
                <option value="0">Sélectionner un pays</option>
                <?php foreach ($list_pays as $lp): ?>
                    <option value="<?= $lp['id'] ?>"><?= htmlspecialchars($lp['nom']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">Enregistrer</button>
        </form>
    </div>
</div>

<!-- JS -->
<script>
    function openModal(id) {
        document.getElementById(id).style.display = "block";
    }

    function closeModal(id) {
        document.getElementById(id).style.display = "none";
    }

    window.onclick = function (event) {
        document.querySelectorAll('.modal').forEach(function (modal) {
            if (event.target === modal) modal.style.display = "none";
        });
    };

    let sortDirection = true;
    function sortTable(index) {
        const table = document.getElementById("tableVilles");
        const tbody = table.tBodies[0];
        const rows = Array.from(tbody.rows);

        rows.sort((a, b) => {
            const aText = a.cells[index].textContent.trim().toLowerCase();
            const bText = b.cells[index].textContent.trim().toLowerCase();
            return sortDirection
                ? aText.localeCompare(bText, undefined, {numeric: true})
                : bText.localeCompare(aText, undefined, {numeric: true});
        });

        sortDirection = !sortDirection;
        rows.forEach(row => tbody.appendChild(row));
    }

    let currentPage = 1;
    const rowsPerPage = 10;
    let allRows = [];
    let rows = [];

    function paginateRows() {
        const tbody = document.querySelector("#tableVilles tbody");
        tbody.innerHTML = "";

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginated = rows.slice(start, end);
        paginated.forEach(row => tbody.appendChild(row));
        updatePaginationUI();
    }

    function updatePaginationUI() {
        const total = rows.length;
        const start = (currentPage - 1) * rowsPerPage + 1;
        const end = Math.min(currentPage * rowsPerPage, total);
        document.getElementById("infoRange").textContent = `Affichage de ${start} à ${end} sur ${total} entrées`;

        const pagination = document.getElementById("pagination");
        pagination.innerHTML = "";
        const totalPages = Math.ceil(total / rowsPerPage);

        if (currentPage > 1) {
            pagination.appendChild(createPageButton("Précédent", currentPage - 1));
        }

        for (let i = 1; i <= totalPages; i++) {
            pagination.appendChild(createPageButton(i, i, i === currentPage));
        }

        if (currentPage < totalPages) {
            pagination.appendChild(createPageButton("Suivant", currentPage + 1));
        }
    }

    function createPageButton(text, page, isActive = false) {
        const btn = document.createElement("button");
        btn.textContent = text;
        btn.className = `px-3 py-1 rounded border ${isActive ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'}`;
        btn.onclick = () => {
            currentPage = page;
            paginateRows();
        };
        return btn;
    }

    function initPagination() {
        const all = document.querySelectorAll("#tableVilles tbody tr");
        allRows = Array.from(all);
        rows = [...allRows];
        paginateRows();
    }

    document.getElementById("searchInput").addEventListener("input", function () {
        const filter = this.value.toLowerCase();
        if (filter === "") {
            rows = [...allRows];
        } else {
            rows = allRows.filter(row =>
                row.cells[1].textContent.toLowerCase().includes(filter) ||
                row.cells[2].textContent.toLowerCase().includes(filter)
            );
        }
        currentPage = 1;
        paginateRows();
    });

    window.addEventListener("load", initPagination);
</script>
</body>
</html>
