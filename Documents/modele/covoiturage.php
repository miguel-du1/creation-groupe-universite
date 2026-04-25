<?php 
class Covoiturage {

    // Attributs
    protected $id;
    protected $nbMax;
    protected $etat;

    // Constructeur
    function __construct ($e=null, $id=null, $nbM=null) {
        $this->id = $id;
        $this->nbMax = $nbM;
        $this->etat = $e;
    }

    public function get($attribut) {return $this->$attribut;}
    public function set($attribut, $valeur) {$this->$attribut = $valeur;}

    // Methodes
    static public function getAllCovoiturage () {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT
                        C.idCovoiturage,
                        C.nbMax as maximum, 
                        C.etatCovoiturage,
                        COUNT(*) as nbCourant
                    FROM AG_COVOITURAGE C
                    LEFT JOIN AG_COVOITURAGE_ETUDIANT CE ON C.idCovoiturage = CE.idCovoiturage
                    GROUP BY C.idCovoiturage
                    ORDER BY C.idCovoiturage";
        $resultat = Connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "covoiturage");
        $lesCovoiturages = $resultat->fetchAll();
        return $lesCovoiturages;
    }

    static public function getListeParticipants ($idCovoiturage) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT 
                        P.nomPersonne as nom,
                        P.prenomPersonne as prenom
                    FROM AG_COVOITURAGE C
                    INNER JOIN AG_COVOITURAGE_ETUDIANT CE ON C.idCovoiturage = CE.idCovoiturage
                    INNER JOIN AG_ETUDIANT E ON CE.idEtudiant = E.idEtudiant
                    INNER JOIN AG_PERSONNE P ON E.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
                    WHERE C.idCovoiturage = ?
                    AND CE.roleCovoiturage = 'Passager'";
        $resultat = Connexion::pdo()->prepare($requete);
        $resultat->execute([$idCovoiturage]);
        $resul = $resultat->fetchAll(PDO::FETCH_ASSOC);
        return $resul;
    }

    static public function getConducteur ($idCovoiturage) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT 
                        P.nomPersonne as nom,
                        P.prenomPersonne as prenom
                    FROM AG_COVOITURAGE C
                    INNER JOIN AG_COVOITURAGE_ETUDIANT CE ON C.idCovoiturage = CE.idCovoiturage
                    INNER JOIN AG_ETUDIANT E ON CE.idEtudiant = E.idEtudiant
                    INNER JOIN AG_PERSONNE P ON E.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
                    WHERE C.idCovoiturage = ?
                    AND CE.roleCovoiturage = 'Conducteur'";
        $resultat = Connexion::pdo()->prepare($requete);
        $resultat->execute([$idCovoiturage]);
        $resul = $resultat->fetch(PDO::FETCH_ASSOC);
        return $resul;
    }
}
?>