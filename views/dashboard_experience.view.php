<?php
$experiences = $experiences ?? [];
$dignitaires = $dignitaires ?? [];
?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Expériences professionnelles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
        }

        .container-box {
            max-width: 95%;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,64,128,0.1);
        }

        .section-title-full {
            background-color: #047857;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border-radius: 6px;
            margin-bottom: 25px;
        }

        .top-left-btn {
            margin-bottom: 20px;
        }

        .btn-secondary {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            color: #047857;
            background-color: #ccc;
            border: none;
            transition: background 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #bbb;
        }

        .btn-success {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            color: white;
            background-color: #047857;
            border: none;
            transition: background 0.3s ease;
        }

        .btn-success:hover {
            background-color: #074b38ff;
        }

        .search-form {
            margin-bottom: 25px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-form input[type="text"] {
            padding: 10px 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            flex: 1;
            min-width: 200px;
        }

        .search-form button {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            background-color: #0077cc;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .search-form button:hover {
            background-color: #005fa3;
        }

        .btn-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table thead {
            background-color: #047857;
            color: white;
        }

        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            border-right: none;
        }

        .table th {
            border-bottom: 2px solid #004080;
        }

        .table td {
            background-color: #fff;
        }

        .table tbody tr:hover td {
            background-color: #f1f8ff !important;
        }

        .table thead tr:hover th {
            background-color: #047857;
        }

        .btn-primary, .btn-danger {
            padding: 6px 10px;
            font-size: 14px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #047857;
            margin-right: 18px;
        }

        .btn-danger {
            background-color: #0077cc;
        }

        .btn-primary:hover {
            background-color: #024f39ff;
        }

        .btn-danger:hover {
            background-color: #005fa3;
        }

        .alert {
            background-color: #e9f1fb;
            padding: 15px;
            border-left: 6px solid #004080;
            margin-top: 20px;
            border-radius: 6px;
            color: #004080;
        }

        .search-add-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

    </style>
</head>
<body>
<div class="container-box">
    <!-- BOUTON RETOUR EN HAUT À GAUCHE -->
    <div class="top-left-btn">
        <a href="index.php?controller=dignitaire&action=index" class="btn-secondary">
            ←
        </a>
    </div>

    <!-- TITRE -->
    <h2 class="section-title-full">Expériences professionnelles du dignitaire</h2>

    <!-- ZONE DE RECHERCHE ET AJOUT SUR UNE LIGNE -->
    <div class="search-add-row">
        <form method="get" action="index.php" style="display: flex; gap: 10px; align-items: center;">
            <input type="hidden" name="controller" value="experience">
            <input type="hidden" name="action" value="listByDignitaire">

            <input type="text" name="recherche_nom" placeholder="Rechercher par nom..." list="suggestions" />
            <datalist id="suggestions">
                <?php foreach ($dignitaires as $d) : ?>
                    <option value="<?= htmlspecialchars($d->getNom()) ?>"></option>
                <?php endforeach; ?>
            </datalist>

            <button type="submit">Rechercher</button>
        </form>



        <a href="index.php?controller=experience&action=addForm&id=<?= $experiences[0]->getDignitaireId() ?>" class="btn-success">
            <i class="bi bi-plus-circle"></i> Ajouter
        </a>
    </div>

    <!-- TABLEAU -->
    <?php if (count($experiences) > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Poste occupé</th>
                    <th>Institution</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($experiences as $exp): ?>
                    <tr>
                        <td><?= htmlspecialchars($exp->getIntitule()) ?></td>
                        <td><?= htmlspecialchars($exp->getStructureNom()) ?></td>
                        <td><?= htmlspecialchars($exp->getDateDebut() ?? '') ?></td>
                        <td><?= htmlspecialchars($exp->getDateFin() ?? '') ?></td>
                        <td>
                            <a href="index.php?controller=experience&action=editForm&id=<?= $exp->getId() ?>" class="btn-primary">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="index.php?controller=experience&action=delete&id=<?= $exp->getId() ?>" class="btn-danger" onclick="return confirm('Confirmer la suppression ?')">
                                <i class="bi bi-trash3"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert">Aucune expérience enregistrée.</div>
    <?php endif; ?>
</div>
</body>
</html>
