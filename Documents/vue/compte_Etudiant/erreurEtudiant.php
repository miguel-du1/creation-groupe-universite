<?php
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: index.php?page=login');
    exit();
}

require_once 'modele/erreur.php';
$liste_type_erreur = Erreur::getAllTypeErreurs();
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
    <title>Signaler Une Erreur</title>
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
    }

    .formulaire_signaler_erreur {
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        gap: 20px;
        width: 100%;
        height: 100%;

        form {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            gap: 20px;
            width: fit-content;
            min-width: 50%;
            height: fit-content;
            background-color: rgba(93, 6, 50, 0.1);
            outline: #5D0632 solid 2px;
            padding: 20px;
            border-radius: 20px;

            .message_success {
                color: #5D0632;
                font-weight: 500;
                text-align: center;
                width: 100%;
            }

            .message_erreur {
                color: #ff0000ff;
                font-weight: 500;
                text-align: center;
                width: 100%;
            }

            .input_feild {
                display: grid;
                grid-template-columns: repeat(1, 1fr);
                width: 100%;
                gap: 20px;

                label {
                    color: #5D0632;
                    font-weight: 400;
                }

                textarea {
                    padding: 20px;
                    border-radius: 10px;
                }
            }

            .input_feild_2_col {
                display: flex;
                justify-content: flex-start;
                align-items: center;
                width: 100%;
                gap: 20px;

                label {
                    color: #5D0632;
                    font-weight: 400;
                }

                select {
                    flex: 1;
                    padding: 10px 20px;
                    border-radius: 10px;
                }
            }

            button {
                font-family: inherit;
                font-size: 20px;
                background: #5D0632;
                color: #fff;
                padding: 0.7em 1em;
                padding-left: 0.9em;
                display: flex;
                justify-content: center;
                align-items: center;
                border: none;
                border-radius: 10px;
                overflow: hidden;
                transition: all 0.2s;
                cursor: pointer;
                width: 100%;
            }

            button span {
                display: block;
                margin-left: 1em;
                transition: all 0.3s ease-in-out;
            }

            button svg {
                display: block;
                transform-origin: center center;
                transition: transform 0.3s ease-in-out;
            }

            button:hover .svg-wrapper {
                animation: fly-1 0.6s ease-in-out infinite alternate;
            }

            button:hover svg {
                transform: translateX(4em) rotate(45deg) scale(1.1);
            }

            button:hover span {
                transform: translateX(40em);
            }

            button:active {
                transform: scale(0.95);
            }

            @keyframes fly-1 {
                from {
                    transform: translateY(0.1em);
                }

                to {
                    transform: translateY(-0.1em);
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
                <h1>Signaler Une Erreur Etudiant</h1>
            </div>
            <div class="formulaire_signaler_erreur">
                <form action="index.php?page=erreurEtu" method="post">
                    <?php 
                        if (isset($_GET['success'])) {
                            echo $_GET['success'] == "true" ? "<p class=\"message_success\">Merci, votre signalement a bien été pris en compte !</p>" : "<p class=\"message_erreur\">Une erreur est survenue. Veuillez réessayer !</p>" ;
                        }
                    ?>
                    <input type="hidden" name="id_user" value="<?php echo "{$_SESSION['user_id']}"; ?>">
                    <input type="hidden" name="dateEnvoie" value="<?php echo date('Y-m-d'); ?>">
                    <div class="input_feild_2_col">
                        <label for="typeErreur">Type d’erreur</label>
                        <select name="typeErreur">
                            <?php 
                                foreach ($liste_type_erreur as $type) {
                                    $id = $type->get("idTypeErreur");
                                    $nom = $type->get("nomTypeErreur");
                                    echo "<option value=\"$id\">$nom</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="input_feild">
                        <label for="description">Description de l’erreur :</label>
                        <textarea name="description" placeholder="Décrivez l’erreur ici" rows="20"></textarea>
                    </div>
                    <button type="submit">
                        <div class="svg-wrapper-1">
                            <div class="svg-wrapper">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                width="24"
                                height="24"
                            >
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path
                                fill="currentColor"
                                d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z"
                                ></path>
                            </svg>
                            </div>
                        </div>
                        <span>Envoyer le rapport</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>