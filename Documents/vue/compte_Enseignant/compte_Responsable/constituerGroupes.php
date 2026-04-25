<?php
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: index.php?page=login&auth=false');
    exit();
}

// Verifier le role du compte
$rolesAutorises = ['Admin', 'Responsable'];
if (!in_array($_SESSION['role_compte'] ?? '', $rolesAutorises, true)) {
    http_response_code(403);
    echo "Accès interdit pour votre compte";
    exit();
}

require_once 'modele/promotion.php';
$liste_promotion = Promotion::getAllPromotion();

$promotionChercher = null;
$liste_etudiant_promo_chercher = [];

if (isset($_GET['promo'])) {
    $promotionChercher = Promotion::getPromotion($_GET['promo']);
    $liste_etudiant_promo_chercher = Promotion::getListeEtudiantsParPromo($_GET['promo']);
    $promotionChercher->set('listEtudiants', $liste_etudiant_promo_chercher);
}

require_once 'modele/groupe.php';
$liste_types_groupe = Groupe::getAllTypesGroupe();
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
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-chubby/css/uicons-regular-chubby.css'>
    <title>Constituer les groupes</title>
</head>
<style>
    .main_content_panel{
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        flex: 1;
        padding: 50px 50px 30px 50px;
        box-sizing: border-box;
    }

    .main_content_panel .nav_content{
        display: flex;
        align-items: center;
        margin-bottom: 50px;
    }

    .main_content_panel .nav_content h1{
        background-color: #fff;
        color: #5D0632;
        border-radius: 10px;
        font-weight: 600;
    }

    .main_content_panel .nav_content form {
        margin-left: 20px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 10px;

        select {
            background-color: #5D0632;
            padding: 5px 20px;
            color: #fff;
            border-radius: 100px;
            font-weight: bold;
            height: 40px;
            width: auto;
            font-size: 16px;
        }
    }

    .layout_panel_content{
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 20px;
        width: 100%;
        height: 100%;

        img {
            width: 100%;
            transform: scale(0.3);
        }
    }

    .liste_promo{
        height: 80vh;
        overflow: hidden;
        width: 20%;
        background: #fff;
        border: 1px solid #e9e9e9;
        border-radius: 10px;
        box-shadow: 0 10px 24px rgba(0,0,0,0.06);
        color: #5D0632;
        padding: 10px;
        position: relative;
    }

    .liste_promo_scroll{
        height: 100%;
        overflow: auto;
        border-radius: 10px;
    }

    .panel_critere{
        height: 100%;
        background: #fff;
        border: 1px solid #e9e9e9;
        border-radius: 10px;
        box-shadow: 0 10px 24px rgba(0,0,0,0.06);
        color: #5D0632;
        padding: 10px;
        max-height: 80vh;
        flex: 1;
    }

    .panel_critere .info_promotion {
        display: flex;
        justify-content: space-evenly;
        width: 100%;
        gap: 10px;
    }


    .panel_critere .info_promotion .etat_encours {
        background-color: #FFE599;
    }
    .panel_critere .info_promotion .etat_termine {
        background-color: #B6D7A8;
    }

    .panel_critere .info_promotion p {
        padding: 10px 20px;
        width: 100%;
        border: rgba(93, 6, 50, 0.5) solid 1px;
        border-radius: 5px;
        background-color: #fff;
        color: #5D0632;
        text-align: center;
    }

    .panel_critere .info_promotion p span{
        font-weight: 500;
    }

    /* ====== Container ====== */
    .panel_critere .liste_critere{
        max-width: 100%;
    }

    .input_feild{
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .input_type_groupe {
        display: flex;
        gap: 20px;
        align-items: center;
    }
    
    .input_type_groupe select{
        flex: 1;
        text-align: center;
        font-weight: 500;
        color: #5D0632;
    }

    /* ====== Label + Select ====== */
    .input_feild label{
        font-weight: 700;
        color: #5D0632;
        letter-spacing: .2px;
    }

    .input_feild select{
        width: 100%;
        padding: 10px 12px;
        border-radius: 12px;
        border: 1px solid rgba(93, 6, 50, .25);
        background: #fff;
        outline: none;
        transition: .2s ease;
    }

    .input_feild select:focus{
        border-color: #5D0632;
        box-shadow: 0 0 0 4px rgba(93, 6, 50, .12);
    }

    /* ====== Mode cards ====== */
    .mode-cards{
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    @media (max-width: 900px){
        .mode-cards{ grid-template-columns: 1fr; }
    }

    .mode{
        position: relative;
        display: block;
        cursor: pointer;
        padding: 14px 14px 14px 14px;
        border-radius: 14px;
        border: 1px solid rgba(93, 6, 50, .18);
        background: linear-gradient(180deg, rgba(93, 6, 50, .05), rgba(93, 6, 50, .01));
        transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
        user-select: none;
    }

    .mode:hover{
        transform: translateY(-1px);
        box-shadow: 0 10px 18px rgba(93, 6, 50, .10);
        border-color: rgba(93, 6, 50, .30);
    }

    /* Hide native radio */
    .mode input[type="radio"]{
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    /* Pill */
    .mode .pill{
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        color: #5D0632;
        background: rgba(93, 6, 50, .08);
        border: 1px solid rgba(93, 6, 50, .18);
    }

    /* Title + Desc */
    .mode .title{
        margin-top: 10px;
        font-weight: 800;
        color: #1d1d1f;
        line-height: 1.25;
    }

    .mode .desc{
        margin-top: 6px;
        color: #5a5a5a;
        font-size: 13px;
        line-height: 1.45;
    }

    .mode:has(input[type="radio"]:checked)::after{
        content: "✓";
        position: absolute;
        top: 12px;
        right: 12px;
        width: 28px;
        height: 28px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        font-weight: 900;
        color: #fff;
        background: #5D0632;
        box-shadow: 0 10px 16px rgba(93, 6, 50, .25);
    }

    /* ====== Buttons ====== */
    .button_panel_form{
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
        width: 100%;
        margin-top: 10px;
    }

    .button_panel_form button{
        appearance: none;
        border: none;
        cursor: pointer;
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 500;
        transition: .15s ease;
    }

    /* First button: primary */
    .button_panel_form button:first-child{
        background: #5D0632;
        color: #fff;
        box-shadow: 0 4px 10px rgba(93, 6, 50, .20);
    }

    .button_panel_form button:first-child:hover{
        transform: translateY(-1px);
        box-shadow: 0 6px 12px rgba(93, 6, 50, .28);
    }

    /* Second button: outline */
    .button_panel_form button:nth-child(2){
        background: transparent;
        color: #5D0632;
        border: 1px solid rgba(93, 6, 50, .35);
    }

    .button_panel_form button:nth-child(2):hover{
        background: rgba(93, 6, 50, .06);
        transform: translateY(-1px);
    }

    .button_panel_form a{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:10px 20px;
        border-radius:12px;
        text-decoration:none;
        font-weight:500;
        color:#5D0632;
        border:1px solid rgba(93, 6, 50, .35);
        background:transparent;
        transition:transform .15s ease, background .15s ease, box-shadow .15s ease;
    }

    .button_panel_form a:hover{
        background:rgba(93, 6, 50, .06);
        transform:translateY(-1px);
        box-shadow:0 6px 12px rgba(93, 6, 50, .12);
    }

    .button_panel_form a:active{
        transform:translateY(0);
        box-shadow:none;
    }

    .button_panel_form a:focus-visible{
        outline:3px solid rgba(93, 6, 50, .25);
        outline-offset:2px;
    }

    /* ====== HR (optional unify) ====== */
    .liste_critere hr{
        border: 0;
        border-top: 1px dashed #5D0632;
        opacity: .55;
        margin: 12px 0;
    }

    .analyse_resultat_apres_creation{
        display:grid;
        grid-template-columns:100px 1fr;
        gap:10px;
        align-items:stretch;
        min-height:420px;
        height:45vh;
        overflow:hidden;
    }

    .liste_groupes_crees{
        position: relative;
        top: auto;
        height: 100%;
        overflow: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .liste_groupes_crees form{margin:0}

    .liste_groupes_crees button{
        width: 100%;
        text-align: left;
        padding: 10px 12px;
        border: 1px solid #ececec;
        border-radius: 12px;
        background: #fafafa;
        cursor: pointer;
        font-weight: 600;
        color: #5D0632;
        text-align: center;
        transition: transform .06s ease, background .15s ease, border-color .15s ease;
    }

    .liste_groupes_crees button:hover{
        background:#f3f3f3;
        border-color:#dedede;
    }

    .liste_groupes_crees button:active{transform:scale(.98)}

    .liste_etudiants_par_groupe{
        height: 100%;
        overflow: auto;
    }

    .liste_etudiants_par_groupe table{
        width:100%;
        border-collapse:collapse;
        font-size:14px;
        table-layout: fixed;
    }

    .liste_etudiants_par_groupe thead td{
        position: sticky;
        top: 1px;
        z-index: 2;
        font-weight: 500;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: .4px;
        padding: 12px 10px;
        background: rgba(255, 244, 249, 1);
        border-bottom: 1px solid #ececec;
        color: #5D0632;
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
    }

    .liste_etudiants_par_groupe tbody td{
        padding: 12px 10px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
        color: #222;
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
        color: #5D0632;
    }

    .analyse_resultat_apres_creation > *{
        min-width:0;
        min-height:0;
    }

    .liste_etudiants_par_groupe tbody tr:nth-child(even){background:#fcfcfc}
    .liste_etudiants_par_groupe tbody tr:hover{background:#f6f6f6}

    @media (max-width:900px){
        .analyse_resultat_apres_creation{grid-template-columns:1fr}
        .liste_groupes_crees{position:relative;top:auto}
        .liste_etudiants_par_groupe{overflow-x:auto}
        .liste_etudiants_par_groupe table{min-width:650px}
    }


    .liste_promo table {
        font-size: 16px;
        width: 100%;
        overflow-y: scroll;
        max-height: calc(80vh + 1px);
        position: relative;
    }

    table > thead > tr > th::before {
        content: "";
        width: calc(100% + 2px);
        height: 9px;
        outline: #fff solid 1px;
        background-color: #fff;
        position: absolute;
        top: -11px;
        right: 0;
    }

    table > thead > tr > th {
        background-color: rgba(255, 244, 249, 1);
        color: #5D0632;
        font-weight: 400;
        border: #5d063280 solid 0.5px;
        outline: #5d063280 solid 0.5px;
        padding: 5px;
        position: sticky;
        top: 2px;
        border-radius: 6px;
    }

    table th, td {
        border: #5d063280 solid 0.5px;
        outline: #5d063280 solid 0.5px;
        padding: 8px 10px;
        text-align: center;
        border-radius: 6px;
    }

    table td > form > button {
        height: 100%;
        aspect-ratio: 1/1;
        background: #5D0632;
        border: 1px solid #5D0632;
        border-radius: 6px;
        box-shadow: rgba(0, 0, 0, 0.1) 1px 2px 4px;
        box-sizing: border-box;
        color: #FFFFFF;
        cursor: pointer;
        display: inline-block;
        line-height: 16px;
        font-weight: 500;
        min-height: 40px;
        outline: 0;
        padding: 10px;
        text-align: center;
        text-rendering: geometricprecision;
        text-transform: none;
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
        vertical-align: middle;
        font-size: 16px;

        i { 
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }

    table td input:enabled{
        background: #5D0632;
        border: 1px solid #5D0632;
        color: #FFFFFF;
        cursor: pointer;
        opacity: 1;
        padding: 10px;
        border-radius: 6px;
        font-size: 16px;
    }

    table td input:enabled:hover{
        filter: brightness(1.05);
    }

    table td input:enabled:active{
        transform: translateY(1px);
    }

    table td input:enabled:focus-visible{
        outline: 3px solid rgba(93, 6, 50, 0.25);
        outline-offset: 2px;
    }

    table td input:disabled {
        padding: 10px;
        background: rgba(255, 244, 249, 1);
        border-radius: 6px;
        border: 1px solid #bdbdbd;
        color: #6f6f6f;
        cursor: not-allowed;
        box-shadow: none;
        opacity: 0.8;
        transform: none;
        filter: none;
        font-size: 16px;
    }

    table td > form > button:hover,
    table td > form > button:active {
        background-color: initial;
        background-position: 0 0;
        color: #5D0632;
    }

    table td > form > button:active {
        opacity: .5;
    }

    table tbody > tr:nth-of-type(even) {
        background-color: #f5f5f5;
    }

    @media (max-width: 820px){
        .table_choisir_controleur form{
            grid-template-columns: 1fr;
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
                <h1>Constitution des groupes TP & TD</h1>
            </div>
            <div class="layout_panel_content">
                <div class="liste_promo">
                    <div class="liste_promo_scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>Année de promotion</th>
                                    <th>NB étudiants courants</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($liste_promotion as $promotion): ?>
                                    <tr>
                                        <td><?php echo $promotion->get("anneePromotion"); ?></td>
                                        <td><?php echo $promotion->get("nbEtudiantPromo"); ?></td>
                                        <td>
                                            <form action="index.php" method="get">
                                                <input type="hidden" name="page" value="constituerGroupeRes">
                                                <input type="hidden" name="promo" value="<?php echo $promotion->get("idPromotion") ?? ""; ?>">
                                                <input type="hidden" name="edit" value="1">
                                                <button type="submit"><i class="fi fi-rc-settings-sliders"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php if (isset($_GET['promo'])): ?>
                    <div class="panel_critere">
                        <div class="info_promotion">
                            <p><?php echo "Promotion</br>" . "<span>" . $promotionChercher->get("anneePromotion") . "</span>"; ?></p>
                            <p><?php echo "NB étudiants</br>" . "<span>" . $promotionChercher->get("nbEtudiantPromo") . "</span>"; ?></p>
                            <p><?php echo "NB hommes</br>" . "<span>" . $promotionChercher->getNbHommePromo() . "</span>"; ?></p>
                            <p><?php echo "NB femmes</br>" . "<span>" . $promotionChercher->getNbFemmePromo() . "</span>"; ?></p>
                            <?php 
                                if ($promotionChercher->get("nbGroupePromo") == 0) echo "<p class=\"etat_encours\">État</br><span>En cours</span></p>";
                                else echo "<p class=\"etat_termine\">État</br><span>Terminé</span></p>";
                            ?>
                        </div>
                        <hr style="border:0; border-top:1px dashed #5D0632; margin:10px 0;">
                        <div class="liste_critere">
                            <form action="index.php" method="GET">
                                <input type="hidden" name="page" value="constituerGroupeRes">
                                <input type="hidden" name="promo" value="<?php echo $_GET['promo']; ?>">
                                <input type="hidden" name="edit" value="1">
                                <input type="hidden" name="action" value="constituer">
                                <div class="input_feild">
                                    <div class="input_type_groupe">
                                        <label>Type du groupe : </label>
                                        <select name="type_groupe" required>
                                            <?php foreach($liste_types_groupe as $type): ?>
                                                <option value="<?php echo $type['idTypeGroupe']; ?>"><?php echo $type['nomTypeGroupe']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label>Semestre : </label>
                                        <select name="semestre" required>
                                            <?php 
                                                $promoPlusRecent = (int) Promotion::getPromotionPlusRecente()->get('anneePromotion');
                                                $anneePromoComparer  = (int) $promotionChercher->get("anneePromotion");
                                                $distance_annee = $promoPlusRecent - $anneePromoComparer;
                                                switch ($distance_annee) {
                                                    case 0:
                                                        echo "<option value=\"1\">S1</option>";
                                                        echo "<option value=\"2\">S2</option>";
                                                        break;
                                                    case 1:
                                                        echo "<option value=\"3\">S3</option>";
                                                        echo "<option value=\"4\">S4</option>";
                                                        break;
                                                    case 2:
                                                        echo "<option value=\"5\">S5</option>";
                                                        echo "<option value=\"6\">S6</option>";
                                                        break;
                                                    default:
                                                        echo "<option value=\"\">La promotion est terminée</option>";
                                                        break;
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mode-cards" id="modeCards">
                                        <label class="mode active" data-mode="mode1">
                                            <input type="radio" name="mode" value="mode_1" checked />
                                            <span class="pill">Mode 1</span>
                                            <div class="title">Niveau / Redoublant / Groupe Anglais</div>
                                            <div class="desc">Équilibrer les moyennes + répartir les redoublants, avec un groupe Anglais dédié.</div>
                                        </label>
                                        <label class="mode" data-mode="mode2">
                                            <input type="radio" name="mode" value="mode_2" />
                                            <span class="pill">Mode 2</span>
                                            <div class="title">Parcours / Covoiturage / Genre / Bac</div>
                                            <div class="desc">Groupes 14–18, respecter parcours & covoiturage, règle filles, équilibrer bacs.</div>
                                        </label>
                                        <label class="mode" data-mode="mode3">
                                            <input type="radio" name="mode" value="mode_3" />
                                            <span class="pill">Mode 3</span>
                                            <div class="title">Parcours + contrôle femmes</div>
                                            <div class="desc">Max 20–25, ≥5 femmes ou 0, regrouper au max par parcours, moyennes équivalentes.</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="button_panel_form">
                                    <?php if (isset($_GET['status'])) : ?>
                                        <button type="submit">Recréer les groupes</button>
                                        <a href="<?php echo "index.php?page=constituerGroupeRes&promo={$_GET['promo']}&edit=1&action=confirmer"; ?>">Confirmer</a>
                                    <?php else: ?>
                                        <button type="submit">Créer les groupes</button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                        <hr style="border:0; border-top:1px dashed #5D0632; margin:10px 0;">
                        <?php if (isset($_SESSION['liste_groupes_constitues']) && isset($_GET['status']) && $_GET['status'] == "created"): ?>
                            <div class="analyse_resultat_apres_creation">
                                <div class="liste_groupes_crees">
                                    <?php $liste_groupes_constitues = $_SESSION['liste_groupes_constitues']; ?>
                                    <?php foreach ($liste_groupes_constitues as $groupe) : ?>
                                        <form action="index.php" method="get">
                                            <input type="hidden" name="page" value="constituerGroupeRes">
                                            <input type="hidden" name="promo" value="<?php echo $_GET['promo']; ?>">
                                            <input type="hidden" name="edit" value="1">
                                            <input type="hidden" name="status" value="created">
                                            <input type="hidden" name="groupe" value="<?php echo $groupe->get('idGroupe'); ?>">
                                            <button type="submit"><?php echo $groupe->get('codeGroupe'); ?></button>
                                        </form>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (isset($_GET['groupe'])) : ?>
                                    <div class="liste_etudiants_par_groupe">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <td>N°</td>
                                                    <td>ID étudiant</td>
                                                    <td>Nom</td>
                                                    <td>Prénom</td>
                                                    <td>E-mail</td>
                                                    <td>Sexe</td>
                                                    <td>Parcours</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($liste_groupes_constitues as $groupe) : ?>
                                                    <?php 
                                                        if (!($groupe->get('idGroupe') == $_GET['groupe'])) continue;
                                                        $liste_etudiants_groupe = $groupe->get('listEtudiants');
                                                    ?>
                                                    <?php $compte = 1; ?>
                                                    <?php foreach ($liste_etudiants_groupe as $etudiant): ?>
                                                        <tr>
                                                            <td><?php echo $compte; $compte++; ?></td>
                                                            <td><?php echo $etudiant->get('idEtudiant'); ?></td>
                                                            <td><?php echo $etudiant->get('nomPersonne'); ?></td>
                                                            <td><?php echo $etudiant->get('prenomPersonne'); ?></td>
                                                            <td><?php echo $etudiant->get('emailPersonne'); ?></td>
                                                            <td><?php echo $etudiant->get('sexePersonne'); ?></td>
                                                            <td><?php echo $etudiant->get('parcoursEtudiant'); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>