<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: index.php?page=login');
    exit();
}
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
    <title>Acceuil</title>
</head>
<style>
    .main_content_panel {
        background: url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2940&auto=format&fit=crop') no-repeat center center fixed;
        background-size: cover;
        position: relative;
        flex: 1;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .main_content_panel::before {
        content: "";
        background-color: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(5px);
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }

    .main_content_panel > img {
        width: 30%;
        position: relative;
        z-index: 10;
        filter: drop-shadow(0 4px 10px rgba(255, 255, 255, 0.4));
    }
</style>
<body>
    <div class="main_layout_panel">
        <?php 
            require_once("controleur/c_sidebar.php");
        ?>
        <div class="main_content_panel">
            <img src="assets/logo_white.png" alt="logo Universite Paris-Saclay" draggable="false">
        </div>
    </div>
</body>
</html>