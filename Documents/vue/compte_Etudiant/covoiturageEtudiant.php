<?php
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: index.php?page=login');
    exit();
}

require_once 'modele/covoiturage.php';
$liste_covoiturages = Covoiturage::getAllCovoiturage();
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
    <title>Covoiturage</title>
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
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        margin-bottom: 50px;

        h1 {
            color: #5D0632;
            font-weight: 600;
            margin-bottom: 10px;
        }

        p {
            color: #5D0632;
            font-weight: 200;
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

        .liste_covoiturages {
            height: 100%;
            outline: #5D0632 solid 2px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 10px;
            flex: 1;
            overflow: scroll;
            
            table {
                font-size: 0.9rem;
                width: 100%;

                caption {

                    .annoncer_covoiturage {
                        color: #5D0632;
                        padding: 10px;
                        font-weight: 500;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: 20px;
                        
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

                }

                thead > tr > th {
                    background-color: rgba(93, 6, 50, 0.1);
                    color: #5D0632;
                    font-weight: 400;
                    outline: #5D0632 solid 0.5px;
                    padding: 5px;
                }

                th, td {
                    outline: #5D0632 solid 0.5px;
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
            </div>
            <div class="table_content_promotion">
                <div class="liste_covoiturages">
                    <table>
                        <?php 
                            if ($_SESSION['covoiturage']) {
                                echo "<caption>";
                                echo "<div class=\"annoncer_covoiturage\">";
                                echo "<p>Vous êtes déjà inscrit(e) à un covoiturage</p>";
                                echo "<form action=\"index.php?page=covoiturageEtu\" method=\"POST\" onsubmit=\"return confirm('Confirmez-vous votre annulation de l’inscription ?');\">";
                                echo "<input type=\"hidden\" name=\"action\" value=\"annuler\">";
                                echo "<button type=\"submit\">Annuler l’inscription au covoiturage</button>";
                                echo "</form>";
                                echo "</div>";
                                echo "</caption>";
                            }
                        ?>
                        <thead>
                            <tr>
                                <th scope="col">N°</th>
                                <th scope="col">ID Covoiturage</th>
                                <th scope="col">Participants</th>
                                <th scope="col">Conducteur</th>
                                <th scope="col">Maximum</th>
                                <th scope="col">Nombre actuel</th>
                                <th scope="col">Statut</th>
                                <th scope="col"></th>
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

                                    echo "<td>$maximum</td>";
                                    echo "<td>$nbCourant</td>";

                                    if ($etat == "Ouvert") {
                                        echo "<td style=\"background: #B6D7A8; color: white; font-weight: 400;\">$etat</td>";
                                        if (!$_SESSION['covoiturage']) {
                                            echo "<td>";
                                            echo "<form action=\"index.php?page=covoiturageEtu\" method=\"POST\" onsubmit=\"return confirm('Confirmez-vous votre inscription à ce covoiturage ?');\">";
                                            echo "<input type=\"hidden\" name=\"covoiturage\" value=\"$idC\">";
                                            echo "<input type=\"hidden\" name=\"action\" value=\"inscrire\">";
                                            echo "<button type=\"submit\">Choisir</button>";
                                            echo "</form>";
                                            echo "</td>";
                                        } else {
                                            echo "<td style=\"color: #5D0632; font-weight: 400;\">fermées</td>";
                                        }
                                    } elseif ($etat == "Complet") {
                                        echo "<td style=\"background: #E06666; color: white; font-weight: 400;\">$etat</td>";
                                        echo "<td style=\"color: #5D0632; font-weight: 400;\">fermées</td>";
                                    }
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