<?php 
class Critere {

    // Attributs

    // Constructeur
    function __construct () {}

    public function get($attribut) {return $this->$attribut;}
    public function set($attribut, $valeur) {$this->$attribut = $valeur;}

    // Methodes
    static public function getAllCriteres() {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT * FROM AG_CRITERE";
        $resultat = Connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "critere");
        $lesCriteres = $resultat->fetchAll();
        return $lesCriteres;
    }
}
?>