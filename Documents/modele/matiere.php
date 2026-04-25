<?php 
class Matiere {

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
    static public function getAllMatiere() {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT * FROM AG_MATIERE ORDER BY idMatiere ASC;";
        $resultat = Connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "matiere");
        $lesMatieres = $resultat->fetchAll();
        return $lesMatieres;
    }

    static public function getAllMatiereParControle($idControle) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT 
                        idMatiere,
                        nomMatiere
                    FROM AG_MATIERE
                    WHERE idMatiere IN (SELECT idMatiere FROM AG_MATIERE_CONTROLE WHERE idControle = ?) 
                    ORDER BY idMatiere ASC";
        $resultat = Connexion::pdo()->prepare($requete);
        $resultat->execute([$idControle]);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "matiere");
        $lesMatieres = $resultat->fetchAll();
        return $lesMatieres;
    }

    static public function getMatiere($idMatiere) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT *
                    FROM AG_MATIERE
                    WHERE idMatiere = ?";
        $resultat = Connexion::pdo()->prepare($requete);
        $resultat->execute([$idMatiere]);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "matiere");
        $matiere = $resultat->fetch();
        return $matiere;
    }
}
?>