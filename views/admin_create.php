<?php
$users = $users ?? [];
$roles = $roles ?? [];
$fonctions = $fonctions ?? [];
$sousfonctions = $sousfonctions ?? [];
$error = $error ?? null;
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des administrateurs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">



    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body class="bg-gray-100 min-h-screen py-8">
<!-- Toast Notification VanillaJS + Tailwind -->
<div id="toast"
     class="fixed top-6 right-6 z-50 min-w-[220px] flex items-center gap-3 px-5 py-3 rounded-lg shadow-2xl text-white bg-green-600 opacity-0 pointer-events-none transition-all duration-300"
     style="display:none;">
    <i id="toast-icon" class="fas fa-check-circle text-2xl"></i>
    <span id="toast-msg" class="flex-1 font-semibold"></span>
    <button type="button" onclick="hideToast()" class="ml-2 focus:outline-none">
        <i class="fas fa-times text-lg"></i>
    </button>
</div>


<div class="top-left-btn">
    <a href="index.php?controller=dignitaire&action=index" class="btn-secondary">
        ←
    </a>
</div>


<div class="max-w-5xl mx-auto">
    <!-- Liste des utilisateurs déjà créés -->
    <div class="bg-white p-6 rounded shadow mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Utilisateurs existants</h2>
        </div>
        <div class="overflow-x-auto">
            <div id="usersTable">
                <?php include __DIR__ . '/users_table_partial.php'; ?>

        </div>
        </div>

    </div>

    <!-- Formulaire de création d'un nouvel utilisateur -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Créer un nouvel utilisateur</h2>
        <?php if (isset($error)): ?>
            <div class="mb-4 text-red-600"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="index.php?controller=admin&action=create" id="userCreateForm" autocomplete="off">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm">Nom d'utilisateur</label>
                    <input type="text" name="username" class="w-full px-3 py-2 mb-2 border rounded" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm">Nom complet</label>
                    <input type="text" name="nom_complet" class="w-full px-3 py-2 mb-2 border rounded" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm">Email</label>
                    <input type="email" name="email" class="w-full px-3 py-2 mb-2 border rounded" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm">Mot de passe</label>
                    <input type="password" name="password" class="w-full px-3 py-2 mb-2 border rounded" required>
                </div>
            </div>
            <div class="mt-4">
                <label class="block mb-2 text-sm">Rôle</label>
                <select name="role_id" class="w-full px-3 py-2 mb-4 border rounded" required>
                    <?php foreach($roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['role_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-2 text-sm">Fonctions autorisées</label>
                <div class="flex flex-wrap gap-3">
                    <?php foreach($fonctions as $fonction): ?>
                        <label class="mr-3">
                            <input type="checkbox" name="fonctions[]" value="<?= $fonction['id'] ?>">
                            <?= htmlspecialchars($fonction['fonction_name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="mb-6">
                <label class="block mb-2 text-sm">Sous-fonctions autorisées</label>
                <div class="grid grid-cols-2 gap-2">
                    <?php foreach($sousfonctions as $sf): ?>
                        <label class="sousfonction-label" data-fonction-id="<?= $sf['fonction_id'] ?>">
                            <input type="checkbox" name="sousfonctions[]" value="<?= $sf['id'] ?>">
                            <?= htmlspecialchars($sf['sousfonction_name']) ?>
                            <span class="text-xs text-gray-400">(<?= htmlspecialchars($sf['fonction_name']) ?>)</span>
                        </label>

                    <?php endforeach; ?>
                </div>
            </div>
            <input type="hidden" name="ajax" value="1">
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Créer l'utilisateur</button>
        </form>
    </div>
</div>



<!-- MODAL EDIT -->
<div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
        <button id="closeEditModal" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-2xl">&times;</button>
        <h2 class="text-lg font-bold mb-4">Modifier l'utilisateur</h2>
        <form id="editUserForm" method="post" action="index.php?controller=admin&action=edit">
            <input type="hidden" name="edit_user_id" id="edit_user_id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm">Nom d'utilisateur</label>
                    <input type="text" name="edit_username" id="edit_username" class="w-full px-3 py-2 mb-2 border rounded" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm">Nom complet</label>
                    <input type="text" name="edit_nom_complet" id="edit_nom_complet" class="w-full px-3 py-2 mb-2 border rounded" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm">Email</label>
                    <input type="email" name="edit_email" id="edit_email" class="w-full px-3 py-2 mb-2 border rounded" required>
                </div>
                <div>
                    <label class="block mb-2 text-sm">Mot de passe (laisser vide pour ne pas changer)</label>
                    <input type="password" name="edit_password" id="edit_password" class="w-full px-3 py-2 mb-2 border rounded">
                </div>
            </div>
            <div class="mt-4">
                <label class="block mb-2 text-sm">Rôle</label>
                <select name="edit_role_id" id="edit_role_id" class="w-full px-3 py-2 mb-4 border rounded" required>
                    <?php foreach($roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['role_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-2 text-sm">Fonctions autorisées</label>
                <div class="flex flex-wrap gap-3" id="edit_fonctions_group">
                    <?php foreach($fonctions as $fonction): ?>
                        <label class="mr-3">
                            <input type="checkbox" name="edit_fonctions[]" value="<?= $fonction['id'] ?>" class="edit-fonction-checkbox">
                            <?= htmlspecialchars($fonction['fonction_name']) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="mb-6">
                <label class="block mb-2 text-sm">Sous-fonctions autorisées</label>
                <div class="grid grid-cols-2 gap-2" id="edit_sousfonctions_group">
                    <?php foreach($sousfonctions as $sf): ?>
                        <label class="sousfonction-label" data-fonction-id="<?= $sf['fonction_id'] ?>">
                            <input type="checkbox" name="edit_sousfonctions[]" value="<?= $sf['id'] ?>" class="edit-sousfonction-checkbox">
                            <?= htmlspecialchars($sf['sousfonction_name']) ?>
                            <span class="text-xs text-gray-400">(<?= htmlspecialchars($sf['fonction_name']) ?>)</span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>


<!-- MODAL DELETE -->
<div id="deleteUserModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6 relative">
        <h3 class="text-lg font-bold mb-4 text-red-700">Suppression utilisateur</h3>
        <p id="deleteUserMsg" class="mb-4 text-gray-700"></p>
        <div class="flex justify-end space-x-3">
            <button id="confirmDeleteUserBtn" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Supprimer</button>
            <button id="cancelDeleteUserBtn" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 transition">Annuler</button>
        </div>
    </div>
</div>


<script>
    function showToast(msg, color = 'bg-green-600', icon = 'fa-check-circle') {
        const el = document.getElementById('toast');
        const iconEl = document.getElementById('toast-icon');
        const msgEl = document.getElementById('toast-msg');
        // Nettoie les anciennes classes bg-*
        el.className = 'fixed top-6 right-6 z-50 min-w-[220px] flex items-center gap-3 px-5 py-3 rounded-lg shadow-2xl text-white opacity-0 pointer-events-none transition-all duration-300 ' + color;
        msgEl.textContent = msg;
        iconEl.className = `fas ${icon} text-2xl`;
        el.style.display = 'flex';
        setTimeout(() => {
            el.classList.remove('opacity-0', 'pointer-events-none');
            el.classList.add('opacity-100');
        }, 10);
        // Hide after 3s
        clearTimeout(window.toastTimeout);
        window.toastTimeout = setTimeout(hideToast, 3000);
    }
    function hideToast() {
        const el = document.getElementById('toast');
        el.classList.remove('opacity-100');
        el.classList.add('opacity-0', 'pointer-events-none');
        setTimeout(() => { el.style.display = 'none'; }, 300);
    }
</script>

<script>

    document.addEventListener('DOMContentLoaded', function () {

        // ---- FONCTIONS/SOUS-FONCTIONS DYNAMIQUES ----
        function attachFonctionCheckboxListeners() {
            const fonctionCheckboxes = document.querySelectorAll('input[name="fonctions[]"]');
            const sousfonctionLabels = document.querySelectorAll('.sousfonction-label');

            fonctionCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateSousfonctionsVisibility);
            });

            function updateSousfonctionsVisibility() {
                const checkedFonctions = Array.from(fonctionCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                sousfonctionLabels.forEach(label => {
                    const fonctionId = label.dataset.fonctionId;
                    const checkbox = label.querySelector('input[type="checkbox"]');
                    if (checkedFonctions.includes(fonctionId)) {
                        label.classList.remove('opacity-50', 'pointer-events-none');
                        checkbox.disabled = false;
                    } else {
                        checkbox.checked = false;
                        checkbox.disabled = true;
                        label.classList.add('opacity-50', 'pointer-events-none');
                    }
                });
            }
            updateSousfonctionsVisibility();
        }

        // ---- ACTIONS UTILISATEUR : MODALS EDIT/DELETE ----
        function attachUserActionListeners() {
            // MODAL EDIT
            const editUserModal = document.getElementById('editUserModal');
            const closeEditModalBtn = document.getElementById('closeEditModal');

            document.querySelectorAll('.edit-user-btn').forEach(btn => {
                btn.onclick = function () {
                    const user = JSON.parse(this.getAttribute('data-user'));
                    document.getElementById('edit_user_id').value = user.id;
                    document.getElementById('edit_username').value = user.username;
                    document.getElementById('edit_nom_complet').value = user.nom_complet;
                    document.getElementById('edit_email').value = user.email;
                    document.getElementById('edit_password').value = '';
                    document.getElementById('edit_role_id').value = user.role_id ?? '';

                    // Coche les fonctions/sous-fonctions
                    const userFonctions = (user.fonctions ?? '').split(', ').filter(Boolean);
                    document.querySelectorAll('.edit-fonction-checkbox').forEach(cb => {
                        cb.checked = userFonctions.includes(cb.closest('label').textContent.trim());
                    });
                    const userSousFonctions = (user.sousfonctions ?? '').split(', ').filter(Boolean);
                    document.querySelectorAll('.edit-sousfonction-checkbox').forEach(cb => {
                        cb.checked = userSousFonctions.includes(cb.closest('label').textContent.trim());
                    });

                    updateEditSousfonctionsVisibility();
                    editUserModal.classList.remove('hidden');
                }
            });
            if (closeEditModalBtn) closeEditModalBtn.onclick = () => editUserModal.classList.add('hidden');
            if (editUserModal) editUserModal.onclick = function (e) {
                if (e.target === editUserModal) editUserModal.classList.add('hidden');
            };

            function updateEditSousfonctionsVisibility() {
                const fonctionCheckboxes = document.querySelectorAll('.edit-fonction-checkbox');
                const sousfonctionLabels = document.querySelectorAll('#edit_sousfonctions_group .sousfonction-label');

                const checkedFonctions = Array.from(fonctionCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                sousfonctionLabels.forEach(label => {
                    const fonctionId = label.dataset.fonctionId;
                    const checkbox = label.querySelector('input[type="checkbox"]');
                    if (checkedFonctions.includes(fonctionId)) {
                        label.classList.remove('opacity-50', 'pointer-events-none');
                        checkbox.disabled = false;
                    } else {
                        checkbox.checked = false;
                        checkbox.disabled = true;
                        label.classList.add('opacity-50', 'pointer-events-none');
                    }
                });
            }




// Après avoir défini updateEditSousfonctionsVisibility()
            document.querySelectorAll('.edit-fonction-checkbox').forEach(cb => {
                cb.addEventListener('change', updateEditSousfonctionsVisibility);
            });







            // MODAL DELETE
            const deleteUserModal = document.getElementById('deleteUserModal');
            const deleteUserMsg = document.getElementById('deleteUserMsg');
            let userIdToDelete = null;

            document.querySelectorAll('.delete-user-btn').forEach(btn => {
                btn.onclick = function () {
                    userIdToDelete = this.getAttribute('data-userid');
                    const username = this.getAttribute('data-username');
                    deleteUserMsg.textContent = `Êtes-vous sûr de vouloir supprimer l'utilisateur « ${username} » ?`;
                    deleteUserModal.classList.remove('hidden');
                }
            });
            document.getElementById('cancelDeleteUserBtn').onclick = function () {
                deleteUserModal.classList.add('hidden');
                userIdToDelete = null;
            };
            document.getElementById('confirmDeleteUserBtn').onclick = function () {
                if (userIdToDelete) {
                    window.location.href = `index.php?controller=admin&action=delete&id=${userIdToDelete}`;
                }
            };
            deleteUserModal.onclick = function (e) {
                if (e.target === deleteUserModal) deleteUserModal.classList.add('hidden');
            };
        }

        // ---- RELOAD TABLE & REBIND EVENTS ----
        function reloadUserList() {
            fetch("index.php?controller=admin&action=create")
                .then(r => r.text())
                .then(html => {
                    document.getElementById("usersTable").innerHTML = html;
                    attachUserActionListeners();
                    attachFonctionCheckboxListeners();
                });
        }

        // ---- FORM CREATION UTILISATEUR ----
        const createUserForm = document.getElementById('userCreateForm');
        if (createUserForm) {
            createUserForm.addEventListener('submit', function (e) {
                e.preventDefault();
                // --- Validations JS ---
                const email = this.email.value.trim();
                const password = this.password.value.trim();
                if (!email.match(/^[^@]+@[^@]+\.[^@]+$/)) {
                    showToast('Email invalide', 'bg-red-500', 'fa-exclamation-triangle');
                    return;
                }
                if (password.length < 6) {
                    showToast("Le mot de passe doit faire au moins 6 caractères", "bg-red-500", "fa-times-circle");
                    return;
                }
                const checkedFonctions = Array.from(this.querySelectorAll('input[name="fonctions[]"]:checked'));
                if (checkedFonctions.length === 0) {
                    showToast("Sélectionnez au moins une fonction", "bg-red-500", "fa-exclamation-triangle");
                    return;
                }
                const checkedSousFonctions = Array.from(this.querySelectorAll('input[name="sousfonctions[]"]:checked'));
                if (checkedSousFonctions.length === 0) {
                    showToast("Sélectionnez au moins une sous-fonction", "bg-red-500", "fa-exclamation-triangle");
                    return;
                }

                // --- ENVOI AJAX ---
                const formData = new FormData(this);
                fetch(this.action, {
                    method: "POST",
                    body: formData,
                })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            showToast('Utilisateur ajouté avec succès !', 'bg-green-600', 'fa-check-circle');
                            reloadUserList();
                            createUserForm.reset();
                            attachFonctionCheckboxListeners();
                        } else {
                            showToast('Erreur lors de l’ajout', 'bg-red-700', 'fa-times-circle');
                        }
                    })
                    .catch(() => showToast("Erreur serveur", "bg-red-700", "fa-times-circle"));
            });
        }

        // --- INITIALISATION au CHARGEMENT ---
        attachUserActionListeners();
        attachFonctionCheckboxListeners();
    });
</script>

</body>



</html>
