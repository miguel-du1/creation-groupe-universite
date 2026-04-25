<?php
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
    header('Location: index.php?page=login');
    exit();
}

// Verifier le role du compte
$rolesAutorises = ['Admin'];
if (!in_array($_SESSION['role_compte'] ?? '', $rolesAutorises, true)) {
    http_response_code(403);
    echo "Accès interdit pour votre compte";
    exit();
}

require_once 'modele/compte.php';
$id_user = $_SESSION['user_id'];
$liste_compte_ensei = Compte::getListeCompteEnsei ($id_user);

$info_compte_ensei_choisi = isset($_GET['ensei']) ? Compte::getDetailInfoDuCompteEns($_GET['ensei']) : array() ;

require_once("modele/pays.php");
$lesPays = Pays::getAllPays();

require_once("modele/matiere.php");
$lesMatieres = Matiere::getAllMatiere();
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
    <title>Gerer Compte Enseignant</title>
</head>
<style>
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

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
    }

    .content_panel {
        display: flex;
        justify-content: flex-start;
        gap: 20px;
        width: 100%;
        height: 100%;
        max-height: 80vh;

        .info_enseignant_panel {
            <?php 
            if (!isset($_GET['ensei'])) echo "display: none;"; 
            else { 
                echo "display: flex;";
                echo "justify-content: flex-start;";
                echo "align-items: flex-start;";
                echo "flex: 1;";
            }
            ?>
            box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
            background-color: white;
            border-radius: 30px;
            padding: 30px 50px;
            color: #5D0632;
            position: relative;
            animation: fadeUp 400ms ease-out both;
            width: 100%;

            form {
                height: 100%;
                width: 100%;
                position: relative;
            }

            .box_content {
                width: 100%;
                height: 100%;
                position: relative;
            }

            .table_content_panel {
                width: 100%;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: stretch;
                position: relative;
                margin-bottom: 20px;

                label {
                    color: #5D0632;
                    font-weight: 300;
                    text-align: left;
                }
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
                    border: #fff solid 8px;
                    border-radius: 50%;
                }
                .content_panel_row {
                    width: 100%;
                    margin-bottom: 10px;
                    display: flex;
                    justify-content: flex-start;
                    align-items: center;
                    gap: 10px;

                    select {
                        border: 2px solid transparent;
                        padding: 5px 10px;
                        padding-left: 0.8em;
                        outline: none;
                        overflow: hidden;
                        background-color: #F3F3F3;
                        border-radius: 10px;
                        transition: all 0.5s;
                        flex: 1;
                        color: #5D0632;
                        font-size: 16px;
                    }

                    input {
                        border: 2px solid transparent;
                        padding: 5px 10px;
                        padding-left: 0.8em;
                        outline: none;
                        overflow: hidden;
                        background-color: #F3F3F3;
                        border-radius: 10px;
                        transition: all 0.5s;
                        flex: 1;
                        color: #5D0632;
                        font-size: 16px;
                    }
                }
            }

            .info_personnelle_box::before {
                content: "";
                position: absolute;
                top: 0;
                left: -25px;
                height: 100%;
                border-left: #5D0632 solid 2px;
            }
        }

        .liste_compte_enseignant_panel {
            box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
            background-color: white;
            border-radius: 30px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            padding: 30px;
            color: #5D0632;
            overflow: scroll;

            h3 {
                margin-bottom: 20px;
                font-weight: 500;
                text-align: center;
                width: 100%;
            }

            form {
                margin-bottom: 10px;
                display: grid;
                grid-template-columns: 1fr 1fr 80px;
                gap: 10px;
                width: 100%;

                p {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: auto;
                    height: 36px;
                    border-bottom: 1px solid #5D0632;
                    border-top: 1px solid #5D0632;
                    font-weight: 300;
                    transition: all 0.3s cubic-bezier(0.15, 0.83, 0.66, 1);
                }

                label {
                    display: flex;
                    align-items: center;
                    width: auto;
                    height: 36px;
                    padding: 0 20px;
                    border-radius: 5px;
                    outline: none;
                    border: 1px solid #e5e5e5;
                    filter: 
                        drop-shadow(0px 1px 0px #efefef)
                        drop-shadow(0px 1px 0.5px rgba(239, 239, 239, 0.5));
                    transition: all 0.3s cubic-bezier(0.15, 0.83, 0.66, 1);
                }

                button {
                    display: flex;
                    flex-direction: row;
                    justify-content: center;
                    align-items: center;
                    padding: 10px 18px;
                    gap: 10px;
                    width: 100%;
                    height: 36px;
                    background: linear-gradient(180deg, #533243ff 0%, #5D0632 50%, #5D0632 100%);
                    box-shadow: 0px 0.5px 0.5px #EFEFEF, 0px 1px 0.5px rgba(239, 239, 239, 0.5);
                    border-radius: 5px;
                    border: 0;
                    font-style: normal;
                    font-weight: 600;
                    font-size: 12px;
                    line-height: 15px;
                    color: #ffffff;
                    cursor: pointer;
                }
            }
        }
    }

    .Btn {
        position: absolute;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: auto;
        border: none;
        padding: 10px 20px;
        background-color: #5D0632;
        color: white;
        font-weight: 500;
        font-size: 16px;
        cursor: pointer;
        border-radius: 10px;
        box-shadow: 5px 5px 0px #5d06326d;
        transition-duration: .3s;
    }

    .svg {
        width: 13px;
        position: absolute;
        right: 0;
        margin-right: 20px;
        fill: white;
        transition-duration: .3s;
    }

    .Btn:hover {
        color: transparent;
    }

    .Btn:hover svg {
        transform: translate(50% , 0%);
        right: 50%;
        margin: 0;
        padding: 0;
        border: none;
        transition-duration: .3s;
    }

    .Btn:active {
        transform: translate(3px , 3px);
        transition-duration: .3s;
        box-shadow: 2px 2px 0px rgb(140, 32, 212);
    }
</style>
<body>
    <div class="main_layout_panel">
        <?php 
            require_once("controleur/c_sidebar.php");
        ?>
        <div class="main_content_panel">
            <div class="nav_panel_promo_page">
                <h1>Gestion des comptes enseignants</h1>
            </div>
            <div class="content_panel">
                <?php if (isset($_GET['ensei'])): ?>
                    <div class="info_enseignant_panel">
                        <form action="index.php?page=compteEns" method="POST" onsubmit="return confirm('Confirmez-vous votre modification à ce compte ?');">
                            <input type="hidden" name="numSocial_ens" value="<?php echo $info_compte_ensei_choisi["numSocial"]; ?>">
                            <div class="box_content info_personnelle_box">
                                <div class="main_content_info_personnelle_panel">
                                    <div class="table_content_panel info_personnel_panel">
                                        <h2>Informations personnelles</h2>
                                        <div class="content_panel_row row_content_2_col">
                                            <input type="text" name="nom_modifier" value="<?php echo $info_compte_ensei_choisi["nom"]; ?>" required>
                                            <input type="text" name="prenom_modifier" value="<?php echo $info_compte_ensei_choisi["prenom"]; ?>" required>
                                        </div>
                                        <div class="content_panel_row row_content_2_col">
                                            <label>Date de naissance</label>
                                            <input type="date" name="dateNaissance_modifier" value="<?php echo $info_compte_ensei_choisi["dateNaissance"]; ?>" required>
                                        </div>
                                        <div class="content_panel_row row_content_3_col">
                                            <select name="sexe_modifier" required>
                                                <option value="homme" <?php echo ($info_compte_ensei_choisi['sexe'] ?? '') === 'M' ? "selected" : ""; ?>>Homme</option>
                                                <option value="femme" <?php echo ($info_compte_ensei_choisi['sexe'] ?? '') === 'F' ? "selected" : ""; ?>>Femme</option>
                                                <option value="autre" <?php echo ($info_compte_ensei_choisi['sexe'] ?? '') === 'autre' ? "selected" : ""; ?>>Autre</option>
                                            </select>
                                            <select name="etatCivil_modifier" required>
                                                <option value="celibataire" <?php echo ($info_compte_ensei_choisi['etatCivil'] ?? '') === 'celibataire' ? "selected" : ""; ?>>Célibataire</option>
                                                <option value="marie" <?php echo ($info_compte_ensei_choisi['etatCivil'] ?? '') === 'marie' ? "selected" : ""; ?>>Marié(e)</option>
                                                <option value="pacse" <?php echo ($info_compte_ensei_choisi['etatCivil'] ?? '') === 'pacse' ? "selected" : ""; ?>>Pacsé(e)</option>
                                                <option value="concubinage" <?php echo ($info_compte_ensei_choisi['etatCivil'] ?? '') === 'concubinage' ? "selected" : ""; ?>>En concubinage / Union libre</option>
                                                <option value="divorce" <?php echo ($info_compte_ensei_choisi['etatCivil'] ?? '') === 'divorce' ? "selected" : ""; ?>>Divorcé(e)</option>
                                                <option value="veuf" <?php echo ($info_compte_ensei_choisi['etatCivil'] ?? '') === 'veuf' ? "selected" : ""; ?>>Veuf / Veuve</option>
                                                <option value="separe" <?php echo ($info_compte_ensei_choisi['etatCivil'] ?? '') === 'separe' ? "selected" : ""; ?>>Séparé(e)</option>
                                            </select>
                                            <select name="nationalite_modifier" required>
                                                <option value="" disabled <?php echo (empty($info_compte_ensei_choisi['nationalite']) ? "selected" : ""); ?>>Nationalité*</option>
                                                <?php 
                                                    foreach ($lesPays as $pays) {
                                                        $nomPays = $pays->get("nomPays");
                                                        $idPays = $pays->get("idPays");
                                                        $isSelected = ($info_compte_ensei_choisi['nationalite'] ?? '') === $nomPays;
                                                        if ($isSelected) echo "<option value=\"$idPays\" selected>$nomPays</option>";
                                                        else echo "<option value=\"$idPays\">$nomPays</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="table_content_panel coordonnees_panel">
                                        <h2>Coordonnées</h2>
                                        <div class="content_panel_row row_content_2_col">
                                            <input type="tel" name="tel_modifier" pattern="^\+?[0-9 ]{6,20}$" value="<?php echo $info_compte_ensei_choisi["phone"]; ?>" required>
                                            <input type="email" name="email_modifier" value="<?php echo $info_compte_ensei_choisi["email"]; ?>" required>
                                        </div>
                                        <?php 
                                            $addr = $info_compte_ensei_choisi["address"];
                                            if (preg_match('/^\s*(.+?)\s*,\s*(\d{5})\s+([^,]+?)\s*,\s*([^,]+?)\s*$/u', $addr, $m)) {
                                                $address = trim($m[1]);
                                                $code_postal = trim($m[2]);
                                                $ville = trim($m[3]); 
                                                $nom_pays = trim($m[4]);
                                            } else {
                                                $address = $code_postal = $ville = $nom_pays = '';
                                            }
                                        ?>
                                        <div class="content_panel_row row_content_1_col">
                                            <input type="text" name="address_modifier" value="<?php echo $address; ?>" required>
                                        </div>
                                        <div class="content_panel_row row_content_2_col">
                                            <input type="number" name="codePostal_modifier" placeholder="Code postal*" value="<?php echo $code_postal; ?>" required>
                                            <input type="text" name="ville_modifier" placeholder="Ville*" value="<?php echo $ville; ?>" required>
                                            <select name="pays_modifier" required>
                                                <?php 
                                                    foreach ($lesPays as $pays) {
                                                        $nomPays = $pays->get("nomPays");
                                                        $isSelected = $nom_pays === $nomPays;
                                                        if ($isSelected) echo "<option value=\"$nomPays\" selected>$nomPays</option>";
                                                        else echo "<option value=\"$nomPays\">$nomPays</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="table_content_panel coordonnees_panel">
                                        <h2>Informations pédagogiques</h2>
                                        <div class="content_panel_row row_content_2_col">
                                            <label>Départerment</label>
                                            <select name="departement_modifier">
                                                <option value="" <?php echo ($info_compte_ensei_choisi['departement'] ?? '') === '' ? "selected" : ""; ?>>Aucun</option>
                                                <option value="Informatique" <?php echo ($info_compte_ensei_choisi['departement'] ?? '') === 'Informatique' ? "selected" : ""; ?>>Informatique</option>
                                                <option value="Mesures Physiques" <?php echo ($info_compte_ensei_choisi['departement'] ?? '') === 'Mesures Physiques' ? "selected" : ""; ?>>Mesures Physiques</option>
                                                <option value="Chimie" <?php echo ($info_compte_ensei_choisi['departement'] ?? '') === 'Chimie' ? "selected" : ""; ?>>Chimie</option>
                                            </select>
                                        </div>
                                        <div class="content_panel_row row_content_2_col">
                                            <label>Responsable</label>
                                            <select name="responsable_modifier">
                                                <option value="" <?php echo ($info_compte_ensei_choisi['responsable'] ?? '') === '' ? "selected" : ""; ?>>Aucun</option>
                                                <?php 
                                                    foreach ($lesMatieres as $matiere) {
                                                        $nomMatiere = $matiere->get("nomMatiere");
                                                        $idMatiere = $matiere->get("idMatiere");
                                                        $isSelected = $info_compte_ensei_choisi["responsable"] === $nomMatiere;
                                                        if ($isSelected) echo "<option value=\"$idMatiere\" selected>$nomMatiere</option>";
                                                        else echo "<option value=\"$idMatiere\">$nomMatiere</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>   
                                    </div>
                                    <div class="table_content_panel role_panel">
                                        <h2>Rôles et Permissions</h2>
                                        <div class="content_panel_row row_content_1_col">
                                            <select name="role_compte_modifier">
                                                <option value="Enseignant" <?php if ($info_compte_ensei_choisi['role_compte'] === 'Enseignant') echo "selected"; ?> >Enseignant</option>
                                                <option value="Admin" <?php if ($info_compte_ensei_choisi['role_compte'] === 'Admin') echo "selected"; ?>>Enseignants responsables de la formation</option>
                                                <option value="Responsable" <?php if ($info_compte_ensei_choisi['role_compte'] === 'Responsable') echo "selected"; ?>>Enseignants responsables de filières, d'année ou de semestre</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="Btn">
                                Enregistrer 
                                <svg class="svg" viewBox="0 0 512 512">
                                <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path></svg>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
                <div class="liste_compte_enseignant_panel">
                    <h3>Liste des comptes enseignants</h3>
                    <hr style="width:100%; border:0; border-top:1px dashed rgba(93, 6, 50, 0.35); margin-bottom: 20px;" />
                    <form>
                        <p>Login</p>
                        <p>Rôle</p>
                    </form>
                    <?php $compterCompte = 1; ?>
                    <?php foreach ($liste_compte_ensei as $compte): ?>
                        <?php $id_compte = $compte['idCompte'];?>
                        <form action="index.php" method="GET" >
                            <input type="hidden" name="page" value="compteEns">
                            <input type="hidden" name="ensei" value="<?php echo "$id_compte"; ?>">
                            <?php
                                $role_compte = $compte['roleCompte'];
                                $username = $compte['username'];
                            ?>
                            <label><?php echo "$username"; ?></label>
                            <label><?php echo "$role_compte"; ?></label>
                            <button type="submit">Modifier</button>
                        </form>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>