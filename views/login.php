<!DOCTYPE html>
<html>
<head>
    <title>Connexion Admin</title>
</head>
<body>
<h2>Connexion Administrateur</h2>
<?php if(isset($error)) echo '<p style="color:red;">'.$error.'</p>'; ?>
<form method="post" action="">
    <label>Nom d'utilisateur</label>
    <input type="text" name="username" required>
    <br>
    <label>Mot de passe</label>
    <input type="password" name="password" required>
    <br>
    <button type="submit">Se connecter</button>
</form>
</body>
</html>
