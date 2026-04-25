<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="icon" type="image/png" href="assets/iconPS.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <title>Université Paris-Saclay - Accueil</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Lexend", sans-serif;
            height: 100vh;
            width: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            
            /* --- IMAGE DE FOND --- */
            background: url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2940&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        /* Calque blanc semi-transparent par-dessus l'image pour l'éclaircir comme sur la photo */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.4); /* Ajustez l'opacité (0.4) pour plus ou moins de transparence */
            z-index: 0;
        }

        /* --- Barre de Navigation Flottante (Pill) --- */
        .navbar {
            position: relative;
            z-index: 10;
            margin-top: 30px;
            background-color: white;
            padding: 10px 20px;
            border-radius: 50px; /* Forme très arrondie */
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 90%;
            max-width: 900px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            height: 5vh;
        }
        
        /* Si vous n'avez pas d'image logo, voici un style texte de remplacement */
        .nav-logo-img {
            color: #581428;
            font-weight: 700;
            font-size: 14px;
            line-height: 1.2;
            text-transform: uppercase;
            height: 100%;
            display: flex;
            align-items: center;
        }
        .nav-logo-img > img {
            height: 70%;
            margin-left: 10px;
        }
        .nav-logo-text span {
            display: block;
            font-size: 10px;
            font-weight: 400;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
        }

        /* Style des boutons */
        .btn {
            text-decoration: none;
            padding: 10px 25px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            transition: transform 0.2s, background-color 0.2s;
        }

        .btn-register {
            background-color: #581428; /* Bordeaux */
            color: white;
        }

        .btn-login {
            background-color: #581428; /* Bordeaux */
            color: white;
        }

        .btn:hover {
            background-color: #7a1c36;
        }

        /* --- Grand Logo Central --- */
        .center-content {
            position: relative;
            z-index: 10;
            flex-grow: 1; /* Prend toute la place restante */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #581428;
            width: 100%;
        }

        .main-logo-img > img {
            width: 20%;
        }

        /* Responsive Mobile */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
                border-radius: 20px;
                padding: 20px;
            }
            .main-logo-text { font-size: 40px; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-logo-img">
            <img src="assets/logo_white.png" alt="Logo Universite" draggable="false">
        </div>
        <div class="nav-buttons">
            <a href="index.php?page=register" class="btn btn-register">S'inscrire</a>
            <a href="index.php?page=login" class="btn btn-login">Se connecter</a>
        </div>
    </nav>
    <div class="center-content">
        <div class="main-logo-img">
            <img src="assets/logo_white.png" alt="Logo Universite" draggable="false">
        </div>
    </div>
</body>
</html>