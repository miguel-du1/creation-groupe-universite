<?php
require_once("config/connexion.php");

/**
 * Affiche l'interface de connexion
 */
function afficherUI() {
    require __DIR__ . '/../vue/seConnecter.php';
}

/**
 * Gère la tentative de connexion de l'utilisateur
 */
function login() {
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = htmlspecialchars($_POST['username']);
        $pass = $_POST['password'];
    
        if (!empty($user) && !empty($pass)) {
    
            Connexion::connect();
    
            $stmt = Connexion::pdo()->prepare("
                SELECT * 
                FROM AG_COMPTE C
                INNER JOIN AG_PERSONNE P ON C.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
                WHERE C.nomUtilisateur = :nom
            ");
            $valeur["nom"] = $user;
    
            $stmt->execute($valeur);
            $utilisateur = $stmt->fetch();
    
            if ($utilisateur) {
                if (password_verify($pass, $utilisateur['motDePasse'])) {

                    $_SESSION['user_id'] = $utilisateur['idCompte'] ?? null;
                    $_SESSION['username'] = $utilisateur['nomUtilisateur'] ?? null;
                    $_SESSION['nom'] = $utilisateur['nomPersonne'] ?? null;
                    $_SESSION['prenom'] = $utilisateur['prenomPersonne'] ?? null;
                    $_SESSION['role_compte'] = $utilisateur['roleCompte'] ?? null;

                    header('Location: index.php?page=homepage');
                    exit();
                } else {
                    header('Location: index.php?page=login&erreur=incorrect');
                }
            } else {
                header('Location: index.php?page=login&erreur=inexiste');
            }
        }
    }
}

/**
 * Déconnecte l'utilisateur et détruit la session
 */
function logout () {
    session_start();
    $_SESSION = [];
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

