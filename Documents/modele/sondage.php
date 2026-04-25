<?php 
class Sondage {

    // Attributs
    protected $id;
    protected $nom;
    protected $debut;
    protected $fin;

    // Constructeur
    function __construct ($id=null, $nom=null, $debut=null, $fin=null) {
        $this->id = $id;
        $this->nom = $nom;
        $this->debut = $debut;
        $this->fin = $fin;
    }

    public function get($attribut) {return $this->$attribut;}
    public function set($attribut, $valeur) {$this->$attribut = $valeur;}

    // Methodes
    static public function getAllSondages() {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT 
                        S.idSondage as idS,
                        S.nomSondage as nomS,
                        DATE_FORMAT(S.dateDebutSondage, '%d-%m-%Y') as debutS,
                        DATE_FORMAT(S.dateFinSondage, '%d-%m-%Y') as finS
                    FROM AG_SONDAGE S
                    ORDER BY debutS DESC, finS ASC";
        $resultat = Connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "sondage");
        $lesSondages = $resultat->fetchAll();
        return $lesSondages;
    }

    static public function getAllSondagesRes() {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT DISTINCT
                        S.idSondage as idS,
                        S.nomSondage as nomS,
                        DATE_FORMAT(S.dateDebutSondage, '%d-%m-%Y') as debutS,
                        DATE_FORMAT(S.dateFinSondage, '%d-%m-%Y') as finS,
                        COUNT(DISTINCT SE.idEtudiant) as nb_reponsesS,
                        P.emailPersonne as emailS
                    FROM AG_SONDAGE S
                    LEFT JOIN AG_SONDAGE_ETUDIANT SE ON S.idSondage = SE.idSondage
                    LEFT JOIN AG_SONDAGE_ENSEIGNANT SEN ON S.idSondage = SEN.idSondage
                    LEFT JOIN AG_ENSEIGNANT E ON SEN.idEnseignant = E.idEnseignant
                    LEFT JOIN AG_PERSONNE P ON E.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
                    GROUP BY S.idSondage, S.nomSondage
                    ORDER BY debutS DESC, finS ASC, idS ASC";
        $resultat = Connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "sondage");
        $lesSondages = $resultat->fetchAll();
        return $lesSondages;
    }

    static public function getSondageRes ($idSondage) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT DISTINCT
                        S.idSondage as idS,
                        S.nomSondage as nomS,
                        DATE_FORMAT(S.dateDebutSondage, '%d-%m-%Y') as debutS,
                        DATE_FORMAT(S.dateFinSondage, '%d-%m-%Y') as finS,
                        COUNT(DISTINCT SE.idEtudiant) as nb_reponsesS,
                        P.emailPersonne as emailS
                    FROM AG_SONDAGE S
                    LEFT JOIN AG_SONDAGE_ETUDIANT SE ON S.idSondage = SE.idSondage
                    LEFT JOIN AG_SONDAGE_ENSEIGNANT SEN ON S.idSondage = SEN.idSondage
                    LEFT JOIN AG_ENSEIGNANT E ON SEN.idEnseignant = E.idEnseignant
                    LEFT JOIN AG_PERSONNE P ON E.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
                    WHERE S.idSondage = ?
                    GROUP BY S.idSondage, S.nomSondage";
        $resultat = Connexion::pdo()->prepare($requete);
        $resultat->execute([$idSondage]);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "sondage");
        $resul = $resultat->fetch();
        return $resul;
    }

    static public function getReponsesSondageRes ($idSondage) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT
                        S.idSondage as idS,                        
                        SE.idEtudiant as id_Etudiant,
                        SE.idCritere as id_Critere,
                        P.nomPersonne as nom,
                        P.prenomPersonne as prenom,
                        P.emailPersonne as email,
                        C.nomCritere as nom_critere,
                        SE.reponseSondage as reponse,
                        SE.dateReponse as date_reponse
                    FROM AG_SONDAGE S
                    LEFT JOIN AG_SONDAGE_ETUDIANT SE ON S.idSondage = SE.idSondage
                    LEFT JOIN AG_ETUDIANT E ON SE.idEtudiant = E.idEtudiant
                    LEFT JOIN AG_PERSONNE P ON E.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne
                    LEFT JOIN AG_CRITERE C ON SE.idCritere = C.idCritere
                    WHERE S.idSondage = ?
                    ORDER BY C.idCritere";
        $resultat = Connexion::pdo()->prepare($requete);
        $resultat->execute([$idSondage]);

        $resul_array = [];
        while ($r = $resultat->fetch(PDO::FETCH_ASSOC)) {
            $idS = $r['idS'];
            $idE = $r['id_Etudiant'];

            if (!isset($resul_array[$idS][$idE])) {
                $resul_array[$idS][$idE] = [
                    'idS' => $idS,
                    'id_Etudiant' => $idE,
                    'nom' => $r['nom'],
                    'prenom' => $r['prenom'],
                    'email' => $r['email'],
                    'date_reponse' => $r['date_reponse'],
                    'reponses' => []
                ];
            }

            $resul_array[$idS][$idE]['reponses'][] = [
                'id_Critere' => $r['id_Critere'],
                'nom_critere' => $r['nom_critere'],
                'reponse' => $r['reponse'],
                'date_reponse' => $r['date_reponse'],
            ];
        }

        return $resul_array;
    }

    static public function getSondage ($idSondage) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT 
                        S.idSondage as idS,
                        S.nomSondage as nomS,
                        DATE_FORMAT(S.dateDebutSondage, '%d-%m-%Y') as debutS,
                        DATE_FORMAT(S.dateFinSondage, '%d-%m-%Y') as finS
                    FROM AG_SONDAGE S
                    WHERE S.idSondage = ?";
        $resultat = Connexion::pdo()->prepare($requete);
        $resultat->execute([$idSondage]);
        $resul = $resultat->fetch(PDO::FETCH_ASSOC);
        return $resul;
    }

    static public function getCritereSondage ($idSondage) {
        require_once("config/connexion.php");
        Connexion::connect();
        $requete = "SELECT 
                        C.idCritere as idC,
                        C.nomCritere as nomC,
                        C.optionsCritere as 'options',
                        TR.nomTypeReponse as type
                    FROM AG_SONDAGE_CRITERE SC
                    INNER JOIN AG_CRITERE C ON SC.idCritere = C.idCritere
                    INNER JOIN AG_TYPE_REPONSE TR ON C.typeReponse = TR.idTypeReponse
                    WHERE SC.idSondage = ?
                    ORDER BY C.idCritere";
        $resultat = Connexion::pdo()->prepare($requete);
        $resultat->execute([$idSondage]);
        $resul = $resultat->fetchAll(PDO::FETCH_ASSOC);
        return $resul;
    }
}
?>