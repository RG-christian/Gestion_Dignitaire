<?php
require_once __DIR__ . '/../config/database.php';
require 'layout.php';

$pdo = getDatabaseConnection();
if (!isset($langues)) $langues = [];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Langues</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
            background-color: #f9f9f9;
        }
        h1, h2 {
            color: #333;
        }
        form {
            margin-bottom: 2rem;
            background: #fff;
            padding: 1rem;
            border: 1px solid #ccc;
            max-width: 400px;
        }
        input[type="text"] {
            padding: 0.5rem;
            width: calc(100% - 1rem);
            margin-top: 0.5rem;
        }
        button {
            padding: 0.5rem 1rem;
            margin-top: 1rem;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        table {
            width: 60%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            padding: 0.75rem;
            border: 1px solid #ccc;
            text-align: left;
        }
        a.supprimer {
            color: red;
            text-decoration: none;
            margin-left: 1rem;
        }
        a.supprimer:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<h1>Gestion des Langues</h1>

<h2>Ajouter une langue</h2>
<form action="index.php?controller=langues&action=ajouter" method="post">
    <label for="langue">Langue :</label><br>
    <input type="text" id="langue" name="langue" required>
    <br>
    <button type="submit">Ajouter</button>
</form>

<h2>Liste des Langues</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Langue</th>
        <th>Action</th>
        <?php foreach($langues as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['nom']) ?></td>
        <td>
            <a href="index.php?controller=langues&action=supprimer&id=<?= $row['id'] ?>" class="supprimer" onclick="return confirm('Supprimer cette langue ?')">Supprimer</a>
        </td>
    </tr>
    <?php endforeach; ?>

</table>
</body>
</html>
