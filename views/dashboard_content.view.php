<div class="max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 dark:text-gray-100">Tableau de Bord</h2>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Dignitaires -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm dark:text-gray-400">Nombre de dignitaires</p>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400"><?= $totalDignitaires ?? 0 ?></p>
                </div>
                <div class="bg-blue-100 p-4 rounded-full dark:bg-blue-900">
                    <i class="fas fa-users text-2xl text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <a href="index.php?controller=dignitaire&action=afficherListe" class="text-blue-600 text-sm mt-2 inline-block hover:underline dark:text-blue-400">
                Voir tous →
            </a>
        </div>

        <!-- Postes -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm dark:text-gray-400">Nombres de postes</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400"><?= $totalPostes ?? 0 ?></p>
                </div>
                <div class="bg-green-100 p-4 rounded-full dark:bg-green-900">
                    <i class="fas fa-briefcase text-2xl text-green-600 dark:text-green-400"></i>
                </div>
            </div>
            <a href="index.php?controller=poste&action=afficherListe" class="text-green-600 text-sm mt-2 inline-block hover:underline dark:text-green-400">
                Voir tous →
            </a>
        </div>

        <!-- Décorations -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm dark:text-gray-400">Décorations données</p>
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400"><?= $totalDecorations ?? 0 ?></p>
                </div>
                <div class="bg-yellow-100 p-4 rounded-full dark:bg-yellow-900">
                    <i class="fas fa-medal text-2xl text-yellow-600 dark:text-yellow-400"></i>
                </div>
            </div>
            <a href="index.php?controller=decoration&action=afficherListe" class="text-yellow-600 text-sm mt-2 inline-block hover:underline dark:text-yellow-400">
                Voir toutes →
            </a>
        </div>

        <!-- Villes -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm dark:text-gray-400">Villes d'affectation</p>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400"><?= $totalVilles ?? 0 ?></p>
                </div>
                <div class="bg-purple-100 p-4 rounded-full dark:bg-purple-900">
                    <i class="fas fa-city text-2xl text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
            <a href="index.php?controller=ville&action=afficherListe" class="text-purple-600 text-sm mt-2 inline-block hover:underline dark:text-purple-400">
                Voir toutes →
            </a>
        </div>

        <!-- Pays -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm dark:text-gray-400">Pays</p>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400"><?= $totalPays ?? 0 ?></p>
                </div>
                <div class="bg-red-100 p-4 rounded-full dark:bg-red-900">
                    <i class="fas fa-globe text-2xl text-red-600 dark:text-red-400"></i>
                </div>
            </div>
            <a href="index.php?controller=pays&action=afficherListe" class="text-red-600 text-sm mt-2 inline-block hover:underline dark:text-red-400">
                Voir tous →
            </a>
        </div>

        <!-- Régions -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm dark:text-gray-400">Régions</p>
                    <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400"><?= $totalRegions ?? 0 ?></p>
                </div>
                <div class="bg-indigo-100 p-4 rounded-full dark:bg-indigo-900">
                    <i class="fas fa-map text-2xl text-indigo-600 dark:text-indigo-400"></i>
                </div>
            </div>
            <a href="index.php?controller=region&action=afficherListe" class="text-indigo-600 text-sm mt-2 inline-block hover:underline dark:text-indigo-400">
                Voir toutes →
            </a>
        </div>
    </div>

    <!-- Section Gestion des Dignitaires -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8 dark:bg-gray-800">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Gestion des Dignitaires</h3>
            <a href="index.php?controller=dignitaire&action=afficherFormulaireAjout" 
               class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                <i class="fas fa-plus mr-2"></i>Ajouter un dignitaire
            </a>
        </div>

        <?php if (!empty($derniersDignitaires)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach (array_slice($derniersDignitaires, 0, 8) as $dig): ?>
            <div class="bg-gray-50 rounded-lg p-4 text-center hover:shadow-md transition dark:bg-gray-700">
                <div class="w-20 h-20 mx-auto mb-3 bg-blue-100 rounded-full flex items-center justify-center dark:bg-blue-900">
                    <?php if ($dig->getPhoto()): ?>
                        <img src="<?= htmlspecialchars($dig->getPhoto()) ?>" alt="Photo" class="w-full h-full rounded-full object-cover">
                    <?php else: ?>
                        <i class="fas fa-user text-3xl text-blue-600 dark:text-blue-400"></i>
                    <?php endif; ?>
                </div>
                <h4 class="font-bold text-gray-800 dark:text-gray-100"><?= htmlspecialchars($dig->getNom()) ?> <?= htmlspecialchars($dig->getPrenom()) ?></h4>
                <p class="text-sm text-gray-600 dark:text-gray-400"><?= htmlspecialchars($dig->getMatricule()) ?></p>
                <a href="index.php?controller=dignitaire&action=afficherDetail&id=<?= $dig->getId() ?>" 
                   class="text-blue-600 text-sm hover:underline dark:text-blue-400">Voir détails</a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-gray-500 text-center py-8 dark:text-gray-400">Aucun dignitaire enregistré</p>
        <?php endif; ?>
    </div>
</div>
