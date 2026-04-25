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

require_once 'modele/matiere.php';
require_once 'modele/controle.php';

$matiere = $_GET['matiere'] ?? null;
$liste_matieres  = Matiere::getAllMatiere();
$liste_controles = $matiere ? Controle::getAllControlesParMatiere($matiere) : [];

$controle = $_GET['examen'] ?? null;
$liste_etudiants_controle = $controle ? Controle::getListeEtudiantsParControle($controle) : [];
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
    <title>Gestion des notes</title>
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
        gap: 30px;
        padding: 50px 50px 30px 50px;
        min-height: 100vh;
        box-sizing: border-box;
    }

    .main_content_panel .nav_content{
        display: flex;
        align-items: center;
    }

    .main_content_panel .nav_content h1{
        background-color: #fff;
        color: #5D0632;
        border-radius: 10px;
        font-weight: 600;
    }

    .layout_panel_content{
        display: flex;
        flex-direction: column;
        width: 100%;
        height: 100%;
        margin: 0 auto;
    }

    .table_choisir_controleur{
        background: #fff;
        border: 1px solid #e9e9e9;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 24px rgba(0,0,0,0.06);
        color: #5D0632;
    }

    .table_choisir_controleur form{
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 30px;
        align-items: end;
    }

    .input_panel_choisir_controleur{
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .input_panel_choisir_controleur label{
        font-weight: 600;
        font-size: 18px;
    }

    .input_panel_choisir_controleur select{
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d9d9d9;
        border-radius: 12px;
        background: #fff;
        font-size: 14px;
        outline: none;
        appearance: none;
        cursor: pointer;
    }

    .input_panel_choisir_controleur select:focus{
        border-color: #5D0632;
        box-shadow: 0 0 0 4px #5d06321a;
    }

    .input_panel_choisir_controleur select:disabled{
        opacity: 0.6;
        cursor: not-allowed;
    }

    .liste_notes_etudiants{
        margin-top: 20px;
        background: #fff;
        border: 1px solid #e9e9e9;
        border-radius: 16px;
        padding: 20px 10px 10px 10px;
        min-height: 240px;
        flex: 1;
        overflow-y: scroll;
        max-height: 65vh;
        position: relative;
    }

    .panel_import_data {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: hsla(0, 0%, 100%, 0.3);
        backdrop-filter: blur(5px);
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 11;
    }

    #form_importer_fichier {
        position: relative;
        width: 50%;
        height: 50%;
        bottom: 10%;
        background: #fff;
        border-radius: 16px;
        border: 4px solid #5D0632;
        padding: 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        gap: 20px;
        z-index: 11;
    }

    #form_importer_fichier h2 {
        font-weight: 400;
    }
    
    #form_importer_fichier h2 span {
        font-weight: 700;
        color: red;
    }
    
    #form_importer_fichier p {
        font-weight: 400;
        color: red;
    }

    #form_importer_fichier input {
        background-color: #fff;
        font-weight: 400;
        color: red;
    }

    #form_importer_fichier .row_button {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        width: calc(100% - 2*30px);    
        position: absolute;
        bottom: 30px;
        right: 30px;
    }

    #form_importer_fichier .row_button a {
        background-color: #fff;
        font-weight: 400;
        color: #5D0632;
        text-decoration: none;
        padding: 10px 20px;
        outline: #5D0632 solid 2px;
        text-align: center;
        border-radius: 10px;
    } 

    #form_importer_fichier .row_button button {
        background-color: #5D0632;
        font-weight: 400;
        color: #fff;
        text-decoration: none;
        padding: 10px 20px;
        border: #5D0632 solid 2px;
        outline: none;
        border-radius: 10px;
        cursor: pointer;
    }

    .navi_export_import {
        position: sticky;
        top: 0;
        right: 10px;
        z-index: 10;
        height: 30px;
        width: 100%;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        align-items: center;
        z-index: 1000;
    }

    #code_erreur {
        color: red;
        font-weight: 500;
    }

    .download-button {
        position: relative;
        border-width: 0;
        color: white;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border-radius: 6px;
        z-index: 1;
        transition: 0.2s;
    }

    .download-button .docs {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        min-height: 30px;
        padding: 0 10px;
        border-radius: 4px;
        z-index: 1;
        background-color: #242a35;
        border: solid 1px #e8e8e82d;
        transition: all 0.5s cubic-bezier(0.77, 0, 0.175, 1);

        i {
            display: flex;
            justify-content: center;
            align-items: center;
            transform: scale(1.4);
        }
    }

    .download-button:hover {
        box-shadow:
            rgba(0, 0, 0, 0.25) 0px 54px 55px,
            rgba(0, 0, 0, 0.12) 0px -12px 30px,
            rgba(0, 0, 0, 0.12) 0px 4px 6px,
            rgba(0, 0, 0, 0.17) 0px 12px 13px,
            rgba(0, 0, 0, 0.09) 0px -3px 5px;
    }

    .download {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        max-width: 90%;
        margin: 0 auto;
        z-index: -1;
        border-radius: 4px;
        transform: translateY(0%);
        background-color: #01e056;
        border: solid 1px #01e0572d;
        transition: all 0.5s cubic-bezier(0.77, 0, 0.175, 1);

        i {
            display: flex;
            justify-content: center;
            align-items: center;
            transform: scale(1.4);
        }
    }

    .download-button:hover .download {
        transform: translateY(100%);
    }

    .download svg polyline,
    .download svg line {
        animation: docs 1s infinite;
    }

    @keyframes docs {
        0% {
            transform: translateY(0%);
        }

        50% {
            transform: translateY(-15%);
        }

        100% {
            transform: translateY(0%);
        }
    }

    table {
        font-size: 16px;
        width: 100%;
        overflow-y: scroll;
        max-height: calc(80vh + 1px);
        position: relative;
    }

    table > thead > tr > th::before {
        content: "";
        width: calc(100% + 2px);
        height: 60px;
        outline: #fff solid 1px;
        background-color: #fff;
        position: absolute;
        top: -62px;
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
        top: 40px;
        z-index: 100;
    }

    table th, td {
        border: #5d063280 solid 0.5px;
        outline: #5d063280 solid 0.5px;
        padding: 8px 10px;
        text-align: center;
        border-radius: 6px;
        height: 30px;
    }
    table tbody {
        padding-top: 10px;
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
                <h1>Gestion des notes des étudiants</h1>
            </div>
            <div class="layout_panel_content">
                <div class="table_choisir_controleur">
                    <form action="index.php" method="get">
                        <input type="hidden" name="page" value="notesRes">
                        <div class="input_panel_choisir_controleur">
                            <label for="matiere">Matière : </label>
                            <select name="matiere" onchange="this.form.submit()">
                                <option value="">Choisir une matière</option>
                                <?php foreach ($liste_matieres as $matiere): ?>
                                    <option value="<?php echo $matiere->get("idMatiere"); ?>" <?php echo $matiere->get("idMatiere") == (isset($_GET['matiere']) ? $_GET['matiere'] : 0) ? "selected" : ""; ?> ><?php echo $matiere->get("nomMatiere"); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input_panel_choisir_controleur">
                            <label for="examen">Examen : </label>
                            <select name="examen" onchange="this.form.submit()">
                                <option value=""><?php echo !isset($_GET['matiere']) ? "Veuillez choisir une matière" : "Choisir un examen"; ?></option>
                                <?php foreach ($liste_controles as $controle): ?>
                                    <option value="<?php echo $controle->get("idControle"); ?>" <?php echo $controle->get("idControle") == (isset($_GET['examen']) ? $_GET['examen'] : 0) ? "selected" : ""; ?> ><?php echo $controle->get("nomControle") . " (" . $controle->get("dateControleC") . ")"; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <?php if (isset($_GET['examen']) && $_GET['examen'] != null) : ?>
                    <div class="liste_notes_etudiants">
                        <?php if (isset($_GET['action']) && $_GET['action'] == "importer"): ?>
                            <div class="panel_import_data">
                                <form action="index.php?page=notesRes" method="POST" enctype="multipart/form-data" id="form_importer_fichier">
                                    <input type="hidden" name="page" value="notesRes">
                                    <input type="hidden" name="matiere" value="<?php echo $_GET['matiere'];?>">
                                    <input type="hidden" name="examen" value="<?php echo $_GET['examen']; ?>">
                                    <input type="hidden" name="action" value="importer">
                                    <h2>⚠️ Format de fichier obligatoire<br>Le fichier <span>.csv</span> doit contenir les colonnes suivantes :</h2>
                                    <p>ID étudiant | Note | Remarques</p>
                                    <input type="file" name="csv" accept=".csv,text/csv" required>
                                    <div class="row_button">
                                        <a id="btn_annuler" href="index.php?page=notesRes&matiere=<?php echo urlencode($_GET['matiere']); ?>&examen=<?php echo urlencode($_GET['examen']); ?>">Annuler</a>
                                        <button id="btn_confirmer" type="submit">Confirmer</button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                        <div class="navi_export_import">
                            <?php
                            $erreur = isset($_GET['erreur']) ? (int)$_GET['erreur'] : 0;

                            switch ($erreur) {
                                case 1:
                                    echo "<p id=\"code_erreur\">Erreur lors du téléversement du fichier CSV</p>";
                                    break;
                                case 2:
                                    echo "<p id=\"code_erreur\">Impossible d’ouvrir le fichier CSV</p>";
                                    break;
                                case 3:
                                    echo "<p id=\"code_erreur\">Le fichier CSV est vide</p>";
                                    break;
                                case 4:
                                    echo "<p id=\"code_erreur\">Colonne manquante dans le CSV</p>";
                                    break;
                                case 5:
                                    echo "<p id=\"code_erreur\">Échec de l’importation</p>";
                                    break;
                            }
                            ?>
                            <form action="index.php?page=notesRes" method="GET" id="form_button_importer_fichier">
                                <input type="hidden" name="page" value="notesRes">
                                <input type="hidden" name="matiere" value="<?php echo $_GET['matiere'];?>">
                                <input type="hidden" name="examen" value="<?php echo $_GET['examen']; ?>">
                                <input type="hidden" name="action" value="importer">
                                <button type="submit" class="download-button">
                                    <div class="docs">
                                        <i class="fi fi-sr-file-csv"></i>Importer .csv
                                    </div>
                                    <div class="download">
                                        <i class="fi fi-br-upload"></i>
                                    </div>
                                </button>
                            </form>
                            <form action="index.php?page=notesRes" method="GET" id="form_exporter_fichier">
                                <input type="hidden" name="page" value="notesRes">
                                <input type="hidden" name="matiere" value="<?php echo $_GET['matiere'];?>">
                                <input type="hidden" name="examen" value="<?php echo $_GET['examen']; ?>">
                                <input type="hidden" name="action" value="exporter">
                                <button type="submit" class="download-button">
                                    <div class="docs">
                                        <i class="fi fi-sr-file-csv"></i>Exporter .csv
                                    </div>
                                    <div class="download">
                                        <i class="fi fi-br-download"></i>
                                    </div>
                                </button>
                            </form>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID étudiant</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>Note</th>
                                    <th>Remarques</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($liste_etudiants_controle as $etudiant): ?>
                                    <tr>
                                        <td><?php echo $etudiant['idEtudiant']; ?></td>
                                        <td><?php echo $etudiant['nom']; ?></td>
                                        <td><?php echo $etudiant['prenom']; ?></td>
                                        <td><?php echo $etudiant['email']; ?></td>
                                        <td><?php echo $etudiant['note'] . "/20"; ?></td>
                                        <td><?php echo $etudiant['remarque']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>