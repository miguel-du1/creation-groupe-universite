<?php 
require_once 'modele/etudiant.php';

class Promotion{

    public const LIST_PARCOURS = ['A', 'B', 'C'];

    // Attributes
    protected $idPromotion;
    protected $anneePromotion;
    protected $nbGroupePromo;
    protected $nbEtudiantPromo;
    protected $listGroupes;
    protected $listEtudiants;

    // Constructeur
    public function __construct($idPromotion = null, $annee = null, $nbEtudiant = null, $nbGroupe = null) {
        if ($idPromotion !== null) $this->idPromotion = $idPromotion;
        if ($annee !== null) $this->anneePromotion = $annee;
        if ($nbEtudiant !== null) $this->nbEtudiantPromo = $nbEtudiant;
        if ($nbGroupe !== null) $this->nbGroupePromo = $nbGroupe;
        $this->listGroupes = [];
        $this->listEtudiants = [];
    }

    public function get($attribut) {return $this->$attribut;}
    public function set($attribut, $valeur) {$this->$attribut = $valeur;}

    // Methodes -> OBJETS
    public function getNbHommePromo () {
        $compter = 0;
        foreach ($this->listEtudiants as $etudiant) {
            if ($etudiant->get('sexePersonne') == "M") $compter++; 
        }
        return $compter;
    }
    public function getNbFemmePromo () {
        $compter = 0;
        foreach ($this->listEtudiants as $etudiant) {
            if ($etudiant->get('sexePersonne') == "F") $compter++; 
        }
        return $compter;
    }


    // Methodes -> DATABASE
    static public function getAllPromotion() {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT * FROM AG_PROMOTION ORDER BY anneePromotion DESC;";
        $resultat = Connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "Promotion");
        $lesPromotions = $resultat->fetchAll();
        return $lesPromotions;
    }

    static public function getPromotion($idPromo) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT * FROM AG_PROMOTION WHERE idPromotion = ?";
        $resultat = Connexion::pdo()->prepare($requete);
        $resultat->execute([$idPromo]);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "Promotion");
        $promotion = $resultat->fetch();
        return $promotion;
    }

    static public function getPromotionPlusRecente() {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT * 
                    FROM AG_PROMOTION
                    ORDER BY anneePromotion DESC
                    LIMIT 1;";
        $resultat = Connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "Promotion");
        $promotion = $resultat->fetch();
        return $promotion;
    }

    static public function chercherGroupesParIdPromo ($idPromo) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT idGroupe, codeGroupe FROM AG_GROUPE WHERE idPromotion = ? ORDER BY codeGroupe ASC";
        $resultat = Connexion::pdo()->prepare($requete);

        $resultat->execute([$idPromo]);
        $resul = $resultat->fetchAll(PDO::FETCH_ASSOC);
        return $resul;
    }
    
    static public function getListeEtudiantsParPromo ($idPromo) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "
            SELECT * 
            FROM AG_ETUDIANT E
            INNER JOIN AG_PERSONNE P ON E.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
            WHERE idPromotion = ?
            ORDER BY P.nomPersonne
        ";
        $resultat = Connexion::pdo()->prepare($requete);
        $resultat->execute([$idPromo]);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "Etudiant");
        $liste_etudiants = $resultat->fetchAll();
        return $liste_etudiants;
    }
}
?>