<?php 
    session_start();

    $old = $_SESSION['old'] ?? [];
    $error_message = $_SESSION['error_message'] ?? '';
    $success_message = $_SESSION['success_message'] ?? '';

    require_once("modele/promotion.php");
    $lesPromotions = Promotion::getAllPromotion();
    require_once("modele/pays.php");
    $lesPays = Pays::getAllPays();

    unset($_SESSION['error_message'], $_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="icon" type="image/png" href="assets/iconPS.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <title>Créer un compte</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Lexend", sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2940&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        @keyframes dropIn {
            0% {
                opacity: 0;
                transform: translateY(-14px);
            }
            60% {
                opacity: 1;
                transform: translateY(4px);
            }
            100% {
                transform: translateY(0);
            }
        }

        @keyframes dropUp {
            0% {
                opacity: 0;
                transform: translate(-50%, -40%);
            }
            60% {
                opacity: 1;
                transform: translate(-50%, -52%);
            }
            100% {
                transform: translate(-50%, -50%);
            }
        }

        /* --- Navbar Blanche --- */
        .navbar {
            background-color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 100px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin: 10px 20px 10px 20px;
            z-index: 100;
            animation: dropIn 700ms ease-out both;
        }

        /* Nouveau Style du Logo */
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .logo img {
            margin-left: 10px;
            height: 30px; /* Taille optimale pour la barre */
            width: auto;
            transition: transform 0.3s ease;
        }

        .logo img:hover {
            transform: scale(1.05);
        }

        .btn-login-nav {
            background-color: #581428;
            color: #ffffff;
            text-decoration: none;
            padding: 8px 25px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-login-nav:hover { background-color: #7a1c36; }

        /* --- Conteneur Glassmorphism --- */
        .glass-container {
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(5px);
            border-radius: 30px;
            border: #fff solid 2px;
            padding: 50px;
            width: 90%;
            max-width: 1000px;
            margin: 0 auto 50px auto;
            box-shadow: 0 10px 10px rgba(0,0,0,0.1);
            position: absolute;
            transform: translate(-50%, -50%);
            top: 50vh;
            left: 50vw;
            animation: dropUp 700ms ease-out both;
        }

        h1 {
            text-align: center;
            color: #5D0632;
            margin-bottom: 40px;
            font-weight: 700;
        }

        /* --- Grille du formulaire --- */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 50px;
            position: relative;
            height: 100%;
        }

        .col-left, .col-right {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 30px;
            position: relative;
        }

        .col-right::before {
            content: "";
            position: absolute;
            border-left: #5D0632 solid 3px;
            top: 0;
            left: -5%;
            height: 100%;
        }

        .info-personnel {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .coordonnes {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* --- Style des Champs (Pillules) --- */
        input, select {
            width: 100%;
            height: auto;
            padding: 10px 20px;
            border: 1px solid #5D0632;
            border-radius: 50px;
            background: #fff;
            box-sizing: border-box;
            color: #333;
            outline: none;
            transition: border-color 0.1s;
        }
        input:focus { 
            border: 1px solid #fff;
            outline: #EFE6E3 solid 3px;
        }

        h3 {
            color: #581428;
            font-weight: 600;
            font-size: 18px;
        }

        /* Layout des lignes */
        .row-2 { 
            display: flex; 
            gap: 10px; 
        }
        .row-3 { 
            display: flex; 
            gap: 10px; 
            align-items: center;
        }
        .row-4 { 
            display: flex; 
            gap: 10px; 
        }
        .row-3 input { flex: 1; }

        label.date-label {
            display: block;
            font-size: 16px;
            color: #581428;
            font-weight: 300;
        }

        /* Bouton Créer */
        .btn-create {
            background-color: #581428;
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 20px;
            transition: 0.3s;
        }
        .btn-create:hover { background-color: #7a1c36; }

        .note {
            color: #ff0000ff;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
        }
        
        /* Messages PHP */
        .success { color: green; text-align: center; margin-bottom: 15px; font-weight: 600; }
        .error { color: red; text-align: center; font-weight: 300; }

        @media (max-width: 800px) {
            .form-grid { flex-direction: column; }
            .col-left, .col-right { width: 100%; }
            .divider { display: none; }
            .navbar { padding: 10px 20px; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">
            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c4/Logo_Universit%C3%A9_Paris-Saclay_2019-12.svg" alt="Logo Université Paris-Saclay">
        </a>
        <a href="index.php?page=login" class="btn-login-nav">Se connecter</a>
    </nav>
    <div class="glass-container">
        <h1>Créer un compte</h1>
        <form id="formCreerCompte" action="index.php?page=register" method="post">
            <div class="form-grid">
                <div class="col-left">
                    <input type="text" name="username" placeholder="Nom d'utilisateur" value="<?php echo $old['username'] ?? ""; ?>" required>
                    <input type="password" name="password" placeholder="Mot de passe" value="<?php echo $old['password'] ?? ""; ?>" required>
                    <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" value="<?php echo $old['confirm'] ?? ""; ?>" required>
                    <select name="role" required>
                        <option value="" disabled <?php echo (empty($old['role']) ? "selected" : ""); ?>>Choisir le rôle du compte</option>
                        <option value="Etudiant" <?php echo ($old['role'] ?? '') === 'etudiant' ? "selected" : ""; ?>>Étudiant</option>
                        <option value="Enseignant" <?php echo ($old['role'] ?? '') === 'enseignant' ? "selected" : ""; ?>>Enseignant</option>
                    </select>
                    <?php if (!empty($error_message)) : ?>
                        <p class="error"><?= htmlspecialchars($error_message) ?></p>
                    <?php endif; ?>
                    <button type="submit" class="btn-create">Créer</button>
                </div>
                <div class="col-right">
                    <div class="info-personnel">
                        <h3>Information personnelle</h3>
                        <div class="row-2">
                            <input type="text" name="nom" placeholder="Nom*" value="<?php echo $old['nom'] ?? ""; ?>" required>
                            <input type="text" name="prenom" placeholder="Prénom*" value="<?php echo $old['prenom'] ?? ""; ?>" required>
                        </div>
                        <div class="row-3">
                            <label class="date-label">Date de naissance</label>
                            <input type="date" name="dateNaissance" value="<?php echo $old['dateNaissance'] ?? ""; ?>" required>
                        </div>
                        <select name="promotion">
                            <option value="" disabled <?php echo (empty($old['promotion']) ? "selected" : ""); ?>>Année d’entrée à l’université</option>
                            <?php 
                                foreach ($lesPromotions as $promo) {
                                    $anneePromo = $promo->get("anneePromotion");
                                    $idPromo = $promo->get("idPromotion");
                                    $isSelected = ($old['promotion'] ?? '') === $idPromo;
                                    if ($isSelected) echo "<option value=\"$idPromo\" selected>$anneePromo</option>";
                                    else echo "<option value=\"$idPromo\">$anneePromo</option>";
                                }
                            ?>
                        </select>
                        <div class="row-4">
                            <select name="sexe" required>
                                <option value="" disabled <?php echo (empty($old['sexe']) ? "selected" : ""); ?>>Sexe*</option>
                                <option value="M" <?php echo ($old['sexe'] ?? '') === 'M' ? "selected" : ""; ?>>Homme</option>
                                <option value="F" <?php echo ($old['sexe'] ?? '') === 'F' ? "selected" : ""; ?>>Femme</option>
                                <option value="autre" <?php echo ($old['sexe'] ?? '') === 'autre' ? "selected" : ""; ?>>Autre</option>
                            </select>
                            <select name="etatCivil" required>
                                <option value="" disabled <?php echo (empty($old['etatCivil']) ? "selected" : ""); ?>>État Civil*</option>
                                <option value="celibataire" <?php echo ($old['etatCivil'] ?? '') === 'celibataire' ? "selected" : ""; ?>>Célibataire</option>
                                <option value="marie" <?php echo ($old['etatCivil'] ?? '') === 'marie' ? "selected" : ""; ?>>Marié(e)</option>
                                <option value="pacse" <?php echo ($old['etatCivil'] ?? '') === 'pacse' ? "selected" : ""; ?>>Pacsé(e)</option>
                                <option value="concubinage" <?php echo ($old['etatCivil'] ?? '') === 'concubinage' ? "selected" : ""; ?>>En concubinage / Union libre</option>
                                <option value="divorce" <?php echo ($old['etatCivil'] ?? '') === 'divorce' ? "selected" : ""; ?>>Divorcé(e)</option>
                                <option value="veuf" <?php echo ($old['etatCivil'] ?? '') === 'veuf' ? "selected" : ""; ?>>Veuf / Veuve</option>
                                <option value="separe" <?php echo ($old['etatCivil'] ?? '') === 'separe' ? "selected" : ""; ?>>Séparé(e)</option>
                            </select>
                            <select name="nationalite" required>
                                <option value="" disabled <?php echo (empty($old['nationalite']) ? "selected" : ""); ?>>Nationalité*</option>
                                <?php 
                                    foreach ($lesPays as $pays) {
                                        $nomPays = $pays->get("nomPays");
                                        $idPays = $pays->get("idPays");
                                        $isSelected = ($old['nationalite'] ?? '') === $idPays;
                                        if ($isSelected) echo "<option value=\"$idPays\" selected>$nomPays</option>";
                                        else echo "<option value=\"$idPays\">$nomPays</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <input type="number" name="numSocial" placeholder="Numéro Sécurité Sociale" value="<?php echo $old['numSocial'] ?? ""; ?>" required>
                    </div>
                    <div class="coordonnes">
                        <h3>Coordonnées</h3>
                        <div class="row-2">
                            <input type="tel" pattern="^\+?[0-9 ]{6,20}$" name="tel" placeholder="Téléphone*" value="<?php echo $old['tel'] ?? ""; ?>" required>
                            <input type="email" name="email" placeholder="E-mail*" value="<?php echo $old['email'] ?? ""; ?>" required>
                        </div>
                        <input type="text" name="adresse" placeholder="Adresse*" value="<?php echo $old['adresse'] ?? ""; ?>" required>
                        <div class="row-2">
                            <input type="number" name="codePostal" placeholder="Code postal*" value="<?php echo $old['codePostal'] ?? ""; ?>" required>
                            <input type="text" name="ville" placeholder="Ville*" value="<?php echo $old['ville'] ?? ""; ?>" required>
                            <select name="pays" required>
                                <?php 
                                    foreach ($lesPays as $pays) {
                                        $nomPays = $pays->get("nomPays");
                                        $isSelected = ($old['pays'] ?? '') === $nomPays;
                                        if ($isSelected) echo "<option value=\"$nomPays\" selected>$nomPays</option>";
                                        else echo "<option value=\"$nomPays\">$nomPays</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="note">Les champs marqués d'un astérisque (*) sont obligatoires.</div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>