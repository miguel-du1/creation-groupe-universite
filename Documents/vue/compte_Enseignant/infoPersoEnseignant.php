<?php
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: index.php?page=login');
    exit();
}

// Verifier le role du compte
$rolesAutorises = ['Admin', 'Responsable', 'Enseignant'];
if (!in_array($_SESSION['role_compte'] ?? '', $rolesAutorises, true)) {
    http_response_code(403);
    echo "Accès interdit pour votre compte";
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
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-bold-rounded/css/uicons-bold-rounded.css'>
    <title>Informations Personnelles</title>
</head>
<style>
.main_content_panel {
    position: relative;
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 50px;
}

.layout_panel_content {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: repeat(2, 1fr) 1.5fr;
    grid-column-gap: 0px;
    grid-row-gap: 0px;
    gap: 40px;
    width: 100%;
    height: 100%;
}

.avt_box { grid-area: 1 / 1 / 3 / 2; }
.info_personnelle_box { grid-area: 1 / 2 / 4 / 4; }
.info_compte_box { grid-area: 3 / 1 / 4 / 2; }

.box_content {
    box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px,
                rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
    background-color: white;
    border-radius: 30px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 20px;
    position: relative;
}

.input-feild {
    margin-bottom: 20px;
}

/* ===== avt_box ===== */
.avt_box h1 {
    color: #5D0632;
    font-weight: 500;
    margin-bottom: 10px;
}

.avt_box p {
    color: #5D0632;
    font-weight: 300;
    margin-bottom: 40px;
}

.avt_box .content_avatar_box {
    width: 80%;
    box-shadow: 0 0 0 10px #5D0632, 0 0 0 15px #fff;
    border-radius: 1000px;
    display: flex;
    justify-content: center;
    align-items: center;
    aspect-ratio: 1 / 1;
    position: relative;
}

.avt_box .content_avatar_box img {
    width: 95%;
    aspect-ratio: 1/1;
    border-radius: 50%;
}

.content_avatar_box > form {
    position: absolute;
    bottom: 10px;
    right: 10px;
    width: 20%;
    aspect-ratio: 1/1;
}

.content_avatar_box > form > button {
    width: 100%;
    aspect-ratio: 1/1;
    border-radius: 50%;
    border: #5D0632 solid 5px;
    outline: none;
    background-color: #fff;
    color: #5D0632;

    i {
        display: flex;
        justify-content: center;
        align-items: center;
        transform: scale(1.5);
    } 
}

.avt_box .charger_avt_box {
    box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px,
                rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
    background-color: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(10px);
    border-radius: 30px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

/* Form layout */
.charger_avt_box form {
    display: flex;
    flex-direction: column;
    gap: 12px;
    align-items: center;
}

/* File input base */
.charger_avt_box input[type="file"] {
    width: 100%;
    max-width: 260px;
    padding: 10px 12px;
    background: rgba(243, 243, 243, 0.9);
    border: 2px solid transparent;
    border-radius: 12px;
    color: #5D0632;
    outline: none;
    transition: all 0.25s;
}

/* Focus/hover like your other inputs */
.charger_avt_box input[type="file"]:hover,
.charger_avt_box input[type="file"]:focus {
    border-color: #5D0632;
    box-shadow: 0px 0px 0px 5px rgba(93, 6, 50, 0.2);
    background: #fff;
}

/* Style the "Choose file" button inside file input */
.charger_avt_box input[type="file"]::file-selector-button {
    margin-right: 10px;
    border: 1px solid #5D0632;
    background: #fff;
    color: #5D0632;
    padding: 8px 12px;
    border-radius: 10px;
    cursor: pointer;
    transition: 0.2s;
}

.charger_avt_box input[type="file"]::file-selector-button:hover {
    background: #5D0632;
    color: #fff;
}

/* Submit button */
.charger_avt_box button {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    border: 1px solid #5D0632;
    background: #fff;
    color: #5D0632;

    display: flex;
    justify-content: center;
    align-items: center;

    cursor: pointer;
    transition: 0.2s;
}

.charger_avt_box button:hover {
    background: #5D0632;
    color: #fff;
    transform: translateY(-1px);
}

.charger_avt_box button:active {
    transform: translateY(0px);
}

/* Icon size */
.charger_avt_box button i {
    font-size: 18px;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* ===== info_compte_box ===== */
.info_compte_box h2 {
    color: #5D0632;
    font-weight: 500;
    margin-bottom: 20px;
}

.info_compte_box p {
    color: #5D0632;
    font-weight: 400;
    margin-bottom: 20px;
    margin-top: 20px;
    text-align: center;
}

.info_compte_box label {
    color: #5D0632;
    font-weight: 200;
    text-align: right;
}

.info_compte_box form {
    display: grid;
    justify-content: center;
    align-items: center;
}

.info_compte_box input {
    border: 2px solid transparent;
    width: 15em;
    padding: 5px 10px;
    padding-left: 0.8em;
    outline: none;
    overflow: hidden;
    background-color: #F3F3F3;
    border-radius: 10px;
    transition: all 0.5s;
}

.info_compte_box input:hover,
.info_compte_box input:focus {
    border: 2px solid #5D0632;
    box-shadow: 0px 0px 0px 5px rgba(93, 6, 50, 0.2);
    background-color: white;
}

.info_compte_box button {
    color: #5D0632;
    width: fit-content;
    justify-self: center;
    background-color: #fff;
    border: #5D0632 solid 1px;
    padding: 5px 20px;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: 0.2s;
}

.info_compte_box button i {
    margin-left: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.info_compte_box button:hover {
    color: #fff;
    justify-self: center;
    background-color: #5D0632;
}

/* ===== info_personnelle_box ===== */
.info_personnelle_box {
    padding: 30px 30px 30px 50px;
    position: relative;
}

.info_personnelle_box::before {
    content: "";
    position: absolute;
    top: 30px;
    left: 25px;
    height: calc(100% - 30px - 30px);
    border-left: #5D0632 solid 2px;
}

/* ===== main_content_info_personnelle_panel ===== */
.main_content_info_personnelle_panel {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    width: 100%;
    height: 100%;
}

.main_content_info_personnelle_panel .info_personnel_panel {
    width: 100%;
}

/* ===== rows ===== */
.row_content_1_col {
    width: 100%;
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 20px;
}

.row_content_2_col {
    width: 100%;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.row_content_3_col {
    width: 100%;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

/* ===== content_panel ===== */
.content_panel {
    width: 100%;
}

.content_panel label {
    color: #5D0632;
    font-weight: 300;
    text-align: left;
}

.content_panel h2 {
    color: #5D0632;
    font-weight: 500;
    margin-bottom: 20px;
    position: relative;
}

.content_panel h2::before {
    content: "";
    position: absolute;
    transform: translate(-50%, -50%);
    top: 50%;
    left: -25px;
    height: 60%;
    aspect-ratio: 1 / 1;
    background-color: #5D0632;
    border: #fff solid 10px;
    border-radius: 50%;
}

.content_panel .content_panel_row {
    width: 100%;
    margin-bottom: 20px;
    display: flex;
    justify-content: flex-start;
    align-items: center;
}

.content_panel .content_panel_row p {
    border: 2px solid transparent;
    padding: 5px 10px;
    padding-left: 0.8em;
    outline: none;
    overflow: hidden;
    background-color: #F3F3F3;
    border-radius: 10px;
    transition: all 0.5s;
    flex: 1;
}

.content_panel .liste_matieres_enseignees {
    border: 2px solid transparent;
    padding: 20px;
    padding-left: 0.8em;
    outline: none;
    background-color: #F3F3F3;
    border-radius: 10px;
    transition: all 0.5s;
}

.content_panel .liste_matieres_enseignees ol li {
    margin-left: 20px;
    margin-bottom: 20px;
}
</style>
<body>
    <div class="main_layout_panel">
        <?php 
            require_once("controleur/c_sidebar.php");
        ?>
        <div class="main_content_panel">
            <div class="layout_panel_content">
                <div class="box_content avt_box">
                    <h1>Mon Compte</h1>
                    <?php echo "<p>Rôle du compte : $role_compte</p>"; ?>
                    <div class="content_avatar_box">
                        <?php if ($info_du_compte['avt'] != null) : ?>
                            <img src="<?php echo $info_du_compte['avt']; ?>" alt="avt" draggable="false">
                        <?php else: ?>
                            <img src="assets/avt_null.png" alt="avt" draggable="false">
                        <?php endif ?>
                        <form action="index.php">
                            <input type="hidden" name="page" value="infoPersonneEns">
                            <input type="hidden" name="action" value="edit_avt">
                            <button type="submit"><i class="fi fi-sr-camera"></i></button>
                        </form>
                    </div>
                    <?php if (isset($_GET['action']) && $_GET['action'] == 'edit_avt') : ?>
                    <div class="charger_avt_box">
                        <form action="index.php?page=infoPersonneEns" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="save_avt">
                            <input type="file" name="image" accept="image/*" required>
                            <button type="submit"><i class="fi fi-br-upload"></i></i></button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="box_content info_personnelle_box">
                    <div class="main_content_info_personnelle_panel">
                        <div class="content_panel info_personnel_panel">
                            <h2>Informations personnelles</h2>
                            <div class="content_panel_row row_content_2_col">
                                <?php echo "<p>{$info_du_compte["nom"]}</p>"; ?>
                                <?php echo "<p>{$info_du_compte["prenom"]}</p>"; ?>
                            </div>
                            <div class="content_panel_row row_content_2_col">
                                <label>Date de naissance</label>
                                <?php echo "<p>{$info_du_compte["dateNaissance"]}</p>"; ?>
                            </div>
                            <div class="content_panel_row row_content_3_col">
                                <?php 
                                    if ($info_du_compte["sexe"] == 'M') echo "<p>Homme</p>";
                                    else echo "<p>Femme</p>"
                                ?>
                                <?php echo "<p>{$info_du_compte["etatCivil"]}</p>"; ?>
                                <?php echo "<p>{$info_du_compte["nationalite"]}</p>"; ?>
                            </div>
                        </div>
                        <div class="content_panel coordonnees_panel">
                            <h2>Coordonnées</h2>
                            <div class="content_panel_row row_content_2_col">
                                <?php echo "<p>{$info_du_compte["phone"]}</p>"; ?>
                                <?php echo "<p>{$info_du_compte["email"]}</p>"; ?>
                            </div>
                            <div class="content_panel_row row_content_1_col">
                                <?php echo "<p>{$info_du_compte["address"]}</p>"; ?>
                            </div>
                        </div>
                        <div class="content_panel info_pedagogique_panel">
                            <h2>Informations pédagogiques</h2>
                            <div class="content_panel_row row_content_2_col">
                                <label>Départerment</label>
                                <?php echo $info_du_compte["departement"] !== null ? "<p>{$info_du_compte["departement"]}</p>" : "<p>Inconnu</p>"; ?>
                            </div>
                            <div class="content_panel_row row_content_2_col">
                                <label>Responsable</label>
                                <?php echo $info_du_compte["responsable"] !== null ? "<p>{$info_du_compte["responsable"]}</p>" : "<p>Inconnu</p>"; ?>
                            </div>
                            <div class="row_content_1_col">
                                <label>Matières enseignées :</label>
                                <div class="liste_matieres_enseignees">
                                    <?php 
                                        $liste_matieres_enseignees = Compte::getListeMatiereEnseignees($id_user);
                                        if ($liste_matieres_enseignees === null) {
                                            echo "<p>Cet enseignant n’enseigne aucune matière.</p>";
                                        } else {
                                            echo "<ol>";
                                            foreach ($liste_matieres_enseignees as $matiere) {
                                                $nom = $matiere['nomMatiere'];
                                                echo "<li>$nom</li>";
                                            }
                                            echo "</ol>";
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>