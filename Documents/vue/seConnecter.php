<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="css/styleSeConnecter.css">
    <link rel="icon" type="image/png" href="assets/iconPS.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <title>Se Connecter</title>
</head>
<body>
    <div class="login-card">
        <h1>Connexion</h1>
        <?php
            $erreur = isset($_GET["erreur"]) ? $_GET["erreur"] : "" ;
            switch ($erreur) {
                case 'incorrect':
                    echo "<p class=\"message\">Mot de passe incorrect<p>";
                    break;
                case 'inexiste':
                    echo "<p class=\"message\">Compte inexistant<p>";
                    break;
            }
        ?>
        <form action="index.php?page=login" method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>
            <button type="submit" class="btn-submit">Se connecter</button>
        </form>
        <a href="index.php?page=register" class="register-link">Pas encore de compte ? Inscrivez-vous !</a>
    </div>
</body>
</html>