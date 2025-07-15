
<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Administrateur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Custom Scrollbar for Sidebar */
        .sidebar {
            overflow-y: auto;
            height: calc(100vh - 64px);
            scrollbar-width: thin;
            scrollbar-color: #6b7280 #ffffff;
        }
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #ffffff;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background-color: #6b7280;
            border-radius: 3px;
            border: 2px solid #ffffff;
        }
        .dark .sidebar::-webkit-scrollbar-track {
            background: #1f2937;
        }
        .dark .sidebar::-webkit-scrollbar-thumb {
            background-color: #9ca3af;
        }
        .sidebar ul {
            padding-bottom: 1rem;
        }

        /* Dropdown Styles */
        .dropdown-content {
            max-height: 0;
            overflow: hidden;
            background-color: #f9fafb;
            transition: max-height 0.3s ease-in-out;
            padding-left: 0.75rem;
        }
        .dropdown-content.active {
            max-height: 300px;
        }
        .profile-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 0.375rem;
            z-index: 30;
            min-width: 10rem;
        }
        .profile-dropdown.active {
            display: block;
        }
        .dark .dropdown-content {
            background-color: #1f2937;
        }
        .dark .profile-dropdown {
            background-color: #1f2937;
        }

        /* Dark Mode Adjustments */
        .dark .bg-gray-100 {
            background-color: #111827;
        }
        .dark .bg-white {
            background-color: #1f2937;
        }
        .dark .text-gray-900 {
            color: #e5e7eb;
        }
        .dark .text-gray-800 {
            color: #e5e7eb;
        }
        .dark .text-gray-700 {
            color: #d1d5db;
        }
        .dark .text-gray-600 {
            color: #d1d5db;
        }
        .dark .text-gray-500 {
            color: #9ca3af;
        }
        .dark .text-blue-500 {
            color: #93c5fd;
        }
        .dark .hover:bg-blue-50 {
            background-color: #374151;
        }
        .dark .border-gray-200 {
            border-color: #374151;
        }

        /* Global Font Size */
        body {
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen font-sans antialiased transition-all duration-300" id="body">
<!-- Header -->
<header class="bg-white text-gray-800 h-16 flex items-center px-3 sm:px-4 shadow-lg fixed top-0 left-0 right-0 z-20 dark:bg-gray-800 dark:text-gray-100">
    <button id="toggleSidebar" class="text-gray-800 focus:outline-none mr-3 dark:text-gray-100">
        <i class="fas fa-bars text-lg transition duration-200"></i>
    </button>
    <div class="flex items-center space-x-2">
        <i class="fas fa-crown text-lg text-blue-500 dark:text-blue-300"></i>
        <a href="index.php?controller=dignitaire&action=afficherListe" class="text-sm sm:text-base font-medium tracking-tight dark:text-gray-100">Gestion Dignitaires</a>
    </div>
    <nav class="ml-auto flex items-center space-x-2">
        <button id="themeToggle" class="text-gray-800 focus:outline-none transition duration-200 dark:text-gray-100">
            <i class="fas fa-adjust text-lg"></i>
        </button>
        <div class="relative group">
            <button class="text-gray-800 focus:outline-none transition duration-200 dark:text-gray-100">
                <i class="fas fa-user-circle text-lg"></i>
            </button>
            <ul class="profile-dropdown py-1">
                <li><a href="#" class="block px-3 py-1.5 text-gray-700 hover:bg-blue-50 hover:text-blue-700 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">Profil</a></li>
                <li><a href="#" class="block px-3 py-1.5 text-gray-700 hover:bg-blue-50 hover:text-blue-700 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">Déconnexion</a></li>
            </ul>
        </div>
    </nav>
</header>

<!-- Layout principal -->
<div class="flex pt-16">
    <!-- Sidebar -->
    <aside id="sidebar" class="bg-white text-gray-800 w-64 min-h-[calc(100vh-64px)] fixed top-16 left-0 shadow-md transition-all duration-300 transform -translate-x-full lg:translate-x-0 z-10 dark:bg-gray-800 dark:text-gray-100">
        <div class="pt-6 px-3 w-full h-full flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-sm font-medium text-gray-800 px-2 dark:text-gray-100">Menu</h2>
                <button id="closeSidebar" class="text-gray-800 focus:outline-none dark:text-gray-100">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto">
                <ul class="space-y-1">
                    <li>
                        <a href="#" class="flex items-center p-2 rounded-md text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition duration-200 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">
                            <i class="fas fa-tachometer-alt w-5 text-blue-500 dark:text-blue-300"></i>
                            <span class="ml-2 text-sm">Tableau</span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="#" class="dropdown-toggle flex items-center p-2 rounded-md text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition duration-200 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="w-5 text-blue-500 dark:text-blue-300">👥</span>
                            <span class="ml-2 text-sm">Gest. Pers.</span>
                            <i class="fas fa-chevron-right ml-auto text-xs"></i>
                        </a>
                        <ul class="dropdown-content">
                            <li><a href="index.php?controller=enfant&action=liste" class="block px-3 py-1.5 text-gray-600 hover:bg-blue-50 hover:text-blue-700 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white text-sm">Enfant</a></li>
                            <li><a href="index.php?controller=dignitaire&action=afficherListe" class="block px-3 py-1.5 text-gray-600 hover:bg-blue-50 hover:text-blue-700 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white text-sm">Dignitaire</a></li>
                            <li><a href="index.php?controller=poste&action=liste" class="block px-3 py-1.5 text-gray-600 hover:bg-blue-50 hover:text-blue-700 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white text-sm">Poste</a></li>
                        </ul>
                    </li>
                    <li class="relative">
                        <a href="#" class="dropdown-toggle flex items-center p-2 rounded-md text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition duration-200 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="w-5 text-blue-500 dark:text-blue-300">🎓</span>
                            <span class="ml-2 text-sm">Éduc. & Qualif.</span>
                            <i class="fas fa-chevron-right ml-auto text-xs"></i>
                        </a>
                        <ul class="dropdown-content">
                            <li><a href="index.php?controller=diplome&action=afficherListe" class="block px-3 py-1.5 text-gray-600 hover:bg-blue-50 hover:text-blue-700 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white text-sm">Diplôme</a></li>
                        </ul>
                    </li>
                    <li class="relative">
                        <a href="#" class="dropdown-toggle flex items-center p-2 rounded-md text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition duration-200 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="w-5 text-blue-500 dark:text-blue-300">💼</span>
                            <span class="ml-2 text-sm">Parcours Pro.</span>
                            <i class="fas fa-chevron-right ml-auto text-xs"></i>
                        </a>
                        <ul class="dropdown-content">
                            <li><a href="index.php?controller=experience&action=liste" class="block px-3 py-1.5 text-gray-600 hover:bg-blue-50 hover:text-blue-700 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white text-sm">Expérience</a></li>
                        </ul>
                    </li>
                    <li class="relative">
                        <a href="#" class="dropdown-toggle flex items-center p-2 rounded-md text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition duration-200 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="w-5 text-blue-500 dark:text-blue-300">🗣️</span>
                            <span class="ml-2 text-sm">Langues</span>
                            <i class="fas fa-chevron-right ml-auto text-xs"></i>
                        </a>
                        <ul class="dropdown-content">
                            <li><a href="index.php?controller=langue_parlee&action=liste" class="block px-3 py-1.5 text-gray-600 hover:bg-blue-50 hover:text-blue-700 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white text-sm">Langues</a></li>
                        </ul>
                    </li>
                    <li class="relative">
                        <a href="#" class="dropdown-toggle flex items-center p-2 rounded-md text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition duration-200 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="w-5 text-blue-500 dark:text-blue-300">🌐</span>
                            <span class="ml-2 text-sm">Géographie</span>
                            <i class="fas fa-chevron-right ml-auto text-xs"></i>
                        </a>
                        <ul class="dropdown-content">
                            <li><a href="#" class="block px-3 py-1.5 text-gray-600 hover:bg-blue-50 hover:text-blue-700 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white text-sm">Pays/Ville</a></li>
                        </ul>
                    </li>
                    <li class="relative">
                        <a href="#" class="dropdown-toggle flex items-center p-2 rounded-md text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition duration-200 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="w-5 text-blue-500 dark:text-blue-300">🏅</span>
                            <span class="ml-2 text-sm">Récomp. & Rec.</span>
                            <i class="fas fa-chevron-right ml-auto text-xs"></i>
                        </a>
                        <ul class="dropdown-content">
                            <li><a href="#" class="block px-3 py-1.5 text-gray-600 hover:bg-blue-50 hover:text-blue-700 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white text-sm">Nomination</a></li>
                            <li><a href="#" class="block px-3 py-1.5 text-gray-600 hover:bg-blue-50 hover:text-blue-700 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white text-sm">Décoration</a></li>
                        </ul>
                    </li>
                    <li class="relative">
                        <a href="#" class="dropdown-toggle flex items-center p-2 rounded-md text-gray-600 hover:bg-blue-50 hover:text-blue-700 transition duration-200 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white">
                            <span class="w-5 text-blue-500 dark:text-blue-300">🏢</span>
                            <span class="ml-2 text-sm">Organisation</span>
                            <i class="fas fa-chevron-right ml-auto text-xs"></i>
                        </a>
                        <ul class="dropdown-content">
                            <li><a href="#" class="block px-3 py-1.5 text-gray-600 hover:bg-blue-50 hover:text-blue-700 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white text-sm">Structure</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="mt-auto pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-gray-500 text-xs dark:text-gray-400">Vous êtes connecté en tant qu'administrateur.</p>
                    <p class="font-medium text-gray-800 text-sm dark:text-gray-100">Bienvenue <?php echo htmlspecialchars($_SESSION['admin_nom_complet']); ?> !</p>
                    <a href="index.php?controller=auth&action=logout" class="mt-2 inline-block px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-200 text-sm">Déconnexion</a>
                </div>
            </div>
        </div>
    </aside>
</div>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const elements = {
            toggleButton: document.getElementById('toggleSidebar'),
            closeButton: document.getElementById('closeSidebar'),
            sidebar: document.getElementById('sidebar'),
            themeToggle: document.getElementById('themeToggle'),
            body: document.getElementById('body')
        };

        if (!elements.toggleButton || !elements.sidebar || !elements.closeButton) {
            console.error('Éléments DOM manquants :', {
                toggleButton: !!elements.toggleButton,
                sidebar: !!elements.sidebar,
                closeButton: !!elements.closeButton
            });
            return;
        }

        function updateSidebarState() {
            const isHidden = elements.sidebar.classList.contains('-translate-x-full');
            const toggleIcon = elements.toggleButton.querySelector('i');
            const closeIcon = elements.closeButton.querySelector('i');

            toggleIcon.classList.toggle('fa-bars', isHidden);
            toggleIcon.classList.toggle('fa-times', !isHidden);
            closeIcon.classList.toggle('fa-bars', isHidden);
            closeIcon.classList.toggle('fa-times', !isHidden);
        }

        function toggleSidebar() {
            // Remove lg:translate-x-0 to allow toggling on large screens
            elements.sidebar.classList.toggle('lg:translate-x-0');
            elements.sidebar.classList.toggle('-translate-x-full');
            updateSidebarState();
        }

        elements.toggleButton.addEventListener('click', toggleSidebar);
        elements.closeButton.addEventListener('click', toggleSidebar);

        function handleResize() {
            const isSmallScreen = window.innerWidth < 1024;
            elements.sidebar.classList.toggle('-translate-x-full', isSmallScreen);
            elements.sidebar.classList.toggle('lg:translate-x-0', !isSmallScreen);
            updateSidebarState();
        }

        // Initialize sidebar state
        handleResize();
        window.addEventListener('resize', handleResize);

        // Gestion des dropdowns dans la sidebar (clic)
        document.querySelectorAll('#sidebar .relative').forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            const content = dropdown.querySelector('.dropdown-content');
            if (toggle && content) {
                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    const isActive = content.classList.contains('active');
                    document.querySelectorAll('#sidebar .dropdown-content').forEach(other => other.classList.remove('active'));
                    content.classList.toggle('active', !isActive);
                });
            }
        });

        // Gestion du menu profil (survol ou clic avec synchronisation)
        const profileToggle = document.querySelector('header .group button');
        const profileDropdown = document.querySelector('.profile-dropdown');
        if (profileToggle && profileDropdown) {
            profileToggle.addEventListener('mouseover', () => profileDropdown.classList.add('active'));
            profileToggle.addEventListener('mouseout', () => {
                setTimeout(() => {
                    if (!profileDropdown.matches(':hover')) profileDropdown.classList.remove('active');
                }, 200);
            });
            profileDropdown.addEventListener('mouseover', () => profileDropdown.classList.add('active'));
            profileDropdown.addEventListener('mouseout', () => profileDropdown.classList.remove('active'));
            profileToggle.addEventListener('click', (e) => {
                e.preventDefault();
                profileDropdown.classList.toggle('active');
            });
            document.addEventListener('click', (e) => {
                if (!profileToggle.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.remove('active');
                }
            });
        }

        // Gestion du thème
        if (elements.themeToggle) {
            elements.themeToggle.addEventListener('click', () => {
                elements.body.classList.toggle('dark');
                const icon = elements.themeToggle.querySelector('i');
                if (icon) {
                    if (elements.body.classList.contains('dark')) {
                        icon.classList.remove('fa-adjust');
                        icon.classList.add('fa-sun');
                    } else {
                        icon.classList.remove('fa-sun');
                        icon.classList.add('fa-adjust');
                    }
                }
            });
        }
    });
</script>
</body>
</html>