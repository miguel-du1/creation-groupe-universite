<?php 
class Groupe {

    // Attributs
    private $idGroupe;
    private $codeGroupe;
    private $nbEtudiantGroupe;
    private $listEtudiants;

    // Constructeur
    public function __construct($type_groupe = "TP", $semestre = "1", $c = null, $id = null, $nbE = null) {
        if ($id !== null) $this->idGroupe = $id;
        if ($c !== null) $this->codeGroupe = $type_groupe . " - " . $semestre . $c;
        if ($nbE !== null) $this->nbEtudiantGroupe = $nbE;
        $this->listEtudiants = [];
    }

    public function get($attribut) {return $this->$attribut;}
    public function set($attribut, $valeur) {$this->$attribut = $valeur;}

    // Methodes -> Objet
    public function ajouteEtudiant(Etudiant $etudiant, $max_groupe) {
        if ($this->nbEtudiantGroupe >= $max_groupe) {
            throw new InvalidArgumentException("Groupe " . $this->codeGroupe . " est complet !");
        }
        $this->listEtudiants[] = $etudiant;
        $this->nbEtudiantGroupe++;
    }

    public function getNbHommeGroupe() {
        $nbHommeGroupe = 0;
        foreach ($this->listEtudiants as $etudiant) {
            if ($etudiant->get('sexePersonne') === 'M') {
                $nbHommeGroupe++;
            }
        }
        return $nbHommeGroupe;
    }

    public function getNbFemmeGroupe() {
        $nbFemmeGroupe = 0;
        foreach ($this->listEtudiants as $etudiant) {
            if ($etudiant->get('sexePersonne') === 'F') {
                $nbFemmeGroupe++;
            }
        }
        return $nbFemmeGroupe;
    }

    public function peutAjouter($nb_max) {
        return ($this->nbEtudiantGroupe + 1) <= $nb_max;
    }


    // Methodes -> Database
    static public function getAllGroupe () {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT * FROM AG_GROUPE ORDER BY idGroupe DESC;";
        $resultat = Connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "groupe");
        $lesGroupes = $resultat->fetchAll();
        return $lesGroupes;
    }

    static public function getListeEtudiants ($idGroupe) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "
            SELECT *
            FROM AG_ETUDIANT E
            INNER JOIN AG_PERSONNE P ON E.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
            WHERE E.idGroupe = ?
            ORDER BY P.nomPersonne
        ";
        $resultat = Connexion::pdo()->prepare($requete);
        $resultat->execute([$idGroupe]);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "Etudiant");
        $liste_etudiants = $resultat->fetchAll();
        return $liste_etudiants;
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

    static public function getAllTypesGroupe () {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT * FROM AG_TYPE_GROUPE";
        $resultat = Connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_ASSOC);
        $lesTypes = $resultat->fetchAll();
        return $lesTypes;
    }

    /**
     * Méthode statique pour constituer des groupes selon un algorithme choisi
     */
    static public function constituerGroupes ($id_promo, $mode_algo, $type, $semes) {
        require_once 'modele/promotion.php';
        $liste_etudiants_promo = Promotion::getListeEtudiantsParPromo($id_promo, $type, $semes);

        $liste_groupe_constitue = [];
        require_once 'services/modes/mode3.php';
        switch ($mode_algo) {
            case 'mode_1':
                $liste_groupe_constitue = Mode3::constituerGlouton2($liste_etudiants_promo, $type, $semes);
                break;
            case 'mode_2':
                $liste_groupe_constitue = Mode3::constituerGlouton2($liste_etudiants_promo, $type, $semes);
                break;
            case 'mode_3':
                $liste_groupe_constitue = Mode3::constituerGlouton2($liste_etudiants_promo, $type, $semes);
                break;
        }

        return $liste_groupe_constitue;
    }
}
?>