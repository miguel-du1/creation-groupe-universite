<?php 
session_start();
require_once("config/connexion.php");

function afficherUIGererCompteEns () {
    require __DIR__ . '/../vue/compte_Enseignant/compte_Admin/gererCompteEns.php';
}

function modifierInfoEns () {
    $numSocial = htmlspecialchars($_POST['numSocial_ens']);
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
    $departement = $_POST['departement_modifier'] ?? null;
    $responsableMatiere = $_POST['responsable_modifier'] ?? null;
    $roleCompte = htmlspecialchars($_POST['role_compte_modifier']);

    $address_complet = "$address, $codePostal $pays";
    
    if ($responsableMatiere === '') {
        $responsableMatiere = null;
    }
    if ($departement === '') {
        $departement = null;
    }

    Connexion::connect();
    $stmt = Connexion::pdo()->prepare("CALL modifier_info_ensei(?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([
        $numSocial, $nom, $prenom, $dateNaissance, $sexe, $etatCivil, $nationalite, $tel, $email, $address_complet, $departement, $responsableMatiere, $roleCompte
    ]);
    header('Location: index.php?page=compteEns');
    exit;
}
?>