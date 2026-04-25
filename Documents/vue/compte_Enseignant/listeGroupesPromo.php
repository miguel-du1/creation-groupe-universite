<?php
session_start();
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

require_once 'modele/promotion.php';
$liste_promotion = Promotion::getAllPromotion();
$liste_groupes = isset($_GET['promo']) ? Promotion::chercherGroupesParIdPromo($_GET['promo']) : array();

require_once 'modele/groupe.php';
$liste_etudiants_par_groupe = isset($_GET['groupe']) ? Groupe::getListeEtudiants($_GET['groupe']) : array();
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
    <title>Groupes</title>
</head>
<style>
    .main_content_panel {
        position: relative;
        flex: 1;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        padding: 50px
    }

    .nav_panel_promo_page {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        margin-bottom: 50px;

        h1 {
            color: #5D0632;
            font-weight: 600;
        }

        form {
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
                font-size: 16px;
            }

            button {
                background-color: #fff;
                padding: 5px 20px;
                color: #5D0632;
                outline: none;
                border: #5D0632 solid 2px;
                border-radius: 100px;
                font-weight: bold;
                font-size: 16px;
                cursor: pointer;
                display: flex;
                justify-content: flex-start;
                align-items: center;
                gap: 10px;
                height: 40px;
                i {
                    display: flex;
                    align-items: center;
                }
            }
            button:active {
                background-color: #f5f5f5;
            }
        }
    }

    .table_content_promotion {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 20px;
        width: 100%;
        height: 100%;
        max-height: 80vh;

        img {
            width: 100%;
            transform: scale(0.3);
        }

        .liste_groupes {
            height: 100%;
            outline: #5D0632 solid 2px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            gap: 10px;
            padding: 20px 20px 20px 0;
            border-radius: 20px;
            min-width: 150px;
            overflow: scroll;

            p {
                color: #5D0632;
                margin-left: 20px;
            }

            form {
                .groupe_courant { 
                    background-color: #5D0632;
                    padding: 5px 20px;
                    color: #fff;
                    border: #5D0632 solid 1px;
                    border-left: #5D0632 solid 0px;
                    outline: none;
                    border-radius: 0px 100px 100px 0px;
                    font-weight: 500;
                    cursor: pointer;
                    gap: 10px;
                    min-width: 150px;
                }
                .groupe_a_choisir {
                    background-color: #fff;
                    padding: 5px 20px;
                    color: #5D0632;
                    border: #5D0632 solid 1px;
                    border-left: #5D0632 solid 0px;
                    outline: none;
                    border-radius: 0px 100px 100px 0px;
                    font-weight: 300;
                    cursor: pointer;
                    gap: 10px;
                    min-width: 150px;
                }
                .groupe_a_choisir:active {
                    background-color: #f5f5f5;
                }
            }
        }

        .liste_etudiants_par_groupe {
            height: 100%;
            outline: #5D0632 solid 2px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            padding: 20px 20px 20px 20px;
            border-radius: 20px;
            flex: 1;
            overflow: scroll;
            
            table {
                font-size: 0.9rem;
                width: 100%;
                outline: #5D0632 solid 1px;

                thead > tr > th {
                    background-color: rgba(93, 6, 50, 0.1);
                    color: #5D0632;
                    font-weight: 400;
                    outline: #5D0632 solid 1px;
                    padding: 5px;
                }

                th, td {
                    outline: #5D0632 solid 1px;
                    padding: 8px 10px;
                    text-align: center;

                    button {
                        width: 100%;
                        height: 100%;
                        background: #5D0632;
                        border: 1px solid #5D0632;
                        border-radius: 6px;
                        box-shadow: rgba(0, 0, 0, 0.1) 1px 2px 4px;
                        box-sizing: border-box;
                        color: #FFFFFF;
                        cursor: pointer;
                        display: inline-block;
                        line-height: 16px;
                        font-weight: 400;
                        min-height: 40px;
                        outline: 0;
                        padding: 12px 14px;
                        text-align: center;
                        text-rendering: geometricprecision;
                        text-transform: none;
                        user-select: none;
                        -webkit-user-select: none;
                        touch-action: manipulation;
                        vertical-align: middle;
                    }

                    button:hover,
                    button:active {
                        background-color: initial;
                        background-position: 0 0;
                        color: #5D0632;
                    }

                    button:active {
                        opacity: .5;
                    }
                }

                tbody > tr:nth-of-type(even) {
                    background-color: #f5f5f5;
                }
            }
        }
    }
</style>
<body>
    <div class="main_layout_panel">
        <?php 
            require_once("controleur/c_sidebar.php");
        ?>
        <div class="main_content_panel">
            <div class="nav_panel_promo_page">
                <h1>Liste des groupes de la promotion</h1>
                <form action="index.php" methode="GET">
                    <input type="hidden" name="page" value="groupeEns">
                    <select name="promo" onchange="this.form.submit()">
                        <?php   
                            $idPromoChoisi = isset($_GET['promo']) ? $_GET['promo'] : 0;
                            foreach ($liste_promotion as $promotion) {
                                $annee = $promotion->get("anneePromotion");
                                $idPromo = $promotion->get("idPromotion");
                                if ($idPromoChoisi == $idPromo) {
                                    echo "<option value=\"$idPromo\" selected>$annee</option>";
                                } else {
                                    echo "<option value=\"$idPromo\">$annee</option>";
                                }
                            }
                        ?>
                    </select>
                </form>
            </div>
            <div class="table_content_promotion">
                <?php if (isset($_GET['promo'])): ?>
                    <div class="liste_groupes">
                        <?php 
                            $idPromo = $_GET['promo'] ?? "";
                            $idGroupeCourant = $_GET['groupe'] ?? "";
                            foreach ($liste_groupes as $groupe) {
                                $idGroupe = $groupe['idGroupe'];
                                $code = $groupe['codeGroupe'];
                                echo "<form action=\"index.php\">";
                                echo "<input type=\"hidden\" name=\"page\" value=\"groupeEns\">";
                                echo "<input type=\"hidden\" name=\"promo\" value=\"$idPromo\">";
                                echo "<input type=\"hidden\" name=\"groupe\" value=\"$idGroupe\">";
                                if ($idGroupeCourant == $idGroupe) {
                                    echo "<button class=\"groupe_courant\" type=\"submit\">$code</button>";
                                } else {
                                    echo "<button class=\"groupe_a_choisir\" type=\"submit\">$code</button>";
                                }
                                echo "</form>";
                            }
                        ?>
                    </div>
                    <?php if (isset($_GET['groupe'])): ?>
                        <div class="liste_etudiants_par_groupe">
                            <table>
                                <thead>
                                    <tr>
                                        <th scope="col">N°</th>
                                        <th scope="col">ID étudiant</th>
                                        <th scope="col">Nom</th>
                                        <th scope="col">Prénom</th>
                                        <th scope="col">Genre</th>
                                        <th scope="col">Courriel universitaire</th>
                                        <th scope="col">Type de bac</th>
                                        <th scope="col">Statut Academique</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $compteE = 1;
                                        foreach ($liste_etudiants_par_groupe as $etudiant) {
                                            $idE = $etudiant["idEtudiant"];
                                            $nom = $etudiant["nom"];
                                            $prenom = $etudiant["prenom"];
                                            $sexe = $etudiant["sexe"];
                                            $email = $etudiant["email"];
                                            $typeBac = $etudiant["typeBac"];
                                            $statut = $etudiant["statutAcademique"];
    
                                            echo "<tr>";
                                            echo "<th scope=\"row\">$compteE</th>";
                                            echo "<td>$idE</td>";
                                            echo "<td>$nom</td>";
                                            echo "<td>$prenom</td>";
                                            echo "<td>$sexe</td>";
                                            echo "<td>$email</td>";
                                            echo "<td>$typeBac</td>";
                                            echo "<td>$statut</td>";
                                            echo "<td>";
                                            echo "<form action=\"index.php\">";
                                            echo "<input type=\"hidden\" name=\"page\" value=\"profilEtu\">";
                                            echo "<input type=\"hidden\" name=\"promo\" value=\"{$_GET['promo']}\">";
                                            echo "<input type=\"hidden\" name=\"groupe\" value=\"{$_GET['groupe']}\">";
                                            echo "<input type=\"hidden\" name=\"etudiant\" value=\"$idE\">";
                                            echo "<button class=\"groupe_a_choisir\" type=\"submit\">Détails</button>";
                                            echo "</form>";
                                            echo "</td>";
                                            echo "</tr>";
                                            $compteE += 1;
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <img src="assets/logo_white.png" alt="logo" draggable="false">
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>