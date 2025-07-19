<!-- views/experience/experience_delete_confirm.view.php -->
<?php
$experiences = $experiences ?? [];
$structures = $structures ?? [];
$experience = $experience ?? null;
?>
<h2>Confirmation de suppression</h2>

<p>Êtes-vous sûr(e) de vouloir supprimer cette expérience ?</p>

<form action="index.php?controller=experience&action=delete&id=<?= $experience->getId() ?>" method="post">
    <button type="submit" class="btn btn-danger">Oui, supprimer</button>
    <a href="index.php?controller=experience&action=listByDignitaire&id=<?= $experience->getDignitaireId() ?>" class="btn btn-secondary">Annuler</a>
</form>
