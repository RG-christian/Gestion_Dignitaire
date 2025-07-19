
<table class="min-w-full text-sm text-left">
    <thead>
    <tr class="bg-gray-200 text-gray-700">
        <th class="py-2 px-3">Nom d'utilisateur</th>
        <th class="py-2 px-3">Nom complet</th>
        <th class="py-2 px-3">Email</th>
        <th class="py-2 px-3">Rôle</th>
        <th class="py-2 px-3">Fonctions</th>
        <th class="py-2 px-3">Sous-fonctions</th>
        <th class="py-2 px-3">Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($users)): ?>
        <tr><td colspan="5" class="py-2 px-3 text-center text-gray-500">Aucun utilisateur trouvé.</td></tr>
    <?php else: ?>
        <?php foreach ($users as $user): ?>
            <tr class="border-b hover:bg-gray-50">
                <td class="py-2 px-3"><?= htmlspecialchars($user['username']) ?></td>
                <td class="py-2 px-3"><?= htmlspecialchars($user['nom_complet']) ?></td>
                <td class="py-2 px-3"><?= htmlspecialchars($user['email']) ?></td>
                <td class="py-2 px-3">
                                <span class="inline-block px-2 py-1 rounded
                                    <?= $user['role_name'] === 'Superadmin' ? 'bg-blue-200 text-blue-800' : 'bg-green-200 text-green-800' ?>">
                                    <?= htmlspecialchars($user['role_name']) ?>
                                </span>
                </td>
                <td class="py-2 px-3">
                    <?php
                    $fcts = explode(', ', $user['fonctions'] ?? '');
                    foreach($fcts as $fct) {
                        if ($fct) echo '<div>' . htmlspecialchars($fct) . '</div>';
                    }
                    ?>
                </td>
                <td class="py-2 px-3 text-xs">
                    <?php
                    $sfs = explode(', ', $user['sousfonctions'] ?? '');
                    foreach($sfs as $sf) {
                        if ($sf) echo '<span class="inline-block mr-1">' . htmlspecialchars($sf) . '</span>';
                    }
                    ?>
                </td>
                <td class="py-2 px-3 flex space-x-2">
                    <button
                        class="text-blue-600 hover:text-blue-900 edit-user-btn"
                        data-user='<?= htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') ?>'
                        title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button
                        class="text-red-600 hover:text-red-900 delete-user-btn"
                        data-userid="<?= $user['id'] ?>"
                        data-username="<?= htmlspecialchars($user['username']) ?>"
                        title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>

            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

