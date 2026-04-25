<div class="main_content_info_personnelle_panel">
    <div class="content_panel info_personnel_panel">
        <h2>Informations personnelles</h2>
        <div class="content_panel_row row_content_2_col">
            <?php echo "<p>{$info_du_compte_etu_res["nom"]}</p>"; ?>
            <?php echo "<p>{$info_du_compte_etu_res["prenom"]}</p>"; ?>
        </div>
        <div class="content_panel_row row_content_2_col">
            <p>Date de naissance</p>
            <?php echo "<p>{$info_du_compte_etu_res["dateNaissance"]}</p>"; ?>
        </div>
        <div class="content_panel_row row_content_3_col">
            <?php echo "<p>{$info_du_compte_etu_res["sexe"]}</p>"; ?>
            <?php echo "<p>{$info_du_compte_etu_res["etatCivil"]}</p>"; ?>
            <?php echo "<p>{$info_du_compte_etu_res["nationalite"]}</p>"; ?>
        </div>
    </div>
    <div class="content_panel coordonnees_panel">
        <h2>Coordonnées</h2>
        <div class="content_panel_row row_content_2_col">
            <?php echo "<p>{$info_du_compte_etu_res["phone"]}</p>"; ?>
            <?php echo "<p>{$info_du_compte_etu_res["email"]}</p>"; ?>
        </div>
        <div class="content_panel_row row_content_1_col">
            <?php echo "<p>{$info_du_compte_etu_res["address"]}</p>"; ?>
        </div>
    </div>
    <div class="content_panel promotion_panel">
        <h2>Promotion</h2>
        <div class="content_panel_row row_content_2_col">
            <?php echo $info_du_compte_etu_res["promotion"] !== null ? "<p>{$info_du_compte_etu_res["promotion"]}</p>" : "<p>Inconnu</p>"; ?>
            <?php echo $info_du_compte_etu_res["groupe"] !== null ? "<p>{$info_du_compte_etu_res["groupe"]}</p>" : "<p>Inconnu</p>"; ?>
        </div>
    </div>
    <div class="content_panel info_pedagogique_panel">
        <h2>Informations pédagogiques</h2>
        <div class="content_panel_row row_content_2_col">
            <label>Numero Etudiant</label>
            <?php echo $info_du_compte_etu_res["idEtudiant"] !== null ? "<p>{$info_du_compte_etu_res["idEtudiant"]}</p>" : "<p>Inconnu</p>"; ?>
        </div>
        <div class="content_panel_row row_content_2_col">
            <label>Type de bac</label>
            <?php echo $info_du_compte_etu_res["typeBac"] !== null ? "<p>{$info_du_compte_etu_res["typeBac"]}</p>" : "<p>Inconnu</p>"; ?>
        </div>
        <div class="content_panel_row row_content_2_col">
            <label>Parcours</label>
            <?php echo $info_du_compte_etu_res["parcoursEtudiant"] !== null ? "<p>{$info_du_compte_etu_res["parcoursEtudiant"]}</p>" : "<p>Inconnu</p>"; ?>
        </div>
        <div class="content_panel_row row_content_2_col">
            <label>Apprentisage</label>
            <?php echo $info_du_compte_etu_res["apprentissageEtudiant"] !== null ? "<p>{$info_du_compte_etu_res["apprentissageEtudiant"]}</p>" : "<p>Inconnu</p>"; ?>
        </div>
        <div class="content_panel_row row_content_2_col">
            <label>Formation en anglais</label>
            <?php echo $info_du_compte_etu_res["formationAnglais"] !== null ? "<p>{$info_du_compte_etu_res["formationAnglais"]}</p>" : "<p>Inconnu</p>"; ?>
        </div>
        <div class="content_panel_row row_content_2_col">
            <label>Statut academique</label>
            <?php echo $info_du_compte_etu_res["statutAcademique"] !== null ? "<p>{$info_du_compte_etu_res["statutAcademique"]}</p>" : "<p>Inconnu</p>"; ?>
        </div>
    </div>
</div>