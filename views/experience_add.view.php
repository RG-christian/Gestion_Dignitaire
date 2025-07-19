<?php
$experiences = $experiences ?? [];
$structures = $structures ?? [];
$experience = $experience ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une expérience</title>

</head>
<body>
<style>
    .form-container {
        max-width: 700px;
        margin: 30px auto;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .form-header {
        background-color: #047857;
        color: white;
        text-align: center;
        padding: 20px;
        font-size: 22px;
        font-weight: bold;
        letter-spacing: 0.5px;
    }

    .form-body {
        padding: 30px 40px;
    }

    .form-body .form-group {
        margin-bottom: 20px;
    }

    .form-body label {
        display: block;
        color: #047857;
        font-weight: bold;
        margin-bottom: 6px;
    }

    .form-body input,
    .form-body select {
        width: 100%;
        padding: 10px 12px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #f9f9f9;
        transition: border-color 0.3s ease;
    }

    .form-body input:focus,
    .form-body select:focus {
        outline: none;
        border-color: #047857;
        background-color: #fff;
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
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        text-decoration: none;
    }

    .btn-submit:hover {
        background-color: #044231ff;
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


<div class="form-container">
    <div class="form-header">Ajouter une expérience</div>

    <div class="form-body">
        <form action="index.php?controller=experience&action=saveNew&id=<?= $_GET['id'] ?>" method="post">
            <div class="form-group">
                <label for="poste">Poste occupé</label>
                <input type="text" class="input-style" id="poste" name="poste" required>
            </div>

            <div class="form-group">
                <label for="institution">Institution</label>
                <select class="input-style" id="institution" name="institution" required>
                    <?php foreach ($structures as $structure): ?>
                        <option value="<?= $structure->getId() ?>">
                            <?= htmlspecialchars($structure->getNom()) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="form-group">
                <label for="date_debut">Date de début</label>
                <input type="date" class="input-style" id="date_debut" name="date_debut" required>
            </div>

            <div class="form-group">
                <label for="date_fin">Date de fin</label>
                <input type="date" class="input-style" id="date_fin" name="date_fin">
            </div>

            <div class="form-buttons">
                <a href="index.php?controller=experience&action=listByDignitaire&id=<?= $_GET['id'] ?>" class="btn-cancel">Annuler</a>
                <button type="submit" class="btn-submit">Enregistrer</button>

            </div>
        </form>
    </div>
</div>
</body>
</html>
