<?php
require_once("config/connexion.php");

/**
 * Affiche l'interface de création de compte
 */
function afficherUI () {
    require __DIR__ . '/../vue/creerCompte.php';
}

/**
 * Gère l'inscription d'un nouvel utilisateur
 */
function inscrireCompte () {
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);
        $confirm = htmlspecialchars($_POST['confirm_password']);
        $role = htmlspecialchars($_POST['role']);
    
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $dateNaissance = htmlspecialchars($_POST['dateNaissance']);
        $promotion = isset($_POST['promotion']) ? htmlspecialchars($_POST['promotion']) : "";
        $sexe = htmlspecialchars($_POST['sexe']);
        $etatCivil = htmlspecialchars($_POST['etatCivil']);
        $nationalite = htmlspecialchars($_POST['nationalite']);
        $numSocial = htmlspecialchars($_POST['numSocial']);
        
        $tel = htmlspecialchars($_POST['tel']);
        $email = htmlspecialchars($_POST['email']);
    
        $adresse = htmlspecialchars($_POST['adresse']);
        $codePostal = htmlspecialchars($_POST['codePostal']);
        $ville = htmlspecialchars($_POST['ville']);
        $pays = htmlspecialchars($_POST['pays']);
    
        // Sauvegarder pour remplir le formulaire en cas d'entrée invalide
        $_SESSION['old'] = [
            'username' => $username,
            'password' => $password,
            'confirm' => $confirm,
            'role' => $role,
            'nom' => $nom,
            'prenom' => $prenom,
            'dateNaissance' => $dateNaissance,
            'promotion' => $promotion,
            'sexe' => $sexe,
            'etatCivil' => $etatCivil,
            'nationalite' => $nationalite,
            'numSocial' => $numSocial,
            'tel' => $tel,
            'email' => $email,
            'adresse' => $adresse,
            'codePostal' => $codePostal,
            'ville' => $ville,
            'pays' => $pays,
        ];
    
        // Vérifier si l'utilisateur existe déjà
        Connexion::connect();
        $stmt = Connexion::pdo()->prepare("
            SELECT COUNT(*) 
            FROM AG_COMPTE 
            WHERE nomUtilisateur = :username
            OR idSecuriteSocialPersonne = :idSocial
        ");
        $stmt->execute([
            'username' => $username,
            'idSocial' => $numSocial
        ]);
        $count = (int) $stmt->fetchColumn();
        $userExists = ($count > 0) ? true : false;
    
        if ($userExists) {
            $_SESSION['error_message'] = "Cet utilisateur existe déjà";
            header('Location: index.php?page=register');
            exit();
        }
    
        if ($password !== $confirm) {
            $_SESSION['error_message'] = "Les mots de passe ne correspondent pas";
            header('Location: index.php?page=register');
            exit();
        }

        if ($role === "Etudiant" && $promotion === "") {
            $_SESSION['error_message'] = "Année d’entrée de l’étudiant obligatoire";
            header('Location: index.php?page=register');
            exit();
        }

        // Sauvegarder dans la base de données
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $addressComplet = "$adresse, $codePostal $ville, $pays";
    
        $requeteInsertPersonne = Connexion::pdo()->prepare("
            INSERT INTO `AG_PERSONNE`(`idSecuriteSocialPersonne`, `nomPersonne`, `prenomPersonne`, `emailPersonne`, `telPersonne`, `naissancePersonne`, `sexePersonne`, `etatCivilPersonne`, `adrPersonne`, `idPays`)
            VALUES (:numSocial, :nom, :prenom, :email, :tel, :dateNaissance, :sexe, :etatCivil, :adresse, :pays)
        ");
        $requeteInsertPersonne->execute([
            'numSocial' => $numSocial,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'tel' => $tel,
            'dateNaissance' => $dateNaissance,
            'sexe' => $sexe,
            'etatCivil' => $etatCivil,
            'adresse' => $addressComplet,
            'pays' => $nationalite
        ]);
        
        if ($role === "Enseignant") {
            $requeteInsertEnseignant = Connexion::pdo()->prepare("CALL ajouter_ensei_promo(?, ?, ?)");
            $requeteInsertEnseignant->execute([null, null, $numSocial]);
        } elseif ($role === "Etudiant") {
            $requeteInsertEtudiant = Connexion::pdo()->prepare("CALL ajouter_etudiant_promo(?, ?, ?, ?, ?, ?, ?, ?)");
            $requeteInsertEtudiant->execute([null, null, null, null, null, $promotion, null, $numSocial]);
        }
    
        $requeteInsertCompte = Connexion::pdo()->prepare("CALL creer_nouveau_compte(?, ?, ?, ?)");
        $requeteInsertCompte->execute([$username, $password_hash, $role, $numSocial]);
    
        unset($_SESSION['old']);
    
        header('Location: index.php?page=login&success=created');
        exit;
    }
}
?>