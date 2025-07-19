<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Administration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
<div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">Connexion administrateur</h2>
    <?php if (isset($error)): ?>
        <div class="mb-4 text-red-600 text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <label class="block mb-2 text-sm font-medium text-gray-700">Nom d'utilisateur</label>
        <input type="text" name="username" class="w-full px-3 py-2 mb-4 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        <label class="block mb-2 text-sm font-medium text-gray-700">Mot de passe</label>
        <input type="password" name="password" class="w-full px-3 py-2 mb-6 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Se connecter</button>
    </form>
</div>
</body>
</html>
