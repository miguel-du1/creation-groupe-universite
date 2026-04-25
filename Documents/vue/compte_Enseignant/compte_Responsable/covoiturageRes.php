<?php
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: index.php?page=login');
    exit();
}

// Verifier le role du compte
$rolesAutorises = ['Admin', 'Responsable'];
if (!in_array($_SESSION['role_compte'] ?? '', $rolesAutorises, true)) {
    http_response_code(403);
    echo "Accès interdit pour votre compte";
    exit();
}

require_once 'modele/covoiturage.php';
$liste_covoiturages = Covoiturage::getAllCovoiturage();

require_once 'modele/etudiant.php';
$liste_etudiants = Etudiant::getListeEtudiantsSansCovoiturage();
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
    <title>Paramètre de covoiturage</title>
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
        padding: 50px;
    }

    .nav_panel_promo_page {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        width: 100%;
        margin-bottom: 50px;
        position: relative;

        h1 {
            color: #5D0632;
            font-weight: 600;
            margin-bottom: 10px;
        }

        p {
            color: #5D0632;
            font-weight: 200;
        }

        #form_creer_covoiturage {
            position: absolute;
            right: 0;
            width: 50%;
            display: flex;
            justify-content: flex-end;
            align-items: stretch;

            select {
                position: relative;
                height: 40px;
                cursor: pointer;
                display: flex;
                align-items: center;
                outline: none;
                border: 2px solid #5D0632;
                background-color: #fff;
                border-radius: 10px;
                margin-left: 10px
                transform: translateX(30px);
                color: #5D0632;
                font-weight: 600;
                padding: 0 20px;
            }
            
            .button {
                position: relative;
                min-width: 240px;
                height: 40px;
                cursor: pointer;
                display: flex;
                align-items: center;
                border: 1px solid #34974d;
                background-color: #3aa856;
                border-radius: 10px;
                margin-left: 10px
            }

            .button, .button__icon, .button__text {
                transition: all 0.3s;
            }

            .button .button__text {
                transform: translateX(30px);
                color: #fff;
                font-weight: 600;
            }

            .button .button__icon {
                position: absolute;
                right: 0;
                height: 100%;
                width: 40px;
                background-color: #34974d;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .button .svg {
                width: 30px;
                stroke: #fff;
            }

            .button:hover {
                background: #34974d;
            }

            .button:hover .button__text {
                color: transparent;
                }

            .button:hover .button__icon {
                width: 100%;
                transform: translateX(0);
            }

            .button:active .button__icon {
                background-color: #2e8644;
            }

            .button:active {
                border: 1px solid #2e8644;
            }
        }
    }

    .table_content_promotion {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 20px;
        width: 100%;

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

        .liste_covoiturages {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            flex: 1;
            overflow-y: auto;
            max-height: 77vh;
            outline: #5d06323c solid 2px;
            
            table {
                font-size: 14px;
                width: 100%;
                border: #5d063280 solid 0.5px;
                outline: #5d063280 solid 0.5px;

                caption {
                    color: #5D0632;
                    padding: 10px;
                    font-weight: 500;

                    form {
                        margin-top: 10px;

                        button {
                            padding: 10px 20px;
                            font-weight: 500;
                            color: #fff;
                            background: #5D0632;
                            border: 1px solid #5D0632;
                            border-radius: 6px;
                            cursor: pointer;
                            transition: 0.1s;
                        }
                        button:hover, button:active {
                            background-color: initial;
                            background-position: 0 0;
                            color: #5D0632;
                        }
                    }
                }

                thead > tr > th {
                    background-color: rgba(255, 244, 249, 1);
                    color: #5D0632;
                    font-weight: 400;
                    border: #5d063280 solid 0.5px;
                    outline: #5d063280 solid 0.5px;
                    padding: 5px;
                    position: sticky;
                    top: 2px;
                }

                th, td {
                    border: #5d063280 solid 0.5px;
                    outline: #5d063280 solid 0.5px;
                    padding: 10px;
                    text-align: center;
                }

                td > form {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                td > form > button {
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
                    font-size: 16px;
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

                td > form > button:hover,
                td > form > button:active {
                    background-color: initial;
                    background-position: 0 0;
                    color: #5D0632;
                }

                td > form > button:active {
                    opacity: .5;
                }

                td > form > input {
                    margin-right: 20px;
                    border-radius: 10px;
                    border: 1px solid #5D0632;
                    width: 40px;
                    font-size: 16px;
                    font-weight: 400;
                    color: #5D0632;
                    background-color: #fff;
                    outline-offset: 3px;
                    padding: 10px 1rem;
                    text-align: center;
                    transition: 0.25s;
                }

                td > form > input:focus {
                    outline-offset: 3px;
                    background-color: #ffffff
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
                <h1>Formulaire de Covoiturage</h1>
                <p>Choisissez votre trajet et inscrivez-vous</p>
                <form action="index.php?page=covoiturageRes" method="POST" id="form_creer_covoiturage">
                    <input type="hidden" name="action" value="creer">
                    <select name="id_etudiant_conducteur" required>
                        <option value="">Sélectionner un étudiant comme conducteur</option>
                        <?php
                            foreach ($liste_etudiants as $etudiant) {
                                echo "<option value=\"{$etudiant->get("idEtudiant")}\">ID : {$etudiant->get("idEtudiant")} - {$etudiant->get("nom")} {$etudiant->get("prenom")}</option>";
                            }
                        ?>
                    </select>
                    <button type="submit" class="button">
                        <span class="button__text">Créer un covoiturage</span>
                        <span class="button__icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor" height="24" fill="none" class="svg"><line y2="19" y1="5" x2="12" x1="12"></line><line y2="12" y1="12" x2="19" x1="5"></line></svg></span>
                    </button>
                </form>
            </div>
            <div class="table_content_promotion">
                <div class="liste_covoiturages">
                    <table>
                        <?php 
                            if (isset($_GET['erreur']) && $_GET['erreur'] == 1) {
                                echo "<caption>Le maximum doit être supérieur ou égal au nombre déjà inscrit</caption>";
                            }
                        ?>
                        <thead>
                            <tr>
                                <th scope="col">N°</th>
                                <th scope="col">ID Covoiturage</th>
                                <th scope="col">Participants</th>
                                <th scope="col">Conducteur</th>
                                <th scope="col">Nombre actuel</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Nombre maximum à modifier</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $compteC = 1;
                                foreach ($liste_covoiturages as $covoiturage) {
                                    $idC = $covoiturage->get("idCovoiturage");
                                    $etat = $covoiturage->get("etatCovoiturage");
                                    $maximum = $covoiturage->get("maximum");
                                    $nbCourant = $covoiturage->get("nbCourant");

                                    echo "<tr>";
                                    echo "<th scope=\"row\">$compteC</th>";
                                    echo "<td>$idC</td>";

                                    $liste_participants = Covoiturage::getListeParticipants($idC);
                                    echo "<td>";
                                    foreach ($liste_participants as $participant) {
                                        echo "<p>{$participant['nom']} {$participant['prenom']}</p>";
                                    }
                                    echo "</td>";
                                    
                                    $conducteur = Covoiturage::getConducteur($idC);
                                    echo "<td>";
                                    echo "<p>{$conducteur['nom']} {$conducteur['prenom']}</p>";
                                    echo "</td>";
                                    echo "<td>$nbCourant</td>";

                                    if ($etat == "Ouvert") {
                                        echo "<td style=\"background: #B6D7A8; color: white; font-weight: 400;\">$etat</td>";
                                    } elseif ($etat == "Complet") {
                                        echo "<td style=\"background: #E06666; color: white; font-weight: 400;\">$etat</td>";
                                    }
                                    echo "<td>";
                                    echo "<form action=\"index.php?page=covoiturageRes\" method=\"POST\" onsubmit=\"return confirm('Confirmez-vous votre modification à ce covoiturage ?');\">";
                                    echo "  <input type=\"hidden\" name=\"covoiturage\" value=\"$idC\">";
                                    echo "  <input type=\"hidden\" name=\"actuel\" value=\"$nbCourant\">";
                                    echo "  <input type=\"hidden\" name=\"max_actuel\" value=\"$nbCourant\">";
                                    echo "  <input type=\"hidden\" name=\"action\" value=\"modifierMax\">";
                                    echo "  <input type=\"number\" name=\"max_modifier\" value=\"$maximum\">";
                                    echo "  <button type=\"submit\">Modifier</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                    $compteC += 1;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>