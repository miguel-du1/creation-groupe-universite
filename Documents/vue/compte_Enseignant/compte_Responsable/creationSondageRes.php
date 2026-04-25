<?php
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: index.php?page=login$auth=false');
    exit();
}

// Verifier le role du compte
$rolesAutorises = ['Admin', 'Responsable'];
if (!in_array($_SESSION['role_compte'] ?? '', $rolesAutorises, true)) {
    http_response_code(403);
    echo "Accès interdit pour votre compte";
    exit();
}

require_once 'modele/critere.php';
$liste_criteres = Critere::getAllCriteres();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/component_style/buttonEditer.css">
    <link rel="stylesheet" href="css/component_style/buttonExporter.css">
    <link rel="stylesheet" href="css/component_style/buttonEnregistrer.css">
    <link rel="stylesheet" href="css/component_style/buttonSupprimer.css">
    <link rel="icon" type="image/png" href="assets/iconPS.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    <title>Création d’un sondage</title>
</head>
<style>
    body{ overflow: auto; }

    .main_content_panel{
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        flex: 1;
        gap: 50px;
        padding: 50px 50px 30px 50px;
        min-height: 100vh;
        box-sizing: border-box;
    }

    .main_content_panel .nav_content{
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .main_content_panel .nav_content form{
        display: flex;
        align-items: center;
        height: 40px;
        width: 40px;
    }

    .main_content_panel .nav_content form button{
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        background-color: #5D0632;
        border-radius: 50%;
        outline: none;
        border: none;
        cursor: pointer;
        transition: all .2s ease;
    }

    .main_content_panel .nav_content form button i{
        display: flex;
        align-items: center;
        color: #fff;
        transform: scale(1.3);
        transition: all .1s ease;
    }

    .main_content_panel .nav_content form button:hover{
        background-color: #fff;
        border: #5D0632 solid 1px;
    }

    .main_content_panel .nav_content form button:hover i{
        color: #5D0632;
    }

    .main_content_panel .nav_content h1{
        background-color: #fff;
        color: #5D0632;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        margin: 0;
    }

    .layout_panel_content{
        width: calc(100% - 50px);
        height: calc(100% - 50px);
        margin: 0 auto;
        padding: 24px;
    }

    .layout_panel_content form{
        display: flex;
        flex-direction: column;
        gap: 30px;
        color: #5D0632;
        width: 100%;
        height: 100%;
    }

    .row_2_col_titre {
        display: flex;
        justify-items: stretch;
        align-items: center;
        gap: 20px;
    }

    .row_2_col_titre input {
        flex: 1;
    }

    .row_2_col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        justify-items: stretch;
        align-items: center;
    }

    .row_2_col label, .row_2_col_titre label{
        font-weight: 600;
        font-size: 18px;
        text-align: center;
    }

    .layout_panel_content input[type="text"],
    .layout_panel_content input[type="date"]{
        width: auto;
        padding: 10px 20px;
        border: 1px solid #d6d6d6;
        border-radius: 10px;
        outline: none;
        font-size: 14px;
        background: #fff;
    }

    .layout_panel_content input[type="text"]:focus,
    .layout_panel_content input[type="date"]:focus{
        border-color: #5D0632;
        box-shadow: 0 0 0 4px #5d06321a;
    }

    .layout_panel_content fieldset{
        border: 1px solid #e6e6e6;
        border-radius: 14px;
        padding: 14px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .layout_panel_content legend{
        padding: 0 10px;
        font-weight: 700;
        font-size: 18px;
    }

    .critere_a_choisir{
        position: relative;
    }

    .critere_a_choisir input[type="checkbox"]{
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .critere_a_choisir label{
        display: block;
        padding: 12px 12px 12px 44px;
        border: 1px solid #e6e6e6;
        border-radius: 14px;
        background: #fff;
        cursor: pointer;
        transition: transform .08s ease, border-color .15s ease, box-shadow .15s ease, background .15s ease;
        min-height: 72px;
    }

    .critere_a_choisir label::before{
        content: "";
        position: absolute;
        left: 14px;
        top: 14px;
        width: 20px;
        height: 20px;
        border-radius: 6px;
        border: 2px solid #cfcfcf;
        background: #fff;
        transition: all .15s ease;
    }

    .critere_a_choisir label::after{
        content: "";
        position: absolute;
        left: 20px;
        top: 20px;
        width: 8px;
        height: 4px;
        border-left: 3px solid transparent;
        border-bottom: 3px solid transparent;
        transform: rotate(-45deg);
        opacity: 0;
        transition: opacity .15s ease;
    }

    .critere_a_choisir label span{
        display: block;
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 6px;
    }

    .critere_a_choisir label p{
        margin: 0;
        font-size: 13px;
        line-height: 1.35;
        color: #666;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    .critere_a_choisir label:hover{
        border-color: #cfc7ff;
        box-shadow: 0 10px 22px rgba(0,0,0,0.06);
        transform: translateY(-1px);
    }

    .critere_a_choisir input[type="checkbox"]:focus-visible + label{
        box-shadow: 0 0 0 4px rgba(123, 97, 255, 0.18);
        border-color: #5D0632;
    }

    .critere_a_choisir input[type="checkbox"]:checked + label{
        border-color: #5D0632;
        background: #5d06321a;
    }

    .critere_a_choisir input[type="checkbox"]:checked + label::before{
        border-color: #5D0632;
        background: #5D0632;
    }

    .critere_a_choisir input[type="checkbox"]:checked + label::after{
        border-left-color: #fff;
        border-bottom-color: #fff;
        opacity: 1;
    }

    .layout_panel_content button[type="submit"]{
        width: 100%;
        padding: 10px 20px;
        border: none;
        outline: none;
        border-radius: 12px;
        background: #5D0632;
        color: #fff;
        font-weight: 500;
        font-size: 18px;
        cursor: pointer;
        transition: transform .08s ease, filter .15s ease;
    }

    .layout_panel_content button[type="submit"]:hover{
        filter: brightness(1.5);
    }

    .layout_panel_content button[type="submit"]:active{
        transform: translateY(1px);
    }

    @media (max-width: 800px){
        .row_2_col,
        .row_2_col:has(input[type="date"]){
            grid-template-columns: 1fr;
        }

        .layout_panel_content fieldset{
            grid-template-columns: 1fr;
        }

        .layout_panel_content button[type="submit"]{
            width: 100%;
        }
    }
</style>
<body>
    <div class="main_layout_panel">
        <?php 
            require_once("controleur/c_sidebar.php");
        ?>
        <div class="main_content_panel">
            <div class="nav_content">
                <form action="index.php" id="form_button_retour">
                    <input type="hidden" name="page" value="sondageRes">
                    <button type="submit">
                        <span><i class="fi fi-br-angle-small-left"></i></span>
                    </button>
                </form>
                <h1>Création d’un sondage</h1>
            </div>
            <div class="layout_panel_content">
                <form action="index.php?page=creerSondageRes" method="post">
                    <input type="hidden" name="personnesResponsable" value="<?php echo $_SESSION['user_id'] ?>">
                    <div class="row_2_col_titre">
                        <label>Titre du sondage : </label>
                        <input type="text" name="titre" required>
                    </div>
                    <div class="row_2_col">
                        <label>Date de début</label>
                        <label>Date de fin</label>
                        <input type="date" name="dateDebut" value="<?php echo date('Y-m-d'); ?>" required>
                        <input type="date" name="dateFin" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                    </div>
                    <fieldset>
                        <legend>Liste des critères à ajouter dans le sondage</legend>
                        <?php foreach ($liste_criteres as $critere): ?>
                            <div class="critere_a_choisir">
                                <input type="checkbox" name="criteres[]" value="<?php echo $critere->get("idCritere"); ?>">
                                <label>
                                    <span><?php echo $critere->get("nomCritere"); ?></span>
                                    <p>
                                        <?php
                                            $json = $critere->get("optionsCritere");
                                            $arr = json_decode($json, true);
                                            foreach ($arr as $item) {
                                                echo 'option : ' . htmlspecialchars($item['option'] ?? '') . '<br>';
                                            }
                                        ?>
                                    </p>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </fieldset>
                    <button type="submit">Publier le sondage</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>