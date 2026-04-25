<?php 
require_once 'modele/personne.php';

class Etudiant extends Personne {

    // Attributs
    protected $idEtudiant;
    protected $parcoursEtudiant;
    protected $typeBac;
    protected $formationAnglais;
    protected $apprentissageEtudiant;
    protected $statutAcademique;

    // Constructeur
    public function __construct(
        $id = null,
        $parcours = null,
        $typeBac = null,
        $formaAnglais = null,
        $apprentissage = null,
        $statut = null,

        $idSecuriteSocialPersonne = null,
        $nom = null,
        $prenom = null,
        $email = null,
        $telephone = null,
        $naissance = null,
        $sexe = null,
        $etatCivil = null,
        $adresse = null
    ) {
        parent::__construct(
            $idSecuriteSocialPersonne,
            $nom,
            $prenom,
            $email,
            $telephone,
            $naissance,
            $sexe,
            $etatCivil,
            $adresse
        );
        if ($id !== null) $this->idEtudiant = $id;
        if ($parcours !== null) $this->parcoursEtudiant = $parcours;
        if ($typeBac !== null) $this->typeBac = $typeBac;
        if ($formaAnglais !== null) $this->formationAnglais = $formaAnglais;
        if ($apprentissage !== null) $this->apprentissageEtudiant = $apprentissage;
        if ($statut !== null) $this->statutAcademique = $statut;
    }

    public function get($attribut) {return $this->$attribut;}
    public function set($attribut, $valeur) {$this->$attribut = $valeur;}


    
    // Methodes
    static public function getListeEtudiants () {
        require_once("config/connexion.php");
        Connexion::connect();
        $resultat = Connexion::pdo()->query("
            SELECT 
                P.nomPersonne AS nom,
                P.prenomPersonne AS prenom,
                E.idEtudiant,
                E.idSecuriteSocialPersonne AS numSocial,
                PM.anneePromotion AS promotion
            FROM AG_ETUDIANT E
            LEFT JOIN AG_PERSONNE P ON E.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
            LEFT JOIN AG_PROMOTION PM ON E.idPromotion = PM.idPromotion
            ORDER BY promotion ,nom, prenom
        ");
        $resultat->setFetchMode(PDO::FETCH_CLASS, "etudiant");
        $lesEtudiants = $resultat->fetchAll();
        return $lesEtudiants;
    }
    
    static public function getListeEtudiantsSansCovoiturage () {
        require_once("config/connexion.php");
        Connexion::connect();
        $resultat = Connexion::pdo()->query("
            SELECT 
                P.nomPersonne AS nom,
                P.prenomPersonne AS prenom,
                E.idEtudiant,
                E.idSecuriteSocialPersonne AS numSocial
            FROM AG_ETUDIANT E
            LEFT JOIN AG_PERSONNE P ON E.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
            WHERE E.idEtudiant NOT IN (SELECT idEtudiant FROM AG_COVOITURAGE_ETUDIANT)
        ");
        $resultat->setFetchMode(PDO::FETCH_CLASS, "etudiant");
        $lesEtudiants = $resultat->fetchAll();
        return $lesEtudiants;
    }
}
?>