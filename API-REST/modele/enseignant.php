<?php
class Enseignant {
    
    // Attributs
    private $conn;

    // Constructeur
    public function __construct ($db) {
        $this->conn = $db;
    }

    // Methodes
    public function getAll () {
        $query = "  SELECT 
                        E.idEnseignant, 
                        P.nomPersonne AS nom,
                        P.prenomPersonne AS prenom,
                        P.emailPersonne AS email,
                        P.sexePersonne AS sexe
                    FROM AG_ENSEIGNANT E
                    INNER JOIN AG_PERSONNE P ON E.idSecuriteSocialPersonne = P.idSecuriteSocialPersonne";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}
?>