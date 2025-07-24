

<?php
require_once __DIR__ . '/../config/database.php';
require 'layout.php';

$pdo = getDatabaseConnection();
if (!isset($regions)) $regions = [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des pays</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .modal { display:none; position:fixed; z-index:1000; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3);}
        .modal-content { background:#fff; margin:8% auto; padding:25px; border-radius:8px; width:480px; position:relative;}
        .close { position:absolute; top:10px; right:18px; font-size:24px; cursor:pointer; }
    </style>
</head>
<body>
<section class="max-w-5xl mx-auto mt-10 mb-12 px-4">
    <h2 class="mb-6 text-3xl font-bold">Liste des pays</h2>

    <!-- Bouton Ajout -->
    <button class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded mb-4"
            onclick="openModal('modal-ajout')">
        Ajouter un pays
    </button>

    <!-- Champ de recherche -->
    <div class="mb-4">
        <input type="text" id="searchInput" placeholder="Rechercher un pays..."
               class="border px-3 py-2 rounded w-full sm:w-64">
    </div>

    <!-- Table des pays -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table id="tablePays" class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(0)">ID</th>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(1)">Code-ISO</th>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(2)">Indicatif</th>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(3)">Continent</th>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(4)">Pays</th>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(4)">Drapeau</th>
                <th class="px-4 py-2 cursor-pointer" onclick="sortTable(5)">Région</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            <?php if (!empty($pays)): ?>
                <?php foreach ($pays as $p):
                    $code = strtolower($p->getCodeIso());
                    ?>
                    <tr>
                        <td class="px-4 py-2"><?= $p->getId() ?></td>
                        <td class="px-4 py-2"><?= ($p->getCodeIso()) ?></td>
                        <td class="px-4 py-2"><?= ($p->getIndicatif()) ?></td>
                        <td class="px-4 py-2"><?= ($p->getContinent()) ?></td>
                        <td class="px-4 py-2"><?= ($p->getNom()) ?></td>
                        <td class="px-4 py-2"><img src="https://flagcdn.com/w20/<?= $code ?>.png" alt="<?= ($p->getNom()) ?> flag"></td>
                        <!-- Display flag with a fallback if the image doesn't exist -->
                        <td class="px-4 py-2"><?= ($p->getRegionId()) ?></td>
                        <!-- Actions -->
                        <td class="px-4 py-2 flex flex-col gap-2 sm:flex-row">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white rounded px-2 py-1 text-xs"
                                    type="button"
                                    onclick="openModal('modal-modif-<?= $p->getId() ?>')">
                                Modifier
                            </button>
                            <a href="index.php?controller=pays&action=supprimer&id=<?= $p->getId() ?>"
                               class="bg-red-600 hover:bg-red-700 text-white rounded px-2 py-1 text-xs"
                               onclick="return confirm('Supprimer <?= htmlspecialchars($p->getNom()) ?> ?')">
                                Supprimer
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Modification -->
                    <div class="modal" id="modal-modif-<?= $p->getId() ?>">
                        <div class="modal-content">
                            <span class="close" onclick="closeModal('modal-modif-<?= $p->getId() ?>')">&times;</span>
                            <h4 class="text-lg font-bold mb-4">Modifier le pays : <?= ($p->getNom()) ?></h4>
                            <form method="post" action="index.php?controller=pays&action=modifier&id=<?= $p->getId() ?>" class="flex flex-col gap-2">
                                <input placeholder="Pays" class="border rounded px-2 py-1" name="nom" value="<?= ($p->getNom()) ?>" required>
                                <input placeholder="Code ISO" class="border rounded px-2 py-1" name="code_iso" value="<?= ($p->getCodeIso()) ?>" required>
                                <input placeholder="Continent" class="border rounded px-2 py-1" name="continent" value="<?= ($p->getContinent()) ?>" required>
                                <input placeholder="Indicatif" class="border rounded px-2 py-1" name="indicatif" value="<?= ($p->getIndicatif()) ?>" required>
                                <select name="region_id" class="border rounded px-2 py-1">
                                    <option value="">Sélectionner une région</option>
                                    <?php foreach ($regions as $r): ?>
                                        <option value="<?= $r['id'] ?>" <?= $r['id'] == $p->getRegionId() ? 'selected' : '' ?>>
                                            <?= ($r['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-2">Modifier</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center py-4">Aucun pays trouvé.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- Zone info + pagination -->
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
        <h4 class="text-lg font-bold mb-4">Ajouter un pays</h4>
        <form method="post" action="index.php?controller=pays&action=ajouter" class="flex flex-col gap-2">
            <input class="border rounded px-2 py-1" name="code_iso" placeholder="Code-ISO" required>
            <input class="border rounded px-2 py-1" name="nom" placeholder="Pays" required>
            <input class="border rounded px-2 py-1" name="continent" placeholder="Continent" required>
            <input class="border rounded px-2 py-1" name="indicatif" placeholder="Indicatif" required>
            <select name="region_id" class="border rounded px-2 py-1">
                <option value="">Sélectionner une région</option>
                <?php foreach ($regions as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= ($r['nom']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded mt-2">Enregistrer</button>
        </form>
    </div>
</div>

<!-- JS pour modals, tri, pagination et recherche -->
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

    let sortDirection = true;
    function sortTable(columnIndex) {
        const table = document.getElementById("tablePays");
        const tbody = table.tBodies[0];
        const rows = Array.from(tbody.rows);

        rows.sort((a, b) => {
            const aText = a.cells[columnIndex].textContent.trim().toLowerCase();
            const bText = b.cells[columnIndex].textContent.trim().toLowerCase();

            if (!isNaN(aText) && !isNaN(bText)) {
                return sortDirection ? aText - bText : bText - aText;
            }
            return sortDirection ? aText.localeCompare(bText) : bText.localeCompare(aText);
        });

        sortDirection = !sortDirection;
        rows.forEach(row => tbody.appendChild(row));
    }

    const rowsPerPage = 10;
    let currentPage = 1;
    let allRows = [];
    let rows = [];

    function paginateRows() {
        const tbody = document.querySelector("#tablePays tbody");
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

        const totalPages = Math.ceil(total / rowsPerPage);
        const pagination = document.getElementById("pagination");
        pagination.innerHTML = "";

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
        const all = document.querySelectorAll("#tablePays tbody tr");
        allRows = Array.from(all);
        rows = [...allRows];
        paginateRows();
    }

    // Recherche dynamique sur le nom du pays (colonne 4)
    document.getElementById("searchInput").addEventListener("input", function () {
        const filter = this.value.toLowerCase();
        if (filter === "") {
            rows = [...allRows];
        } else {
            rows = allRows.filter(row =>
                row.cells[4].textContent.toLowerCase().includes(filter)
            );
        }
        currentPage = 1;
        paginateRows();
    });

    window.addEventListener("load", initPagination);
</script>


</body>
</html>
