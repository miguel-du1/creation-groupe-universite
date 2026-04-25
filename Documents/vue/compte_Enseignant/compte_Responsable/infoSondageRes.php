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

require_once 'modele/sondage.php';
$id_sondage = $_GET['sondage'] ?? null;
$sondage = Sondage::getSondageRes($id_sondage);
$reponses_sondage = Sondage::getReponsesSondageRes($id_sondage);
$criteres_sondage = Sondage::getCritereSondage($id_sondage);

$data = $reponses_sondage;
$stats = [];
foreach ($data as $idS => $students) {
    foreach ($students as $idEtudiant => $student) {
        foreach (($student['reponses'] ?? []) as $rep) {
            $idCritere  = $rep['id_Critere'] ?? null;
            if ($idCritere === null) continue;
            $nomCritere = $rep['nom_critere'] ?? '';
            $raw        = $rep['reponse'] ?? '';
            if (!isset($stats[$idCritere])) {
                $stats[$idCritere] = [
                    'nom_critere' => $nomCritere,
                    'reponses'    => []
                ];
            }
            if ($stats[$idCritere]['nom_critere'] === '' && $nomCritere !== '') {
                $stats[$idCritere]['nom_critere'] = $nomCritere;
            }
            $answers = [];
            if (is_array($raw)) {
                $answers = $raw;
            } else {
                $s = trim((string)$raw);
                $decoded = json_decode($s, true);
                if (is_array($decoded)) {
                    if (isset($decoded[0]) && is_array($decoded[0])) {
                        $answers = $decoded[0];
                    } else {
                        $answers = $decoded;
                    }
                } else {
                    $answers = [$s];
                }
            }
            foreach ($answers as $a) {
                $a = trim((string)$a);
                if ($a === '') continue;
                $stats[$idCritere]['reponses'][$a] = ($stats[$idCritere]['reponses'][$a] ?? 0) + 1;
            }
        }
    }
}


$data_critere = $criteres_sondage;
foreach ($data_critere as &$critere) {
    $json = $critere['options'] ?? '[]';
    $decoded = is_string($json) ? json_decode($json, true) : $json;
    if (!is_array($decoded)) $decoded = [];
    $kv = [];
    foreach ($decoded as $item) {
        if (!is_array($item)) continue;
        $opt = $item['option'] ?? null;
        if ($opt === null || $opt === '') continue;
        $kv[$opt] = $opt;
    }
    $critere['options'] = $kv;
}
unset($critere);
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
    <title>Informations Personnelles</title>
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
        display: grid;
        grid-template-columns: 1fr;
        grid-template-rows: auto auto auto;
        justify-content: start;
        align-content: start;    
        gap: 30px;
        width: 100%;
        flex: 1;
        height: calc(100vh - 100px);
        min-height: 0;
        position: relative;
        overflow: visible;
        box-sizing: border-box;
    }

    .box_content{
        background-color: #fff;
        border-radius: 5px;
        display: flex;
        flex-direction: column;
        position: relative;
        box-sizing: border-box;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        box-shadow: #5d06329d 0px 0px 0px 1px;
    }

    .info_sondage_box{
        grid-row: 1;
    }

    .info_sondage_box table{
        width: 100%;
        border-collapse: collapse;
        color: #5D0632;
        border: rgba(93, 6, 50, 0.5) solid 2px;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: #5d06329d 0px 0px 0px 1px;
    }

    .info_sondage_box thead th{
        background-color: rgba(255, 244, 249, 1);
        padding: 10px 20px;
        font-weight: 400;
        font-size: 18px;
        border: #5d06329d solid 1px;
    }

    .info_sondage_box tbody td{
        background-color: #ffffff;
        padding: 10px;
        font-weight: 200;
        font-size: 18px;
        border: #5D0632 solid 1px;
        text-align: center;
    }

    .statistiques_box {
        grid-row: 2;
        min-height: 0;
        height: 100%;
        max-height: 280px;
        overflow: hidden;
        position: relative;
    }

    .resultats_box{
        grid-row: 3;
        overflow-x: hidden;
        height: 100%;
        max-height: 400px;
        overflow-y: auto !important;
        min-height: 0 !important;
        position: relative;
    }

    .statistiques_box .content_statistiques_box,
    .resultats_box .content_resultats_box {
        width: 100%;
        overflow: auto;
        min-height: 0;
        position: relative;
    }

    .statistiques_box .content_statistiques_box table,
    .resultats_box .content_resultats_box table {
        width: 100%;
        border-collapse: collapse;
        color: #5D0632;
        border: rgba(93, 6, 50, 0.5) solid 2px;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: #5d06329d 0px 0px 0px 1px;
        position: relative;
        z-index: 10;
    }

    .statistiques_box .content_statistiques_box table thead,
    .resultats_box .content_resultats_box table thead {
        background-color: rgba(255, 244, 249, 1);
        padding: 10px 20px;
        font-weight: 400;
        font-size: 16px;
        border: #5d06329d solid 1px;
        position: static;
        z-index: 2;
    }

    .statistiques_box .content_statistiques_box table thead th,
    .resultats_box .content_resultats_box table thead th {
        background-color: rgba(255, 244, 249, 1);
        padding: 10px 20px;
        font-weight: 400;
        font-size: 16px;
        border: #5d06329d solid 1px;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .statistiques_box .content_statistiques_box table tbody td,
    .resultats_box .content_resultats_box table tbody td {
        background-color: #ffffff;
        padding: 5px;
        font-weight: 200;
        font-size: 14px;
        border: #5D0632 solid 1px;
        text-align: center;
    }

    .input-feild{
        margin-bottom: 20px;
    }

    .layout_panel_content{ overflow: visible; }
    .statistiques_box, .resultats_box{ height: 100%; min-height: 0; }
    .box_content{ overflow-y: auto; }
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
                <h1>Analyse détaillée du sondage</h1>
            </div>
            <div class="layout_panel_content">
                <div class="info_sondage_box">
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $compteE = 1;
                                $id = $sondage->get("idS");
                                $nom = $sondage->get("nomS");
                                $debut = $sondage->get("debutS");
                                $fin = $sondage->get("finS");
                                $nb_reponses = $sondage->get("nb_reponsesS");
                                $email = $sondage->get("emailS");

                                echo "<tr>";
                                echo "<td>$compteE</td>";
                                echo "<td>$id</td>";
                                echo "<td>$nom</td>";
                                echo "<td>$debut</td>";
                                echo "<td>$fin</td>";
                                echo "<td>$nb_reponses</td>";
                                echo "<td>$email</td>";
                                echo "</tr>";

                                $compteE += 1;
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="box_content statistiques_box">
                    <div class="content_statistiques_box">
                        <table>
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Nom du critère</th>
                                    <th scope="col">Type de réponse</th>
                                    <th scope="col">Option</th>
                                    <th scope="col">Nombre de réponses</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data_critere as $critere): ?>
                                    <?php
                                        $options = $critere['options'] ?? [];
                                        $count_options = max(1, count($options));
                                        $first = true;
                                    ?>
                                    <?php if ($count_options === 1 && empty($options)): ?>
                                        <tr>
                                            <td rowspan="1"><?php echo $critere['idC']; ?></td>
                                            <td rowspan="1"><?php echo $critere['nomC']; ?></td>
                                            <td rowspan="1"><?php echo $critere['type']; ?></td>
                                            <td>—</td>
                                            <td>—</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($options as $optKey => $optVal): ?>
                                            <tr>
                                                <?php if ($first): ?>
                                                    <td rowspan="<?php echo $count_options ?>"><?php echo $critere['idC']; ?></td>
                                                    <td rowspan="<?php echo $count_options ?>"><?php echo $critere['nomC']; ?></td>
                                                    <td rowspan="<?php echo $count_options ?>"><?php echo $critere['type']; ?></td>
                                                <?php $first = false; ?>
                                                <?php endif; ?>
                                                <td><?php echo is_string($optVal) ? $optVal : $optKey ?></td>
                                                <td><?php echo is_string($optVal) ? $stats[$critere['idC']]['reponses'][$optKey] ?? 0 : $optKey . " OK" ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box_content resultats_box">
                    <div class="content_resultats_box">
                        <table>
                            <thead>
                                <tr>
                                    <th scope="col">ID de l’étudiant</th>
                                    <th scope="col">Nom</th>
                                    <th scope="col">Prénom</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Date de réponse</th>
                                    <th scope="col">Nom du critère</th>
                                    <th scope="col">Option choisie</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reponses_sondage[$_GET['sondage']] as $info_etudiant): ?>
                                    <?php
                                        $options = $info_etudiant['reponses'] ?? [];
                                        $count_options = max(1, count($options));
                                        $first = true;
                                    ?>
                                    <?php if ($count_options === 1 && empty($options)): ?>
                                        <tr>
                                            <td rowspan="1"><?php echo $info_etudiant['id_Etudiant']; ?></td>
                                            <td rowspan="1"><?php echo $info_etudiant['nom']; ?></td>
                                            <td rowspan="1"><?php echo $info_etudiant['prenom']; ?></td>
                                            <td rowspan="1"><?php echo $info_etudiant['email']; ?></td>
                                            <td rowspan="1"><?php echo $info_etudiant['date_reponse']; ?></td>
                                            <td>—</td>
                                            <td>—</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($options as $optKey => $optVal): ?>
                                            <tr>
                                                <?php if ($first): ?>
                                                    <td rowspan="<?php echo $count_options ?>"><?php echo $info_etudiant['id_Etudiant']; ?></td>
                                                    <td rowspan="<?php echo $count_options ?>"><?php echo $info_etudiant['nom']; ?></td>
                                                    <td rowspan="<?php echo $count_options ?>"><?php echo $info_etudiant['prenom']; ?></td>
                                                    <td rowspan="<?php echo $count_options ?>"><?php echo $info_etudiant['email']; ?></td>
                                                    <td rowspan="<?php echo $count_options ?>"><?php echo $info_etudiant['date_reponse']; ?></td>
                                                    <?php $first = false; ?>
                                                <?php endif; ?>
                                                <td><?php echo $optVal['nom_critere']; ?></td>
                                                <?php 
                                                    $raw = $optVal['reponse'] ?? '';
                                                    $decoded = is_string($raw) ? json_decode($raw, true) : $raw;

                                                    if (is_array($decoded)) {
                                                        $val = implode(', ', $decoded);
                                                    } else {
                                                        $val = (string)$raw;
                                                    }
                                                ?>
                                                <td><?php echo $val; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>