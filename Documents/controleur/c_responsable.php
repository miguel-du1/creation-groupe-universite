<?php 
require_once 'modele/groupe.php';
require_once 'modele/etudiant.php';
require_once 'modele/personne.php';
session_start();
require_once("config/connexion.php");

/**
 * Affiche la liste des étudiants d'une promotion (Vue Responsable)
 */
function afficherUIPromo () {
    require __DIR__ . '/../vue/compte_Enseignant/compte_Responsable/listeEtudiantsPromoRes.php';
}

/**
 * Affiche la liste des groupes d'une promotion
 */
function afficherUIGroupe () {
    require __DIR__ . '/../vue/compte_Enseignant/compte_Responsable/listeGroupesPromoRes.php';
}

function afficherUICovoiturage () {
    require __DIR__ . '/../vue/compte_Enseignant/compte_Responsable/covoiturageRes.php';
}

function afficherUISondage () {
    require __DIR__ . '/../vue/compte_Enseignant/compte_Responsable/sondageRes.php';
}

function afficherUIDetailsSondage () {
    require __DIR__ . '/../vue/compte_Enseignant/compte_Responsable/infoSondageRes.php';
}

function afficherUICreerSondage () {
    require __DIR__ . '/../vue/compte_Enseignant/compte_Responsable/creationSondageRes.php';
}

function afficherUIGererNotesEtudiants () {
    require __DIR__ . '/../vue/compte_Enseignant/compte_Responsable/importerNotesRes.php';
}

function afficherUIConstituerGroupes () {
    require __DIR__ . '/../vue/compte_Enseignant/compte_Responsable/constituerGroupes.php';
}

function afficherUIAjouterEtuPromo () {
    require __DIR__ . '/../vue/compte_Enseignant/compte_Responsable/ajouterEtudiantPromo.php';
}

function afficherUIInfoCompteEtudiant () {
    require __DIR__ . '/../vue/compte_Enseignant/compte_Responsable/infoCompteEtudiantRes.php';
}



// METHODES
function modifierInfoEtudiant() {
    $numSocial = htmlspecialchars($_POST['numSocial_etu']);
    $nom = htmlspecialchars($_POST['nom_modifier']);
    $prenom = htmlspecialchars($_POST['prenom_modifier']);
    $dateNaissance = htmlspecialchars($_POST['dateNaissance_modifier']);
    $sexe = htmlspecialchars($_POST['sexe_modifier']);
    $etatCivil = htmlspecialchars($_POST['etatCivil_modifier']);
    $nationalite = htmlspecialchars($_POST['nationalite_modifier']);
    $tel = htmlspecialchars($_POST['tel_modifier']);
    $email = htmlspecialchars($_POST['email_modifier']);
    $address = htmlspecialchars($_POST['address_modifier']);
    $codePostal = htmlspecialchars($_POST['codePostal_modifier']);
    $pays = htmlspecialchars($_POST['pays_modifier']);
    $typeBac = htmlspecialchars($_POST['typeBac_modifier']) ?? null;
    $parcours = htmlspecialchars($_POST['parcours_modifier']) ?? null;
    $apprentisage = htmlspecialchars($_POST['apprentisage_modifier']) ?? null;
    $formation_anglais = htmlspecialchars($_POST['formation_anglais_modifier']) ?? null;
    $statut_academique = htmlspecialchars($_POST['statut_academique_modifier']) ?? null;

    $address_complet = "$address, $codePostal $pays";
    
    if ($typeBac === '') $typeBac = null;
    if ($parcours === '') $parcours = null;
    if ($apprentisage === '') $apprentisage = null;
    if ($formation_anglais === '') $formation_anglais = null;
    if ($statut_academique === '') $statut_academique = null;
    

    Connexion::connect();
    $stmt = Connexion::pdo()->prepare("CALL modifier_info_etudiant (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([
        $numSocial, $nom, $prenom, $dateNaissance, $sexe, $etatCivil, $nationalite, $tel, $email, $address_complet, $typeBac, $parcours, $apprentisage, $formation_anglais, $statut_academique
    ]);

    $id_promo = $_POST['promo'];
    $id_etudiant = $_POST['etudiant'];

    header("Location: index.php?page=profilEtuRes&promo=$id_promo&etudiant=$id_etudiant&edit=false");
    exit;
}

/**
 * Ajoute un étudiant à une promotion
 */
function ajouterEtudiantPromo () {
    $id_etudiant = $_POST['etudiant'];
    $id_promotion = $_POST['promo'];

    Connexion::connect();
    $stmt = Connexion::pdo()->prepare("
        UPDATE AG_ETUDIANT SET idPromotion = ? WHERE idEtudiant = ?;
        UPDATE AG_PROMOTION SET nbEtudiantPromo = nbEtudiantPromo + 1 WHERE idPromotion = ?;
    ");
    $stmt->execute([$id_promotion, $id_etudiant, $id_promotion]);
    header('Location: index.php?page=ajouterEtuRes');
    exit;
}

function supprimerEtudiantPromo () {
    $id_etudiant = $_POST['etudiant'];
    $id_promo = $_POST['promo'];
    if (isset($_POST['groupe'])) $id_groupe = $_POST['groupe'];

    Connexion::connect();
    $stmt = Connexion::pdo()->prepare("
        UPDATE AG_ETUDIANT SET idPromotion = null WHERE idEtudiant = ?;
        UPDATE AG_PROMOTION SET nbEtudiantPromo = nbEtudiantPromo - 1 WHERE idPromotion = ?;
    ");
    $stmt->execute([$id_etudiant, $id_promo]);
    if (isset($_POST['groupe'])) header("Location: index.php?page=promotionRes&promo=$id_promo&groupe=$id_groupe");
    else header("Location: index.php?page=promotionRes&promo=$id_promo");
    exit;
}

function exporterDonneesEtudiant () {
    $id_etudiant = $_GET['etudiant'];
    
    Connexion::connect();
    $stmt = Connexion::pdo()->prepare("
        SELECT 
            P.idSecuriteSocialPersonne,
            E.idEtudiant,
            P.nomPersonne,
            P.prenomPersonne,
            P.naissancePersonne,
            P.sexePersonne,
            P.etatCivilPersonne,
            PY.nomPays,
            P.telPersonne,
            P.emailPersonne,
            P.adrPersonne,
            PM.anneePromotion,
            IFNULL(G.codeGroupe, 'Inconnu'),
            IFNULL(E.typeBac, 'Inconnu'),
            IFNULL(E.parcoursEtudiant, 'Inconnu'),
            IFNULL(E.apprentissageEtudiant, 'Inconnu'),
            IFNULL(E.formationAnglais, 'Inconnu'),
            IFNULL(E.statutAcademique, 'Inconnu')
        FROM AG_ETUDIANT E
        LEFT JOIN AG_PERSONNE P ON E.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
        LEFT JOIN AG_PAYS PY ON P.idPays = PY.idPays
        LEFT JOIN AG_PROMOTION PM ON E.idPromotion = PM.idPromotion
        LEFT JOIN AG_GROUPE G ON E.idGroupe = G.idGroupe
        WHERE E.idEtudiant = ?
    ");
    $stmt->execute([$id_etudiant]);

    $filename = "etudiant_$id_etudiant" . ".csv";
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $out = fopen('php://output', 'w');

    fputcsv($out, [
        'idSecuriteSocialPersonne',
        'idEtudiant',
        'nomPersonne',
        'prenomPersonne',
        'naissancePersonne',
        'sexePersonne',
        'etatCivilPersonne',
        'nomPays',
        'telPersonne',
        'emailPersonne',
        'adrPersonne',
        'anneePromotion',
        'codeGroupe',
        'typeBac',
        'parcoursEtudiant',
        'apprentissageEtudiant',
        'formationAnglais',
        'statutAcademique'
    ], ';');

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($out, $row, ';');
    }

    fclose($out);
    exit;
}

function modifierNbMaxCovoiturage () {
    $id_covoiturage = htmlspecialchars($_POST['covoiturage']);
    $actuel = htmlspecialchars($_POST['actuel']);
    $max_actuel = htmlspecialchars($_POST['max_actuel']);
    $max_modifier = htmlspecialchars($_POST['max_modifier']);
    $etat = "Ouvert";

    if ($max_modifier < $actuel)  {
        header("Location: index.php?page=covoiturageRes&erreur=1");
        exit;
    }

    if ($max_modifier == $actuel) $etat = "Complet";

    Connexion::connect();
    $stmt = Connexion::pdo()->prepare("UPDATE AG_COVOITURAGE SET nbMax = ?, etatCovoiturage = ? WHERE idCovoiturage = ?");
    $stmt->execute([$max_modifier, $etat, $id_covoiturage]);

    header("Location: index.php?page=covoiturageRes");
    exit;
}

function creerCovoiturage () {
    $id_etudiant = $_POST['id_etudiant_conducteur'];

    Connexion::connect();
    $stmt = Connexion::pdo()->prepare("CALL creer_covoiturage (?)");
    $stmt->execute([$id_etudiant]);

    header("Location: index.php?page=covoiturageRes");
    exit;
}

function creerSondageNouveau () {
    Connexion::connect();
    $status = true;
    try {
        // TABLE AG_SONDAGE
        $nomSondage = $_POST['titre'];
        $dateDebut = $_POST['dateDebut'];
        $dateFin = $_POST['dateFin'];
        $criteres = $_POST['criteres'] ?? [];

        if ($dateDebut > $dateFin) {
            header("Location: index.php?page=creerSondageRes&erreur=1");
            exit;
        }

        if (count($criteres) == 0) {
            header("Location: index.php?page=creerSondageRes&erreur=2");
            exit;
        }

        $stmt = Connexion::pdo()->prepare("CALL creation_sondage(?, ?, ?, @id)");
        $ok = $stmt->execute([$nomSondage, $dateDebut, $dateFin]);
        $idSondageCourant = (int)(Connexion::pdo()->query("SELECT @id")->fetchColumn());
        if (!$ok) $status = false;

        // TABLE AG_SONDAGE_ENSEIGNANT
        $id_compte = (int) $_POST['personnesResponsable'];
        $stmt = Connexion::pdo()->prepare("
            INSERT INTO AG_SONDAGE_ENSEIGNANT (idEnseignant, idSondage) 
            VALUES 
            (
                (
                    SELECT E.idEnseignant
                    FROM AG_ENSEIGNANT E
                    INNER JOIN AG_COMPTE C ON E.idSecuriteSocialPersonne = C.idSecuriteSocialPersonne
                    WHERE C.idCompte = ?
                ),
                ?
            )
        ");
        $ok = $stmt->execute([$id_compte, $idSondageCourant]);
        if (!$ok) $status = false;

        // TABLE AG_SONDAGE_CRITERE
        foreach ($criteres as $c) {
            $idCri = (int) $c;
            $stmt = Connexion::pdo()->prepare("INSERT INTO AG_SONDAGE_CRITERE(idSondage, idCritere) VALUES (?,?)");
            $ok = $stmt->execute([$idSondageCourant, $idCri]);
            if (!$ok) $status = false;
        }
        
        header("Location: index.php?page=sondageRes");
        exit;

    } catch(PDOException $e) {
        print "Error!: " . $e->getMessage() . "</br>";
        return false;
    }
}

function exporterNoteCSV () {
    require_once 'modele/matiere.php';
    require_once 'modele/controle.php';

    $id_matiere = $_GET['matiere'];
    $id_controle = $_GET['examen'];
    $controle = Controle::getControle($id_controle);
    $nom_controle = $controle->get('nomControle');
    $date_controle = $controle->get('dateControle');
    
    Connexion::connect();
    $stmt = Connexion::pdo()->prepare("
        SELECT 
            P.idSecuriteSocialPersonne as numSocial,
            E.idEtudiant,
            P.nomPersonne as nom,
            P.prenomPersonne as prenom,
            P.emailPersonne as email,
            CE.note,
            CE.commentaire
        FROM AG_CONTROLE_ETUDIANT CE
        INNER JOIN AG_ETUDIANT E ON CE.idEtudiant = E.idEtudiant
        INNER JOIN AG_PERSONNE P ON P.idSecuriteSocialPersonne = E.idSecuriteSocialPersonne 
        WHERE CE.idControle = ?
        ORDER BY nom
    ");
    $stmt->execute([$id_controle]);

    $filename = "Controle_$id_controle" . "_" . "$nom_controle" . "_" . "$date_controle" . ".csv";
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $out = fopen('php://output', 'w');

    fprintf($out, "\xEF\xBB\xBF");

    fputcsv($out, [
        'numSocial',
        'idEtudiant',
        'nomEtudiant',
        'prenomEtudiant',
        'emailEtudiant',
        'note',
        'commentaire'
    ], ';');

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($out, $row, ';');
    }

    fclose($out);
    exit;
}

function importerNoteCSV () {
    $idControle = isset($_POST['examen']) ? (int)$_POST['examen'] : 0;

    if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
        die("Erreur lors du téléversement du fichier CSV.");
        header("Location: index.php?page=notesRes&matiere={$_POST['matiere']}&examen={$_POST['examen']}&erreur=1");
    }

    $tmp = $_FILES['csv']['tmp_name'];
    $handle = fopen($tmp, 'r');
    if (!$handle) {
        die("Impossible d’ouvrir le fichier CSV.");
        header("Location: index.php?page=notesRes&matiere={$_POST['matiere']}&examen={$_POST['examen']}&erreur=2");
    }

    Connexion::connect();

    $delimiter = ';';

    $firstRow = fgetcsv($handle, 0, $delimiter);
    if ($firstRow === false) {
        fclose($handle);
        die("Le fichier CSV est vide.");
        header("Location: index.php?page=notesRes&matiere={$_POST['matiere']}&examen={$_POST['examen']}&erreur=3");
    }

    $firstRow[0] = preg_replace('/^\xEF\xBB\xBF/', '', $firstRow[0]);

    $hasHeader = false;
    $headerMap = [];

    $normalized = array_map(fn($x) => strtolower(trim((string)$x)), $firstRow);
    if (in_array('idetudiant', $normalized, true)) {
        $hasHeader = true;
        $headerMap = array_flip($normalized);

        foreach (['idetudiant', 'note', 'commentaire'] as $col) {
            if (!isset($headerMap[$col])) {
                fclose($handle);
                die("Colonne manquante dans le CSV : $col");
                header("Location: index.php?page=notesRes&matiere={$_POST['matiere']}&examen={$_POST['examen']}&erreur=4");
            }
        }
    } else {
        $headerMap = ['idetudiant' => 0, 'note' => 1, 'commentaire' => 2];
    }

    $sql = "
        INSERT INTO AG_CONTROLE_ETUDIANT (idControle, idEtudiant, note, commentaire)
        VALUES (:idControle, :idEtudiant, :note, :commentaire)
        ON DUPLICATE KEY UPDATE
            note = VALUES(note),
            commentaire = VALUES(commentaire)
    ";
    $stmt = Connexion::pdo()->prepare($sql);

    $imported = 0;
    $skipped  = 0;

    Connexion::pdo()->beginTransaction();
    try {
        if (!$hasHeader) {
            $row = $firstRow;

            $idEtudiant  = trim($row[$headerMap['idetudiant']] ?? '');
            $noteRaw     = trim($row[$headerMap['note']] ?? '');
            $commentaire = trim($row[$headerMap['commentaire']] ?? '');

            if ($idEtudiant !== '') {
                $note = ($noteRaw === '') ? null : (float)str_replace(',', '.', $noteRaw);

                $stmt->execute([
                    ':idControle'  => $idControle,
                    ':idEtudiant'  => (int)$idEtudiant,
                    ':note'        => $note,
                    ':commentaire' => $commentaire,
                ]);
                $imported++;
            } else {
                $skipped++;
            }
        }

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (!$row || (count($row) === 1 && trim($row[0]) === '')) continue;

            $idEtudiant  = trim($row[$headerMap['idetudiant']] ?? '');
            $noteRaw     = trim($row[$headerMap['note']] ?? '');
            $commentaire = trim($row[$headerMap['commentaire']] ?? '');

            if ($idEtudiant === '') { $skipped++; continue; }

            $note = ($noteRaw === '') ? null : (float)str_replace(',', '.', $noteRaw);

            $stmt->execute([
                ':idControle'  => $idControle,
                ':idEtudiant'  => (int)$idEtudiant,
                ':note'        => $note,
                ':commentaire' => $commentaire,
            ]);
            $imported++;
        }

        Connexion::pdo()->commit();
    } catch (Throwable $e) {
        Connexion::pdo()->rollBack();
        fclose($handle);
        die("Échec de l’importation : " . $e->getMessage());
        header("Location: index.php?page=notesRes&matiere={$_POST['matiere']}&examen={$_POST['examen']}&erreur=5");
    }

    fclose($handle);
    header("Location: index.php?page=notesRes&matiere={$_POST['matiere']}&examen={$_POST['examen']}");
}

/**
 * Constitue les groupes pour une promotion donnée
 */
function constituerGroupeParPromo () {
    require_once 'modele/groupe.php';
    $type_gr = $_GET['type_groupe'];
    $semestre_groupe = $_GET['semestre'];
    $liste_groupes_constitues = Groupe::constituerGroupes($_GET['promo'], $_GET['mode'], $type_gr, $semestre_groupe);

    $_SESSION['liste_groupes_constitues'] = $liste_groupes_constitues;
    $_SESSION['type_groupes_crees'] = $type_gr;
    $_SESSION['semestre_groupe'] = $semestre_groupe;
    header("Location: index.php?page=constituerGroupeRes&promo={$_GET['promo']}&edit=1&status=created");
    exit;
}

/**
 * Sauvegarde les nouveaux groupes constitués dans la base de données
 */
function sauvegarderNouveauGroupesDB() {
    Connexion::connect();

    $liste_groupes_crees = $_SESSION['liste_groupes_constitues'];
    $promotion_G = $_GET['promo'];
    $type_gr = $_SESSION['type_groupes_crees'];
    $semestre_groupe = $_SESSION['semestre_groupe'];
    $compter_groupe = 0;

    foreach ($liste_groupes_crees as $groupe_cree) {
        $compter_groupe += 1;
        $stmt = Connexion::pdo()->prepare("CALL creer_groupe(?, ?, ?, ?, ?, @id)");
        $stmt->execute([
            $groupe_cree->get('codeGroupe'), 
            $type_gr, 
            $promotion_G, 
            $groupe_cree->get('nbEtudiantGroupe'),
            $semestre_groupe
        ]);
        $idGroupeNouveau = (int)(Connexion::pdo()->query("SELECT @id")->fetchColumn());

        $liste_etudiants_groupe = $groupe_cree->get('listEtudiants');
        foreach ($liste_etudiants_groupe as $etudiant) {
            $stmt = Connexion::pdo()->prepare("UPDATE AG_ETUDIANT SET idGroupe = ? WHERE idEtudiant = ?");
            $stmt->execute([$idGroupeNouveau, $etudiant->get('idEtudiant')]);
        }
    }

    $stmt = Connexion::pdo()->prepare("UPDATE AG_PROMOTION SET nbGroupePromo = ? WHERE idPromotion = ?");
    $stmt->execute([$compter_groupe, $promotion_G]);

    unset($_SESSION['liste_groupes_constitues']);
    unset($_SESSION['type_groupes_crees']);
    unset($_SESSION['semestre_groupe']);

    header("Location: index.php?page=constituerGroupeRes");
    exit;
}
?>