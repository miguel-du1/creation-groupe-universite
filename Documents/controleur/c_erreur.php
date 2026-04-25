<?php
session_start();
require_once("config/connexion.php");

function afficherUI() {
    require __DIR__ . '/../vue/compte_Etudiant/erreurEtudiant.php';
}

function envoyerErreur() {
    Connexion::connect();
    $id_user = $_POST['id_user'];
    $idType = $_POST['typeErreur'];
    $description = $_POST['description'];
    $dateEnvoie = $_POST['dateEnvoie'];

    // Supprimer les anciens reponses
    $stmt = Connexion::pdo()->prepare("CALL envoyer_erreur_etudiant(?,?,?,?)");
    $stmt->execute([$id_user, $idType, $description, $dateEnvoie]);

    if ($stmt) {
        header('Location: index.php?page=erreurEtu&success=true');
    } else {
        header('Location: index.php?page=erreurEtu&success=false');
    }
    exit;
}
?>