<?php
session_start();
require_once("config/connexion.php");

function afficherUI () {
    Connexion::connect();
    $idUser = $_SESSION['user_id'];
    $stmt = Connexion::pdo()->prepare("
        SELECT COUNT(*) AS existe 
        FROM AG_ETUDIANT E
        INNER JOIN AG_COVOITURAGE_ETUDIANT CE ON CE.idEtudiant = E.idEtudiant
        INNER JOIN AG_COMPTE C ON E.idSecuriteSocialPersonne = C.idSecuriteSocialPersonne
        WHERE C.idCompte = ?
    ");
    $stmt->execute([$idUser]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $existe = (int)($row['existe'] ?? 0);

    if ($existe > 0) $_SESSION['covoiturage'] = true;
    else $_SESSION['covoiturage'] = false;

    require __DIR__ . '/../vue/compte_Etudiant/covoiturageEtudiant.php';
}

function inscrireCovoiturage ($idUser, $idCovoi) {
    Connexion::connect();
    $stmt = Connexion::pdo()->prepare("CALL ajouter_participant_covoiturage(?,?)");
    $stmt->execute([$idCovoi, $idUser]);
    header('Location: index.php?page=covoiturageEtu');
    exit;
}

function annulerCovoiturage ($idUser) {
    Connexion::connect();
    $stmt = Connexion::pdo()->prepare("CALL annuler_participant_covoiturage(?)");
    $stmt->execute([$idUser]);
    header('Location: index.php?page=covoiturageEtu');
    exit;
}


?>