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

require_once 'modele/etudiant.php';
$liste_etudiants = Etudiant::getListeEtudiants();

require_once 'modele/promotion.php';
$liste_promotions = Promotion::getAllPromotion();
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
    <title>Ajouter Étudiant</title>
</head>
<style>
    .main_content_panel {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        flex: 1;
        height: calc(100vh);
        padding: 50px;
        box-sizing: border-box;
    }

    .layout_panel_content {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        gap: 30px;
        width: 100%;
        height: 100%;
        position: relative;

        .btn_retour {
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

        .form_ajouter {
            display: grid;
            grid-template-columns: 8fr 2fr;
            grid-template-rows: 9fr auto;
            grid-column-gap: 30px;
            grid-row-gap: 30px;
            height: 93%;
        }

        .liste_etudiant_box { grid-area: 1 / 1 / 2 / 2; }
        .liste_promo_box { grid-area: 1 / 2 / 2 / 3; }
        .btn_ajouter { grid-area: 2 / 1 / 3 / 3; }
    }

    .box_content {
        box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
        background-color: white;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        padding: 0 20px 20px 20px;
        position: relative;
        overflow: scroll;
        overflow-y: auto;
    }  

    /* From Uiverse.io by Na3ar-17 */ 
    .radio-input {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100%;
    }

    .radio-input * {
        box-sizing: border-box;
        padding: 0;
        margin: 0;
    }

    .radio-input .label_liste_etudiant,
    .radio-input .label_liste_promotion {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 0px 20px;
        width: 100%;
        cursor: pointer;
        height: 50px;
        position: relative;
    }

    .radio-input .label_liste_etudiant::before,
    .radio-input .label_liste_promotion::before {
        position: absolute;
        content: "";
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 45px;
        z-index: 1;
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        border-radius: 10px;
        border: 2px solid transparent;
    }
    .radio-input .label_liste_etudiant:hover::before,
    .radio-input .label_liste_promotion:hover::before {
        transition: all 0.2s ease;
        background-color: #EFE7EB;
    }

    .radio-input .label_liste_etudiant:has(input:checked)::before,
    .radio-input .label_liste_promotion:has(input:checked)::before {
        background-color: #EFE7EB;
        border-color: #5D0632;
        height: 50px;
    }
    .radio-input .label_liste_etudiant .text,
    .radio-input .label_liste_promotion .text {
        color: #5D0632;
        font-size: 18px;
        z-index: 2;
    }

    .radio-input .label_liste_etudiant input[type="radio"],
    .radio-input .label_liste_promotion input[type="radio"] {
        background-color: #5D0632;
        appearance: none;
        width: 17px;
        height: 17px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2;
    }
    .radio-input .label_liste_etudiant input[type="radio"]:checked,
    .radio-input .label_liste_promotion input[type="radio"]:checked {
        background-color: rgba(130, 9, 69, 0.4);
        -webkit-animation: puls 0.7s forwards;
        animation: pulse 0.7s forwards;
    }

    .radio-input .label_liste_etudiant input[type="radio"]:before,
    .radio-input .label_liste_promotion input[type="radio"]:before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        transition: all 0.1s cubic-bezier(0.165, 0.84, 0.44, 1);
        background-color: #fff;
        transform: scale(0);
    }

    .radio-input .label_liste_etudiant input[type="radio"]:checked::before,
    .radio-input .label_liste_promotion input[type="radio"]:checked::before {
        transform: scale(1);
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
        }
        70% {
            box-shadow: 0 0 0 8px rgba(255, 255, 255, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
        }
    }   

    .radio-input > .label_liste_etudiant, .radio-input > .heading_liste_etudiant {
        display: grid;
        grid-template-columns: 0.5fr 1fr 1fr 1fr 1fr 1fr;
        justify-items: center;
        align-items: center;
    }

    .heading_liste_etudiant {
        padding: 20px 20px 10px 20px;
        background-color: #fff;
        border-bottom: #5D0632 solid 2px;
        position: sticky;
        top: 0;
        z-index: 5;

        p {
            font-weight: 400;
            color: #5D0632;
        }
    }

    .radio-input > label, .radio-input > .heading_liste_promotion {
        display: grid;
        grid-template-columns: 1fr;
        justify-items: center;
        align-items: center;
    }

    .heading_liste_promotion {
        padding: 20px 20px 10px 20px;
        background-color: #fff;
        border-bottom: #5D0632 solid 2px;
        position: sticky;
        top: 0;
        z-index: 5;

        p {
            font-weight: 400;
            color: #5D0632;
        }
    }

    .btn_ajouter {
        display: flex;
        height: 50px;
        width: 100%;
        align-items: center;
        justify-content: center;
        transition: all 0.2s linear;
        cursor: pointer;
        border: none;
        border-radius: 10px;
        background: #5D0632;
        box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
        font-size: 16px;
        font-weight: 500;
        color: #fff;
    }

    .btn_ajouter:hover > svg {
        font-size: 1.2em;
        transform: translateX(-5px);
    }

    .btn_ajouter:hover {
        box-shadow: 9px 9px 33px #d1d1d1, -9px -9px 10px #ffffff;
        transform: translateY(-2px);
    }
</style>
<body>
    <div class="main_layout_panel">
        <?php 
            require_once("controleur/c_sidebar.php");
        ?>
        <div class="main_content_panel">
            <div class="layout_panel_content">
                <form action="index.php" class="btn_retour">
                    <input type="hidden" name="page" value="promotionRes">
                    <button type="submit">
                        <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024"><path d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z"></path></svg>
                        <span>Retour</span>
                    </button>
                </form>
                <form action="index.php?page=ajouterEtuRes" method="post" class="form_ajouter" >
                    <div class="box_content liste_etudiant_box">
                        <div class="radio-input">
                            <div class="heading_liste_etudiant">
                                <p class="text"></p>
                                <p class="text">ID d'étudiant</p>
                                <p class="text">Numéro social</p>
                                <p class="text">Nom</p>
                                <p class="text">Prénom</p>
                                <p class="text">Promotion en cours</p>
                            </div>
                            <?php foreach ($liste_etudiants as $etudiant): ?>
                                <label class="label_liste_etudiant">
                                    <input
                                        type="radio"
                                        name="etudiant"
                                        value="<?php echo "{$etudiant->get("idEtudiant")}"; ?>"
                                    />
                                    <p class="text"><?php echo "{$etudiant->get("idEtudiant")}"; ?></p>
                                    <p class="text"><?php echo "{$etudiant->get("numSocial")}"; ?></p>
                                    <p class="text"><?php echo "{$etudiant->get("nom")}"; ?></p>
                                    <p class="text"><?php echo "{$etudiant->get("prenom")}"; ?></p>
                                    <p class="text"><?php echo "{$etudiant->get("promotion")}"; ?></p>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="box_content liste_promo_box">
                        <div class="radio-input">
                            <div class="heading_liste_promotion">
                                <p class="text"></p>
                                <p class="text">Année Promotion</p>
                            </div>
                            <?php foreach ($liste_promotions as $promotion): ?>
                                <label class="label_liste_promotion">
                                    <input
                                        type="radio"
                                        name="promo"
                                        value="<?php echo "{$promotion->get("idPromotion")}"; ?>"
                                    />
                                    <p class="text"><?php echo "{$promotion->get("anneePromotion")}"; ?></p>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="submit" class="btn_ajouter">Inscrire l’étudiant à la promotion sélectionnée</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>