<?php 
    $role_compte = $_SESSION['role_compte'];
    $nom = $_SESSION['nom'] ?? "";
    $prenom = $_SESSION['prenom'] ?? "";

    require_once 'modele/compte.php';
    $id_user = $_SESSION['user_id'];
    if ($role_compte == "Etudiant") $info_du_compte = Compte::getDetailInfoDuCompteEtu($id_user);
    else $info_du_compte = Compte::getDetailInfoDuCompteEns ($id_user);

    $list_role_compte = array(
        "Admin" => "admin",
        "Responsable" => "res",
        "Enseignant" => "ens",
        "Etudiant" => "etu"
    );

    $list_botton_navi_etu = array(
        "Accueil" => "homepage",
        "Infomations personnelles" => "infoPersonneEtu",
        "Promotions" => "promotionEtu",
        "Covoiturage" => "covoiturageEtu",
        "Sondage" => "sondageEtu",
        "Signaler une erreur" => "erreurEtu"
    );
    $list_botton_navi_ens = array(
        "Accueil" => "homepage",
        "Infomations personnelles" => "infoPersonneEns",
        "Promotions" => "promotionEns",
        "Groupes" => "groupeEns"
    );
    $list_botton_navi_responsable = array(
        "Accueil" => "homepage",
        "Infomations personnelles" => "infoPersonneEns",
        "Promotions" => "promotionRes",
        "Groupes" => "groupeRes",
        "Covoiturage" => "covoiturageRes",
        "Sondage" => "sondageRes",
        "Gérer la note" => "notesRes",
        "Constituer les groupes" => "constituerGroupeRes"
    );
    $list_botton_navi_admin = array(
        "Accueil" => "homepage",
        "Infomations personnelles" => "infoPersonneEns",
        "Promotions" => "promotionRes",
        "Groupes" => "groupeRes",
        "Covoiturage" => "covoiturageRes",
        "Sondage" => "sondageRes",
        "Gérer la note" => "notesRes",
        "Constituer les groupes" => "constituerGroupeRes",
        "Gérer compte d’enseignant" => "compteEns"
    );

    switch ($list_role_compte[$role_compte]) {
        case "admin":
            $list_button_navi = $list_botton_navi_admin;
            break;
        case "res":
            $list_button_navi = $list_botton_navi_responsable;
            break;
        case "ens":
            $list_button_navi = $list_botton_navi_ens;
            break;
        case "etu":
            $list_button_navi = $list_botton_navi_etu;
            break;
    }

    $page = (isset($_GET["page"]) ? $_GET["page"] : "");
    $pageCourant = "";
    switch ($page) {
        // Page pour les 2 comptes
        case 'homepage':
            $pageCourant = "Acceuil";
            break;

        // ------ COMPTE ETUDIANT ------ //
        case 'infoPersonneEtu':
            $pageCourant = "Infomations personnelles";
            break;
        case 'promotionEtu':
            $pageCourant = "Promotions";
            break;
        case 'covoiturageEtu':
            $pageCourant = "Covoiturage";
            break;
        case 'sondageEtu':
            $pageCourant = "Sondage";
            break;
        case 'erreurEtu':
            $pageCourant = "Signaler une erreur";
            break;

        // ------ COMPTE ENSEIGNANT ------ //
        case 'infoPersonneEns':
            $pageCourant = "Infomations personnelles";
            break;
        case 'promotionEns':
            $pageCourant = "Promotions";
            break;
        case 'groupeEns':
            $pageCourant = "Groupes";
            break;
        case 'covoiturageRes':
            $pageCourant = "Covoiturage";
            break;
        case 'profilEtu':
            if (isset($_GET['groupe'])) {
                $pageCourant = "Groupes";
            } else {
                $pageCourant = "Promotions";
            }
            break;
        
        // ------ COMPTE ADMIN ------ //
        case 'compteEns':
            $pageCourant = "Gérer compte d’enseignant";
            break;
        
        // ------ COMPTE RESPONSABLE ------ //
        case 'promotionRes':
            $pageCourant = "Promotions";
            break;
        case 'groupeRes':
            $pageCourant = "Groupes";
            break;
        case 'sondageRes':
            $pageCourant = "Sondage";
            break;
        case 'sondageDetailsRes':
            $pageCourant = "Sondage";
            break;
        case 'creerSondageRes':
            $pageCourant = "Sondage";
            break;
        case 'notesRes':
            $pageCourant = "Gérer la note";
            break;
        case 'constituerGroupeRes':
            $pageCourant = "Constituer les groupes";
            break;
        case 'ajouterEtuRes':
            $pageCourant = "Promotions";
            break;
        case 'profilEtuRes':
            if (isset($_GET['groupe'])) {
                $pageCourant = "Groupes";
            } else {
                $pageCourant = "Promotions";
            }
            break;

        default:
            $pageCourant = "Acceuil";
            break;
    }

    require_once("vue/sidebarUI.php");
?>