<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: index.php?page=login$auth=false');
    exit();
}

// Verifier le role du compte
$rolesAutorises = ['Admin', 'Responsable', 'Enseignant'];
if (!in_array($_SESSION['role_compte'] ?? '', $rolesAutorises, true)) {
    http_response_code(403);
    echo "Accès interdit pour votre compte";
    exit();
}

require_once 'modele/compte.php';
$id_etudiant = $_GET['etudiant'];
$id_user = Compte::getIdUserParIdEtudiant($id_etudiant);
$id_user = $id_user['idCompte'];
$info_du_compte_etu_ens = Compte::getDetailInfoDuCompteEtu($id_user);
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
    <title>Informations Personnelles</title>
</head>
<style>
    .main_content_panel {
        position: relative;
        flex: 1;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 50px 50px 20px 50px;
    }

    .layout_panel_content {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-template-rows: repeat(2, 1fr) 1.5fr;
        grid-column-gap: 0px;
        grid-row-gap: 0px;
        gap: 30px;
        width: 100%;
        height: 100%;
        position: relative;

        form {
            position: absolute;

            button {
                display: flex;
                height: 40px;
                width: 140px;
                align-items: center;
                justify-content: center;
                transition: all 0.2s linear;
                cursor: pointer;
                border: none;
                border-radius: 10px;
                background: #fff;
                box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;

                span {
                    font-size: 16px;
                    font-weight: 500;
                    color: #5D0632;
                }
            }

            button > svg {
                margin-right: 10px;
                margin-left: 0px;
                font-size: 20px;
                color: #5D0632;
                transition: all 0.4s ease-in;
            }

            button:hover > svg {
                font-size: 1.2em;
                transform: translateX(-5px);
            }

            button:hover {
                box-shadow: 9px 9px 33px #d1d1d1, -9px -9px 10px #ffffff;
                transform: translateY(-2px);
            }
        }
    }

    .avt_box { grid-area: 1 / 1 / 3 / 2; }
    .info_personnelle_box { grid-area: 1 / 2 / 4 / 4; }
    .info_compte_box { grid-area: 3 / 1 / 4 / 2; }

    .box_content {
        box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
        background-color: white;
        border-radius: 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }  

    .input-feild {
        margin-bottom: 20px;
    }

    .avt_box {
        height: fit-content;
        padding-bottom: 40px;
        margin-top: 60px;

        h1 {
            color: #5D0632;
            font-weight: 500;
            margin-bottom: 40px;
        }
        .content_avatar_box {
            width: 80%;
            box-shadow: 0 0 0 10px #5D0632, 0 0 0 15px #fff;
            border-radius: 1000px;
            display: flex;
            justify-content: center;
            align-items: center;  
            aspect-ratio: 1 / 1;  
            img {
                width: 95%;
            }
        }
    }

    .info_compte_box {
        h2 {
            color: #5D0632;
            font-weight: 500;
            margin-bottom: 20px;
        }
        p {
            color: #5D0632;
            font-weight: 400;
            margin-bottom: 20px;
            margin-top: 20px;
            text-align: center;
        }
        label {
            color: #5D0632;
            font-weight: 200;
            text-align: right;
        }
        form {
            display: grid;
            justify-content: center;
            align-items: center;  
        }
        input {
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
        input:hover,
        input:focus {
            border: 2px solid #5D0632;
            box-shadow: 0px 0px 0px 5px rgba(93, 6, 50, 0.2);
            background-color: white;
        }
        button {
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
            i {
                margin-left: 10px;
                display: flex;
                justify-content: center;
                align-items: center;  
            }
        }
        button:hover {
            color: #fff;
            justify-self: center;
            background-color: #5D0632;
        }
    }

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

    .main_content_info_personnelle_panel {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        width: 100%;
        height: 100%;

        .info_personnel_panel {
            width: 100%;
        }
    }
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

    .content_panel {
        width: 100%;
        h2 {
            color: #5D0632;
            font-weight: 500;
            margin-bottom: 20px;
            position: relative;
        }
        h2::before {
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
        .content_panel_row {
            width: 100%;
            margin-bottom: 20px;
            p {
                border: 2px solid transparent;
                padding: 5px 10px;
                padding-left: 0.8em;
                outline: none;
                overflow: hidden;
                background-color: #F3F3F3;
                border-radius: 10px;
                transition: all 0.5s;
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
            <div class="layout_panel_content">
                <form action="index.php">
                    <?php 
                        if (isset($_GET['groupe'])) {
                            echo "<input type=\"hidden\" name=\"page\" value=\"groupeEns\">";
                            echo "<input type=\"hidden\" name=\"promo\" value=\"{$_GET['promo']}\">";
                            echo "<input type=\"hidden\" name=\"groupe\" value=\"{$_GET['groupe']}\">";
                        } else {
                            echo "<input type=\"hidden\" name=\"page\" value=\"promotionEns\">";
                            echo "<input type=\"hidden\" name=\"promo\" value=\"{$_GET['promo']}\">";
                        }
                    ?>
                    <button type="submit">
                        <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024"><path d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"></path></svg>
                        <span>Retour</span>
                    </button>
                </form>
                <div class="box_content avt_box">
                    <h1>Compte Étudiant</h1>
                    <div class="content_avatar_box">
                        <img src="assets/avt_null.png" alt="avt" draggable="false">
                    </div>
                </div>
                <div class="box_content info_personnelle_box">
                    <div class="main_content_info_personnelle_panel">
                        <div class="content_panel info_personnel_panel">
                            <h2>Informations personnelles</h2>
                            <div class="content_panel_row row_content_2_col">
                                <?php echo "<p>{$info_du_compte_etu_ens["nom"]}</p>"; ?>
                                <?php echo "<p>{$info_du_compte_etu_ens["prenom"]}</p>"; ?>
                            </div>
                            <div class="content_panel_row row_content_2_col">
                                <p>Date de naissance</p>
                                <?php echo "<p>{$info_du_compte_etu_ens["dateNaissance"]}</p>"; ?>
                            </div>
                            <div class="content_panel_row row_content_3_col">
                                <?php echo "<p>{$info_du_compte_etu_ens["sexe"]}</p>"; ?>
                                <?php echo "<p>{$info_du_compte_etu_ens["etatCivil"]}</p>"; ?>
                                <?php echo "<p>{$info_du_compte_etu_ens["nationalite"]}</p>"; ?>
                            </div>
                        </div>
                        <div class="content_panel coordonnees_panel">
                            <h2>Coordonnées</h2>
                            <div class="content_panel_row row_content_2_col">
                                <?php echo "<p>{$info_du_compte_etu_ens["phone"]}</p>"; ?>
                                <?php echo "<p>{$info_du_compte_etu_ens["email"]}</p>"; ?>
                            </div>
                            <div class="content_panel_row row_content_1_col">
                                <?php echo "<p>{$info_du_compte_etu_ens["address"]}</p>"; ?>
                            </div>
                        </div>
                        <div class="content_panel promotion_panel">
                            <h2>Promotion</h2>
                            <div class="content_panel_row row_content_2_col">
                                <?php echo $info_du_compte_etu_ens["promotion"] !== null ? "<p>{$info_du_compte_etu_ens["promotion"]}</p>" : "<p>Inconnu</p>"; ?>
                                <?php echo $info_du_compte_etu_ens["groupe"] !== null ? "<p>{$info_du_compte_etu_ens["groupe"]}</p>" : "<p>Inconnu</p>"; ?>
                            </div>
                        </div>
                        <div class="content_panel info_pedagogique_panel">
                            <h2>Informations pédagogiques</h2>
                            <div class="content_panel_row row_content_2_col">
                                <label>Numero Etudiant</label>
                                <?php echo $info_du_compte_etu_ens["idEtudiant"] !== null ? "<p>{$info_du_compte_etu_ens["idEtudiant"]}</p>" : "<p>Inconnu</p>"; ?>
                            </div>
                            <div class="content_panel_row row_content_2_col">
                                <label>Type de bac</label>
                                <?php echo $info_du_compte_etu_ens["typeBac"] !== null ? "<p>{$info_du_compte_etu_ens["typeBac"]}</p>" : "<p>Inconnu</p>"; ?>
                            </div>
                            <div class="content_panel_row row_content_2_col">
                                <label>Parcours</label>
                                <?php echo $info_du_compte_etu_ens["parcoursEtudiant"] !== null ? "<p>{$info_du_compte_etu_ens["parcoursEtudiant"]}</p>" : "<p>Inconnu</p>"; ?>
                            </div>
                            <div class="content_panel_row row_content_2_col">
                                <label>Apprentisage</label>
                                <?php echo $info_du_compte_etu_ens["apprentissageEtudiant"] !== null ? "<p>{$info_du_compte_etu_ens["apprentissageEtudiant"]}</p>" : "<p>Inconnu</p>"; ?>
                            </div>
                            <div class="content_panel_row row_content_2_col">
                                <label>Formation en anglais</label>
                                <?php echo $info_du_compte_etu_ens["formationAnglais"] !== null ? "<p>{$info_du_compte_etu_ens["formationAnglais"]}</p>" : "<p>Inconnu</p>"; ?>
                            </div>
                            <div class="content_panel_row row_content_2_col">
                                <label>Statut academique</label>
                                <?php echo $info_du_compte_etu_ens["statutAcademique"] !== null ? "<p>{$info_du_compte_etu_ens["statutAcademique"]}</p>" : "<p>Inconnu</p>"; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>