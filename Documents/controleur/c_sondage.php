<?php
session_start();
require_once("config/connexion.php");

function afficherUI () {
    Connexion::connect();
    $idUser = $_SESSION['user_id'];
    $stmt = Connexion::pdo()->prepare("
        SELECT DISTINCT idSondage
        FROM AG_SONDAGE_ETUDIANT SE
        INNER JOIN AG_ETUDIANT E ON SE.idEtudiant = E.idEtudiant
        INNER JOIN AG_PERSONNE P ON E.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
        INNER JOIN AG_COMPTE C ON P.idSecuriteSocialPersonne = C.idSecuriteSocialPersonne
        WHERE C.idCompte = ?
    ");
    $stmt->execute([$idUser]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $_SESSION['sondageRepondu'] = $rows;

    require __DIR__ . '/../vue/compte_Etudiant/sondageEtudiant.php';
}

function envoyerReponses () {
    Connexion::connect();

    $dateEnvoie = $_POST['dateEnvoie'];
    $id_user = $_POST['id_user'];
    $id_sondage = $_POST['id_sondage'];
    $reponses = $_POST['reponses'];

    // Supprimer les anciens reponses
    $stmt = Connexion::pdo()->prepare("CALL delete_sondage_etudiant(?,?)");
    $stmt->execute([$id_user, $id_sondage]);
    $stmt->closeCursor();

    foreach ($reponses as $critere => $reponses) {
        if (is_array($reponses)) {
            $reponse_json = json_encode($reponses, TRUE);
            $stmt = Connexion::pdo()->prepare("CALL ajouter_sondage_etudiant(?,?,?,?,?)");
            $stmt->execute([$id_user, $id_sondage, $critere, $reponse_json, $dateEnvoie]);
            $stmt->closeCursor();
        } else {
            $stmt = Connexion::pdo()->prepare("CALL ajouter_sondage_etudiant(?,?,?,?,?)");
            $stmt->execute([$id_user, $id_sondage, $critere, $reponses, $dateEnvoie]);
            $stmt->closeCursor();
        }
    } 

    header('Location: index.php?page=sondageEtu');
    exit;
}
?>