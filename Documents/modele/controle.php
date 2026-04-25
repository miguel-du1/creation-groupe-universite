<?php 
class Controle {

    // Attributs

    // Constructeur
    function __construct () {}

    public function get($attribut) {return $this->$attribut;}
    public function set($attribut, $valeur) {$this->$attribut = $valeur;}

    // Methodes
    static public function getAllControles() {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT 
                        idControle,
                        nomControle,
                        DATE_FORMAT(dateControle, '%d/%m/%Y') as dateControleC,
                        tempsDebut,
                        tempsFin
                    FROM AG_CONTROLE 
                    ORDER BY dateControle DESC";
        $resultat = Connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "controle");
        $lesControles = $resultat->fetchAll();
        return $lesControles;
    }

    static public function getAllControlesParMatiere($id_matiere) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT 
                        idControle,
                        nomControle,
                        DATE_FORMAT(dateControle, '%d/%m/%Y') as dateControleC,
                        tempsDebut,
                        tempsFin
                    FROM AG_CONTROLE 
                    WHERE idControle IN (SELECT idControle FROM AG_MATIERE_CONTROLE WHERE idMatiere = ?)
                    ORDER BY dateControle DESC";
        $resultat = Connexion::pdo()->prepare($requete);
        $resultat->execute([$id_matiere]);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "controle");
        $lesControles = $resultat->fetchAll();
        return $lesControles;
    }

    static public function getListeEtudiantsParControle ($id_controle) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT 
                        E.idEtudiant,
                        P.nomPersonne as nom,
                        P.prenomPersonne as prenom,
                        P.emailPersonne as email,
                        CE.note,
                        CE.commentaire as remarque
                    FROM AG_CONTROLE_ETUDIANT CE
                    INNER JOIN AG_ETUDIANT E ON CE.idEtudiant = E.idEtudiant
                    INNER JOIN AG_PERSONNE P ON P.idSecuriteSocialPersonne = E.idSecuriteSocialPersonne 
                    WHERE CE.idControle = ?
                    ORDER BY nom";
        $resultat = Connexion::pdo()->prepare($requete);
        $resultat->execute([$id_controle]);
        $resultat->setFetchMode(PDO::FETCH_ASSOC);
        $liste_etudiants = $resultat->fetchAll();
        return $liste_etudiants;
    }

    static public function getControle($idControle) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT *
                    FROM AG_CONTROLE
                    WHERE idControle = ?";
        $resultat = Connexion::pdo()->prepare($requete);
        $resultat->execute([$idControle]);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "controle");
        $controle = $resultat->fetch();
        return $controle;
    }
}
?>