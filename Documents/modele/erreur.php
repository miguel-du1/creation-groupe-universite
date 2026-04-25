<?php 
class Erreur {

    // Attributs
    protected $id;
    protected $nom;

    // Constructeur
    function __construct ($id=null, $nom=null) {
        $this->id = $id;
        $this->nom = $nom;
    }

    public function get($attribut) {return $this->$attribut;}
    public function set($attribut, $valeur) {$this->$attribut = $valeur;}

    // Methodes
    static public function getAllTypeErreurs() {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT * FROM AG_TYPE_ERREUR";
        $resultat = Connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "erreur");
        $lesTypeErreurs = $resultat->fetchAll();
        return $lesTypeErreurs;
    }
}
?>