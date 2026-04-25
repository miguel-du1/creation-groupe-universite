<?php
declare(strict_types=1);

define('BASE_PATH', __DIR__);
define('VIEW_PATH', BASE_PATH . '/vue');

$url = trim($_GET['url'] ?? '', '/');

// Par défaut, redirige vers l'accueil
$page = $_GET['page'] ?? 'accueil';
$methode = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = isset($_POST['action']) ? $_POST['action'] : '';

/**
 * Routeur principal du site
 * Gère l'affichage des vues et l'appel des contrôleurs en fonction du paramètre 'page'
 */


switch ($page) {
    case 'accueil':
        require VIEW_PATH . '/accueil.php';
        break;

    case 'login':
        require_once __DIR__ . '/controleur/c_seConnecter.php';
        if ($methode === "POST") login();
        else afficherUI();
        break;
    case 'logout':
        require_once __DIR__ . '/controleur/c_seConnecter.php';
        if ($methode === "POST") logout();
        break;

    case 'register':
        require_once __DIR__ . '/controleur/c_CreeCompte.php';
        if ($methode === "POST") inscrireCompte();
        else afficherUI();
        break;

    case 'homepage':
        require VIEW_PATH . '/homePage.php';
        break;
    
    // ------ COMPTE ETUDIANT ------ //
    case 'infoPersonneEtu':
        require_once __DIR__ . '/controleur/c_avt.php';
        if ($methode === "POST" && $action == "save_avt") enregistrerNouveauAvt();
        else afficherUIInfoCompteEtu();
        break;

    case 'promotionEtu':
        require_once __DIR__ . '/controleur/c_promotion.php';
        afficherUIEtu();
        break;
    
    case 'covoiturageEtu':
        require_once __DIR__ . '/controleur/c_covoiturage.php';
        if ($methode === "POST") {
            switch ($action) {
                case 'inscrire':
                    inscrireCovoiturage($_SESSION['user_id'], $_POST['covoiturage']);
                    break;
                case 'annuler':
                    annulerCovoiturage($_SESSION['user_id']);
                    break;
            }
        }
        else afficherUI();
        break;
    
    case 'sondageEtu':
        require_once __DIR__ . '/controleur/c_sondage.php';
        if ($methode === "POST") envoyerReponses();
        else afficherUI();
        break;
    
    case 'erreurEtu':
        require_once __DIR__ . '/controleur/c_erreur.php';
        if ($methode === "POST") envoyerErreur();
        else afficherUI();
        break;
    
    // ------ COMPTE ENSEIGNANT ------ //
    case 'infoPersonneEns':
        require_once __DIR__ . '/controleur/c_avt.php';
        if ($methode === "POST" && $action == "save_avt") enregistrerNouveauAvt();
        else afficherUIInfoCompteEns();
        break;
    case 'promotionEns':
        require_once __DIR__ . '/controleur/c_promotion.php';
        afficherListeEtudiantPromoUI();
        break;
    case 'groupeEns':
        require_once __DIR__ . '/controleur/c_promotion.php';
        afficherUIEns();
        break;
    case 'profilEtu':
        require VIEW_PATH . '/compte_Enseignant/infoCompteEtudiant.php';
        break;

    // ------ COMPTE ADMIN ------ //
    case 'compteEns':
        require_once __DIR__ . '/controleur/c_admin.php';
        if ($methode === "POST") modifierInfoEns();
        else afficherUIGererCompteEns();
        break;
    
    // ------ COMPTE RESPONSABLE ------ //
    case 'promotionRes':
        require_once __DIR__ . '/controleur/c_responsable.php';
        afficherUIPromo();
        break;
    case 'groupeRes':
        require_once __DIR__ . '/controleur/c_responsable.php';
        afficherUIGroupe();
        break;
    case 'covoiturageRes':
        require_once __DIR__ . '/controleur/c_responsable.php';
        if ($methode === "POST" && $_POST['action'] == "modifierMax") modifierNbMaxCovoiturage();
        elseif ($methode === "POST" && $_POST['action'] == "creer") creerCovoiturage();
        else afficherUICovoiturage();
        break;
    case 'sondageRes':
        require_once __DIR__ . '/controleur/c_responsable.php';
        afficherUISondage();
        break;
    case 'sondageDetailsRes':
        require_once __DIR__ . '/controleur/c_responsable.php';
        afficherUIDetailsSondage();
        break;
    case 'creerSondageRes':
        require_once __DIR__ . '/controleur/c_responsable.php';
        if ($methode === "POST") creerSondageNouveau();
        else afficherUICreerSondage();
        break;
    case 'notesRes':
        require_once __DIR__ . '/controleur/c_responsable.php';
        if ($methode === "POST" && $action === "importer") importerNoteCSV();
        elseif ($methode === "GET" && isset($_GET['action']) && $_GET['action'] == 'exporter') exporterNoteCSV();
        else afficherUIGererNotesEtudiants();
        break;
    case 'constituerGroupeRes':
        require_once __DIR__ . '/controleur/c_responsable.php';
        if ($methode === "POST" && $action === "") ;
        elseif ($methode === "GET" && isset($_GET['action']) && $_GET['action'] === "constituer") constituerGroupeParPromo();
        elseif (isset($_GET['action']) && $_GET['action'] === "confimer") sauvegarderNouveauGroupesDB();
        else afficherUIConstituerGroupes();
        break;
    case 'ajouterEtuRes':
        require_once __DIR__ . '/controleur/c_responsable.php';
        if ($methode === "POST") ajouterEtudiantPromo();
        else afficherUIAjouterEtuPromo();
        break;
    case 'profilEtuRes':
        require_once __DIR__ . '/controleur/c_responsable.php';
        if ($methode === "POST") modifierInfoEtudiant();
        elseif ($methode === "GET" && isset($_GET['action']) && $_GET['action'] == 'exporter') exporterDonneesEtudiant();
        else afficherUIInfoCompteEtudiant();
        break;
    case 'supprimerEtuPromo':
        require_once __DIR__ . '/controleur/c_responsable.php';
        if ($methode === "POST") supprimerEtudiantPromo();
        break;

    default:
        require VIEW_PATH . '/accueil.php';
        break;
}
?>