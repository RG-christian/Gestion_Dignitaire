<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Gestion Dignitaire</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-800 text-white flex flex-col">
            <div class="p-4 border-b border-blue-700">
                <h1 class="text-xl font-bold">Gestion Dignitaires</h1>
            </div>
            
            <nav class="flex-1 overflow-y-auto p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="index.php?controller=dashboard&action=index" 
                           class="flex items-center gap-3 px-4 py-2 rounded bg-blue-700 hover:bg-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Tableau de Bord
                        </a>
                    </li>
                    
                    <li>
                        <a href="index.php?controller=dignitaire&action=afficherListe" 
                           class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Dignitaires
                        </a>
                    </li>
                    
                    <li>
                        <a href="index.php?controller=poste&action=afficherListe" 
                           class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Postes
                        </a>
                    </li>
                    
                    <li>
                        <a href="index.php?controller=decoration&action=afficherListe" 
                           class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                            Décorations
                        </a>
                    </li>
                    
                    <li>
                        <a href="index.php?controller=nomination&action=afficherListe" 
                           class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Nominations
                        </a>
                    </li>
                    
                    <li class="pt-4 border-t border-blue-700">
                        <p class="px-4 py-2 text-xs text-blue-300 uppercase">Géographie</p>
                    </li>
                    
                    <li>
                        <a href="index.php?controller=pays&action=afficherListe" 
                           class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pays
                        </a>
                    </li>
                    
                    <li>
                        <a href="index.php?controller=region&action=afficherListe" 
                           class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                            Régions
                        </a>
                    </li>
                    
                    <li>
                        <a href="index.php?controller=ville&action=afficherListe" 
                           class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Villes
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="p-4 border-t border-blue-700">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-lg font-bold"><?= strtoupper(substr($_SESSION['admin_nom_complet'] ?? 'A', 0, 1)) ?></span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium"><?= htmlspecialchars($_SESSION['admin_nom_complet'] ?? 'Admin') ?></p>
                        <p class="text-xs text-blue-300"><?= htmlspecialchars($_SESSION['role_name'] ?? 'Administrateur') ?></p>
                    </div>
                </div>
                <a href="index.php?controller=auth&action=logout" 
                   class="flex items-center justify-center gap-2 w-full bg-red-600 px-4 py-2 rounded hover:bg-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Déconnexion
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm p-6">
                <h2 class="text-3xl font-bold text-gray-800">Tableau de Bord</h2>
                <p class="text-gray-600 mt-1">Vue d'ensemble de la gestion des dignitaires</p>
            </header>

            <div class="p-6">
                <!-- Statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Dignitaires -->
                    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Dignitaires</p>
                                <p class="text-3xl font-bold text-blue-600"><?= $totalDignitaires ?? 0 ?></p>
                            </div>
                            <div class="bg-blue-100 p-4 rounded-full">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <a href="index.php?controller=dignitaire&action=afficherListe" class="text-blue-600 text-sm mt-2 inline-block hover:underline">
                            Voir tous →
                        </a>
                    </div>

                    <!-- Postes -->
                    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Postes</p>
                                <p class="text-3xl font-bold text-green-600"><?= $totalPostes ?? 0 ?></p>
                            </div>
                            <div class="bg-green-100 p-4 rounded-full">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <a href="index.php?controller=poste&action=afficherListe" class="text-green-600 text-sm mt-2 inline-block hover:underline">
                            Voir tous →
                        </a>
                    </div>

                    <!-- Décorations -->
                    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Décorations</p>
                                <p class="text-3xl font-bold text-yellow-600"><?= $totalDecorations ?? 0 ?></p>
                            </div>
                            <div class="bg-yellow-100 p-4 rounded-full">
                                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </div>
                        </div>
                        <a href="index.php?controller=decoration&action=afficherListe" class="text-yellow-600 text-sm mt-2 inline-block hover:underline">
                            Voir toutes →
                        </a>
                    </div>

                    <!-- Villes -->
                    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Villes</p>
                                <p class="text-3xl font-bold text-purple-600"><?= $totalVilles ?? 0 ?></p>
                            </div>
                            <div class="bg-purple-100 p-4 rounded-full">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <a href="index.php?controller=ville&action=afficherListe" class="text-purple-600 text-sm mt-2 inline-block hover:underline">
                            Voir toutes →
                        </a>
                    </div>

                    <!-- Pays -->
                    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Pays</p>
                                <p class="text-3xl font-bold text-red-600"><?= $totalPays ?? 0 ?></p>
                            </div>
                            <div class="bg-red-100 p-4 rounded-full">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <a href="index.php?controller=pays&action=afficherListe" class="text-red-600 text-sm mt-2 inline-block hover:underline">
                            Voir tous →
                        </a>
                    </div>

                    <!-- Régions -->
                    <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Régions</p>
                                <p class="text-3xl font-bold text-indigo-600"><?= $totalRegions ?? 0 ?></p>
                            </div>
                            <div class="bg-indigo-100 p-4 rounded-full">
                                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                            </div>
                        </div>
                        <a href="index.php?controller=region&action=afficherListe" class="text-indigo-600 text-sm mt-2 inline-block hover:underline">
                            Voir toutes →
                        </a>
                    </div>
                </div>

                <!-- Derniers dignitaires -->
                <?php if (!empty($derniersDignitaires)): ?>
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-bold mb-4">Derniers Dignitaires</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prénom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($derniersDignitaires as $dig): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($dig->getMatricule()) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($dig->getNom()) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($dig->getPrenom()) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= htmlspecialchars($dig->getTel() ?? '-') ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="index.php?controller=dignitaire&action=afficherDetail&id=<?= $dig->getId() ?>" 
                                           class="text-blue-600 hover:underline">Voir</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
