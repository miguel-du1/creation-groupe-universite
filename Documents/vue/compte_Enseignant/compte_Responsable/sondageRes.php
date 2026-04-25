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

require_once 'modele/sondage.php';
$liste_sondages = Sondage::getAllSondagesRes();
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
    <title>Gestion des sondages</title>
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

        #form_creer_sondage {
            position: absolute;
            right: 50px;
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
                margin-left: 10px;
                transform: translateX(30px);
                color: #5D0632;
                font-weight: 600;
                padding: 0 20px;
            }
            
            .button {
                position: relative;
                min-width: 210px;
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
        align-items: flex-start;
        gap: 20px;
        width: 100%;
        max-height: calc(80vh + 1px);
        position: relative;

        .liste_sondages {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            flex: 1;
            height: 100%;
            overflow-y: auto;
            max-height: calc(80vh + 1px);
            border: #5d063280 solid 0.5px;
            outline: #5d063280 solid 0.5px;
            position: relative;
            
            table {
                font-size: 16px;
                width: 100%;
                overflow-y: auto;
                height: 100%;
                max-height: calc(80vh + 1px);

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
                    padding: 8px 10px;
                    text-align: center;
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
                    font-weight: 500;
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
                    font-size: 16px;
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
                <h1>Gestion des sondages</h1>
                <form action="index.php" method="GET" id="form_creer_sondage">
                    <input type="hidden" name="page" value="creerSondageRes">
                    <button type="submit" class="button">
                        <span class="button__text">Créer un sondage</span>
                        <span class="button__icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor" height="24" fill="none" class="svg"><line y2="19" y1="5" x2="12" x1="12"></line><line y2="12" y1="12" x2="19" x1="5"></line></svg></span>
                    </button>
                </form>
            </div>
            <div class="table_content_promotion">
                <div class="liste_sondages">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">N°</th>
                                <th scope="col">ID du sondage</th>
                                <th scope="col">Nom du sondage</th>
                                <th scope="col">Début</th>
                                <th scope="col">Fin</th>
                                <th scope="col">Nombre de réponses</th>
                                <th scope="col">Créé par</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $compteE = 1;
                                $liste_sondage_repondu = $_SESSION['sondageRepondu'] ?? [];
                                foreach ($liste_sondages as $sondage) {
                                    $id = $sondage->get("idS");
                                    $nom = $sondage->get("nomS");
                                    $debut = $sondage->get("debutS");
                                    $fin = $sondage->get("finS");
                                    $nb_reponses = $sondage->get("nb_reponsesS");
                                    $email = $sondage->get("emailS");

                                    echo "<tr>";
                                    echo "<th scope=\"row\">$compteE</th>";
                                    echo "<td>$id</td>";
                                    echo "<td>$nom</td>";
                                    echo "<td>$debut</td>";
                                    echo "<td>$fin</td>";
                                    echo "<td>$nb_reponses</td>";
                                    echo "<td>$email</td>";
                                    
                                    echo "<td>";
                                    echo "<form action=\"index.php\">";
                                    echo "  <input type=\"hidden\" name=\"page\" value=\"sondageDetailsRes\">";
                                    echo "  <input type=\"hidden\" name=\"sondage\" value=\"$id\">";
                                    echo "  <button type=\"submit\">Voir détails</button>";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                    $compteE += 1;
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