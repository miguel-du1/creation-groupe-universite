<?php
require_once("config/connexion.php");

function afficherUIEtu() {
    require __DIR__ . '/../vue/compte_Etudiant/promotionEtudiant.php';
}

function afficherUIEns() {
    require __DIR__ . '/../vue/compte_Enseignant/listeGroupesPromo.php';
}

function afficherListeEtudiantPromoUI() {
    require __DIR__ . '/../vue/compte_Enseignant/listeEtudiantsPromo.php';
}
?>

