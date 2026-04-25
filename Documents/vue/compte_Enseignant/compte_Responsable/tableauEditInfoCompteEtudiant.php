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

require_once 'modele/compte.php';
$id_etudiant = $_GET['etudiant'];
$id_user = Compte::getIdUserParIdEtudiant($id_etudiant);
$id_user = $id_user['idCompte'];
$info_du_compte = Compte::getDetailInfoDuCompteEtu($id_user);

require_once("modele/pays.php");
$lesPays = Pays::getAllPays();
?>

<div class="main_content_info_personnelle_panel">
    <form action="index.php?page=profilEtuRes" method="post" >
        <?php 
            if (isset($_GET['groupe'])) {
                echo "<input type=\"hidden\" name=\"promo\" value=\"{$_GET['promo']}\">";
                echo "<input type=\"hidden\" name=\"groupe\" value=\"{$_GET['groupe']}\">";
                echo "<input type=\"hidden\" name=\"etudiant\" value=\"{$_GET['etudiant']}\">";
            } else {
                echo "<input type=\"hidden\" name=\"promo\" value=\"{$_GET['promo']}\">";
                echo "<input type=\"hidden\" name=\"etudiant\" value=\"{$_GET['etudiant']}\">";
            }
            echo "<input type=\"hidden\" name=\"numSocial_etu\" value=\"{$info_du_compte["numSocial"]}\">";    
        ?>
        <div class="content_panel info_personnel_panel">
            <h2>Informations personnelles</h2>
            <div class="content_panel_row row_content_2_col">
                <input type="text" name="nom_modifier" value="<?php echo $info_du_compte["nom"]; ?>" required>
                <input type="text" name="prenom_modifier" value="<?php echo $info_du_compte["prenom"]; ?>" required>
            </div>
            <div class="content_panel_row row_content_2_col">
                <p>Date de naissance</p>
                <input type="date" name="dateNaissance_modifier" value="<?php echo $info_du_compte["dateNaissance"]; ?>" required>
            </div>
            <div class="content_panel_row row_content_3_col">
                <select name="sexe_modifier" required>
                    <option value="homme" <?php echo ($info_du_compte['sexe'] ?? '') === 'M' ? "selected" : ""; ?>>Homme</option>
                    <option value="femme" <?php echo ($info_du_compte['sexe'] ?? '') === 'F' ? "selected" : ""; ?>>Femme</option>
                    <option value="autre" <?php echo ($info_du_compte['sexe'] ?? '') === 'autre' ? "selected" : ""; ?>>Autre</option>
                </select>
                <select name="etatCivil_modifier" required>
                    <option value="celibataire" <?php echo ($info_du_compte['etatCivil'] ?? '') === 'celibataire' ? "selected" : ""; ?>>Célibataire</option>
                    <option value="marie" <?php echo ($info_du_compte['etatCivil'] ?? '') === 'marie' ? "selected" : ""; ?>>Marié(e)</option>
                    <option value="pacse" <?php echo ($info_du_compte['etatCivil'] ?? '') === 'pacse' ? "selected" : ""; ?>>Pacsé(e)</option>
                    <option value="concubinage" <?php echo ($info_du_compte['etatCivil'] ?? '') === 'concubinage' ? "selected" : ""; ?>>En concubinage / Union libre</option>
                    <option value="divorce" <?php echo ($info_du_compte['etatCivil'] ?? '') === 'divorce' ? "selected" : ""; ?>>Divorcé(e)</option>
                    <option value="veuf" <?php echo ($info_du_compte['etatCivil'] ?? '') === 'veuf' ? "selected" : ""; ?>>Veuf / Veuve</option>
                    <option value="separe" <?php echo ($info_du_compte['etatCivil'] ?? '') === 'separe' ? "selected" : ""; ?>>Séparé(e)</option>
                </select>
                <select name="nationalite_modifier" required>
                    <?php 
                        foreach ($lesPays as $pays) {
                            $nomPays = $pays->get("nomPays");
                            $idPays = $pays->get("idPays");
                            $isSelected = ($info_du_compte['nationalite'] ?? '') === $nomPays;
                            if ($isSelected) echo "<option value=\"$idPays\" selected>$nomPays</option>";
                            else echo "<option value=\"$idPays\">$nomPays</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="content_panel coordonnees_panel">
            <h2>Coordonnées</h2>
            <div class="content_panel_row row_content_2_col">
                <input type="tel" name="tel_modifier" pattern="^\+?[0-9 ]{6,20}$" value="<?php echo $info_du_compte["phone"]; ?>" required>
                <input type="email" name="email_modifier" value="<?php echo $info_du_compte["email"]; ?>" required>
            </div>
            <div class="content_panel_row row_content_1_col">
                <?php 
                    $addr = $info_du_compte["address"];
                    if (preg_match('/^\s*(.+?)\s*,\s*(\d{5})\s+([^,]+?)\s*,\s*([^,]+?)\s*$/u', $addr, $m)) {
                        $address = trim($m[1]);
                        $code_postal = trim($m[2]);
                        $ville = trim($m[3]); 
                        $nom_pays = trim($m[4]);
                    } else {
                        $address = $code_postal = $ville = $nom_pays = '';
                    }
                ?>
                <input type="text" name="address_modifier" value="<?php echo $address; ?>" required>
            </div>
            <div class="content_panel_row row_content_3_col">
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
        <div class="content_panel info_pedagogique_panel">
            <h2>Informations pédagogiques</h2>
            <div class="content_panel_row row_content_2_col">
                <label>Numero Etudiant</label>
                <?php echo $info_du_compte["idEtudiant"] !== null ? "<p>{$info_du_compte["idEtudiant"]}</p>" : "<p>Inconnu</p>"; ?>
            </div>
            <div class="content_panel_row row_content_2_col">
                <label>Type de bac</label>
                <select name="typeBac_modifier">
                    <option value="" <?php echo ($info_du_compte['typeBac'] ?? '') === '' ? "selected" : ""; ?>>Inconnu</option>
                    <option value="Général" <?php echo ($info_du_compte['typeBac'] ?? '') === 'Général' ? "selected" : ""; ?>>Général</option>
                    <option value="Technologique" <?php echo ($info_du_compte['typeBac'] ?? '') === 'Technologique' ? "selected" : ""; ?>>Technologique</option>
                    <option value="Professionnel" <?php echo ($info_du_compte['typeBac'] ?? '') === 'Professionnel' ? "selected" : ""; ?>>Professionnel</option>
                </select>
            </div>
            <div class="content_panel_row row_content_2_col">
                <label>Parcours</label>
                <select name="parcours_modifier">
                    <option value="" <?php echo ($info_du_compte['parcoursEtudiant'] ?? '') === '' ? "selected" : ""; ?>>Inconnu</option>
                    <option value="A" <?php echo ($info_du_compte['parcoursEtudiant'] ?? '') === 'Général' ? "selected" : ""; ?>>Parcours A :  Réalisation d'applications : conception, développement, validation (en formation initiale et en apprentissage)</option>
                    <option value="B" <?php echo ($info_du_compte['parcoursEtudiant'] ?? '') === 'Technologique' ? "selected" : ""; ?>>Parcours B : Déploiement d’applications communicantes et sécurisées par apprentissage</option>
                    <option value="C" <?php echo ($info_du_compte['parcoursEtudiant'] ?? '') === 'Professionnel' ? "selected" : ""; ?>>Parcours C : Administration, gestion et exploitation des données par apprentissage</option>
                </select>
            </div>
            <div class="content_panel_row row_content_2_col">
                <label>Apprentisage</label>
                <select name="apprentisage_modifier">
                    <option value="" <?php echo ($info_du_compte['apprentissageEtudiant'] ?? '') === '' ? "selected" : ""; ?>>Inconnu</option>
                    <option value="1" <?php echo ($info_du_compte['apprentissageEtudiant'] ?? '') === '1' ? "selected" : ""; ?>>Oui</option>
                    <option value="0" <?php echo ($info_du_compte['apprentissageEtudiant'] ?? '') === '0' ? "selected" : ""; ?>>Non</option>
                </select>
            </div>
            <div class="content_panel_row row_content_2_col">
                <label>Formation en anglais</label>
                <select name="formation_anglais_modifier">
                    <option value="" <?php echo ($info_du_compte['formationAnglais'] ?? '') === '' ? "selected" : ""; ?>>Inconnu</option>
                    <option value="1" <?php echo ($info_du_compte['formationAnglais'] ?? '') === '1' ? "selected" : ""; ?>>Oui</option>
                    <option value="0" <?php echo ($info_du_compte['formationAnglais'] ?? '') === '0' ? "selected" : ""; ?>>Non</option>
                </select>
            </div>
            <div class="content_panel_row row_content_2_col">
                <label>Statut academique</label>
                <input type="text" name="statut_academique_modifier" value="<?php echo $info_du_compte["statutAcademique"]; ?>" placeholder="Saisissez ici ...">
            </div>
        </div>
        <?php require_once 'vue/compte_Enseignant/compte_Responsable/buttonEnregistrer.php'; ?>
    </form>
</div>