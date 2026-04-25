<?php
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: index.php?page=login');
    exit();
}

require_once 'modele/sondage.php';
$liste_sondages = Sondage::getAllSondages();
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
    <title>Sondage</title>
</head>
<style>
.main_content_panel {
    position: relative;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    padding: 50px;
}

/* nav panel */
.nav_panel_promo_page {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    margin-bottom: 50px;
}

.nav_panel_promo_page h1 {
    color: #5D0632;
    font-weight: 600;
}

.nav_panel_promo_page form {
    margin-left: 20px;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 10px;
}

.nav_panel_promo_page form select {
    background-color: #5D0632;
    padding: 5px 20px;
    color: #fff;
    border-radius: 100px;
    font-weight: 300;
}

.nav_panel_promo_page form button {
    background-color: #fff;
    padding: 5px 20px;
    color: #5D0632;
    outline: none;
    border: #5D0632 solid 1px;
    border-radius: 100px;
    font-weight: 300;
    cursor: pointer;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 10px;
}

.nav_panel_promo_page form button i {
    display: flex;
    align-items: center;
}

.nav_panel_promo_page form button:active {
    background-color: #f5f5f5;
}

/* table content */
.table_content_promotion {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 20px;
    width: 100%;
    height: 100%;
    max-height: 80vh;
}

/* liste groupes */
.table_content_promotion .liste_groupes {
    height: 100%;
    outline: #EFE6E3 solid 1px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border-radius: 20px;
    min-width: 150px;
}

.table_content_promotion .liste_groupes p {
    color: #5D0632;
    margin-left: 20px;
}

.table_content_promotion .liste_groupes form .groupe_courant {
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

.table_content_promotion .liste_groupes form .groupe_a_choisir {
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

.table_content_promotion .liste_groupes form .groupe_a_choisir:active {
    background-color: #f5f5f5;
}

/* liste sondages */
.table_content_promotion .liste_sondages {
    height: 100%;
    outline: #5D0632 solid 2px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border-radius: 10px;
    overflow: scroll;
}

.table_content_promotion .liste_sondages table {
    font-size: 0.9rem;
    width: 100%;
    outline: #5D0632 solid 1px;
}

.table_content_promotion .liste_sondages table thead > tr > th {
    background-color: rgba(93, 6, 50, 0.1);
    color: #5D0632;
    font-weight: 400;
    outline: #5D0632 solid 1px;
    padding: 5px;
}

.table_content_promotion .liste_sondages table th,
.table_content_promotion .liste_sondages table td {
    outline: #5D0632 solid 1px;
    padding: 8px 10px;
    text-align: center;
}

.table_content_promotion .liste_sondages table td > form > button {
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
}

.table_content_promotion .liste_sondages table td > form > button:hover,
.table_content_promotion .liste_sondages table td > form > button:active {
    background-color: initial;
    background-position: 0 0;
    color: #5D0632;
}

.table_content_promotion .liste_sondages table td > form > button:active {
    opacity: 0.5;
}

.table_content_promotion .liste_sondages table tbody > tr:nth-of-type(even) {
    background-color: #f5f5f5;
}

/* detail sondage */
.table_content_promotion .detail_sondage {
    height: 100%;
    outline: #5D0632 solid 2px;
    background-color: #EFE7EB;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    gap: 10px;
    padding: 20px;
    border-radius: 20px;
    overflow: scroll;
    flex: 1;
}

.table_content_promotion .detail_sondage .row_info_sondage {
    width: calc(100%);
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    justify-content: center;
    gap: 10px;
    padding: 5px;
}

.table_content_promotion .detail_sondage .row_info_sondage p {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    background-color: #fff;
    outline: #5D0632 solid 1px;
    font-weight: 300;
    padding: 10px 20px;
    border-radius: 10px;
    color: #5D0632;
}

.table_content_promotion .detail_sondage .liste_critere_sondage {
    width: calc(100% - 40px);
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    outline: #5D0632 solid 1px;
    flex: 1;
}

.table_content_promotion .detail_sondage .liste_critere_sondage form {
    height: 100%;
    position: relative;
}

.table_content_promotion .detail_sondage .liste_critere_sondage form .btn_submit_sondage {
    width: 100%;
    padding: 10px 20px;
    position: absolute;
    bottom: 0;
    text-align: center;
    background-color: #5D0632;
    outline: none;
    border: #5D0632 solid 1px;
    font-weight: 500;
    color: #fff;
    cursor: pointer;
}

.table_content_promotion .detail_sondage .liste_critere_sondage form > .question_critere {
    font-weight: 300;
    color: #5D0632;
    margin-bottom: 10px;
}

.table_content_promotion .detail_sondage .liste_critere_sondage form > .box_reponse_critere {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 20px;
}

.table_content_promotion .detail_sondage .liste_critere_sondage form > .box_reponse_critere label {
    margin-left: 10px;
}

.table_content_promotion .detail_sondage .liste_critere_sondage form > .box_reponse_critere .input_text_libre {
    width: calc(100% - 40px);
    padding: 10px 20px;
}
</style>
<body>
    <div class="main_layout_panel">
        <?php 
            require_once("controleur/c_sidebar.php");
        ?>
        <div class="main_content_panel">
            <div class="nav_panel_promo_page">
                <h1>Réponse aux sondages</h1>
            </div>
            <div class="table_content_promotion">
                <div class="liste_sondages">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">N°</th>
                                <th scope="col">ID sondage</th>
                                <th scope="col">Nom du sondage</th>
                                <th scope="col">Début</th>
                                <th scope="col">Fin</th>
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

                                    echo "<tr>";
                                    echo "<th scope=\"row\">$compteE</th>";
                                    echo "<td>$id</td>";
                                    echo "<td>$nom</td>";
                                    echo "<td>$debut</td>";
                                    echo "<td>$fin</td>";

                                    echo "<td>";
                                    echo "<form action=\"index.php\">";
                                    echo "<input type=\"hidden\" name=\"page\" value=\"sondageEtu\">";
                                    echo "<input type=\"hidden\" name=\"sondage\" value=\"$id\">";

                                    if (strtotime($fin) <= strtotime(date('Y-m-d'))) {
                                        echo '<button type="button" style="width:100%;background:#5D0632;border:1px solid #5D0632;color:#fff;border-radius:6px;min-height:40px;padding:12px 14px;opacity:.45;cursor:not-allowed;"  disabled title="Sondage déjà terminée">FERMÉ</button>';
                                    } elseif (in_array($id, array_column($liste_sondage_repondu, 'idSondage'), true)) {
                                        echo "<button type=\"submit\">Modifier</button>";
                                    } else {
                                        echo "<button type=\"submit\">Répondre</button>";
                                    }

                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";

                                    $compteE += 1;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php if (isset($_GET['sondage'])): ?>
                    <div class="detail_sondage">
                        <div class="row_info_sondage">
                            <?php 
                                $idSondageChercher = $_GET['sondage'] ?? 0;
                                $sondage_chercher = Sondage::getSondage($idSondageChercher);
                                echo "<p>{$sondage_chercher["nomS"]}</p>";
                                echo "<p>Debut<br />{$sondage_chercher["debutS"]}</p>";
                                echo "<p>Fin<br />{$sondage_chercher["finS"]}</p>";
                            ?>
                        </div>
                        <hr style="width: 100%; border: 0; border-top: 2px double #5D0632;" />
                        <div class="liste_critere_sondage">
                            <form action="index.php?page=sondageEtu" method="POST">
                                <input type="hidden" name="dateEnvoie" value="<?php echo date('Y-m-d'); ?>">
                                <input type="hidden" name="id_user" value="<?php echo "{$_SESSION['user_id']}"; ?>">
                                <input type="hidden" name="id_sondage" value="<?php echo "{$_GET['sondage']}"; ?>">
                                <?php 
                                    $liste_critere = Sondage::getCritereSondage($idSondageChercher);
                                    foreach ($liste_critere as $critere) {
                                        $idC = $critere['idC'];
                                        $nomC = $critere['nomC'];
                                        $typeC = $critere['type'];
                                        $option_json = $critere['options'];
                                        $options = json_decode($option_json, true);

                                        echo "<h3 class=\"question_critere\">$nomC</h3>";
                                        if ($typeC === "Choix Unique") {
                                            echo "<div class=\"box_reponse_critere\">";
                                            foreach ($options as $key => $option) {
                                                echo "    <div class=\"input_reponse_critere\">";
                                                echo "        <input type=\"radio\" name=\"reponses[$idC]\" value=\"{$option['option']}\" required />";
                                                echo "        <label>{$option['option']}</label>";
                                                echo "    </div>";
                                            }
                                            echo "</div>";
                                        } elseif ($typeC === "Choix Multiple") {
                                            echo "<div class=\"box_reponse_critere\">";
                                            foreach ($options as $key => $option) {
                                                echo "    <div class=\"input_reponse_critere\">";
                                                echo "        <input type=\"checkbox\" name=\"reponses[$idC][]\" value=\"{$option['option']}\" required />";
                                                echo "        <label>{$option['option']}</label>";
                                                echo "    </div>";
                                            }
                                            echo "</div>";
                                        } elseif ($typeC === "Texte Libre") {
                                            echo "<div class=\"box_reponse_critere\">";
                                            echo "        <input class=\"input_text_libre\" type=\"text\" name=\"reponses[$idC]\" placeholder=\"Saisissez votre réponse\" required />";
                                            echo "</div>";
                                        }
                                        echo "<hr style=\"width: 100%; border: 0; border-top: 1px double #5d06323f; margin-bottom: 20px;\" />";
                                    }   
                                ?>
                                <button class="btn_submit_sondage" type="submit">Sauvegarder la réponse</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>