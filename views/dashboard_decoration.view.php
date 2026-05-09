<?php
require_once __DIR__ . '/../config/database.php';
require 'layout.php';

$pdo = getDatabaseConnection();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des décorations</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .modal { display:none; position:fixed; z-index:1000; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3);}
        .modal-content { background:#fff; margin:5% auto; padding:25px; border-radius:8px; width:90%; max-width:600px; position:relative; max-height:85vh; overflow-y:auto;}
        .close { position:absolute; top:10px; right:18px; font-size:24px; cursor:pointer; }
    </style>
</head>
<body>
<section class="max-w-6xl mx-auto mt-10 mb-12 px-4">
    <h2 class="mb-6 text-3xl font-bold">Liste des décorations</h2>

    <!-- Bouton Ajout -->
    <button class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded mb-4"
            onclick="openModal('modal-ajout')">
        Ajouter une décoration
    </button>

    <!-- Champ de recherche -->
    <div class="mb-4">
        <input type="text" id="searchInput" placeholder="Rechercher une décoration..."
               class="border px-3 py-2 rounded w-full sm:w-64">
    </div>

    <!-- Table des décorations -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table id="tableDecorations" class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(0)">ID</th>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(1)">Nom</th>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(2)">Type</th>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(3)">Niveau</th>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(4)">Grade</th>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(5)">Date</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            <?php if (!empty($decorations)): ?>
                <?php foreach ($decorations as $d): ?>
                    <tr>
                        <td class="px-4 py-2"><?= $d->getId() ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($d->getNom()) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($d->getType()) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($d->getNiveau()) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($d->getGrade()) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($d->getDateObtention()) ?></td>
                        <td class="px-4 py-2 flex flex-col gap-2 sm:flex-row">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white rounded px-2 py-1 text-xs"
                                    type="button"
                                    onclick="openModal('modal-modif-<?= $d->getId() ?>')">
                                Modifier
                            </button>
                            <a href="index.php?controller=decoration&action=supprimer&id=<?= $d->getId() ?>"
                               class="bg-red-600 hover:bg-red-700 text-white rounded px-2 py-1 text-xs"
                               onclick="return confirm('Supprimer <?= htmlspecialchars($d->getNom()) ?> ?')">
                                Supprimer
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Modification -->
                    <div class="modal" id="modal-modif-<?= $d->getId() ?>">
                        <div class="modal-content">
                            <span class="close" onclick="closeModal('modal-modif-<?= $d->getId() ?>')">&times;</span>
                            <h4 class="text-lg font-bold mb-4">Modifier la décoration : <?= htmlspecialchars($d->getNom()) ?></h4>
                            <form method="post" action="index.php?controller=decoration&action=modifier&id=<?= $d->getId() ?>" class="flex flex-col gap-3">
                                <input class="border rounded px-2 py-1" name="deco_nom" placeholder="Nom" value="<?= htmlspecialchars($d->getNom()) ?>" required>
                                <input class="border rounded px-2 py-1" name="deco_type" placeholder="Type" value="<?= htmlspecialchars($d->getType()) ?>" required>
                                <input class="border rounded px-2 py-1" name="deco_niveau" placeholder="Niveau" value="<?= htmlspecialchars($d->getNiveau()) ?>">
                                <input class="border rounded px-2 py-1" name="deco_grade" placeholder="Grade" value="<?= htmlspecialchars($d->getGrade()) ?>">
                                <input type="date" class="border rounded px-2 py-1" name="deco_date_obtention" value="<?= htmlspecialchars($d->getDateObtention()) ?>">
                                <input class="border rounded px-2 py-1" name="deco_autorite" placeholder="Autorité" value="<?= htmlspecialchars($d->getAutorite()) ?>">
                                <textarea class="border rounded px-2 py-1" name="deco_motif" placeholder="Motif" rows="2"><?= htmlspecialchars($d->getMotif()) ?></textarea>
                                <textarea class="border rounded px-2 py-1" name="deco_description" placeholder="Description" rows="2"><?= htmlspecialchars($d->getDescription()) ?></textarea>
                                <input class="border rounded px-2 py-1" name="deco_fichierAttestation" placeholder="Fichier attestation" value="<?= htmlspecialchars($d->getFichierAttestation()) ?>">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-2">Modifier</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center py-4">Aucune décoration trouvée.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- Info + Pagination -->
        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between text-sm text-gray-700 px-4 pb-4">
            <div id="infoRange"></div>
            <div id="pagination" class="flex flex-wrap gap-1 mt-2 sm:mt-0"></div>
        </div>
    </div>
</section>

<!-- Modal Ajout -->
<div class="modal" id="modal-ajout">
    <div class="modal-content">
        <span class="close" onclick="closeModal('modal-ajout')">&times;</span>
        <h4 class="text-lg font-bold mb-4">Ajouter une décoration</h4>
        <form method="post" action="index.php?controller=decoration&action=ajouter" class="flex flex-col gap-3">
            <input class="border rounded px-2 py-1" name="deco_nom" placeholder="Nom de la décoration" required>
            <input class="border rounded px-2 py-1" name="deco_type" placeholder="Type" required>
            <input class="border rounded px-2 py-1" name="deco_niveau" placeholder="Niveau">
            <input class="border rounded px-2 py-1" name="deco_grade" placeholder="Grade">
            <input type="date" class="border rounded px-2 py-1" name="deco_date_obtention" placeholder="Date d'obtention">
            <input class="border rounded px-2 py-1" name="deco_autorite" placeholder="Autorité délivrant">
            <textarea class="border rounded px-2 py-1" name="deco_motif" placeholder="Motif" rows="2"></textarea>
            <textarea class="border rounded px-2 py-1" name="deco_description" placeholder="Description" rows="2"></textarea>
            <input class="border rounded px-2 py-1" name="deco_fichierAttestation" placeholder="Fichier attestation">
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
        const table = document.getElementById("tableDecorations");
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
        const tbody = document.querySelector("#tableDecorations tbody");
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
        const all = document.querySelectorAll("#tableDecorations tbody tr");
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
                row.cells[2].textContent.toLowerCase().includes(filter) ||
                row.cells[3].textContent.toLowerCase().includes(filter)
            );
        }
        currentPage = 1;
        paginateRows();
    });

    window.addEventListener("load", initPagination);
</script>
</body>
</html>
