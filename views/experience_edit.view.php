<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une expérience</title>

    <style>
        body {
            background-color: #f3f6f9;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 40px;
        }

        .form-container {
            max-width: 600px;
            background: #ffffff;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .form-header {
            background-color: #047857;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 22px;
            font-weight: bold;
        }

        .form-body {
            padding: 30px 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #047857;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #047857;
        }

        .form-buttons {
         display: flex;
         justify-content: space-between;
         margin-top: 30px;
        }

        .btn-submit {
            background-color: #047857;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #044a36ff;
        }

        .btn-cancel {
         background-color: #cccccc;
         color: #047857;
         border: none;
         padding: 12px 20px;
         border-radius: 8px;
         font-size: 16px;
         cursor: pointer;
         text-decoration: none;
         transition: background-color 0.3s ease;
        }

        .btn-cancel:hover {
         background-color: #bbbbbb;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <div class="form-header">Modifier une expérience</div>

        <div class="form-body">
            <form action="index.php?controller=experience&action=update&id=<?= $experience->getId() ?>" method="post">
                <div class="form-group">
                    <label for="poste">Poste occupé</label>
                    <input type="text" id="poste" name="poste" value="<?= htmlspecialchars($experience->getIntitule()) ?>" required>
                </div>

                <div class="form-group">
                    <label for="institution">Institution</label>
                    <select id="institution" name="institution" required>
                        <?php foreach ($structures as $structure): ?>
                            <option value="<?= $structure->getId() ?>" <?= $structure->getId() == $experience->getStructureId() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($structure->getNom()) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date_debut">Date de début</label>
                    <input type="date" id="date_debut" name="date_debut" value="<?= $experience->getDateDebut() ?>" required>
                </div>

                <div class="form-group">
                    <label for="date_fin">Date de fin</label>
                    <input type="date" id="date_fin" name="date_fin" value="<?= $experience->getDateFin() ?>">
                </div>

                <div class="form-buttons">
                <a href="index.php?controller=experience&action=listByDignitaire&id=<?= $experience->getDignitaireId() ?>" class="btn-cancel">Annuler</a>
                <button type="submit" class="btn-submit">Enregistrer</button>

            </div>
            </form>
        </div>
    </div>

</body>
</html>
